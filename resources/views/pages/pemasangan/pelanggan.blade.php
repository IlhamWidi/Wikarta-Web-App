<!-- TABLE START -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pt-2 pb-2">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <li class="bx bx-table"></li>&nbsp;&nbsp;
                        <span class="card-title" style="font-size: 15px;"> Butuh Dipasang</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Table view (desktop) -->
                <div class="d-none d-md-block">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%;font-size: 12px;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Input</th>
                                <th>Pelanggan</th>
                                <th>Catatan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pelanggan as $k => $v)
                                <tr>
                                    <td style="text-align: center;">{{ $k + 1 }}</td>
                                    <td>{{ $v->created_at->format('d-m-Y H:i') }}</td>
                                    <td>{{ $v->nama_pelanggan }}<br>
                                        {{ $v->nomor_hp }}<br>
                                        {{ $v->alamat_psb ?? '-' }}
                                    </td>
                                    <td class="text-danger">{{ $v->keterangan ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <button type="button" class="btn btn-soft-success btn-sm"
                                            onclick="confirmPickOrder('{{ $v->id }}')">
                                            <i class="ri-add-circle-line"></i> Ambil Orderan
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- List view (mobile) -->
                <div class="d-md-none">
                    @foreach($pelanggan as $k => $v)
                        <div class="card mb-3 border">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <h6 class="fw-bold mb-0">{{ $v->nama_pelanggan }}</h6>
                                    <span class="badge bg-soft-primary text-primary">No. {{ $k + 1 }}</span>
                                </div>
                                <div class="mb-2">
                                    <i class="ri-phone-line me-1"></i> {{ $v->nomor_hp }}
                                </div>
                                <div class="mb-2">
                                    <i class="ri-map-pin-line me-1"></i> {{ $v->alamat_psb ?? '-' }}
                                </div>
                                <div class="mb-3">
                                    <i class="ri-file-text-line me-1"></i>
                                    <span class="text-danger">{{ $v->keterangan ?? '-' }}</span>
                                </div>
                                <button type="button" class="btn btn-soft-success btn-sm w-100"
                                    onclick="confirmPickOrder('{{ $v->id }}')">
                                    <i class="ri-add-circle-line"></i> Ambil Orderan
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- TABLE END -->