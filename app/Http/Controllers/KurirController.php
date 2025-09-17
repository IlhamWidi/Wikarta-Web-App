<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\GiveAway;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KurirController extends Controller
{
    //
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $cabang_id = $request->get('cabang');
        $search = $request->get('search');
        $user = Auth::user();

        // Query dasar untuk pelanggan (butuh diantar)
        $query = GiveAway::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', '!=', 'SELESAI')
            ->whereNull('kurir_id');

        // Filter berdasarkan allowed_branches jika ada
        if ($user && !empty($user->allowed_branches)) {
            $query->whereHas('pelanggan', function ($q) use ($user) {
                $q->whereIn('branch_id', $user->allowed_branches);
            });
        }

        // Tambah filter cabang jika dipilih
        if ($cabang_id) {
            $query->whereHas('pelanggan', function ($q) use ($cabang_id) {
                $q->where('branch_id', $cabang_id);
            });
        }

        // Filter by customer name/phone
        if ($search) {
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        $pelanggan = $query->get();

        // Query untuk giveaway yang sedang diantar/selesai
        $giveawayQuery = GiveAway::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun);

        // Filter berdasarkan allowed_branches jika ada
        if ($user && !empty($user->allowed_branches)) {
            $giveawayQuery->whereHas('pelanggan', function ($q) use ($user) {
                $q->whereIn('branch_id', $user->allowed_branches);
            });
        }

        // Jika role bukan SUPERUSER, filter berdasarkan kurir_id
        if ($user->role_id != 'SUPERUSER') {
            $giveawayQuery->where('kurir_id', $user->id);
        }

        // Tambah filter cabang jika dipilih
        if ($cabang_id) {
            $giveawayQuery->whereHas('pelanggan', function ($q) use ($cabang_id) {
                $q->where('branch_id', $cabang_id);
            });
        }

        // Filter by customer name/phone
        if ($search) {
            $giveawayQuery->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        $giveaways = $giveawayQuery->get();

        // Ambil data cabang untuk filter
        if ($user && !empty($user->allowed_branches)) {
            $cabangs = Branch::whereIn('id', $user->allowed_branches)->get();
        } else {
            $cabangs = Branch::all();
        }

        return view('pages.kurir.index', compact('pelanggan', 'giveaways', 'cabangs', 'search'));
    }

    /**
     * Handle pengambilan order oleh kurir
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function pickOrder(Request $request, $id)
    {
        try {
            $giveaway = GiveAway::findOrFail($id);

            // Update status dan kurir_id
            $giveaway->update([
                'status' => 'SEDANG_DIPROSES',
                'kurir_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fungsi untuk mengupdate foto pengantaran giveaway
     */
    public function updateFoto(Request $request, $id)
    {
        try {
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $giveaway = GiveAway::findOrFail($id);

            // Hapus foto lama jika ada
            if ($giveaway->foto_rumah && Storage::exists($giveaway->foto_rumah)) {
                Storage::delete($giveaway->foto_rumah);
            }

            // Upload foto baru
            $path = $request->file('foto')->store('public/foto-giveaway');
            $giveaway->recipient_photo = str_replace('public/', 'storage/', $path);
            $giveaway->delivery_note = $request->note;
            $giveaway->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
