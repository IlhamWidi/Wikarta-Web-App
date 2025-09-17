@extends('layouts.main')
@section('style')
@endsection

@section('content')
<!-- TABLE START -->
<div class="row">
    <div class="col-lg-12" id="card-none2">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-table"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Data Stock</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body collapse show" id="collapseexample2">
                <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%;font-size: 12px;">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>BARCODE</th>
                            <th>NAMA</th>
                            <th>KATEGORI</th>
                            <th>STK.OFFLINE</th>
                            <th>STK.SHOPEE</th>
                            <th>STK.TOPED</th>
                            <th>STK.LAZADA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data))
                        @foreach($data as $k => $v)
                        <tr>
                            <td style="text-align: center;">{{ $k+1 }}</td>
                            <td style="color: red;">{{ $v->barcode }}</td>
                            <td>{{ $v->name ?? '-'}}</td>
                            <td>{{ $v->categories->name ?? '-' }}</td>
                            <td>{{ $v->stock_list["OFFLINE"] ?? 0 }}</td>
                            <td>{{ $v->stock_list["SHOPEE"] ?? 0 }}</td>
                            <td>{{ $v->stock_list["TOKOPEDIA"] ?? 0 }}</td>
                            <td>{{ $v->stock_list["LAZADA"] ?? 0 }}</td>
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
</div>
<!-- FORM WIZARD END -->
<!-- Modal Delete -->
@endsection
@section('script')
@endsection