@extends('layouts.main')
@section('style')
@endsection
@section('content')
<!-- FORM START -->
<form method="post" action="{{ route('invoice.store') }}">@csrf
    <div class="row">
        <div class="col-xl-12" id="card-none1">
            <div class="card">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-edit"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Form Invoice</span>
                        </div>
                        <div class="flex-shrink-0">
                        </div>
                    </div>
                </div>
                <div class="card-body collapse show" id="collapseexample1">
                    <div class="row">
                        <div class="col-lg-4">
                            <label for="basiInput" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control" disabled value="{{ $one->invoice_number ?? 'GENERATED' }}">
                            <label for="basiInput" class="form-label mt-2">Customer</label>
                            <select class="form-control js-example-basic-single" name="user_id">
                                <option value="">Select Customer</option>
                                @if(isset($ms_users))
                                @foreach($ms_users as $k => $v)
                                <option value="{{ $v->id }}" <?= isset($one->user_id) && $one->user_id == $v->id ? 'selected=selected' : ''; ?>>{{ $v->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <label for="due_date" class="form-label mt-2">Due Date</label>
                            <input name="due_date" type="date" class="form-control" id="due_date">
                        </div>
                        <div class="col-lg-4">
                            <label for="iconrightInput" class="form-label">Amount</label>
                            <input id="amount" name="amount" type="text" class="form-control autonumber" value="{{ $one->amount ?? old('amount') }}">
                            <label for="exampleFormControlTextarea5" class="form-label mt-2">Invoice Description</label>
                            <textarea name="invoice_description" style="max-height: 95px;" class="form-control" id="exampleFormControlTextarea5" rows="4">{{ $one->invoice_description ?? '' }}</textarea>
                        </div>
                        <div class="col-lg-4">
                            <label for="choices-single-default" class="form-label mb-0">Payment Method</label>
                            <select class="form-control js-example-basic-single" id="payment_method" name="payment_method">
                                <option value="">Select</option>
                                @if(isset($ms_payment_methods))
                                @foreach($ms_payment_methods as $x)
                                <option value="{{ $x }}">{{ $x }}</option>
                                @endforeach
                                @endif
                            </select>
                            <label for="choices-single-default" class="form-label mb-0">Status</label>
                            <select class="form-control js-example-basic-single" id="invoice_status" name="invoice_status">
                                <option value="">Select</option>
                                @if(isset($ms_invoice_statuses))
                                @foreach($ms_invoice_statuses as $x)
                                <option value="{{ $x }}">{{ $x }}</option>
                                @endforeach
                                @endif
                            </select>
                            <label for="paid_off_date" class="form-label mt-2">Paid Off Date</label>
                            <input name="paid_off_date" type="date" class="form-control" id="paid_off_date">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <center>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <li class="bx bx-save"></li> Submit
                                    </button>
                                    <a class="btn btn-sm btn-danger" href="{{ route('invoice') }}">
                                        <li class="bx bx-x"></li> Back
                                    </a>
                                </div>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- FORM END -->
@endsection
@section('script')
<script src="{{ asset('assets/js/autoNumeric/autoNumeric.js') }}"></script>
<script>
    jQuery(function($) {
        $('.autonumber').autoNumeric('init');
    });
</script>
@endsection