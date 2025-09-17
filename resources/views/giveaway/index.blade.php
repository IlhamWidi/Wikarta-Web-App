@extends('layouts.main')
@section('title') Give Away @endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Daftar Give Away</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex gap-2">
                            {{-- Filter Bulan dan Tahun --}}
                            <select id="bulan" class="form-select" style="width: 130px;">
                                @php
                                    $bulan_sekarang = request('bulan', date('m'));
                                    $bulan = [
                                        '01' => 'Januari',
                                        '02' => 'Februari',
                                        '03' => 'Maret',
                                        '04' => 'April',
                                        '05' => 'Mei',
                                        '06' => 'Juni',
                                        '07' => 'Juli',
                                        '08' => 'Agustus',
                                        '09' => 'September',
                                        '10' => 'Oktober',
                                        '11' => 'November',
                                        '12' => 'Desember'
                                    ];
                                @endphp
                                @foreach($bulan as $key => $nama)
                                    <option value="{{ $key }}" {{ $bulan_sekarang == $key ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="tahun" class="form-select" style="width: 100px;">
                                @php
                                    $tahun_sekarang = request('tahun', date('Y'));
                                    $tahun_awal = 2020;
                                    $tahun_akhir = date('Y');
                                @endphp
                                @for($tahun = $tahun_akhir; $tahun >= $tahun_awal; $tahun--)
                                    <option value="{{ $tahun }}" {{ $tahun_sekarang == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <a href="{{ route('giveaway.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-bottom me-1"></i> Tambah
                        </a>
                    </div>
                    <div class="clearfix"></div>

                    <!-- Desktop View -->
                    <div class="table-responsive d-none d-md-block">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Give Away</th>
                                    <th>Operator</th>
                                    <th>Kurir</th>
                                    <th>Status</th>
                                    <th>Foto Penerima</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($giveaways as $item)
                                    <tr>
                                        <td>{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $item->pelanggan->name ?? '-' }}</td>
                                        <td>{{ $item->give_away ?? '-' }}</td>
                                        <td>{{ $item->operator->name }}</td>
                                        <td>{{ $item->kurir->name ?? '-' }}</td>
                                        <td>
                                            <div class="mt-2">
                                                @if($item->status == 'REGISTER')
                                                    <span class="badge bg-warning">{{ $item->status }}</span>
                                                @elseif($item->status == 'SEDANG_DIPROSES')
                                                    <span class="badge bg-info">{{ $item->status }}</span>
                                                @elseif($item->status == 'SELESAI')
                                                    <span class="badge bg-success">{{ $item->status }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->recipient_photo)
                                                <img src="{{ asset($item->recipient_photo) }}" alt="Foto Penerima"
                                                    class="img-thumbnail" width="100">
                                            @else
                                                <span class="badge bg-secondary">Tidak ada foto</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status == 'REGISTER')
                                                <div class="hstack gap-3 fs-15">
                                                    <a href="{{ route('giveaway.edit', $item->id) }}" class="link-primary">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                    <form action="{{ route('giveaway.destroy', $item->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="link-danger bg-transparent border-0"
                                                            onclick="return confirm('Yakin ingin menghapus?')">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="d-md-none">
                        @if($giveaways->isEmpty())
                            <div class="text-center py-4">
                                <h4>Data Tidak Tersedia</h4>
                                <p class="text-muted">Belum ada data give away yang tersedia.</p>
                            </div>
                        @else
                            @foreach($giveaways as $item)
                                <div class="card border mb-2">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $item->pelanggan->name ?? '-' }}</h6>
                                                <p class="text-muted mb-0">
                                                    {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                @if($item->status == 'REGISTER')
                                                    <span class="badge bg-warning">{{ $item->status }}</span>
                                                @elseif($item->status == 'SEDANG_DIPROSES')
                                                    <span class="badge bg-info">{{ $item->status }}</span>
                                                @elseif($item->status == 'SELESAI')
                                                    <span class="badge bg-success">{{ $item->status }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="pt-2">
                                            <p class="text-muted mb-2">Give Away: {{ $item->give_away ?? '-' }}</p>
                                            <p class="text-muted mb-2">Operator: {{ $item->operator->name }}</p>
                                            <p class="text-muted mb-2">Kurir: {{ $item->kurir->name ?? '-' }}</p>
                                            @if($item->status == 'REGISTER')
                                                <div class="hstack gap-2">
                                                    <a href="{{ route('giveaway.edit', $item->id) }}" class="btn btn-sm btn-info">
                                                        <i class="ri-pencil-line"></i> Edit
                                                    </a>
                                                    <form action="{{ route('giveaway.destroy', $item->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Yakin ingin menghapus?')">
                                                            <i class="ri-delete-bin-line"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable
            $('#datatable').DataTable({
                responsive: true,
            });

            // Handler perubahan filter
            $('#bulan, #tahun').change(function () {
                let bulan = $('#bulan').val();
                let tahun = $('#tahun').val();
                window.location.href = "{{ route('giveaway.index') }}?bulan=" + bulan + "&tahun=" + tahun;
            });
        });
    </script>
@endsection