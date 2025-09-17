@extends('layouts.frontend')
@section('style')
@endsection

@section('content')
<center>
    <div class="col-lg-12">
        <div class="card" style="max-width: 400px;margin-top: 10%;box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.05) 0px 8px 32px;border-radius: 7px;margin-top: 15%;">
            <!--<div class="card-header" style="background-color: #38454a;">-->
            <div class="card-header" style="background-color: #ffffff;">
                @include('components.message')
                <span class="logo-lg">
                    <img src="{{ asset('assets/images/logo-bg-white.png') }}" alt="" height="50">
                </span>
            </div>
            <div class="card-body p-2">
                <div class="p-1 mt-1">
                    <form action="{{ route('landing.process-payment', ['id' => $id])  }}" method="post">@csrf
                        <div class="mt-0">
                            <label for="name" class="form-label" style="float: left;">Nama</label>
                            <input name="name" type="text" class="form-control" id="name" value="{{ $data->users->name ?? old('name') }}" disabled>
                        </div>
                        <div class="mt-2">
                            <label for="invoice_description" class="form-label" style="float: left;">Deskripsi</label>
                            <input name="invoice_description" type="text" class="form-control" id="invoice_description" value="{{ $data->invoice_description ?? old('invoice_description') }}" disabled>
                        </div>
                        <div class="mt-2">
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <label for="periode" class="form-label" style="float: left;">Periode</label>
                                    <input name="periode" type="text" class="form-control" id="periode" value="{{ $data->periode ?? old('periode') }}" disabled>
                                </div>
                                <div class="col-6">
                                    <label for="amount" class="form-label" style="float: left;">Tagihan</label>
                                    <input name="amount" type="text" class="form-control" id="amount" value="{{ $data->amount ?? old('amount') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label for="metode_pembayaran" class="form-label" style="float: left;">Metode Pembayaran</label>
                            <select class="form-control js-example-basic-single" data-choices name="metode_pembayaran" id="metode_pembayaran">
                                <option value="">Pilih Metode Pembayaran</option>
                                @if(isset($ms_metode_pembayaran))
                                @foreach($ms_metode_pembayaran as $k => $v)
                                <option value="{{ $v->id }}">{{ $v->description }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <!-- <div class="mt-2">
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <label for="app_fee" class="form-label" style="float: left;">Biaya Aplikasi</label>
                                    <input name="app_fee" type="text" class="form-control" id="app_fee" value="" disabled>
                                </div>
                                <div class="col-6">
                                    <label for="service_fee" class="form-label" style="float: left;">Biaya Layanan</label>
                                    <input name="service_fee" type="text" class="form-control" id="service_fee" value="" disabled>
                                </div>
                            </div>
                        </div> -->
                        <div class="mt-3">
                            <button class="btn btn-success w-100" type="submit">Lakukan Pembayaran</button>
                        </div>
                    </form>
                </div>
                <div class="mt-2 mb-2 text-center">
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
</center>
@endsection

@section('script')
@endsection