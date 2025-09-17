@extends('layouts.main')
@section('style')
    <link data-require="sweet-alert@*" data-semver="0.4.2" rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
@endsection
@section('content')
    <!-- TABLE START -->
    <div class="row">
        <div class="col-xl-12" id="card-none2">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-4">
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <div class="list-grid-nav hstack gap-1">
                                <a href="{{ route('invoice.create') }}" class="btn btn-success"><i
                                        class="ri-add-fill me-1 align-bottom"></i> Membuat Invoice Manual</a>
                                <a href="{{ route('invoice.export', request()->all()) }}" class="btn btn-outline-primary"><i
                                        class="ri-download-2-line me-1 align-bottom"></i> Export Data Bulanan</a>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            @include('pages.invoice.filter')

            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-table"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Data Invoice</span>
                        </div>
                        <div class="flex-shrink-0">
                        </div>
                    </div>
                </div>

                <div class="card-body collapse show" id="collapseexample2">

                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%;font-size: 12px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Branch</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Due Date</th>
                                <th>Paid Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($data))
                                @foreach($data as $k => $v)
                                    <tr>
                                        <td style="text-align: center;">{{ $k + 1 }}</td>
                                        <td>{{ $v->branches->name ?? '-' }}</td>
                                        <td style="color: red;">{{ $v->invoice_number }}</td>
                                        <td>{{ $v->users->name ?? '-' }}</td>
                                        <td><a href="">{{ isset($v->due_date) ? date('d-m-Y', strtotime($v->due_date)) : '-' }}</a>
                                        </td>
                                        <td><a
                                                href="">{{ isset($v->paid_off_date) ? date('d-m-Y', strtotime($v->paid_off_date)) : '-' }}</a>
                                        </td>
                                        <td>Rp.{{ number_format($v->amount) }}</td>
                                        <td>
                                            @if($v->invoice_status == 'UNPAID')
                                                <span class="badge text-bg-danger">{{ $v->invoice_status }}</span>
                                            @else
                                                <span class="badge text-bg-success">{{ $v->invoice_status }}</span>
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
                                                        <a type="button" data-bs-toggle="modal" data-bs-target="#modalEdit"
                                                            data-id="{{ $v['id'] }}"
                                                            class="dropdown-item remove-item-btn edit-form">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                                        </a>
                                                    </li>
                                                    @if($v->invoice_status == 'SETTLEMENT')
                                                        <li>
                                                            <a href="{{ route('invoice.print', ['id' => $v->id]) }}"
                                                                class="dropdown-item">
                                                                <i class="ri-printer-line align-bottom me-2 text-muted"></i> Print
                                                            </a>
                                                        </li>
                                                    @endif
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
    @include('pages.invoice.modal')
    <!-- TABLE END -->
@endsection
@section('script')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).on('click', '.edit-form', function () {
            let id = $(this).attr('data-id');
            $('#id_edit').val(id);
            const token = "{!! csrf_token() !!}";
            $.ajax({
                type: "POST",
                data: {
                    _token: token,
                    id: id
                },
                url: "<?= route('invoice.edit'); ?>",
                success: function (response) {
                    if (response.error == true) {
                        swal("Failed", response.message, "error");
                    } else {
                        console.log(response)
                        $("#invoice_number").val(response.data.invoice_number)
                        $("#description").val(response.data.invoice_description)
                        $("#customer").val(response.data.users.name)
                        $("#due_date").val(response.data.due_date)
                        $("#amount").val(response.data.amount)
                        $("#branch").val(response.data.branches.name)
                        $("#invoice_status").val(response.data.invoice_status).trigger("change")
                        $("#payment_method").val(response.data.payment_method).trigger("change")
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
@endsection