@extends('layouts.main')
@section('style')
@endsection
@section('content')
<!-- FORM START -->
<form method="post" action="{{ isset($one) ? route('pelanggan.update', ['id' => $one->id]) : route('pelanggan.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-xl-12" id="card-none1">
            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-edit"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Form Pelanggan</span>
                        </div>
                    </div>
                </div>
                <div class="card-body collapse show" id="collapseexample1">
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="form-label mb-0">Nama Pelanggan</label>
                            <input name="nama_pelanggan" type="text" class="form-control" value="{{ $one->nama_pelanggan ?? old('nama_pelanggan') }}" required>

                            <label class="form-label mb-0 mt-2">Nomor HP</label>
                            <input name="nomor_hp" type="text" class="form-control" value="{{ $one->nomor_hp ?? old('nomor_hp') }}" required>

                            <label for="choices-single-default" class="form-label mt-2">Branch</label>
                            <select class="form-control js-example-basic-single" name="branch_id" id="branch_id">
                                <option value="">Select Branch</option>
                                @if(isset($ms_brances))
                                @foreach($ms_brances as $k => $v)
                                <option value="{{ $v->id }}" <?= isset($one->branch_id) && $one->branch_id == $v->id ? 'selected=selected' : ''; ?>>{{ $v->code }}-{{ $v->name }}</option>
                                @endforeach
                                @endif
                            </select>

                            <label class="form-label mb-0 mt-2">Paket</label>
                            <select name="paket_id" class="form-control" required>
                                <option value="">Pilih Paket</option>
                                @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ (isset($one) && $one->paket_id == $package->id) ? 'selected' : '' }}>
                                    {{ $package->name }} ({{ $package->description }}) Rp. {{ $package->subscribe_price }}
                                </option>
                                @endforeach
                            </select>

                            <label class="form-label mb-0 mt-2">Alamat PSB</label>
                            <textarea name="alamat_psb" class="form-control" rows="3" required>{{ $one->alamat_psb ?? old('alamat_psb') }}</textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label mb-0">ODP</label>
                            <input name="odp" type="text" class="form-control" value="{{ $one->odp ?? old('odp') }}">

                            <label class="form-label mb-0 mt-2">Panjang Kabel</label>
                            <input name="panjang_kabel" type="text" class="form-control" value="{{ $one->panjang_kabel ?? old('panjang_kabel') }}">

                            <label class="form-label mb-0 mt-2">Foto KTP</label>
                            <input name="foto_ktp" type="file" class="form-control" accept="image/*" id="imageInput" onchange="handleImageUpload(event)" {{ !isset($one) ? 'required' : '' }}>
                            <small class="text-muted">Maksimal ukuran file: 500KB. File yang lebih besar akan dikompresi secara otomatis.</small>
                            <div id="imagePreview" class="mt-2">
                                @if(isset($one) && $one->foto_ktp)
                                <img src="{{ asset($one->foto_ktp) }}" class="img-thumbnail mt-2 mb-1" style="max-height: 100px"><br>
                                @endif
                            </div>
                            <!-- Hidden input untuk menyimpan hasil kompresi -->
                            <input type="hidden" name="compressed_image" id="compressedImage">

                            <label class="form-label mb-0 mt-2">Catatan</label>
                            <textarea name="keterangan" class="form-control" rows="3" required>{{ $one->keterangan ?? old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <center>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <li class="bx bx-save"></li> Simpan
                                    </button>
                                    <a class="btn btn-sm btn-danger" href="{{ route('pelanggan') }}">
                                        <li class="bx bx-x"></li> Reset
                                    </a>
                                </div>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('script')
<script>
    // Fungsi untuk mengkonversi ukuran file ke format yang mudah dibaca
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Fungsi untuk mengkompresi gambar
    async function compressImage(file) {
        const maxSize = 500 * 1024; // 500KB dalam bytes
        if (file.size <= maxSize) {
            return file;
        }

        const reader = new FileReader();
        const img = new Image();
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        return new Promise((resolve) => {
            reader.onload = function(e) {
                img.src = e.target.result;
                img.onload = function() {
                    let quality = 0.7;
                    let width = img.width;
                    let height = img.height;

                    // Jika dimensi terlalu besar, resize gambar
                    const maxDimension = 1200;
                    if (width > maxDimension || height > maxDimension) {
                        if (width > height) {
                            height = Math.round((height * maxDimension) / width);
                            width = maxDimension;
                        } else {
                            width = Math.round((width * maxDimension) / height);
                            height = maxDimension;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    // Kompresi dengan kualitas yang disesuaikan
                    canvas.toBlob(function(blob) {
                        resolve(new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }));
                    }, 'image/jpeg', quality);
                };
            };
            reader.readAsDataURL(file);
        });
    }

    // Fungsi untuk menangani upload gambar
    async function handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Tampilkan ukuran file asli
        console.log('Ukuran file asli:', formatFileSize(file.size));

        // Kompresi gambar
        const compressedFile = await compressImage(file);
        console.log('Ukuran file setelah kompresi:', formatFileSize(compressedFile.size));

        // Preview gambar
        const preview = document.getElementById('imagePreview');
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail mt-2 mb-1" style="max-height: 100px">
                               <br><small>Ukuran file: ${formatFileSize(compressedFile.size)}</small>`;
        }
        reader.readAsDataURL(compressedFile);

        // Convert compressed image to base64 for submission
        const base64reader = new FileReader();
        base64reader.onload = function(e) {
            document.getElementById('compressedImage').value = e.target.result;
        }
        base64reader.readAsDataURL(compressedFile);
    }
</script>
@endsection
<!-- FORM END -->