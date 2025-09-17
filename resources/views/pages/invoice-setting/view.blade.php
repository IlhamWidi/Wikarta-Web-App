@extends('layouts.main')
@section('style')
@endsection
@section('content')
<!-- TABLE START -->
<div class="row">
    <div class="col-lg-6" id="card-none2">

        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bxs-info-circle"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Information</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>

            <div class="card-body collapse show" id="collapseexample2">

                <div class="row">
                    <div class="col-lg-12">

                        <table id="" class="table table-bordered tableRole" style="font-size: 13px;">
                            <thead>
                                <tr>
                                    <th colspan="2" style="background-color: #455358;color: white;text-align: center;">INFORMATION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 30%;">
                                        Invoice Date
                                    </td>
                                    <td>
                                        Invoice akan dicetak setiap tanggal <b style="color:red;">{{ $data->invoice_date ?? '' }}</b>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Notification Days Before
                                    </td>
                                    <td>
                                        Pemberitahuan pembayaran invoice akan disampaikan setiap tanggal <b style="color:red;">{{ $data->notification_days_before ?? '' }}</b>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Notification Days Warning
                                    </td>
                                    <td>
                                        Peringatan akan dilakukan setiap jeda <b style="color:red;">{{ $data->notification_days_warning ?? '' }}</b> hari sampai customer melakukan pembayaran
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Notification Template Invoice
                                    </td>
                                    <td>
                                        {{ $data->notification_template_invoice ?? '' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Notification Template Paid
                                    </td>
                                    <td>
                                        {{ $data->notification_template_paid ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

        </div>

    </div>


    <div class="col-lg-6" id="card-none1">
        <div class="card">

            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-edit"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Setup</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>

            <div class="card-body collapse show" id="collapseexample1">

                <form method="post" action="{{ route('invoice-setting.store') }}">@csrf
                    <fieldset>
                        <div class="row">
                            <div class="col-lg-12">

                                <label for="basiInput" class="form-label mt-2">Invoice Date (Tanggal Cetak Invoice)</label>
                                <input type="number" class="form-control" name="invoice_date" max="31" min="1" value="{{ $data->invoice_date ?? old('invoice_date') }}">

                                <label for="basiInput" class="form-label mt-2">Notification Days Before (Tanggal H- Peringatan)</label>
                                <input type="number" class="form-control" name="notification_days_before" max="31" min="1" value="{{ $data->notification_days_before ?? old('notification_days_before') }}">

                                <label for="basiInput" class="form-label mt-2">Notification Days Warning (Jeda Hari Peringatan)</label>
                                <input type="number" class="form-control" name="notification_days_warning" max="31" min="1" value="{{ $data->notification_days_warning ?? old('notification_days_warning') }}">

                                <label for="notification_template_invoice" class="form-label mt-2">Notification Template Invoice (Template Peringatan Pembayaran)</label>
                                <textarea rows="4" name="notification_template_invoice" class="form-control" id="notification_template_invoice" rows="3">{{ $data->notification_template_invoice ?? old('notification_template_invoice') }}</textarea>

                                <label for="notification_template_paid" class="form-label mt-2">Notification Template Paid (Template Pemberitahuan Sudah Membayar)</label>
                                <textarea name="notification_template_paid" class="form-control" id="notification_template_paid" rows="3">{{ $data->notification_template_paid ?? old('notification_template_paid') }}</textarea>
                            </div>
                        </div>

                        <hr class="mb-10">

                        <div class="row">
                            <div class="col-lg-12">
                                <center>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-success">
                                            <li class="bx bx-save"></li> Submit
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <li class="bx bx-x"></li> Cancel
                                        </button>
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
@endsection
@section('script')
@endsection