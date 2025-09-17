@extends('layouts.main')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 text-white"><i class="bx bx-user me-2"></i>Detail Absensi: {{ $user->name }}</h4>
                    <a href="{{ route('attendance_monitor.index') }}" class="btn btn-light"><i
                            class="fa fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Absensi</label>
                            <input type="date" name="date" value="{{ $date }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </form>
                    <div class="mb-4">
                        <div id="attendanceMap" style="height: 350px; width: 100%;"></div>
                    </div>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Absen Masuk</th>
                                    <th>Koordinat Masuk</th>
                                    <th>Absen Pulang</th>
                                    <th>Koordinat Pulang</th>
                                    <th>Jam Kerja</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                    <tr>
                                        <td>{{ $att->date }}</td>
                                        <td>{{ $att->in_time ?? '-' }}</td>
                                        <td>@if($att->in_lat && $att->in_lng) {{ $att->in_lat }}, {{ $att->in_lng }} @else -
                                        @endif</td>
                                        <td>{{ $att->out_time ?? '-' }}</td>
                                        <td>@if($att->out_lat && $att->out_lng) {{ $att->out_lat }}, {{ $att->out_lng }} @else -
                                        @endif</td>
                                        <td>
                                            @if($att->in_time && $att->out_time)
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-block d-md-none">
                        @forelse($attendances as $att)
                            <div class="card mb-2 shadow-sm border-info">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold">{{ $att->date }}</span>
                                        <span class="badge bg-primary">{{ $user->name }}</span>
                                    </div>
                                    <div class="mb-1"><b>Absen Masuk:</b> {{ $att->in_time ?? '-' }}</div>
                                    <div class="mb-1"><b>Koordinat Masuk:</b> @if($att->in_lat && $att->in_lng)
                                    {{ $att->in_lat }}, {{ $att->in_lng }} @else - @endif
                                    </div>
                                    <div class="mb-1"><b>Absen Pulang:</b> {{ $att->out_time ?? '-' }}</div>
                                    <div class="mb-1"><b>Koordinat Pulang:</b> @if($att->out_lat && $att->out_lng)
                                    {{ $att->out_lat }}, {{ $att->out_lng }} @else - @endif
                                    </div>
                                    <div class="mb-1"><b>Jam Kerja:</b>
                                        @if($att->in_time && $att->out_time)
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
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">Tidak ada data tersedia.</div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $attendances->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('attendanceMap').setView([-2.5489, 118.0149], 5); // Center Indonesia
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap'
            }).addTo(map);
            var bounds = [];
            <?php foreach ($attendances as $att): ?>
            @if($att->in_lat && $att->in_lng)
                var markerIn = L.marker([{{ $att->in_lat }}, {{ $att->in_lng }}], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
                        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup('Absen Masuk: {{ $att->date }} {{ $att->in_time ?? '' }}');
                bounds.push([{{ $att->in_lat }}, {{ $att->in_lng }}]);
            @endif
                @if($att->out_lat && $att->out_lng)
                    var markerOut = L.marker([{{ $att->out_lat }}, {{ $att->out_lng }}], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map).bindPopup('Absen Pulang: {{ $att->date }} {{ $att->out_time ?? '' }}');
                    bounds.push([{{ $att->out_lat }}, {{ $att->out_lng }}]);
                @endif
            <?php endforeach; ?>
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        });
    </script>
@endsection