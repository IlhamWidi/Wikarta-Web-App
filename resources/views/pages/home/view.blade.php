@section('style')
    <style>
        /* Scrollable nav-tabs for mobile, single row, no wrap */
        @media (max-width: 767.98px) {
            .shortcut-scroll-tabs {
                overflow-x: auto;
                overflow-y: hidden;
                white-space: nowrap;
                flex-wrap: nowrap !important;
                -webkit-overflow-scrolling: touch;
                border-bottom: 1px solid #e9ecef;
            }

            .shortcut-scroll-tabs .nav-item {
                display: inline-block;
                float: none;
                white-space: nowrap;
            }

            .shortcut-scroll-tabs .nav-link {
                display: inline-block;
                min-width: 90px;
                font-size: 13px;
                padding: 8px 14px;
            }

            .shortcut-card-row {
                display: flex;
                flex-wrap: wrap;
                margin-left: -8px;
                margin-right: -8px;
            }

            .shortcut-card-col {
                flex: 0 0 50%;
                max-width: 50%;
                padding-left: 8px;
                padding-right: 8px;
                margin-bottom: 12px;
            }

            .shortcut-card-img {
                width: 100%;
                height: 70px;
                object-fit: contain;
                background: #f8f9fa;
            }

            .shortcut-card-icon {
                width: 100%;
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8f9fa;
                color: #16a34a;
            }

            .shortcut-card-icon i {
                font-size: 1.75rem;
            }

            .shortcut-card-title {
                font-size: 0.85rem !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }

        @media (min-width: 768px) {
            .shortcut-scroll-tabs {
                display: flex;
                flex-wrap: nowrap;
                width: 100%;
            }

            .shortcut-scroll-tabs .nav-item {
                flex: 1 1 0;
                text-align: center;
            }

            .shortcut-scroll-tabs .nav-link {
                width: 100%;
                font-size: 15px;
                padding: 12px 0;
            }

            .shortcut-card-row {
                display: flex;
                flex-wrap: wrap;
            }

            .shortcut-card-col {
                flex: 0 0 16.6667%;
                max-width: 16.6667%;
                padding-left: 8px;
                padding-right: 8px;
                margin-bottom: 16px;
            }

            .shortcut-card-img {
                width: 100%;
                height: 100px;
                object-fit: contain;
                background: #f8f9fa;
            }

            .shortcut-card-title {
                font-size: 1rem !important;
            }

            .shortcut-card-icon {
                width: 100%;
                height: 100px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8f9fa;
                color: #16a34a;
            }

            .shortcut-card-icon i {
                font-size: 2.25rem;
            }
        }
    </style>
@endsection
@extends('layouts.main')


@section('content')
    <!-- DASHBOARD ABSENSI MOBILE VIEW ONLY -->
    <div class="d-block d-md-none mb-3">
        <div class="card shadow border-0" style="background: linear-gradient(135deg, #f8fafc 60%, #e0e7ef 100%);">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <img src="{{ asset('assets/images/attendance-mobile.svg') }}" alt="Absensi" style="height:56px;">
                </div>
                <h5 class="fw-bold mb-1" style="color:#2b3a55;">Absensi Kehadiran</h5>
                <div class="mb-2 text-muted" style="font-size:13px;">Pastikan Anda melakukan absen setiap hari kerja.</div>
                <div class="mb-3">
                    @if($attendance && $attendance->in_time)
                        <span class="badge bg-success" id="absen-status">Sudah Absen Masuk ({{ $attendance->in_time }})</span>
                    @else
                        <span class="badge bg-danger" id="absen-status">Belum Absen Masuk</span>
                    @endif
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <form method="POST" action="{{ route('absensi.in') }}" id="formAbsenIn">
                        @csrf
                        <input type="hidden" name="lat" id="absenInLat">
                        <input type="hidden" name="lng" id="absenInLng">
                        <button type="submit" class="btn btn-primary w-100 px-4" @if($attendance && $attendance->in_time)
                        disabled @endif>
                            <i class="fa fa-sign-in-alt me-1"></i> Absen Masuk
                        </button>
                    </form>
                    <form method="POST" action="{{ route('absensi.out') }}" id="formAbsenOut">
                        @csrf
                        <input type="hidden" name="lat" id="absenOutLat">
                        <input type="hidden" name="lng" id="absenOutLng">
                        <button type="submit" class="btn btn-outline-secondary w-100 px-4" @if(!$attendance || !$attendance->in_time || $attendance->out_time) disabled @endif>
                            <i class="fa fa-sign-out-alt me-1"></i> Absen Pulang
                        </button>
                    </form>
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        @if($attendance && $attendance->in_time) Masuk: {{ $attendance->in_time }} @endif
                        @if($attendance && $attendance->out_time) | Pulang: {{ $attendance->out_time }} @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- PANEL 2 START -->
    <div class="row">
        <div class="col-lg-12" id="card-none3">
            <div class="card">

                <div class="card-header pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <li class="bx bx-file"></li>&nbsp;&nbsp;
                            <span class="card-title" style="font-size: 15px;"> Shortcut Navigations</span>
                        </div>
                        <div class="flex-shrink-0">

                        </div>
                    </div>
                </div>

                <div class="card-body collapse show pt-1" id="collapseexample3">

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-success shortcut-scroll-tabs mb-3" role="tablist">
                                @foreach($menus->where('parent_id', null) as $parent)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                                            href="#menu-tab-{{ $parent->id }}" role="tab"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            @if($parent->icon)
                                                <i class="{{ $parent->icon }}"></i>
                                            @endif
                                            {{ $parent->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content text-muted">
                                @foreach($menus->where('parent_id', null) as $parent)
                                    <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="menu-tab-{{ $parent->id }}"
                                        role="tabpanel">
                                        <div class="shortcut-card-row">
                                            @foreach($menus->where('parent_id', $parent->id) as $child)
                                                <div class="shortcut-card-col">
                                                    <div class="card" style="border: 1px solid #f2f2f2;">
                                                        @if($child->photo)
                                                            <img class="card-img-top img-fluid shortcut-card-img"
                                                                src="{{ asset($child->photo) }}" alt="Card image cap">
                                                        @else
                                                            <div class="shortcut-card-icon">
                                                                <i class="{{ $child->icon ?? 'ri-apps-line' }}" aria-hidden="true"></i>
                                                            </div>
                                                        @endif
                                                        <div class="card-body p-2">
                                                            <h4 class="card-title mb-2 shortcut-card-title">{{ $child->name }}</h4>
                                                            <!-- <p class="card-text" style="font-size: 12px;">{{ $child->description }}</p> -->
                                                            <a href="{{ $child->routes ? route($child->routes) : '#' }}"
                                                                class="btn btn-sm btn-success" style="width: 100%;">Entering</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- panel 2 end -->

@endsection

@section('script')
    <script>
        function setLocationAndSubmit(form, prefix) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    form.querySelector('input[name="lat"]').value = position.coords.latitude;
                    form.querySelector('input[name="lng"]').value = position.coords.longitude;
                    form.submit();
                }, function () {
                    alert('Gagal mendapatkan lokasi. Pastikan izin lokasi aktif.');
                });
            } else {
                alert('Geolocation tidak didukung browser ini.');
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            var absenInForm = document.getElementById('formAbsenIn');
            if (absenInForm) {
                absenInForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    setLocationAndSubmit(absenInForm, 'absenIn');
                });
            }
            var absenOutForm = document.getElementById('formAbsenOut');
            if (absenOutForm) {
                absenOutForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    setLocationAndSubmit(absenOutForm, 'absenOut');
                });
            }
        });
    </script>
@endsection