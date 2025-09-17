@extends('layouts.main')
@section('content')
    @php
        $isMobile = request()->header('User-Agent') && (strpos(request()->header('User-Agent'), 'Mobile') !== false);
    @endphp
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 text-white"><i class="bx bx-list-ul me-2"></i>Daftar Tiket Support</h4>
                    <a href="{{ route('support_ticket.create') }}" class="btn btn-light"><i
                            class="bx bx-add-to-queue me-2"></i>Tambah</a>
                </div>
                <div class="card-body">
                    @if($isMobile)
                        <button class="btn btn-outline-primary mb-3" type="button" id="toggleFilterMobile">
                            <i class="bx bx-filter-alt"></i> Filter
                        </button>
                        <div id="filterFormMobile" style="display:none;">
                            <form method="GET" class="row g-2 mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Kode Tiket</label>
                                    <input type="text" name="kode" value="{{ request('kode') }}" class="form-control"
                                        placeholder="Kode Tiket">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nama/No Telp</label>
                                    <input type="text" name="nama" value="{{ request('nama') }}" class="form-control"
                                        placeholder="Nama/No Telp">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cabang</label>
                                    <select name="branch_id" class="form-select">
                                        <option value="">Semua Cabang</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" @if(request('branch_id') == $branch->id) selected
                                                @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="REGISTER" @if(request('status') == 'REGISTER') selected @endif>REGISTER
                                        </option>
                                        <option value="FORWARD_TEKNISI" @if(request('status') == 'FORWARD_TEKNISI') selected
                                        @endif>FORWARD TEKNISI</option>
                                        <option value="PROSES" @if(request('status') == 'PROSES') selected @endif>PROSES</option>
                                        <option value="SELESAI" @if(request('status') == 'SELESAI') selected @endif>SELESAI</option>
                                        <option value="DIBATALKAN" @if(request('status') == 'DIBATALKAN') selected @endif>DIBATALKAN
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Filter</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <form method="GET" class="row g-2 mb-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Kode Tiket</label>
                                <input type="text" name="kode" value="{{ request('kode') }}" class="form-control"
                                    placeholder="Kode Tiket">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nama/No Telp</label>
                                <input type="text" name="nama" value="{{ request('nama') }}" class="form-control"
                                    placeholder="Nama/No Telp">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cabang</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @if(request('branch_id') == $branch->id) selected
                                            @endif>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="REGISTER" @if(request('status') == 'REGISTER') selected @endif>REGISTER
                                    </option>
                                    <option value="FORWARD_TEKNISI" @if(request('status') == 'FORWARD_TEKNISI') selected
                                    @endif>FORWARD TEKNISI</option>
                                    <option value="PROSES" @if(request('status') == 'PROSES') selected @endif>PROSES</option>
                                    <option value="SELESAI" @if(request('status') == 'SELESAI') selected @endif>SELESAI</option>
                                    <option value="DIBATALKAN" @if(request('status') == 'DIBATALKAN') selected @endif>DIBATALKAN
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Filter</button>
                            </div>
                        </form>
                    @endif
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>PIC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr
                                        class="@if($ticket->status == 'REGISTER') table-warning @elseif($ticket->status == 'FORWARD_TEKNISI') table-info @elseif($ticket->status == 'PROSES') table-info @elseif($ticket->status == 'SELESAI') table-success @elseif($ticket->status == 'DIBATALKAN') table-danger @endif">
                                        <td>Kode : {{ $ticket->code }}<br> Tanggal : {{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                        <td>{{ $ticket->customer->name ?? '-' }}<br>
                                        Cabang : {{ $ticket->branch->name ?? '-' }}<br>
                                        Paket : {{ $ticket->package->name ?? '-' }}</td>
                                        <td>Keluhan :{{ $ticket->keluhan }}<br> Feedback :{{ $ticket->feedback ?? '-' }}</td>
                                        <td><span
                                                class="badge @if($ticket->status == 'REGISTER') bg-warning text-dark @elseif($ticket->status == 'FORWARD_TEKNISI') bg-info text-dark @elseif($ticket->status == 'PROSES') bg-info text-dark @elseif($ticket->status == 'SELESAI') bg-success @elseif($ticket->status == 'DIBATALKAN') bg-danger @endif">{{ $ticket->status }}</span>
                                                @if($ticket->status == 'SELESAI')
                                                    <br>
                                                    <small class="text-muted">Selesai pada {{ $ticket->updated_at->format('d-m-Y H:i') }}</small>
                                                @endif
                                        </td>
                                        <td>{{ $ticket->teknisi->name ?? '-' }}</td>
                                        <td>
                                            @if($ticket->status != 'SELESAI' && $ticket->status != 'DIBATALKAN')
                                                <form method="POST" action="{{ route('support_ticket.updateStatus', $ticket->id) }}" class="form-batalkan">
                                                    @csrf
                                                    <input type="hidden" name="status" value="DIBATALKAN">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-times"></i> Batalkan
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Data tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-block d-md-none">
                        @forelse($tickets as $ticket)
                            <div class="card mb-3 shadow-sm border-@if($ticket->status == 'REGISTER')warning @elseif($ticket->status == 'FORWARD_TEKNISI')info @elseif($ticket->status == 'PROSES')info @elseif($ticket->status == 'SELESAI')success @elseif($ticket->status == 'DIBATALKAN')danger @endif">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge @if($ticket->status == 'REGISTER') bg-warning text-dark @elseif($ticket->status == 'FORWARD_TEKNISI') bg-info text-dark @elseif($ticket->status == 'PROSES') bg-info text-dark @elseif($ticket->status == 'SELESAI') bg-success @elseif($ticket->status == 'DIBATALKAN') bg-danger @endif">{{ $ticket->status }}</span>
                                        <small class="text-muted">{{ $ticket->created_at->format('d-m-Y H:i') }}</small>
                                    </div>
                                    <h5 class="mb-1">{{ $ticket->code }}</h5>
                                    <div class="mb-1"><i class="fa fa-user me-1"></i> {{ $ticket->customer->name ?? '-' }}</div>
                                    <div class="mb-1"><i class="fa fa-building me-1"></i> {{ $ticket->branch->name ?? '-' }}</div>
                                    <div class="mb-1"><i class="fa fa-box me-1"></i> {{ $ticket->package->name ?? '-' }}</div>
                                    <div class="mb-1"><i class="fa fa-comment me-1"></i> <b>Keluhan:</b> {{ $ticket->keluhan }}</div>
                                    <div class="mb-1"><i class="fa fa-reply me-1"></i> <b>Feedback:</b> {{ $ticket->feedback ?? '-' }}</div>
                                    <div class="mb-1"><i class="fa fa-user-cog me-1"></i> <b>PIC:</b> {{ $ticket->teknisi->name ?? '-' }}</div>
                                    @if($ticket->status == 'SELESAI')
                                        <div class="mb-1"><small class="text-muted">Selesai pada {{ $ticket->updated_at->format('d-m-Y H:i') }}</small></div>
                                    @endif
                                    <div class="mt-2">
                                        @if($ticket->status != 'SELESAI' && $ticket->status != 'DIBATALKAN')
                                            <form method="POST" action="{{ route('support_ticket.updateStatus', $ticket->id) }}" class="form-batalkan d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="DIBATALKAN">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-times"></i> Batalkan</button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">Data tidak tersedia.</div>
                        @endforelse
                        <div class="mt-3">
                            {{ $tickets->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle filter on mobile
            var btn = document.getElementById('toggleFilterMobile');
            var filter = document.getElementById('filterFormMobile');
            if(btn && filter) {
                btn.addEventListener('click', function() {
                    filter.style.display = filter.style.display === 'none' ? 'block' : 'none';
                });
            }
            document.querySelectorAll('.form-batalkan').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var statusInput = form.querySelector('input[name="status"]');
                    if (statusInput && statusInput.value === 'DIBATALKAN') {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Batalkan Tiket?',
                            text: 'Yakin ingin membatalkan tiket ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Batalkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection