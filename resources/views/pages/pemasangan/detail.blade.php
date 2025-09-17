@extends('layouts.main')
@section('content')
    <div class="row mb-2">
        <div class="col-lg-12">
            <a href="{{ route('pemasangan') }}" class="btn btn-sm btn-light"
                style="border: 1px solid #dfdfdf;background-color: white;">
                <i class="bx bx-undo"></i>&nbsp; Kembali
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header pt-2 pb-2">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <li class="bx bx-info-circle"></li>&nbsp;&nbsp;
                    <span class="card-title" style="font-size: 15px;">Detail Pemasangan</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered" style="font-size: 13px;">
                <tbody>
                    <tr>
                        <th>Pelanggan</th>
                        <td>{{ $data->pelanggan->nama_pelanggan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Teknisi</th>
                        <td>{{ $data->teknisi->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>ODP</th>
                        <td>{{ $data->odp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>OPM</th>
                        <td>{{ $data->opm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>ODC</th>
                        <td>{{ $data->odc ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Panjang Kabel</th>
                        <td>{{ $data->panjang_kabel ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Username PPOE</th>
                        <td>{{ $data->user_ppoe ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Password PPOE</th>
                        <td>{{ $data->password_ppoe ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>VLAN PPOE</th>
                        <td>{{ $data->vlan_ppoe ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $data->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Foto Rumah</th>
                        <td>
                            @if($data->foto_rumah)
                                <img src="{{ asset($data->foto_rumah) }}" class="img-thumbnail" style="max-height:120px;">
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection