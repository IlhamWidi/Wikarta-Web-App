@extends('layouts.main')
@section('title') Edit Give Away @endsection
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Give Away</h4>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('giveaway.update', $giveaway->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <select name="pelanggan_id" class="form-select">
                            <option value="">Pilih Pelanggan</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $giveaway->pelanggan_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ $giveaway->tanggal?->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Give Away</label>
                        <input type="text" name="give_away" class="form-control" maxlength="255" value="{{ $giveaway->give_away }}">
                    </div>
                    <div class="mb-3">
                        <label for="recipient_photo" class="form-label">Foto Penerima</label>
                        @if(isset($giveaway) && $giveaway->recipient_photo)
                        <div class="mt-2">
                            <img src="{{ asset($giveaway->recipient_photo) }}" alt="Foto Penerima" class="img-thumbnail" width="200">
                        </div>
                        @else
                        <span class="text-danger">Belum ada foto penerima...</span>
                        @endif
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection