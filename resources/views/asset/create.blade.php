@extends('layouts.main')
@section('title') Tambah Asset @endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Tambah Asset Baru</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('data-asset.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Asset</label>
                        <input type="text"
                            class="form-control @error('nama_asset') is-invalid @enderror"
                            name="nama_asset"
                            value="{{ old('nama_asset') }}"
                            required>
                        @error('nama_asset')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Asset</label>
                        <select class="form-select @error('tipe_asset') is-invalid @enderror"
                            name="tipe_asset"
                            required>
                            <option value="">Pilih Tipe</option>
                            <option value="ASSET">ASSET</option>
                            <option value="NON_ASSET">NON ASSET</option>
                        </select>
                        @error('tipe_asset')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <select class="form-select @error('satuan') is-invalid @enderror"
                            name="satuan"
                            required>
                            <option value="">Pilih Satuan</option>
                            <option value="PCS">PCS</option>
                            <option value="PAKET">PAKET</option>
                            <option value="METER">METER</option>
                        </select>
                        @error('satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file"
                            class="form-control @error('photo') is-invalid @enderror"
                            name="photo">
                        @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('data-asset.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection