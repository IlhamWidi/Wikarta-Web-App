@extends('layouts.main')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fa fa-tools me-2"></i>Daftar Tiket Support yang Ditugaskan</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>No. Telp</th>
                                    <th>Server Info</th>
                                    <th>ODP/OPM/ODC</th>
                                    <th>Keluhan</th>
                                    <th>Foto Keluhan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->code }}</td>
                                        <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                        <td>{{ $ticket->customer->name ?? '-' }}</td>
                                        <td>{{ $ticket->customer->phone_number ?? '-' }}</td>
                                        <td>
                                            <strong>Kode:</strong> {{ $ticket->kode_server }}<br>
                                            <strong>Password:</strong> {{ $ticket->password_server }}<br>
                                            <strong>VLAN:</strong> {{ $ticket->vlan }}
                                        </td>
                                        <td>
                                            <strong>ODP:</strong> {{ $ticket->odp }}<br>
                                            <strong>OPM:</strong> {{ $ticket->opm }}<br>
                                            <strong>ODC:</strong> {{ $ticket->odc }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge @if($ticket->status == 'REGISTER') bg-warning text-dark @elseif($ticket->status == 'FORWARD_TEKNISI') bg-warning text-dark @elseif($ticket->status == 'PROSES') bg-info text-dark @elseif($ticket->status == 'SELESAI') bg-success @elseif($ticket->status == 'DIBATALKAN') bg-danger @endif">{{ $ticket->status }}</span>
                                            <br>
                                            <strong>Keluhan:</strong> {{ $ticket->keluhan }}<br>
                                            @if($ticket->keterangan)
                                                <div class="mt-1"><strong>Feedback:</strong> {{ $ticket->keterangan }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->photo)
                                                <a href="{{ asset('storage/' . $ticket->photo) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $ticket->photo) }}" alt="Foto Keluhan"
                                                        style="max-width:80px;max-height:80px;object-fit:cover;">
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!in_array($ticket->status, ['SELESAI', 'DIBATALKAN']))
                                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#solveModal{{ $ticket->id }}">
                                                    <i class="fa fa-tools"></i> Tindakan
                                                </button>
                                                <!-- Modal -->
                                                <div class="modal fade" id="solveModal{{ $ticket->id }}" tabindex="-1"
                                                    aria-labelledby="solveModalLabel{{ $ticket->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST"
                                                                action="{{ route('resolve_ticket.solve', $ticket->id) }}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="solveModalLabel{{ $ticket->id }}">
                                                                        Solving Tiket</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Tindakan</label>
                                                                        <select name="action" class="form-select action-select"
                                                                            required data-ticket="{{ $ticket->id }}">
                                                                            <option value="">Pilih Tindakan</option>
                                                                            <option value="solved">Dapat Diperbaiki</option>
                                                                            <option value="forward">Diteruskan ke Teknisi Lain
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3 mt-2 d-none admin-user-select"
                                                                        id="adminUserSelect-{{ $ticket->id }}">
                                                                        <label class="form-label">PIC Ditunjuk</label>
                                                                        <select name="admin_id" class="form-select">
                                                                            <option value="">Pilih PIC</option>
                                                                            @foreach($adminUsers as $admin)
                                                                                <option value="{{ $admin->id }}">{{ $admin->name }}
                                                                                    ({{ $admin->phone_number }})</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Feedback</label>
                                                                        <textarea name="feedback" class="form-control"
                                                                            required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Data tidak tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-block d-md-none">
                        @forelse($tickets as $ticket)
                            <div class="card mb-3">
                                <div
                                    class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-ticket-alt me-1"></i> {{ $ticket->code }}</span>
                                    <span
                                        class="badge @if($ticket->status == 'REGISTER') bg-warning text-dark @elseif($ticket->status == 'FORWARD_TEKNISI') bg-warning text-dark @elseif($ticket->status == 'PROSES') bg-info text-dark @elseif($ticket->status == 'SELESAI') bg-success @elseif($ticket->status == 'DIBATALKAN') bg-danger @endif">{{ $ticket->status }}</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2"><strong>Tanggal:</strong> {{ $ticket->created_at->format('d-m-Y H:i') }}
                                    </div>
                                    <div class="mb-2"><strong>Nama:</strong> {{ $ticket->customer->name ?? '-' }}</div>
                                    <div class="mb-2"><strong>No. Telp:</strong> {{ $ticket->customer->phone_number ?? '-' }}
                                    </div>
                                    <div class="mb-2"><strong>Server Info:</strong><br>
                                        <span>Kode: {{ $ticket->kode_server }}</span><br>
                                        <span>Password: {{ $ticket->password_server }}</span><br>
                                        <span>VLAN: {{ $ticket->vlan }}</span>
                                    </div>
                                    <div class="mb-2"><strong>ODP/OPM/ODC:</strong><br>
                                        <span>ODP: {{ $ticket->odp }}</span><br>
                                        <span>OPM: {{ $ticket->opm }}</span><br>
                                        <span>ODC: {{ $ticket->odc }}</span>
                                    </div>
                                    <div class="mb-2"><strong>Keluhan:</strong> {{ $ticket->keluhan }}</div>
                                    @if($ticket->keterangan)
                                        <div class="mb-2"><strong>Feedback:</strong> {{ $ticket->keterangan }}</div>
                                    @endif
                                    <div class="mb-2">
                                        <strong>Foto Keluhan:</strong><br>
                                        @if($ticket->photo)
                                            <a href="{{ asset('storage/' . $ticket->photo) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $ticket->photo) }}" alt="Foto Keluhan"
                                                    style="max-width:100px;max-height:100px;object-fit:cover;">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    @if(!in_array($ticket->status, ['SELESAI', 'DIBATALKAN']))
                                        <button class="btn btn-success btn-sm w-100 mt-2" data-bs-toggle="modal"
                                            data-bs-target="#solveModalMobile{{ $ticket->id }}">
                                            <i class="fa fa-tools"></i> Tindakan
                                        </button>
                                        <!-- Modal Mobile -->
                                        <div class="modal fade" id="solveModalMobile{{ $ticket->id }}" tabindex="-1"
                                            aria-labelledby="solveModalMobileLabel{{ $ticket->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('resolve_ticket.solve', $ticket->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="solveModalMobileLabel{{ $ticket->id }}">
                                                                Solving Tiket</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tindakan</label>
                                                                <select name="action" class="form-select" required>
                                                                    <option value="">Pilih Tindakan</option>
                                                                    <option value="solved">Dapat Diperbaiki</option>
                                                                    <option value="forward">Diteruskan ke Teknisi Lain</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Feedback</label>
                                                                <textarea name="feedback" class="form-control" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">Data tidak tersedia.</div>
                        @endforelse
                        <div class="mt-3">{{ $tickets->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function () {
            $('.action-select').on('change', function () {
                var ticketId = $(this).data('ticket');
                var val = $(this).val();
                var selectDiv = $('#adminUserSelect-' + ticketId);
                if (val === 'forward') {
                    selectDiv.removeClass('d-none');
                } else {
                    selectDiv.addClass('d-none');
                }
            });
        });
    </script>
@endsection