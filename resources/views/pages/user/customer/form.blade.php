@extends('layouts.main')
@section('style')
@endsection
@section('content')
    <!-- TABLE START -->
    <div class="row">
        <div class="col-xl-12" id="card-none2">

            <div class="card" style="border: 1px solid #dfdfdf;">
                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-info-circle"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Customer Information</span>
                        </div>
                        <div class="flex-shrink-0">
                        </div>
                    </div>
                </div>
                <div class="card-body collapse show" id="collapseexample2">
                    <div class="row">
                        <!-- FORM START -->
                        <form method="post"
                            action="{{ isset($one) ? route('user-customer.update', ['id' => $one->id]) : route('user-customer.store') }}"
                            enctype="multipart/form-data">@csrf
                            <div class="row">
                                <div class="col-xl-12" id="card-none1">
                                    <div class="card">
                                        <div class="card-body collapse show" id="collapseexample1">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label for="basiInput" class="form-label mt-2">ID Registration</label>
                                                    <input name="code" type="text" class="form-control"
                                                        value="{{ $one->code ?? old('code') }}"
                                                        placeholder="biarkan kosong, auto-generate">
                                                    <label for="basiInput" class="form-label mt-2">Name</label>
                                                    <input name="name" type="text" class="form-control"
                                                        value="{{ $one->name ?? old('name') }}">
                                                    <label for="basiInput" class="form-label mt-2">Username</label>
                                                    <input name="username" type="text" class="form-control"
                                                        value="{{ $one->username ?? old('username') }}">
                                                    <label for="iconrightInput" class="form-label mt-2">Password</label>
                                                    <div class="form-icon right">
                                                        <input name="password" type="password"
                                                            class="form-control form-control-icon" id="iconrightInput"
                                                            placeholder="">
                                                    </div>
                                                    <label for="basiInput" class="form-label mt-2">Email</label>
                                                    <input name="email" type="text" class="form-control"
                                                        value="{{ $one->email ?? old('email') }}">
                                                    <div class="form-check form-switch form-switch-md mb-3 mt-3" dir="ltr">
                                                        <label class="form-check-label"
                                                            for="customSwitchsizemd">Status(Activate)</label>
                                                        <input name="status" type="checkbox" class="form-check-input"
                                                            id="customSwitchsizemd" {{ isset($one->status) && $one->status == 1 ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="choices-single-default"
                                                        class="form-label mt-2">Branch</label>
                                                    <select class="form-control js-example-basic-single" name="branch_id"
                                                        id="branch_id">
                                                        <option value="">Select Branch</option>
                                                        @if(isset($ms_brances))
                                                            @foreach($ms_brances as $k => $v)
                                                                <option value="{{ $v->id }}" <?= isset($one->branch_id) && $one->branch_id == $v->id ? 'selected=selected' : ''; ?>>
                                                                    {{ $v->code }}-{{ $v->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="choices-single-default"
                                                        class="form-label mt-2">Package</label>
                                                    <select class="form-control js-example-basic-single" name="package_id"
                                                        id="package_id">
                                                        <option value="">Select Package</option>
                                                        @if(isset($ms_packages))
                                                            @foreach($ms_packages as $k => $v)
                                                                <option value="{{ $v->id }}" <?= isset($one->package_id) && $one->package_id == $v->id ? 'selected=selected' : ''; ?>>
                                                                    {{ $v->name }} ({{ $v->speed }} Mbps)
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <label for="iconrightInput" class="form-label mt-2">Subcribe Fee</label>
                                                    <div class="form-icon right">
                                                        <input id="subscribe_price" name="subscribe_price" type="text"
                                                            class="form-control autonumber"
                                                            value="{{ $one->subscribe_price ?? old('subscribe_price') }}">
                                                    </div>
                                                    <label for="iconrightInput" class="form-label mt-2">Registration
                                                        Fee</label>
                                                    <div class="form-icon right">
                                                        <input id="registration_price" name="registration_price" type="text"
                                                            class="form-control autonumber"
                                                            value="{{ $one->registration_price ?? old('registration_price') }}">
                                                    </div>
                                                    <label for="identity_number" class="form-label mt-2">Identity Number
                                                        (NIK KTP)</label>
                                                    <input name="identity_number" type="text" class="form-control"
                                                        value="{{ $one->identity_number ?? old('identity_number') }}">
                                                    <label for="phone_number" class="form-label mt-2">PhoneNumber</label>
                                                    <input name="phone_number" type="text" class="form-control"
                                                        value="{{ $one->phone_number ?? old('phone_number') }}">
                                                </div>
                                                <div class="col-lg-4">
                                                    <label for="iconrightInput" class="form-label mt-2">Lampiran Foto
                                                        KTP</label>
                                                    <div class="form-icon right">
                                                        <input name="lampiran_foto_ktp" type="file" class="form-control" />
                                                    </div>
                                                    <label for="iconrightInput" class="form-label mt-2">Lampiran Foto
                                                        Rumah</label>
                                                    <div class="form-icon right">
                                                        <input name="lampiran_foto_rumah" type="file"
                                                            class="form-control" />
                                                    </div>
                                                    <label for="choices-single-default" class="form-label mt-2">Coordinates
                                                        (Latitude,
                                                        Longitude)</label>
                                                    <input id="coordinates" name="coordinates" type="text"
                                                        class="form-control"
                                                        value="{{ $one->coordinates ?? old('coordinates') }}">
                                                    <label for="exampleFormControlTextarea5"
                                                        class="form-label mt-2">Address</label>
                                                    <textarea id="address" name="address" style="max-height: 110px;"
                                                        class="form-control" id="exampleFormControlTextarea5"
                                                        rows="4">{{ $one->address ?? '-' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Kode Server</label>
                                                        <input type="text" class="form-control" name="kode_server"
                                                            value="{{ isset($one) ? $one->kode_server : old('kode_server') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Password Server</label>
                                                        <input type="text" class="form-control" name="password_server"
                                                            value="{{ isset($one) ? $one->password_server : old('password_server') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">VLAN</label>
                                                        <input type="text" class="form-control" name="vlan"
                                                            value="{{ isset($one) ? $one->vlan : old('vlan') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">ODP</label>
                                                        <input type="text" class="form-control" name="odp"
                                                            value="{{ old('odp', $one->odp ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">OPM</label>
                                                        <input type="text" class="form-control" name="opm"
                                                            value="{{ old('opm', $one->opm ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">ODC</label>
                                                        <input type="text" class="form-control" name="odc"
                                                            value="{{ old('odc', $one->odc ?? '') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Keterangan</label>
                                                        <textarea class="form-control" name="keterangan"
                                                            rows="1">{{ old('keterangan', $one->keterangan ?? '') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <center>
                                                        <div class="btn-group">
                                                            <button class="btn btn-sm btn-success" type="submit">
                                                                <li class="bx bx-save"></li> Submit
                                                            </button>
                                                            <a class="btn btn-sm btn-danger"
                                                                href="{{ route('user-customer') }}">
                                                                <li class="bx bx-x"></li> Reset
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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ asset('assets/js/autoNumeric/autoNumeric.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).on('click', '.delete-form', function () {
            let id = $(this).attr('data-id');
            $('#id_delete').val(id);
        });
        jQuery(function ($) {
            $('.autonumber').autoNumeric('init');
        });
    </script>
    <script>
        $("#package_id").change(function () {
            let id = $(this).val()
            const token = "{!! csrf_token() !!}";
            $.ajax({
                type: "POST",
                data: {
                    _token: token,
                    id: id
                },
                url: "<?= route('package.show'); ?>",
                success: function (response) {
                    if (response.error == true) {
                        swal("Failed", response.message, "error");
                    } else {
                        $("#registration_price").val(response.data.registration_price)
                        $("#subscribe_price").val(response.data.subscribe_price)
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
    <script>
        $("#coordinates").on('keyup', function () {

            let coordinates = $(this).val().split(",");
            let lat = parseFloat(coordinates[0]);
            let lon = parseFloat(coordinates[1]);
            // address_lookup(lat, lon);
        })

        function address_lookup(lat, lon) {
            $.ajax({
                type: "GET",
                url: "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=" + lat + "&lon=" + lon,
                success: function (response) {
                    $("#address").val(response.display_name)
                },
                failure: function (response) {
                    console.log('faliure')
                    console.log(response)
                },
                error: function (response) {
                    console.log('error')
                    console.log(response)
                }
            });
        }
    </script>
@endsection