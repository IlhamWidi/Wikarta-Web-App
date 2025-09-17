@extends('layouts.main')
@section('style')
@endsection
@section('content')
@include('pages.pelanggan.form')
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
                </div>
            </div>
            <div class="card-body">
                <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%;font-size: 12px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Paket</th>
                            <th>Alamat PSB</th>
                            <th>ODP</th>
                            <th>Panjang Kabel</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $k => $v)
                        <tr>
                            <td style="text-align: center;">{{ $k+1 }}</td>
                            <td>{{ $v->nama_pelanggan }}</td>
                            <td>{{ $v->nomor_hp }}</td>
                            <td>{{ $v->paket->name ?? '-' }}</td>
                            <td>{{ $v->alamat_psb }}</td>
                            <td>{{ $v->odp }}</td>
                            <td>{{ $v->panjang_kabel }}</td>
                            <td style="text-align: center;">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-item-btn" href="{{ route('pelanggan.edit', ['id'=> $v->id]) }}">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete" data-id="{{ $v->id }}" class="dropdown-item remove-item-btn delete-form">
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
            </div>
        </div>
    </div>
</div>
<!-- TABLE END -->
@include('pages.pelanggan.modal')
@endsection
@section('script')
<script>
    $(document).on('click', '.delete-form', function() {
        let id = $(this).attr('data-id');
        $('#id_delete').val(id);
    });
</script>
@endsection