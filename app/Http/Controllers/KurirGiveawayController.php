<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GiveAway;
use App\Models\User;
use App\Models\Notification as Notif;
use Illuminate\Support\Facades\Log;

class KurirGiveawayController extends Controller
{
    // ...existing code...

    /**
     * Memeriksa keberadaan foto pada giveaway
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPhoto($id)
    {
        $giveaway = GiveAway::find($id);

        if (!$giveaway) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'has_photo' => !empty($giveaway->recipient_photo)
        ]);
    }

    /**
     * Mengubah status pengantaran menjadi selesai
     * @param int $id ID dari data giveaway
     * @return JsonResponse
     */
    public function complete($id)
    {
        try {
            $giveaway = GiveAway::findOrFail($id);

            // Cek apakah ada foto bukti pengantaran
            if (!$giveaway->recipient_photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto bukti pengantaran belum diupload'
                ]);
            }

            // Update status menjadi SELESAI
            $giveaway->status = 'SELESAI';
            // $giveaway->delivered_at = now();
            $giveaway->save();

            $pelanggan = User::where(['id' => $giveaway->pelanggan_id])->first();
            if (isset($pelanggan)) {
                try {
                    $operator = User::find($giveaway->kurir_id);

                    $message = "Hi {$pelanggan->name}.\n\n";
                    $message .= "Selamat giveaway anda telah diterima pada tanggal " . date('d-m-Y', strtotime($giveaway->tanggal));
                    $message .= " berupa {$giveaway->give_away} yang telah diberikan oleh {$operator->name}.\n\n";
                    if (isset($giveaway->delivery_note)) {
                        $message .= "Keterangan: {$giveaway->delivery_note}\n\n";
                    }
                    $message .= "Terima kasih.";

                    // Kirim pesan menggunakan class Notification
                    Notif::SendMessage($pelanggan->phone_number, $message);
                } catch (\Exception $e) {
                    // Log error jika gagal mengirim pesan
                    Log::error('Gagal mengirim notifikasi WhatsApp: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah menjadi selesai'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function updateFoto(Request $request, $id)
    {
        try {
            $giveaway = Giveaway::findOrFail($id);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/giveaway'), $fileName);

                $giveaway->recipient_photo = 'uploads/giveaway/' . $fileName;
                $giveaway->delivery_note = $request->note; // Simpan keterangan
                $giveaway->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil diupload'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // Query data dengan filter bulan dan tahun
        $pelanggan = GiveAway::where('status', 'REGISTER')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        $data = GiveAway::where('status', '!=', 'REGISTER')
            ->where('kurir_id', auth()->id())
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        return view('pages.kurir.index', compact('pelanggan', 'data'));
    }

    // ...existing code...
}
