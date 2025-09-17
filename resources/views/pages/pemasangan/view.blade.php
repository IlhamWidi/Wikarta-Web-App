@extends('layouts.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Ganti/hapus Sweet Alert css lama -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
    @include('pages.pemasangan.pelanggan')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <i class="ri-installation-line"></i>&nbsp;
                            <span class="card-title">Data Pemasangan</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Desktop -->
                    <form method="get" class="row g-2 mb-3 d-none d-md-flex">
                        <div class="col-md-4">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Cari nama atau nomor HP pelanggan...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit"><i class="fa fa-search"></i> Cari</button>
                        </div>
                    </form>
                    <!-- Filter Mobile -->
                    <div class="d-md-none mb-2">
                        <button class="btn btn-outline-secondary btn-sm mb-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filterMobile" aria-expanded="false" aria-controls="filterMobile">
                            <i class="ri-filter-3-line"></i> Filter
                        </button>
                        <div class="collapse" id="filterMobile">
                            <form method="get" class="row g-2 mb-2">
                                <div class="col-12">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                        placeholder="Cari nama atau nomor HP pelanggan...">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100" type="submit"><i class="fa fa-search"></i>
                                        Cari</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Tampilan Tabel untuk Desktop -->
                    <div class="d-none d-md-block">
                        <table class="table table-bordered dt-responsive nowrap table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pasang</th>
                                    <th>Pelanggan</th>
                                    <th>ODP/OPM/ODC</th>
                                    <th>Panjang Kabel</th>
                                    <th>PPOE</th>
                                    <th>Teknisi</th>
                                    <th>Foto</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $k => $v)
                                    <tr>
                                        <td>{{ $data->firstItem() + $k }}</td>
                                        <td>{{ $v->created_at->format('d-m-Y H:i') }}</td>
                                        <td>{{ $v->pelanggan->nama_pelanggan }}<br>
                                            {{ $v->pelanggan->nomor_hp ?? $v->pelanggan->no_telp }}</td>
                                        <td>
                                            ODP: {{ $v->odp }}<br>
                                            OPM: {{ $v->opm }}<br>
                                            ODC: {{ $v->odc }}
                                        </td>
                                        <td>{{ $v->panjang_kabel }}</td>
                                        <td>
                                            Username: {{ $v->user_ppoe }}<br>
                                            Password: {{ $v->password_ppoe }}<br>
                                            VLAN: {{ $v->vlan_ppoe }}
                                        </td>
                                        <td>{{ $v->teknisi->name }}</td>
                                        <td>
                                            @if($v->foto_rumah)
                                                <img src="{{ asset($v->foto_rumah) }}" width="50">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if($v->status != 'SELESAI')
                                                        <li>
                                                            <a href="{{ route('pemasangan.edit', $v->id) }}" class="dropdown-item">
                                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" onclick="confirmDelete('{{ $v->id }}')">
                                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                Delete
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" onclick="confirmComplete('{{ $v->id }}')">
                                                                <i class="ri-check-double-line align-bottom me-2 text-muted"></i>
                                                                Selesai
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="{{ route('pemasangan.detail', $v->id) }}"
                                                                class="dropdown-item">
                                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Detail
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $data->links('vendor.pagination.bootstrap-5') }}</div>
                    </div>
                    <!-- Tampilan List untuk Mobile -->
                    <div class="d-md-none">
                        @foreach($data as $k => $v)
                            <div class="card mb-3 border">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">{{ $v->pelanggan->nama_pelanggan }}</h6>
                                        <span class="badge bg-soft-primary text-primary">No.
                                            {{ $data->firstItem() + $k }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ri-phone-line me-1"></i>
                                        {{ $v->pelanggan->nomor_hp ?? $v->pelanggan->no_telp }}
                                    </div>
                                    <div class="mb-2">
                                        <i class="ri-router-line me-1"></i> ODP: {{ $v->odp }} | OPM: {{ $v->opm }} | ODC:
                                        {{ $v->odc }}
                                    </div>
                                    <div class="mb-2">
                                        <i class="ri-equalizer-line me-1"></i> Panjang Kabel: {{ $v->panjang_kabel }}
                                    </div>
                                    <div class="mb-2">
                                        <i class="ri-shield-keyhole-line me-1"></i> PPOE:<br>
                                        <div class="ms-3">
                                            <small>Username: {{ $v->user_ppoe }}</small><br>
                                            <small>Password: {{ $v->password_ppoe }}</small><br>
                                            <small>VLAN: {{ $v->vlan_ppoe }}</small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <i class="ri-user-settings-line me-1"></i> Teknisi: {{ $v->teknisi->name }}
                                    </div>
                                    @if($v->foto_rumah)
                                        <div class="mb-3">
                                            <i class="ri-image-line me-1"></i> Foto:<br>
                                            <img src="{{ asset($v->foto_rumah) }}" class="mt-2 rounded" style="max-width: 200px">
                                        </div>
                                    @endif
                                    <div class="d-flex gap-2">
                                        @if($v->status != 'SELESAI')
                                            <a href="{{ route('pemasangan.edit', $v->id) }}"
                                                class="btn btn-soft-warning btn-sm flex-grow-1">
                                                <i class="ri-pencil-fill align-bottom me-1"></i> Edit
                                            </a>
                                            <a href="{{ route('pemasangan.detail', $v->id) }}"
                                                class="btn btn-soft-info btn-sm flex-grow-1">
                                                <i class="ri-eye-fill align-bottom me-1"></i> Detail
                                            </a>
                                            <button class="btn btn-soft-danger btn-sm flex-grow-1"
                                                onclick="confirmDelete('{{ $v->id }}')">
                                                <i class="ri-delete-bin-fill align-bottom me-1"></i> Delete
                                            </button>
                                            <button class="btn btn-soft-success btn-sm flex-grow-1"
                                                onclick="confirmComplete('{{ $v->id }}')">
                                                <i class="ri-check-double-line align-bottom me-1"></i> Selesai
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-3">{{ $data->links('vendor.pagination.bootstrap-5') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Sweet Alert js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <script>
        // Inisialisasi DataTables dengan bahasa Indonesia
        $(document).ready(function () {
            $('#example2').DataTable({
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                order: [] // Menonaktifkan pengurutan default
            });
        });

        // Fungsi untuk konfirmasi pengambilan orderan
        function confirmPickOrder(id) {
            Swal.fire({
                title: 'Konfirmasi Pengambilan Order',
                text: "Anda yakin ingin mengambil orderan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ambil!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0ab39c',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim form
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("pemasangan.pick_order") }}';

                    var csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    var orderId = document.createElement('input');
                    orderId.type = 'hidden';
                    orderId.name = 'pelanggan_id';
                    orderId.value = id;

                    form.appendChild(csrf);
                    form.appendChild(orderId);
                    document.body.appendChild(form);
                    form.submit();
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
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/pemasangan/' + id;

                    var csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    var method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';

                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Fungsi untuk konfirmasi selesai pemasangan
        function confirmComplete(id) {
            Swal.fire({
                title: 'Konfirmasi Selesai',
                text: "Apakah anda yakin menyelesaikan pemasangan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0ab39c',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("pemasangan.complete", "") }}/' + id;

                    var csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    var method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PATCH';

                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection