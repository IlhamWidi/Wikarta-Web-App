<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Menampilkan daftar asset
     */
    public function index()
    {
        $assets = Asset::latest()->paginate(10);
        return view('asset.index', compact('assets'));
    }

    /**
     * Menampilkan form tambah asset
     */
    public function create()
    {
        return view('asset.create');
    }

    /**
     * Menyimpan asset baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_asset' => 'required|string|max:255',
            'tipe_asset' => 'required|in:ASSET,NON_ASSET',
            'satuan' => 'required|in:PCS,PAKET,METER',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {

            $ext = $request->file('photo')->guessExtension();
            $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->nama_asset), time());
            $photo = "storage/" . $request->file('photo')->storeAs('image', $name . '.' . $ext, 'public');
            $data['photo'] = $photo;
        }

        Asset::create($data);

        return redirect()->route('data-asset.index')
            ->with('success', 'Asset berhasil ditambahkan');
    }


    /**
     * Menampilkan form edit asset
     */
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        return view('asset.edit', compact('asset'));
    }

    /**
     * Mengupdate data asset
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'nama_asset' => 'required|string|max:255',
            'tipe_asset' => 'required|in:ASSET,NON_ASSET',
            'satuan' => 'required|in:PCS,PAKET,METER',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->except('stok'); // Stok tidak bisa diubah langsung

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($asset->photo) {
                Storage::delete('public/' . $asset->photo);
            }

            $ext = $request->file('photo')->guessExtension();
            $name = sprintf("%s_%s_ktp", Helper::str_to_slug($request->nama_asset), time());
            $photo = "storage/" . $request->file('photo')->storeAs('image', $name . '.' . $ext, 'public');
            $data['photo'] = $photo;
        }

        $asset->update($data);

        return redirect()->route('data-asset.index')
            ->with('success', 'Asset berhasil diperbarui');
    }

    /**
     * Menghapus asset
     */
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset->photo) {
            Storage::delete('public/assets/' . $asset->photo);
        }

        $asset->delete();

        return redirect()->route('data-asset.index')
            ->with('success', 'Asset berhasil dihapus');
    }
}
