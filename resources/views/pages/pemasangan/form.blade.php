<!-- MODAL ADD -->
@extends('layouts.main')
@section('style')
@endsection
@section('content')
    <form action="{{ route('pemasangan.update', ['id' => $data->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-edit"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Data Pemasangan</span>
                    </div>
                </div>
            </div>
            <div class="card-body collapse show" id="collapseexample1">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">Pelanggan</label>
                            <input type="text" class="form-control" name="pelanggan"
                                value="{{ $data->pelanggan->nama_pelanggan }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">Teknisi</label>
                            <input type="text" class="form-control" name="teknisi" value="{{ $data->teknisi->name }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">ODP</label>
                            <input type="text" class="form-control" name="odp" value="{{ $data->odp }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">OPM</label>
                            <input type="text" class="form-control" name="opm" value="{{ $data->opm }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">Panjang Kabel</label>
                            <input type="text" class="form-control" name="panjang_kabel" value="{{ $data->panjang_kabel }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">Username PPOE</label>
                            <input type="text" class="form-control" name="user_ppoe" value="{{ $data->user_ppoe }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">Password PPOE</label>
                            <input type="text" class="form-control" name="password_ppoe" value="{{ $data->password_ppoe }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">VLAN PPOE</label>
                            <input type="text" class="form-control" name="vlan_ppoe" value="{{ $data->vlan_ppoe }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">ODC</label>
                            <input type="text" class="form-control" name="odc" value="{{ old('odc', $data->odc ?? '') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan"
                                rows="2">{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div>
                            <label class="form-label">Foto Rumah</label>
                            <input type="file" class="form-control" name="foto_rumah" accept="image/*" id="imageInput"
                                onchange="handleImageUpload(event)">
                            <small class="text-muted">Maksimal ukuran file: 500KB. File yang lebih besar akan dikompresi
                                secara otomatis.</small>
                            <div id="imagePreview" class="mt-2">
                                @if(isset($data) && $data->foto_rumah)
                                    <img src="{{ asset($data->foto_rumah) }}" class="img-thumbnail mt-2 mb-1"
                                        style="max-height: 100px"><br>
                                @endif
                            </div>
                            <!-- Hidden input untuk menyimpan hasil kompresi -->
                            <input type="hidden" name="compressed_image" id="compressedImage">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="hstack gap-2 justify-content-end">
                    <a href="{{ route('pemasangan') }}" type="button" class="btn btn-light">Kembali</a>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script src="{{ asset('assets/js/autoNumeric/autoNumeric.js') }}"></script>
    <script>
        jQuery(function ($) {
            $('.autonumber').autoNumeric('init');
        });

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
                reader.onload = function (e) {
                    img.src = e.target.result;
                    img.onload = function () {
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
                        canvas.toBlob(function (blob) {
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
            reader.onload = function (e) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail mt-2 mb-1" style="max-height: 100px">
                                           <br><small>Ukuran file: ${formatFileSize(compressedFile.size)}</small>`;
            }
            reader.readAsDataURL(compressedFile);

            // Convert compressed image to base64 for submission
            const base64reader = new FileReader();
            base64reader.onload = function (e) {
                document.getElementById('compressedImage').value = e.target.result;
            }
            base64reader.readAsDataURL(compressedFile);
        }
    </script>
@endsection