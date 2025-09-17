@extends('layouts.main')
@section('style')
@endsection
@section('content')
<!-- TABLE START -->
<!-- FORM START -->
<div class="row">
    <div class="col-xl-12" id="card-none1">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-search-alt-2"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Search / Filter</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>

            <div class="card-body collapse show" id="collapseexample1">
                <form method="post" action="{{ route('finance-report') }}">@csrf

                    <div class="row">
                        <div class="col-lg-4">
                            <label for="choices-single-default" class="form-label mb-0">Start</label>
                            <input name="start" type="date" class="form-control" value="{{ $start ?? ''}}">
                        </div>
                        <div class="col-lg-4">
                            <label for="choices-single-default" class="form-label mb-0">End</label>
                            <input name="end" type="date" class="form-control" value="{{ $end ?? '' }}">
                        </div>
                        <div class="col-lg-4 mt-3">
                            <button class="btn btn btn-primary" type="submit">
                                <li class="bx bx-search-alt-2"></li> Search
                            </button>
                            <a class="btn btn btn-danger" href="{{ route('omzet') }}">
                                <li class="bx bx-x"></li> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
<!-- FORM END -->
<div class="row">
    <div class="col-xl-12" id="card-none2">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-table"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Laporan Keuangan</span>
                    </div>
                    <div class="flex-shrink-0">
                    </div>
                </div>
            </div>
            <div class="card-body collapse show" id="collapseexample2">
                <div class="row">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card crm-widget">
                                <div class="card-body p-0">
                                    <div class="row row-cols-xl-5 row-cols-md-3 row-cols-1 g-0">
                                        <div class="col">
                                            <a href="#">
                                                <div class="py-4 px-3">
                                                    <h5 class="text-muted text-uppercase fs-13">TOTAL MODAL <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-wallet-line display-6 text-primary cfs-22"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="{{ $total_modal ?? 0 }}" id="devices_total">{{ $total_modal ?? 0 }}</span></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <a href="#">
                                                <div class="mt-3 mt-md-0 py-4 px-3">
                                                    <h5 class="text-muted text-uppercase fs-13">TOTA PENJUALAN <i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-money-dollar-circle-line display-6 text-success cfs-22"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="{{ $total_penjualan ?? 0 }}" id="devices_online">{{ $total_penjualan ?? 0 }}</span></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <a href="#">
                                                <div class="mt-3 mt-md-0 py-4 px-3">
                                                    <h5 class="text-muted text-uppercase fs-13">ADMIN FEE <i class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i></h5>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-bank-card-2-line display-6 text-danger cfs-22"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="{{ $admin_fee ?? 0 }}" id="devices_offline">{{ $admin_fee ?? 0 }}</span></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <a href="#">
                                                <div class="mt-3 mt-lg-0 py-4 px-3">
                                                    <h5 class="text-muted text-uppercase fs-13"> TOTAL PROFIT<i class="ri-arrow-up-circle-line text-success fs-18 float-end align-middle"></i></h5>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-price-tag-3-line display-6 text-warning cfs-22"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="{{ $total_profit ?? 0 }}" id="today_alarm">{{ $total_profit ?? 0 }}</span></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div><!-- end col -->
                                        <div class="col">
                                            <a href="#">
                                                <div class="mt-3 mt-lg-0 py-4 px-3">
                                                    <h5 class="text-muted text-uppercase fs-13">SISA MODAL <i class="ri-arrow-down-circle-line text-danger fs-18 float-end align-middle"></i></h5>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <i class="ri-wallet-3-fill display-6 text-danger cfs-22"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h2 class="mb-0 cfs-22"><span class="counter-value" data-target="{{ $sisa_modal ?? 0 }}" id="today_offline">{{ $sisa_modal ?? 0 }}</span></h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div> <!-- end row-->
            </div>
        </div>
    </div>
</div>
<!-- TABLE END -->
@endsection
@section('script')
<script src="{{ asset('assets/js/autoNumeric/autoNumeric.js') }}"></script>
<script>
    jQuery(function($) {
        $('.autonumber').autoNumeric('init');
    });
</script>
@endsection