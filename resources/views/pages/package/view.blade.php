@extends('layouts.main')
@section('style')
@endsection
@section('content')
@include('pages.package.form')
<!-- TABLE START -->
<div class="row">
    <div class="col-xl-12" id="card-none2">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-table"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Data Paket</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body collapse show" id="collapseexample2">
                <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%;font-size: 12px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Subscribe Price</th>
                            <th>Registration Fee</th>
                            <th>Sequence</th>
                            <th>Publish Web</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data))
                        @foreach($data as $k => $v)
                        <tr>
                            <td style="text-align: center;">{{ $k+1 }}</td>
                            <td style="color: red;">{{ $v->name ?? '-' }}</td>
                            <td><a href="#">{{ $v->description }}</a></td>
                            <td>Rp.{{ number_format($v->subscribe_price) }}</td>
                            <td>Rp.{{ number_format($v->registration_price) }}</td>
                            <td style="color: green;">{{ $v->sequence }}</td>
                            <td>
                                @if($v->publish == 1)
                                <span class="badge text-bg-success">Publised</span>
                                @else
                                <span class="badge text-bg-danger">Draft</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid #cfcfcf;">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-item-btn" href="{{ route('package.edit', ['id'=> $v->id]) }}">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a type="button" data-bs-toggle="modal" data-bs-target="#modalDelete" data-id="{{ $v['id'] }}" class="dropdown-item remove-item-btn delete-form">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- TABLE END -->
@include('pages.package.modal')
@endsection
@section('script')
<script>
    $(document).on('click', '.delete-form', function() {
        let id = $(this).attr('data-id');
        $('#id_delete').val(id);
    });
</script>
<script src="{{ asset('assets/js/autoNumeric/autoNumeric.js') }}"></script>
<script type="text/javascript">
    jQuery(function($) {
        $('.autonumber').autoNumeric('init');
    });
</script>
@endsection