<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        // ...existing code...
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                @if(auth()->user()->role == 'SUPERUSER')
                <!-- Semua Menu Ditampilkan -->
                // ...existing code...
                @elseif(auth()->user()->role == 'OPERATOR')
                <!-- Menu Website -->
                <li class="nav-item">
                    <a href="{{ route('website') }}" class="nav-link">
                        <i class="ri-global-line"></i> Website
                    </a>
                </li>
                <!-- Menu Transaction -->
                <li class="nav-item">
                    <a href="{{ route('transaction') }}" class="nav-link">
                        <i class="ri-exchange-dollar-line"></i> Transaction
                    </a>
                </li>
                <!-- Menu Gudang -->
                <li class="nav-item">
                    <a href="{{ route('gudang') }}" class="nav-link">
                        <i class="ri-store-2-line"></i> Gudang
                    </a>
                </li>
                <!-- Menu Kasir -->
                <li class="nav-item">
                    <a href="{{ route('kasir') }}" class="nav-link">
                        <i class="ri-money-dollar-box-line"></i> Kasir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('omzet-statistic.index') }}">
                        <i class="ri-bar-chart-fill"></i> <span data-key="t-omzet-statistic">Statistik Omzet</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('assets.index') }}">
                        <i class="ri-archive-line"></i> <span data-key="t-assets">Manajemen Asset</span>
                    </a>
                </li>
                @elseif(auth()->user()->role == 'MARKETING')
                <!-- Menu Marketing -->
                <li class="nav-item">
                    <a href="{{ route('marketing') }}" class="nav-link">
                        <i class="ri-line-chart-line"></i> Marketing
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>