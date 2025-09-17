<!-- Desktop Table View -->
<div class="d-none d-md-block">
    <table class="table table-bordered dt-responsive nowrap table-striped align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>ODP</th>
                <th>OPM</th>
                <th>ODC</th>
                <th>Panjang Kabel</th>
                <th>PPOE</th>
                <th>Teknisi</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $k => $v)
                <tr>
                    <td>{{ $data->firstItem() + $k }}</td>
                    <td>{{ $v->pelanggan->nama_pelanggan ?? '-' }}</td>
                    <td>{{ $v->odp }}</td>
                    <td>{{ $v->opm }}</td>
                    <td>{{ $v->odc }}</td>
                    <td>{{ $v->panjang_kabel }}</td>
                    <td>
                        Username: {{ $v->user_ppoe }}<br>
                        Password: {{ $v->password_ppoe }}<br>
                        VLAN: {{ $v->vlan_ppoe }}
                    </td>
                    <td>{{ $v->teknisi->name ?? '-' }}</td>
                    <td>
                        @if($v->foto_rumah)
                            <img src="{{ asset($v->foto_rumah) }}" width="50">
                        @endif
                    </td>
                    <td>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="{{ route('pemasangan.edit', $v->id) }}" class="dropdown-item">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" onclick="confirmDelete('{{ $v->id }}')">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                        Hapus
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" onclick="confirmComplete('{{ $v->id }}')">
                                        <i class="ri-check-double-line align-bottom me-2 text-muted"></i>
                                        Selesai
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div>
        {{ $data->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

<!-- Mobile List View -->
<div class="d-md-none">
    @forelse($data as $k => $v)
        <div class="card mb-3 border">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">{{ $v->pelanggan->nama_pelanggan ?? '-' }}</h6>
                    <span class="badge bg-soft-primary text-primary">No. {{ $data->firstItem() + $k }}</span>
                </div>
                <div class="mb-2">
                    <i class="ri-router-line me-1"></i> ODP: {{ $v->odp }}
                </div>
                <div class="mb-2">
                    <i class="ri-signal-tower-line me-1"></i> OPM: {{ $v->opm }}
                </div>
                <div class="mb-2">
                    <i class="ri-equalizer-line me-1"></i> Panjang Kabel: {{ $v->panjang_kabel }}
                </div>
                <div class="mb-2">
                    <i class="ri-shield-keyhole-line me-1"></i> PPOE:<br>
                    <div class="ms-3">
                        <small>Username: {{ $v->user_ppoe }}</small><br>
                        <small>Password: {{ $v->password_ppoe }}</small><br>
                        <small>VLAN: {{ $v->vlan_ppoe }}</small>
                    </div>
                </div>
                <div class="mb-2">
                    <i class="ri-user-settings-line me-1"></i> Teknisi: {{ $v->teknisi->name ?? '-' }}
                </div>
                @if($v->foto_rumah)
                    <div class="mb-3">
                        <i class="ri-image-line me-1"></i> Foto:<br>
                        <img src="{{ asset($v->foto_rumah) }}" class="mt-2 rounded" style="max-width: 200px">
                    </div>
                @endif
                <div class="d-flex gap-2">
                    <a href="{{ route('pemasangan.edit', $v->id) }}" class="btn btn-soft-warning btn-sm flex-grow-1">
                        <i class="ri-pencil-fill align-bottom me-1"></i> Edit
                    </a>
                    <button class="btn btn-soft-danger btn-sm flex-grow-1" onclick="confirmDelete('{{ $v->id }}')">
                        <i class="ri-delete-bin-fill align-bottom me-1"></i> Hapus
                    </button>
                    <button class="btn btn-soft-success btn-sm flex-grow-1" onclick="confirmComplete('{{ $v->id }}')">
                        <i class="ri-check-double-line align-bottom me-1"></i> Selesai
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Tidak ada data.</div>
    @endforelse
    <div>
        {{ $data->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>