@extends('layouts.main')
@section('title') Edit Asset @endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit Asset</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('data-asset.update', ['id' => $asset->id]) }}"
                    method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Asset</label>
                        <input type="text"
                            class="form-control @error('nama_asset') is-invalid @enderror"
                            name="nama_asset"
                            value="{{ old('nama_asset', $asset->nama_asset) }}"
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
                            <option value="ASSET" {{ $asset->tipe_asset == 'ASSET' ? 'selected' : '' }}>
                                ASSET
                            </option>
                            <option value="NON_ASSET" {{ $asset->tipe_asset == 'NON_ASSET' ? 'selected' : '' }}>
                                NON ASSET
                            </option>
                        </select>
                        @error('tipe_asset')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="text"
                            class="form-control"
                            value="{{ $asset->stok }}"
                            disabled>
                        <small class="text-muted">Stok hanya bisa diubah melalui modul inventory</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <select class="form-select @error('satuan') is-invalid @enderror"
                            name="satuan"
                            required>
                            <option value="PCS" {{ $asset->satuan == 'PCS' ? 'selected' : '' }}>PCS</option>
                            <option value="PAKET" {{ $asset->satuan == 'PAKET' ? 'selected' : '' }}>PAKET</option>
                            <option value="METER" {{ $asset->satuan == 'METER' ? 'selected' : '' }}>METER</option>
                        </select>
                        @error('satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        @if($asset->photo)
                        <div class="mb-2">
                            <img src="{{ asset($asset->photo) }}"
                                alt="Current Photo"
                                class="img-thumbnail"
                                style="max-width: 200px">
                        </div>
                        @endif
                        <input type="file"
                            class="form-control @error('photo') is-invalid @enderror"
                            name="photo">
                        @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('data-asset.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection