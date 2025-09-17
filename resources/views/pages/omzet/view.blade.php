@extends('layouts.main')
@section('style')
@endsection
@section('content')
@include('pages.omzet.filter')
@include('pages.omzet.summary')
<!-- TABLE START -->
<div class="row">
    <div class="col-xl-12" id="card-none2">

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

                <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%;font-size: 12px;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Settlement</th>
                            <th>Branch</th>
                            <th>Invoice No</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Methods</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($response['data']))
                        @foreach($response['data'] as $k => $v)
                        <tr>
                            <td style="text-align: center;">{{ $k+1 }}</td>
                            <td><a href="">{{ date('d-m-Y', strtotime($v->paid_off_date)) }}</a></td>
                            <td>{{ $v->branches->name ?? '-' }}</td>
                            <td style="color: red;">{{ $v->invoice_number }}</td>
                            <td>{{ $v->users->name ?? '-' }}</td>
                            <td>Rp.{{ number_format($v->amount) }}</td>
                            <td>
                                @if($v->payment_method == 'MIDTRANS')
                                <span class="badge text-bg-success">{{ $v->payment_method }}</span>
                                @else
                                <span class="badge text-bg-primary">{{ $v->payment_method }}</span>
                                @endif
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
@endsection
@section('script')