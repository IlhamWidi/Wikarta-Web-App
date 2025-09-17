@extends('layouts.main')
@section('style')
    <link data-require="sweet-alert@*" data-semver="0.4.2" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        .mobile-card {
            display: none;
        }

        @media screen and (max-width: 768px) {
            #customer-table {
                display: none;
            }

            .mobile-card {
                display: block;
            }

            .card-customer {
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            }
        }
    </style>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-group-line" style="font-size:2rem;color:#556ee6;"></i></div>
                    <h6 class="mb-1">TOTAL CUSTOMER</h6>
                    <h4 class="mb-0">{{ $total_customer ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-user-follow-line" style="font-size:2rem;color:#34c38f;"></i></div>
                    <h6 class="mb-1">TOTAL ACTIVE</h6>
                    <h4 class="mb-0">{{ $total_active ?? 0}}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-shield-user-line" style="font-size:2rem;color:#f1b44c;"></i></div>
                    <h6 class="mb-1">TOTAL ISOLIR</h6>
                    <h4 class="mb-0">{{ $total_isolir ?? 0}}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="mb-2"><i class="ri-delete-bin-2-line" style="font-size:2rem;color:#f46a6a;"></i></div>
                    <h6 class="mb-1">TOTAL DISMANTEL</h6>
                    <h4 class="mb-0">{{ $total_dismantel ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
    <!-- TABLE START -->
    <div class="row">
        <div class="col-xl-12" id="card-none2">

            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-table"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Data User Customer</span>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="d-flex justify-content-end align-items-center"
                                style="margin-left: 12px; margin-right: 12px;">
                                <a href="{{ route('user-customer.create') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tombol Tambah Data -->
                <!-- Filter Section -->
                <div class="mb-3 mt-3 d-none d-md-block" style="margin-left: 12px; margin-right: 12px;">
                    <form method="get" class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label mb-1">ID</label>
                            <input type="text" name="code" class="form-control" value="{{ $filters['code'] ?? '' }}"
                                placeholder="ID">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $filters['name'] ?? '' }}"
                                placeholder="Nama">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Phone</label>
                            <input type="text" name="phone_number" class="form-control"
                                value="{{ $filters['phone_number'] ?? '' }}" placeholder="No HP">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">Semua Cabang</option>
                                @foreach($ms_brances as $branch)
                                    <option value="{{ $branch->id }}" {{ ($filters['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1">Package</label>
                            <select name="package_id" class="form-select">
                                <option value="">Semua Paket</option>
                                @foreach($ms_packages as $package)
                                    <option value="{{ $package->id }}" {{ ($filters['package_id'] ?? '') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </form>
                </div>
                <!-- Mobile Filter Toggle -->
                <div class="d-md-none mb-2">
                    <button class="btn btn-outline-primary w-100" type="button" data-bs-toggle="collapse"
                        data-bs-target="#mobileFilterCollapse" aria-expanded="false" aria-controls="mobileFilterCollapse">
                        <i class="fa fa-filter"></i> Filter Data
                    </button>
                    <div class="collapse mt-2" id="mobileFilterCollapse">
                        <form method="get" class="row g-2">
                            <div class="col-12">
                                <input type="text" name="code" class="form-control mb-2"
                                    value="{{ $filters['code'] ?? '' }}" placeholder="ID">
                                <input type="text" name="name" class="form-control mb-2"
                                    value="{{ $filters['name'] ?? '' }}" placeholder="Nama">
                                <input type="text" name="phone_number" class="form-control mb-2"
                                    value="{{ $filters['phone_number'] ?? '' }}" placeholder="No HP">
                                <select name="branch_id" class="form-select mb-2">
                                    <option value="">Semua Cabang</option>
                                    @foreach($ms_brances as $branch)
                                        <option value="{{ $branch->id }}" {{ ($filters['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <select name="package_id" class="form-select mb-2">
                                    <option value="">Semua Paket</option>
                                    @foreach($ms_packages as $package)
                                        <option value="{{ $package->id }}" {{ ($filters['package_id'] ?? '') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary w-100" type="submit"><i class="fa fa-search"></i>
                                    Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="d-none d-md-block" style="margin-left: 12px;margin-right: 12px;">
                    @if($isEmpty)
                        <div class="text-center py-4">
                            <h4>Data Tidak Tersedia</h4>
                            <p class="text-muted">Belum ada data customer.</p>
                        </div>
                    @else
                        <table class="table table-bordered dt-responsive nowrap table-striped align-middle"
                            style="width:100%;font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>PhoneNumber</th>
                                    <th>Branch</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $k => $v)
                                    <tr>
                                        <td style="text-align: center;">{{ ($data->currentPage() - 1) * $data->perPage() + $k + 1 }}
                                        </td>
                                        <td style="color: red;">{{ $v->code }}</td>
                                        <td>{{ $v->name ?? '-' }}</td>
                                        <td>{{ $v->username ?? '-' }}</td>
                                        <td><a href="tel:{{ $v->phone_number }}">{{ $v->phone_number }}</a></td>
                                        <td>{{ $v->branches->name ?? '-' }}</td>
                                        <td style="color: green;">{{ $v->packages->name }}</td>
                                        <td>
                                            @if($v->status == 1)
                                                <span class="badge text-bg-success">Active</span>
                                            @else
                                                <span class="badge text-bg-danger">In-Active</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false"
                                                    style="border: 1px solid #cfcfcf;">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('user-customer.detail', ['id' => $v->id]) }}"
                                                            class="dropdown-item">
                                                            <i class="ri-eye-fill align-bottom me-2 text-muted"></i> Detail
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item edit-item-btn"
                                                            href="{{ route('user-customer.edit', ['id' => $v->id]) }}">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete"
                                                            data-id="{{ $v['id'] }}"
                                                            class="dropdown-item remove-item-btn delete-form">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

                <!-- Mobile Card/List View -->
                <div class="d-md-none">
                    @if($data->isEmpty())
                        <div class="text-center py-4">
                            <h4>Data Tidak Tersedia</h4>
                            <p class="text-muted">Belum ada data customer.</p>
                        </div>
                    @else
                        @foreach($data as $v)
                            <div class="card mb-3 border">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold text-danger">{{ $v->code }}</span>
                                        <span class="badge bg-soft-primary text-primary">{{ $v->branches->name ?? '-' }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <i class="ri-user-line me-1"></i> {{ $v->name ?? '-' }}
                                    </div>
                                    <div class="mb-1">
                                        <i class="ri-phone-line me-1"></i> <a
                                            href="tel:{{ $v->phone_number }}">{{ $v->phone_number }}</a>
                                    </div>
                                    <div class="mb-1">
                                        <i class="ri-router-line me-1"></i> Paket: <span
                                            class="text-success">{{ $v->packages->name }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <i class="ri-user-settings-line me-1"></i> Username: {{ $v->username ?? '-' }}
                                    </div>
                                    <div class="mb-2">
                                        <i class="ri-shield-user-line me-1"></i> Status:
                                        @if($v->status == 1)
                                            <span class="badge text-bg-success">Active</span>
                                        @else
                                            <span class="badge text-bg-danger">In-Active</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('user-customer.detail', ['id' => $v->id]) }}"
                                            class="btn btn-soft-info btn-sm flex-grow-1">
                                            <i class="ri-eye-fill align-bottom me-1"></i> Detail
                                        </a>
                                        <a href="{{ route('user-customer.edit', ['id' => $v->id]) }}"
                                            class="btn btn-soft-primary btn-sm flex-grow-1">
                                            <i class="ri-pencil-fill align-bottom me-1"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-soft-danger btn-sm flex-grow-1 delete-form"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete" data-id="{{ $v->id }}">
                                            <i class="ri-delete-bin-fill align-bottom me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-2">
                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
    <!-- TABLE END -->
    @include('pages.user.customer.modal')
@endsection

@section('script')
    <script src="{{ asset('assets/js/autoNumeric/autoNumeric.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).on('click', '.delete-form', function () {
            let id = $(this).attr('data-id');
            $('#id_delete').val(id);
        });
        jQuery(function ($) {
            $('.autonumber').autoNumeric('init');
        });
    </script>
    <script>
        $("#package_id").change(function () {
            let id = $(this).val()
            const token = "{!! csrf_token() !!}";
            $.ajax({
                type: "POST",
                data: {
                    _token: token,
                    id: id
                },
                url: "<?= route('package.show'); ?>",
                success: function (response) {
                    if (response.error == true) {
                        swal("Failed", response.message, "error");
                    } else {
                        $("#registration_price").val(response.data.registration_price)
                        $("#subscribe_price").val(response.data.subscribe_price)
                    }
                },
                failure: function (response) {
                    console.log('faliure')
                    swal("Failed", "Terjadi failure system", "error");
                },
                error: function (response) {
                    console.log('error')
                    swal("Failed", "Terjadi failure system", "error");
                }
            });
        });
    </script>
    <script>
        $("#coordinates").on('keyup', function () {

            let coordinates = $(this).val().split(",");
            let lat = parseFloat(coordinates[0]);
            let lon = parseFloat(coordinates[1]);
            address_lookup(lat, lon);
        })

        function address_lookup(lat, lon) {
            $.ajax({
                type: "GET",
                url: "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=" + lat + "&lon=" + lon,
                success: function (response) {
                    $("#address").val(response.display_name)
                },
                failure: function (response) {
                    console.log('faliure')
                    console.log(response)
                },
                error: function (response) {
                    console.log('error')
                    console.log(response)
                }
            });
        }
    </script>
@endsection