@extends('layouts.main')
@section('style')
@endsection
@section('content')
    @include('components.message')

    <div class="row mb-2">
        <div class="col-lg-12">
            <a href="{{ route('user-customer') }}" class="btn btn-sm btn-light"
                style="border: 1px solid #dfdfdf;background-color: white;"><i class="bx bx-undo"></i>&nbsp; Previous</a>
        </div>
    </div>

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
                        <div class="col-lg-6">
                            <table class="table table-bordered dt-responsive nowrap tableRole mb-1"
                                style="width:100%;font-size: 12px;border:1px solid #dfdfdf;border-radius: 50px;">
                                <tbody>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">User ID</td>
                                        <td style="color: red;">{{ $data->code ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Branch</td>
                                        <td>{{ $data->branches->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Full Name</td>
                                        <td>{{ $data->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Email</td>
                                        <td>
                                            <a href="#">{{ $data->email ?? '' }}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Telephone</td>
                                        <td>{{ $data->phone_number ?? ''  }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">NIK</td>
                                        <td>{{ $data->identity_number ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Package</td>
                                        <td>{{ $data->packages->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Subscribe Fee</td>
                                        <td>{{ isset($data->subscribe_price) ? number_format($data->subscribe_price) : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Registration Fee</td>
                                        <td>{{ isset($data->registration_price) ? number_format($data->registration_price) : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Address</td>
                                        <td>{{ $data->address ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Status</td>
                                        <td>
                                            @if($data->status == 1)
                                                <span class="badge text-bg-success">active</span>
                                            @else
                                                <span class="badge text-bg-danger">inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Kode Server</td>
                                        <td>{{ $data->kode_server ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Password Server</td>
                                        <td>{{ $data->password_server ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">VLAN</td>
                                        <td>{{ $data->vlan ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">ODP</td>
                                        <td>{{ $data->odp ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">OPM</td>
                                        <td>{{ $data->opm ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">ODC</td>
                                        <td>{{ $data->odc ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="table-light" style="width: 25%;">Keterangan</td>
                                        <td>{{ $data->keterangan ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6">
                            <div style="border: 1px solid #dfdfdf;padding: 15px;text-align: center;">
                                <a href="{{ asset($data->lampiran_foto_ktp) }}" target="_blank">
                                    <img src="{{ asset($data->lampiran_foto_ktp) }}"
                                        style="border: 1px solid #dfdfdf;width: auto;height: 150px;">
                                </a>
                            </div>
                            <div style="border: 1px solid #dfdfdf;padding: 15px;text-align: center;">
                                <a href="{{ asset($data->lampiran_foto_rumah) }}" target="_blank">
                                    <img src="{{ asset($data->lampiran_foto_rumah) }}"
                                        style="border: 1px solid #dfdfdf;width: auto;height: 150px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TABLE END -->


@endsection
@section('script')

@endsection