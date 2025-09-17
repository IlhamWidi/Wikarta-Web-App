<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Helper;
use App\Models\Package;
use App\Models\Pemasangan;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PemasanganController extends Controller
{
    /**
     * Menampilkan daftar pemasangan
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Pemasangan::with(['pelanggan', 'teknisi']);
        if ($user->role_id != 'SUPERUSER') {
            $query->where('teknisi_id', $user->id);
        }
        // Filter nama/no telp pelanggan
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('pelanggan', function ($sub) use ($q) {
                $sub->where('nama_pelanggan', 'like', "%$q%")
                    ->orWhere('nomor_hp', 'like', "%$q%");
            });
        }
        $data = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $pelanggan = Pelanggan::where(['active' => 1, 'status' => 'REGISTER'])->orderBy('created_at', 'desc')->get();
        return view('pages.pemasangan.view', compact('data', 'pelanggan'));
    }

    /**
     * Menampilkan form tambah pemasangan
     */
    public function create()
    {
        $pelanggan = Pelanggan::all();
        $teknisi = User::where('role', 'teknisi')->get();
        return view('pages.pemasangan.form', compact('pelanggan', 'teknisi'));
    }

    /**
     * Menyimpan data pemasangan baru
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['id'] = Str::uuid();

        if ($request->hasFile('foto_rumah')) {
            $ext = $request->file('foto_rumah')->guessExtension();
            $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->name), time());
            $foto_rumah = "storage/" . $request->file('foto_rumah')->storeAs('image', $name . '.' . $ext, 'public');
            $data['foto_rumah'] = $foto_rumah;
        }

        //ambil data dari pelanggan saat input dari marketing

        Pemasangan::create($data);
        return redirect()->route('pemasangan')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit pemasangan
     */
    public function edit($id)
    {
        $data = Pemasangan::find($id);
        $pelanggan = null; //Pelanggan::all();
        $teknisi = null; //User::where('role', 'teknisi')->get();
        return view('pages.pemasangan.form', compact('data', 'pelanggan', 'teknisi'));
    }

    /**
     * Update data pemasangan
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $pemasangan = Pemasangan::find($id);

        if ($request->hasFile('foto_rumah')) {
            $ext = $request->file('foto_rumah')->guessExtension();
            $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->name), time());
            $foto_rumah = "storage/" . $request->file('foto_rumah')->storeAs('image', $name . '.' . $ext, 'public');
            $data['foto_rumah'] = $foto_rumah;
        }

        $pemasangan->update($data);
        return redirect()->route('pemasangan')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Hapus data pemasangan
     */
    public function destroy($id)
    {
        $pemasangan = Pemasangan::find($id);
        $pemasangan->delete();

        return redirect()->route('pemasangan')->with('success', 'Data berhasil dihapus');
    }

    /**
     * Fungsi untuk memproses pengambilan orderan oleh teknisi
     * @param Request $request
     * @return RedirectResponse
     */
    public function pickOrder(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Update status pelanggan
            $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);
            $pelanggan->update([
                'status' => 'SEDANG_DIPROSES',
                'teknisi_id' => auth()->id()
            ]);

            // Insert ke table pemasangan
            Pemasangan::create([
                'id' => Str::uuid(),
                'pelanggan_id' => $request->pelanggan_id,
                'teknisi_id' => auth()->id(),
                'tanggal_pemasangan' => now(),
                'status' => 'SEDANG_DIPROSES',
                'odp' => $pelanggan->odp,
                'panjang_kabel' => $pelanggan->panjang_kabel,
            ]);

            DB::commit();

            return redirect()->back()->with('message_success', 'Orderan berhasil diambil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status pemasangan dan pelanggan menjadi SELESAI
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function complete($id): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Update status pemasangan
            $pemasangan = Pemasangan::find($id);
            $pemasangan->status = "SELESAI";
            $pemasangan->save();

            // Ambil data pelanggan
            $pelanggan = $pemasangan->pelanggan;
            $pelanggan->status = "SELESAI";
            $pelanggan->save();

            $paket = Package::where('id', $pelanggan->paket_id)->first();

            // Buat user baru
            $user = new User();
            $user->user_type = Constant::USER_TYPE_CUSTOMER;
            $user->name = $pelanggan->nama_pelanggan;
            $user->code = User::generateCustomerCode();
            $user->username = $pelanggan->nomor_hp;//Helper::str_to_slug($pelanggan->nama_pelanggan);
            $user->password = $pelanggan->nomor_hp;
            $user->phone_number = $pelanggan->nomor_hp;
            $user->address = $pelanggan->alamat_psb;
            $user->coordinates = $pelanggan->coordinates;
            $user->status = true;
            $user->package_id = $pelanggan->paket_id;
            $user->collection_date = now();
            $user->lampiran_foto_ktp = $pelanggan->foto_ktp;
            $user->lampiran_foto_rumah = $pemasangan->foto_rumah;
            $user->subscribe_price = $paket->subscribe_price;
            $user->registration_price = $paket->registration_price;
            $user->branch_id = $pelanggan->branch_id;
            $user->odp = $pemasangan->odp;
            $user->opm = $pemasangan->opm;
            $user->odc = $pemasangan->odc;
            $user->keterangan = $pemasangan->keterangan;
            $user->kode_server = $pemasangan->user_ppoe;
            $user->password_server = $pemasangan->password_ppoe;
            $user->vlan = $pemasangan->vlan_ppoe;
            $user->save();

            DB::commit();
            return redirect()->route('pemasangan')->with('message_success', 'Pemasangan berhasil diselesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message_error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail pemasangan
     */
    public function detail($id)
    {
        $data = \App\Models\Pemasangan::with(['pelanggan', 'teknisi'])->findOrFail($id);
        return view('pages.pemasangan.detail', compact('data'));
    }
}
