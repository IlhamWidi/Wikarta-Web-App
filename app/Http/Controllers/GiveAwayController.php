<?php

namespace App\Http\Controllers;

use App\Models\GiveAway;
use App\Models\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification as Notif;
use Illuminate\Support\Facades\Log;

class GiveAwayController extends Controller
{
    /**
     * Menampilkan daftar give away
     */
    public function index(Request $request)
    {
        // Ambil parameter filter
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Query dengan filter
        $giveaways = GiveAway::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->with(['pelanggan', 'operator', 'kurir'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('giveaway.index', compact('giveaways'));
    }

    /**
     * Menampilkan form tambah give away
     */
    public function create()
    {
        $customers = User::where('user_type', 'CUSTOMER')->get();
        return view('giveaway.create', compact('customers'));
    }

    /**
     * Menyimpan data give away baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'nullable|exists:users,id',
            'tanggal' => 'nullable|date',
            'give_away' => 'nullable|string|max:255',
        ]);

        $data = [
            'pelanggan_id' => $request->pelanggan_id,
            'tanggal' => $request->tanggal,
            'give_away' => $request->give_away,
            'status' => 'REGISTER',
            'operator_id' => Auth::id(),
            'active' => true
        ];

        if ($request->hasFile('recipient_photo')) {
            $ext = $request->file('recipient_photo')->guessExtension();
            $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->give_away), time());
            $recipient_photo = "storage/" . $request->file('recipient_photo')->storeAs('image', $name . '.' . $ext, 'public');
            $data['recipient_photo'] = $recipient_photo;
        }
        GiveAway::create($data);

        return redirect()->route('giveaway.index')->with('success', 'Data berhasil disimpan');
    }

    /**
     * Menampilkan form edit give away
     */
    public function edit(GiveAway $giveaway)
    {
        $customers = User::where('user_type', 'CUSTOMER')->get();
        return view('giveaway.edit', compact('giveaway', 'customers'));
    }

    /**
     * Mengupdate data give away
     */
    public function update(Request $request, GiveAway $giveaway)
    {
        $request->validate([
            'pelanggan_id' => 'nullable|exists:users,id',
            'tanggal' => 'nullable|date',
            'give_away' => 'nullable|string|max:255',
            'active' => 'boolean'
        ]);

        $data = $request->all();
        if ($request->hasFile('recipient_photo')) {
            $ext = $request->file('recipient_photo')->guessExtension();
            $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->give_away), time());
            $recipient_photo = "storage/" . $request->file('recipient_photo')->storeAs('image', $name . '.' . $ext, 'public');
            $data['recipient_photo'] = $recipient_photo;
        }
        $giveaway->update($data);
        return redirect()->route('giveaway.index')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Menghapus data give away
     */
    public function destroy(GiveAway $giveaway)
    {
        $giveaway->delete();
        return redirect()->route('giveaway.index')->with('success', 'Data berhasil dihapus');
    }
}
