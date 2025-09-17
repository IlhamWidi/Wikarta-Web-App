@extends('layouts.main')
@section('style')
    <style>
        .mobile-card {
            display: none;
        }

        @media screen and (max-width: 768px) {
            #example {
                display: none;
            }

            .mobile-card {
                display: block;
            }

            .card-pelanggan {
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            }
        }
    </style>
@endsection
@section('content')
    <!-- TABLE START -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-table"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Data Pelanggan</span>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('pelanggan.create') }}" class="btn btn-primary btn-sm"
                                style="border: 1px solid #cfcfcf;">
                                <i class="ri-add-line align-bottom me-1"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Desktop -->
                    <div class="mb-3 d-none d-md-block">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Nama/No HP</label>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                    placeholder="Nama/No HP">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Marketing</label>
                                <select name="marketing_id" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($ms_marketing as $m)
                                        <option value="{{ $m->id }}" @if(request('marketing_id') == $m->id) selected @endif>
                                            {{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Teknisi</label>
                                <select name="teknisi_id" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($ms_teknisi as $t)
                                        <option value="{{ $t->id }}" @if(request('teknisi_id') == $t->id) selected @endif>
                                            {{ $t->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach($statuses as $key => $val)
                                        <option value="{{ $key }}" @if(request('status') == $key) selected @endif>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100"><i
                                        class="fa fa-search"></i>Filter</button>
                            </div>
                        </form>
                    </div>
                    <!-- Filter Mobile -->
                    <div class="d-block d-md-none mb-2">
                        <button class="btn btn-outline-secondary btn-sm mb-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#filterMobile" aria-expanded="false" aria-controls="filterMobile">
                            <i class="ri-filter-3-line"></i> Filter
                        </button>
                        <div class="collapse" id="filterMobile">
                            <form method="GET" class="row g-2">
                                <div class="col-12">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control mb-2"
                                        placeholder="Nama/No HP">
                                </div>
                                <div class="col-12">
                                    <select name="marketing_id" class="form-select mb-2">
                                        <option value="">Semua Marketing</option>
                                        @foreach($ms_marketing as $m)
                                            <option value="{{ $m->id }}" @if(request('marketing_id') == $m->id) selected @endif>
                                                {{ $m->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select name="teknisi_id" class="form-select mb-2">
                                        <option value="">Semua Teknisi</option>
                                        @foreach($ms_teknisi as $t)
                                            <option value="{{ $t->id }}" @if(request('teknisi_id') == $t->id) selected @endif>
                                                {{ $t->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select name="status" class="form-select mb-2">
                                        <option value="">Semua Status</option>
                                        @foreach($statuses as $key => $val)
                                            <option value="{{ $key }}" @if(request('status') == $key) selected @endif>
                                                {{ $val }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Cari
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tampilan Table Desktop -->
                    <div class="d-none d-md-block">
                        <table class="table table-bordered dt-responsive nowrap table-striped align-middle"
                            style="width:100%;font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Paket</th>
                                    <th>Marketing</th>
                                    <th>Teknisi</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $k => $v)
                                    <tr>
                                        <td style="text-align: center;">{{ $k + 1 }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($v->created_at)->format('d-m-Y H:i') }}
                                        </td>
                                        <td>{{ $v->nama_pelanggan }}<br>
                                            {{ $v->nomor_hp }}<br>
                                            {{ $v->alamat_psb }}
                                        </td>
                                        <td>{{ $v->paket->name . ' (' . $v->paket->description . ')' ?? '-' }}</td>
                                        <td>{{ $v->marketing->name ?? '-' }}</td>
                                        <td>{{ $v->teknisi->name ?? '-' }}</td>
                                        <td>
                                            @if($v->status == 'REGISTER')
                                                <span class="badge bg-warning">{{ $v->status }}</span>
                                            @elseif($v->status == 'SEDANG_DIPROSES')
                                                <span class="badge bg-info">{{ $v->status }}</span>
                                            @elseif($v->status == 'SELESAI')
                                                <span class="badge bg-success">{{ $v->status }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item edit-item-btn"
                                                            href="{{ route('pelanggan.edit', ['id' => $v->id]) }}">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete"
                                                            data-id="{{ $v->id }}"
                                                            class="dropdown-item remove-item-btn delete-form">
                                                            <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                            Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $data->links('pagination::bootstrap-5') }}</div>
                    </div>
                    <!-- Tampilan Mobile Card -->
                    <div class="mobile-card d-block d-md-none">
                        @foreach($data as $k => $v)
                            <div class="card card-pelanggan">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">{{ $v->nama_pelanggan }}</h6>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item edit-item-btn"
                                                        href="{{ route('pelanggan.edit', ['id' => $v->id]) }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete"
                                                        data-id="{{ $v->id }}"
                                                        class="dropdown-item remove-item-btn delete-form">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="ri-phone-line me-1"></i> {{ $v->nomor_hp }}
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="ri-box-3-line me-1"></i>
                                        {{ $v->paket->name . ' (' . $v->paket->description . ')' ?? '-' }}
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="ri-user-3-line me-1"></i> Marketing: {{ $v->marketing->name ?? '-' }}
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="ri-tools-line me-1"></i> Teknisi: {{ $v->teknisi->name ?? '-' }}
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="ri-map-pin-line me-1"></i> {{ $v->alamat_psb }}
                                    </div>
                                    <div class="mt-2">
                                        @if($v->status == 'REGISTER')
                                            <span class="badge bg-warning">{{ $v->status }}</span>
                                        @elseif($v->status == 'SEDANG_DIPROSES')
                                            <span class="badge bg-info">{{ $v->status }}</span>
                                        @elseif($v->status == 'SELESAI')
                                            <span class="badge bg-success">{{ $v->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-3">{{ $data->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TABLE END -->
    @include('pages.pelanggan.modal')
@endsection
@section('script')
    <script>
        $(document).on('click', '.delete-form', function () {
            let id = $(this).attr('data-id');
            $('#id_delete').val(id);
        });
    </script>
@endsection