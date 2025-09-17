<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Package;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    /**
     * Menampilkan daftar pelanggan
     */
    public function index(Request $request)
    {
        $query = Pelanggan::query()->with(['paket', 'marketing', 'teknisi']);
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pelanggan', 'like', '%' . $request->q . '%')
                    ->orWhere('nomor_hp', 'like', '%' . $request->q . '%');
            });
        }
        if ($request->filled('marketing_id')) {
            $query->where('marketing_id', $request->marketing_id);
        }
        if ($request->filled('teknisi_id')) {
            $query->where('teknisi_id', $request->teknisi_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $data = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $ms_marketing = \App\Models\User::where('role_id', 'MARKETING')->get();
        $ms_teknisi = \App\Models\User::where('role_id', 'TEKNISI')->get();
        $statuses = ['REGISTER' => 'REGISTER', 'SEDANG_DIPROSES' => 'SEDANG DIPROSES', 'SELESAI' => 'SELESAI'];

        return view('pages.pelanggan.view', compact('data', 'ms_marketing', 'ms_teknisi', 'statuses', 'request'));
    }

    public function create(Request $request)
    {
        $packages = Package::where(['active' => 1, 'publish' => 1])->orderBy('name', 'asc')->get();
        $ms_brances = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.pelanggan.form', compact('packages', 'ms_brances'));
    }
    /**
     * Menyimpan data pelanggan baru
     */
    public function store(Request $request)
    {
        $rules = [
            'nama_pelanggan' => 'required',
            'nomor_hp' => 'required',
            'alamat_psb' => 'required',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("pelanggan")
                ->withErrors($validator)
                ->withInput($request->all());
        }

        // Cek nomor HP sudah terdaftar
        $exists = \App\Models\Pelanggan::where('nomor_hp', $request->nomor_hp)->exists();
        if ($exists) {
            return redirect()->route("pelanggan.create")
                ->withErrors(['nomor_hp' => 'Nomor telepon sudah terdaftar, silakan gunakan nomor telepon lain yang support WhatsApp.'])
                ->withInput($request->all());
        }

        try {
            DB::beginTransaction();

            $foto_ktp = null;
            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $ext = $file->getClientOriginalExtension();
                $filename = 'ktp_' . time() . '.' . $ext;
                $file->storeAs('public/ktp', $filename);
                $foto_ktp = 'storage/ktp/' . $filename;
            }

            $pelanggan = new Pelanggan();
            $pelanggan->id = Str::uuid();
            $pelanggan->nama_pelanggan = $request->nama_pelanggan;
            $pelanggan->paket_id = $request->paket_id;
            $pelanggan->nomor_hp = $request->nomor_hp;
            $pelanggan->alamat_psb = $request->alamat_psb;
            $pelanggan->odp = $request->odp;
            $pelanggan->panjang_kabel = $request->panjang_kabel;
            $pelanggan->foto_ktp = $foto_ktp;
            $pelanggan->keterangan = $request->keterangan;
            $pelanggan->marketing_id = auth()->id();
            $pelanggan->branch_id = $request->branch_id;
            $pelanggan->save();

            DB::commit();
            return redirect()->route('pelanggan')->with('message_success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pelanggan')
                ->withInput($request->all())
                ->with('message_error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit pelanggan
     */
    public function edit($id)
    {
        $data = Pelanggan::where(['active' => 1])->orderBy('created_at', 'desc')->get();
        $packages = Package::where(['active' => 1])->orderBy('name', 'asc')->get();
        $one = Pelanggan::findOrFail($id);
        $ms_brances = Branch::where(['active' => 1])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pages.pelanggan.form', compact('data', 'packages', 'one', 'ms_brances'));
    }

    /**
     * Update data pelanggan
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'nama_pelanggan' => 'required',
            'nomor_hp' => 'required',
            'alamat_psb' => 'required',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route("pelanggan.edit", $id)
                ->withErrors($validator)
                ->withInput($request->all());
        }

        try {
            DB::beginTransaction();

            $pelanggan = Pelanggan::findOrFail($id);

            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $ext = $file->getClientOriginalExtension();
                $filename = 'ktp_' . time() . '.' . $ext;
                $file->storeAs('public/ktp', $filename);
                $pelanggan->foto_ktp = 'storage/ktp/' . $filename;
            }

            $pelanggan->nama_pelanggan = $request->nama_pelanggan;
            $pelanggan->paket_id = $request->paket_id;
            $pelanggan->nomor_hp = $request->nomor_hp;
            $pelanggan->alamat_psb = $request->alamat_psb;
            $pelanggan->odp = $request->odp;
            $pelanggan->panjang_kabel = $request->panjang_kabel;
            $pelanggan->keterangan = $request->keterangan;
            $pelanggan->branch_id = $request->branch_id;
            $pelanggan->save();

            DB::commit();
            return redirect()->route('pelanggan')->with('message_success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pelanggan.edit', $id)
                ->withInput($request->all())
                ->with('message_error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data pelanggan (soft delete)
     */
    public function delete(Request $request)
    {
        $pelanggan = Pelanggan::findOrFail($request->id);
        $pelanggan->active = 0;
        $pelanggan->save();

        return redirect()->route('pelanggan')
            ->with('message_success', 'Data berhasil dihapus');
    }
}
