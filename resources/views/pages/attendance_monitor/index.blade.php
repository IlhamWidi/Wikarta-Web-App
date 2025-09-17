@extends('layouts.main')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 text-white"><i class="bx bx-list-ul me-2"></i>Rekap Absensi Admin Hari Ini</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Nama/No HP</label>
                            <input type="text" name="nama" value="{{ request('nama') }}" class="form-control"
                                placeholder="Nama/No HP">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Absensi</label>
                            <input type="date" name="date" value="{{ $date }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </form>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Absen Masuk</th>
                                    <th>Absen Pulang</th>
                                    <th>Jam Kerja</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $i => $user)
                                    @php $att = $attendances[$user->id] ?? null; @endphp
                                    <tr>
                                        <td>{{ $users->firstItem() + $i }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->role_id ?? '-' }}</td>
                                        <td>{{ $att ? ($att->in_time ?? '-') : '-' }}</td>
                                        <td>{{ $att ? ($att->out_time ?? '-') : '-' }}</td>
                                        <td>
                                            @if($att && $att->in_time && $att->out_time)
                                                @php
                                                    $in = \Carbon\Carbon::parse($att->in_time);
                                                    $out = \Carbon\Carbon::parse($att->out_time);
                                                    $diff = $in->diff($out);
                                                @endphp
                                                {{ $diff->h }} jam {{ $diff->i }} menit
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('attendance_monitor.show', $user->id) }}"
                                                class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-block d-md-none">
                        @forelse($users as $i => $user)
                            @php $att = $attendances[$user->id] ?? null; @endphp
                            <div class="card mb-2 shadow-sm border-info">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">{{ $user->name }}</span>
                                        <span class="badge bg-primary">{{ $user->user_type }}</span>
                                    </div>
                                    <div class="mb-1"><b>Absen Masuk:</b> {{ $att ? ($att->in_time ?? '-') : '-' }}</div>
                                    <div class="mb-1"><b>Absen Pulang:</b> {{ $att ? ($att->out_time ?? '-') : '-' }}</div>
                                    <div class="mb-1"><b>Jam Kerja:</b>
                                        @if($att && $att->in_time && $att->out_time)
                                            @php
                                                $in = \Carbon\Carbon::parse($att->in_time);
                                                $out = \Carbon\Carbon::parse($att->out_time);
                                                $diff = $in->diff($out);
                                            @endphp
                                            {{ $diff->h }} jam {{ $diff->i }} menit
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="mb-1"><b>No.:</b> {{ $users->firstItem() + $i }}</div>
                                    <div class="mt-2">
                                        <a href="{{ route('attendance_monitor.show', $user->id) }}"
                                            class="btn btn-sm btn-info w-100"><i class="fa fa-eye"></i> Detail</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">Tidak ada data tersedia.</div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection