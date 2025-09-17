@extends('layouts.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Ganti/hapus Sweet Alert css lama -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pt-2 pb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <li class="bx bx-table"></li>&nbsp;&nbsp;
                                <span class="card-title" style="font-size: 15px;"> Butuh Diantar</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Bulan, Tahun, dan Cabang -->
                        <div class="d-flex flex-column flex-md-row gap-2 mb-3">
                            <select id="cabang" class="form-select mb-2 mb-md-0" style="min-width: 200px;">
                                <option value="">Semua Cabang</option>
                                @foreach($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}" {{ request('cabang') == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="d-flex gap-2">
                                <select id="bulan" class="form-select" style="min-width: 130px;">
                                    @php
    $bulan_sekarang = request('bulan', date('m'));
    $bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
                                    @endphp
                                    @foreach($bulan as $key => $nama)
                                        <option value="{{ $key }}" {{ $bulan_sekarang == $key ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>

                                <select id="tahun" class="form-select" style="min-width: 100px;">
                                    @php
    $tahun_sekarang = request('tahun', date('Y'));
    $tahun_awal = 2020;
    $tahun_akhir = date('Y');
                                    @endphp
                                    @for($tahun = $tahun_akhir; $tahun >= $tahun_awal; $tahun--)
                                        <option value="{{ $tahun }}" {{ $tahun_sekarang == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Table view (desktop) -->
                        <div class="d-none d-md-block">
                            <form method="get" class="mb-2" id="searchFormDesktop">
                                <input type="hidden" name="bulan" value="{{ request('bulan', date('m')) }}">
                                <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">
                                <input type="hidden" name="cabang" value="{{ request('cabang') }}">
                                <div class="input-group">
                                    <input type="text" name="search" id="searchTableDesktop" class="form-control" placeholder="Cari nama/telepon pelanggan..." value="{{ $search ?? '' }}">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </form>
                            <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%;font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Cabang</th>
                                        <th>GiveAway</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pelanggan as $k => $v)
                                        <tr>
                                            <td style="text-align: center;">{{ $k + 1 }}</td>
                                            <td>{{ $v->created_at ? $v->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                                            <td>Nama : {{ $v->pelanggan->name ?? '-' }}<br>
                                                HP: {{ $v->pelanggan->phone_number ?? '-' }}<br>
                                                Alamat : {{ $v->pelanggan->address ?? '-' }}
                                            </td>
                                            <td>{{ $v->pelanggan->branches->name ?? '-' }}</td>
                                            <td class="text-danger">{{ $v->give_away ?? '-' }}</td>
                                            <td style="text-align: center;">
                                                <button type="button" class="btn btn-soft-success btn-sm"
                                                    onclick="confirmPickOrder('{{ $v->id }}')">
                                                    <i class="ri-add-circle-line"></i> Ambil Orderan
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- List view (mobile) -->
                        <div class="d-md-none">
                            <form method="get" class="mb-2" id="searchFormMobile">
                                <input type="hidden" name="bulan" value="{{ request('bulan', date('m')) }}">
                                <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">
                                <input type="hidden" name="cabang" value="{{ request('cabang') }}">
                                <div class="input-group">
                                    <input type="text" name="search" id="searchTableMobile" class="form-control" placeholder="Cari nama/telepon pelanggan..." value="{{ $search ?? '' }}">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </form>
                            @if($pelanggan->isEmpty())
                                <div class="text-center py-4">
                                    <h4>Data Tidak Tersedia</h4>
                                    <p class="text-muted">Belum ada data pengantaran yang perlu diproses.</p>
                                </div>
                            @else
                                <div id="mobileListData">
                                    @foreach($pelanggan as $k => $v)
                                        <div class="card mb-3 border mobile-list-item">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <h6 class="fw-bold mb-0">{{ $v->pelanggan->name }}</h6>
                                                    <span class="badge bg-soft-primary text-primary">No. {{ $k + 1 }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <i class="ri-building-line me-1"></i> {{ $v->pelanggan->branches->name ?? '-' }}
                                                </div>
                                                <div class="mb-2">
                                                    <i class="ri-phone-line me-1"></i> {{ $v->pelanggan->phone_number }}
                                                </div>
                                                <div class="mb-2">
                                                    <i class="ri-map-pin-line me-1"></i> {{ $v->pelanggan->address ?? '-' }}
                                                </div>
                                                <div class="mb-3">
                                                    <i class="ri-file-text-line me-1"></i>
                                                    <span class="text-danger">{{ $v->give_away ?? '-' }}</span>
                                                </div>
                                                <button type="button" class="btn btn-soft-success btn-sm w-100"
                                                    onclick="confirmPickOrder('{{ $v->id }}')">
                                                    <i class="ri-add-circle-line"></i> Segera Antar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pt-2 pb-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <i class="ri-installation-line"></i>&nbsp;
                                <span class="card-title">Data Giveaway</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Tampilan Tabel untuk Desktop -->
                        <div class="d-none d-md-block">
                            <table id="example2" class="table table-bordered dt-responsive nowrap table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Pelanggan</th>
                                        <th>Petugas</th>
                                        <th>Giveaway</th>
                                        <th>Foto</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($giveaways as $k => $v)
                                        <tr>
                                            <td>{{ $k + 1 }}</td>
                                            <td>
                                                {{ $v->status == 'SELESAI' ? $v->updated_at->format('d/m/Y H:i:s') : '-' }}  
                                            </td>
                                            <td>
                                                Nama : {{ $v->pelanggan->name ?? '-' }}<br>
                                                Telepon : {{ $v->pelanggan->phone_number ?? '-' }}<br>
                                                Alamat : {{ $v->pelanggan->address ?? '-' }}
                                            </td>
                                            <td>
                                                Admin : {{ $v->operator->name ?? '-' }}<br>
                                                Kurir : {{ $v->kurir->name ?? '-' }}
                                            </td>
                                            <td class="text-danger">{{ $v->give_away ?? '-' }}</td>
                                            <td>
                                                @if($v->recipient_photo)
                                                    <img src="{{ asset($v->recipient_photo) }}" width="50">
                                                @endif
                                            </td>
                                            <td>{{ $v->delivery_note ?? '-' }}</td>
                                            <td>
                                                @if($v->status != 'SELESAI')
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="ri-more-fill align-middle"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" onclick="confirmComplete('{{ $v->id }}')">
                                                                    <i class="ri-check-double-line align-bottom me-2 text-muted"></i>
                                                                    Selesai
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" onclick="showUploadModal('{{ $v->id }}')">
                                                                    <i class="ri-upload-line align-bottom me-2 text-muted"></i> Upload
                                                                    Foto
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Tampilan List untuk Mobile -->
                        <div class="d-md-none">
                            @if($giveaways->isEmpty())
                                <div class="text-center py-4">
                                    <h4>Data Tidak Tersedia</h4>
                                    <p class="text-muted">Belum ada data pengantaran yang perlu diproses.</p>
                                </div>
                            @else
                                @foreach($giveaways as $k => $v)
                                    <div class="card mb-3 border">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold mb-0">{{ $v->pelanggan->name ?? '-' }}</h6>
                                                <span class="badge bg-soft-primary text-primary">No. {{ $k + 1 }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <i class="ri-phone-line me-1"></i> {{ $v->pelanggan->phone_number ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="ri-map-pin-line me-1"></i> {{ $v->pelanggan->address ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="ri-router-line me-1"></i> Operator: {{ $v->operator->name ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="ri-signal-tower-line me-1"></i> Kurir: {{ $v->kurir->name ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="ri-equalizer-line me-1"></i> Giveaway: {{ $v->give_away ?? '-' }}
                                            </div>
                                            <div class="mb-2">
                                                <i class="ri-chat-3-line me-1"></i> Keterangan: {{ $v->delivery_note ?? '-' }}
                                            </div>
                                            @if($v->recipient_photo)
                                                <div class="mb-3">
                                                    <i class="ri-image-line me-1"></i> Foto:<br>
                                                    <img src="{{ asset($v->recipient_photo) }}" class="mt-2 rounded"
                                                        style="max-width: 200px">
                                                </div>
                                            @endif
                                            <div class="d-flex gap-2">
                                                @if($v->status != 'SELESAI')
                                                    <button class="btn btn-soft-success btn-sm flex-grow-1"
                                                        onclick="confirmComplete('{{ $v->id }}')">
                                                        <i class="ri-check-double-line align-bottom me-1"></i> Selesai
                                                    </button>
                                                    <button class="btn btn-soft-primary btn-sm flex-grow-1"
                                                        onclick="showUploadModal('{{ $v->id }}')">
                                                        <i class="ri-upload-line align-bottom me-1"></i> Upload Foto
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="uploadFotoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Foto Pengantaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadForm">
                            <input type="hidden" id="giveawayId">
                            <div class="mb-3">
                                <label class="form-label">Pilih Foto</label>
                                <input type="file" class="form-control" id="fotoInput" accept="image/*" required>
                            </div>
                            <div id="imagePreview" class="text-center mb-3" style="display:none;">
                                <img src="" class="img-fluid rounded" style="max-height: 200px">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="noteInput" rows="3"
                                    placeholder="Masukkan keterangan pengantaran..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnUpload">Upload</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function () {
            // Handler perubahan filter
            $('#bulan, #tahun').change(function () {
                let bulan = $('#bulan').val();
                let tahun = $('#tahun').val();
                let cabang = $('#cabang').val();
                window.location.href = "{{ route('kurir-giveaway') }}?bulan=" + bulan + "&tahun=" + tahun + "&cabang=" + cabang;
            });

            // Tambahkan handler untuk filter cabang
            $('#cabang').change(function () {
                let bulan = $('#bulan').val();
                let tahun = $('#tahun').val();
                let cabang = $(this).val();
                window.location.href = "{{ route('kurir-giveaway') }}?bulan=" + bulan + "&tahun=" + tahun + "&cabang=" + cabang;
            });

            // Hilangkan search client-side, gunakan server-side search
        });

        // Fungsi untuk konfirmasi pengambilan order
        function confirmPickOrder(id) {
            Swal.fire({
                title: 'Konfirmasi Pengantaran',
                text: "Anda yakin akan mengambil orderan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Antar!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request ke server
                    $.ajax({
                        url: '<?= url('kurir-giveaway/pick-order'); ?>/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Berhasil!',
                                    'Order berhasil diambil.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat mengambil order.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Fungsi untuk konfirmasi hapus data
        function confirmDelete(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= url('kurir-giveaway/delete'); ?>/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus data.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Fungsi untuk konfirmasi selesai pengantaran
        function confirmComplete(id) {
            // Cek foto terlebih dahulu
            $.ajax({
                url: '<?= url('kurir-giveaway/check-photo'); ?>/' + id,
                type: 'GET',
                success: function (response) {
                    if (!response.has_photo) {
                        Swal.fire(
                            'Peringatan!',
                            'Anda harus mengupload foto bukti pengantaran terlebih dahulu!',
                            'warning'
                        );
                        return;
                    }

                    // Jika ada foto, lanjutkan konfirmasi
                    Swal.fire({
                        title: 'Konfirmasi Selesai',
                        text: "Anda yakin pengantaran sudah selesai?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Selesai!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '<?= url('kurir-giveaway/complete'); ?>/' + id,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Berhasil!',
                                            'Status pengantaran telah diubah menjadi selesai.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Gagal!',
                                            response.message || 'Terjadi kesalahan saat mengubah status.',
                                            'error'
                                        );
                                    }
                                },
                                error: function (xhr) {
                                    Swal.fire(
                                        'Error!',
                                        'Terjadi kesalahan saat mengubah status.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                },
                error: function (xhr) {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat memeriksa foto.',
                        'error'
                    );
                }
            });
        }

        // Fungsi untuk membuka modal upload foto
        function showUploadModal(id) {
            $('#giveawayId').val(id);
            $('#fotoInput').val('');
            $('#imagePreview').hide();
            $('#uploadFotoModal').modal('show');
        }

        // Preview foto yang akan diupload
        $('#fotoInput').change(function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview img').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Fungsi untuk kompresi gambar
        async function compressImage(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.src = e.target.result;
                    img.onload = function () {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        // Menentukan ukuran maksimal
                        const MAX_WIDTH = 1024;
                        const MAX_HEIGHT = 1024;

                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height *= MAX_WIDTH / width;
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width *= MAX_HEIGHT / height;
                                height = MAX_HEIGHT;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        // Konversi ke blob dengan kualitas yang disesuaikan
                        canvas.toBlob((blob) => {
                            resolve(blob);
                        }, 'image/jpeg', 0.7);
                    }
                }
                reader.readAsDataURL(file);
            });
        }

        // Handler untuk upload foto
        $('#btnUpload').click(async function () {
            const file = $('#fotoInput')[0].files[0];
            const note = $('#noteInput').val();

            if (!file) {
                Swal.fire('Error', 'Pilih foto terlebih dahulu', 'error');
                return;
            }

            const loadingMsg = Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                let photoToUpload = file;
                if (file.size > 500 * 1024) {
                    photoToUpload = await compressImage(file);
                }

                const formData = new FormData();
                formData.append('foto', photoToUpload);
                formData.append('note', note);
                formData.append('_token', '{{ csrf_token() }}');

                const response = await fetch('<?= url('/kurir-giveaway/update-foto'); ?>' + '/' + $('#giveawayId').val(), {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                loadingMsg.close();

                if (result.success) {
                    Swal.fire('Berhasil', 'Foto berhasil diupload', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error', result.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                loadingMsg.close();
                Swal.fire('Error', 'Terjadi kesalahan saat upload', 'error');
            }
        });
    </script>
@endsection