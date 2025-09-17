@extends('layouts.main')
@section('style')
@endsection

@section('content')
<!-- TABLE START -->
<div class="row">
    <div class="col-lg-9" id="card-none2">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-table"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Data Retur</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body collapse show" id="collapseexample2">
                <table id="example" class="table table-bretured dt-responsive nowrap table-striped align-middle" style="width:100%;font-size: 12px;">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>DATE</th>
                            <th>ORDER</th>
                            <th>STATUS</th>
                            <th>QTY</th>
                            <th>PRICE</th>
                            <th>NOTE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data))
                        @foreach($data as $k => $v)
                        <tr>
                            <td style="text-align: center;">{{ $k+1 }}</td>
                            <td style="color: red;">{{ $v->created_at }}</td>
                            <td>{{ $v->orders->products->name ?? '' }} - {{ $v->orders->channel ?? '' }}</td>
                            <td>{{ $v->status ?? '-' }}</td>
                            <td>{{ $v->orders->qty ?? '-' }}</td>
                            <td>{{ isset($v->orders->price) ? number_format($v->orders->price) : '-' }}</td>
                            <td>{{ $v->notes ?? '-' }}</td>
                            <td style="text-align: center;">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="bretur: 1px solid #cfcfcf;">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">

                                        <!-- <li><a href="{{ route('retur.edit', ['id'=> $v->id]) }}" class="dropdown-item edit-item-btn">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a>
                                        </li> -->
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
    <!--</div>-->
    <!-- TABLE END -->
    <!-- FORM WIZARD START -->
    <!--<div class="row">-->
    <div class="col-lg-3" id="card-none1">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-edit"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Form Retur</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body collapse show" id="collapseexample1">
                <form method="post" action="{{ isset($one) ? route('retur.update', ['id' => $one->id]) : route('retur.store') }}">@csrf
                    <fieldset>
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="choices-single-default" class="form-label mt-2">ORDER</label>
                                <select class="form-control js-example-basic-single" id="order_id" name="order_id">
                                    <option value="">Select</option>
                                    @if(isset($ms_order))
                                    @foreach($ms_order as $k => $v)
                                    <option value="{{ $v->id }}" <?= isset($one) && $one->order_id == $v->id ? 'selected=selected' : ''; ?>>{{ isset($v->products->name) ? $v->products->name."-" : '' }} {{ $v->channel }} {{ isset($v->qty) ? '('.$v->qty.')' : '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <label for="choices-single-default" class="form-label mt-2">STATUS</label>
                                <select class="form-control js-example-basic-single" id="status" name="status">
                                    <option value="">Select</option>
                                    @if(isset($ms_retur_status))
                                    @foreach($ms_retur_status as $k => $v)
                                    <option value="{{ $v }}" <?= isset($one) && $one->status == $v ? 'selected=selected' : ''; ?>>{{ $v }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <label for="basiInput" class="form-label mt-2">NOTES</label>
                                <input name="notes" type="text" class="form-control" value="{{ $one->notes ?? '' }}">
                            </div>
                        </div>
                        <hr class="mb-10">
                        <div class="row">
                            <div class="col-lg-12">
                                <center>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-success" type="submit">
                                            <li class="bx bx-save"></li> Submit
                                        </button>
                                        <a class="btn btn-sm btn-danger" href="{{ route('retur') }}">
                                            <li class="bx bx-x"></li> Reset
                                        </a>
                                    </div>
                                </center>
                            </div>
                        </div>

                    </fieldset>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- FORM WIZARD END -->
<!-- Modal Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('retur.delete') }}" method="post">@csrf
            <div class="modal-content">
                <div class="modal-header">
                    <li class="bx bx-trash"></li>&nbsp;
                    <h6 class="modal-title" id="myExtraLargeModalLabel">Delete Confirmation</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="d-flex align-items-center flex-wrap">
                        <input type="hidden" id="id_delete" name="id" />
                        <div class="text-xl text-dark font-weight-bolder text-center">
                            Anda yakin menghapus data ini ?
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <a href="javascript:void(0);" class="btn btn-sm btn-warning" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1 align-middle"></i> Close</a>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <li class="bx bx-trash me-1 align-middle"></li> Delete
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).on('click', '.delete-form', function() {
        let id = $(this).attr('data-id');
        $('#id_delete').val(id);
    });
</script>
@endsection