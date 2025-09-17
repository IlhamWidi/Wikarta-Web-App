@extends('layouts.main')
@section('title') Manajemen Asset @endsection
@section('css')
<!--datatable css-->
<link href="{{ URL::asset('build/libs/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-table"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Daftar Asset</span>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('data-asset.create') }}" class="btn btn-success">
                            <i class="ri-add-line align-bottom"></i> Tambah Asset
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <table id="data-asset" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nama Asset</th>
                            <th>Tipe</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Photo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr>
                            <td>{{ $asset->nama_asset }}</td>
                            <td>{{ $asset->tipe_asset }}</td>
                            <td>{{ $asset->stok }}</td>
                            <td>{{ $asset->satuan }}</td>
                            <td>
                                @if($asset->photo)
                                <img src="{{ asset($asset->photo) }}"
                                    alt="Asset Photo"
                                    class="img-thumbnail"
                                    style="max-width: 100px">
                                @else
                                <span class="badge bg-light text-muted">No Photo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('data-asset.edit', $asset->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <form action="{{ route('data-asset.destroy', $asset->id) }}"
                                    method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/jquery.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/datatables.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/dataTables.responsive.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Inisialisasi DataTable
        var table = $('#data-asset').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'asc']
            ], // Sort berdasarkan kolom nama asset
            columnDefs: [{
                    targets: -1, // Kolom aksi
                    orderable: false,
                    searchable: false
                },
                {
                    targets: -2, // Kolom photo
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>
@endsection