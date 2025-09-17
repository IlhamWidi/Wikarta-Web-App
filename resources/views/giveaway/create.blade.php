@extends('layouts.main')
@section('title') Tambah Give Away @endsection

@section('css')
<!-- Select2 css -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Tambah Give Away</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('giveaway.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <select name="pelanggan_id" class="form-select" id="select2Pelanggan">
                            <option value="">Pilih Pelanggan</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Give Away</label>
                        <input type="text" name="give_away" class="form-control" maxlength="255">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- Select2 js -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Inisialisasi Select2 untuk dropdown pelanggan
    $(document).ready(function() {
        $('#select2Pelanggan').select2({
            placeholder: "Pilih Pelanggan",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection