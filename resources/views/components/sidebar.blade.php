<!-- LEFT SIDEBAR START -->
<div class="app-menu navbar-menu" style="margin-top: 65px;">
    <div class="navbar-brand-box">
        <a href="{{ route('home') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-dark.png" alt="" height="45">
            </span>
        </a>
        <a href="{{ route('home') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-light.png" alt="" height="45">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <a class="nav-link menu-link" href="{{ route('home') }}" role="button" aria-expanded="false"
                    aria-controls="sidebarDashboards" style="font-size: 14px;">
                    <i class="bx bx-desktop"></i> <span data-key="t-dashboards">Dashboards</span>
                </a>

                @foreach($menus->where('parent_id', null) as $parent)
                    <li class="nav-item">
                        @if($menus->where('parent_id', $parent->id)->count())
                            <a class="nav-link menu-link" href="#sidebarMenu{{ $parent->id }}" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarMenu{{ $parent->id }}"
                                style="font-size: 14px;">
                                @if($parent->icon)
                                    <i class="{{ $parent->icon }}"></i>
                                @endif
                                <span>{{ $parent->name }}</span>
                                <!-- <i class="bx bx-chevron-down float-end"></i> -->
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarMenu{{ $parent->id }}">
                                <ul class="nav nav-sm flex-column">
                                    @foreach($menus->where('parent_id', $parent->id) as $child)
                                        <li class="nav-item">
                                            <a href="{{ $child->routes ? route($child->routes) : '#' }}" class="nav-link"
                                                style="font-size: 14px;">
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }}"></i>
                                                @endif
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <a class="nav-link menu-link"
                                href="{{ $parent->routes && $parent->routes != '#' ? route($parent->routes) : '#' }}"
                                style="font-size: 14px;">
                                @if($parent->icon)
                                    <i class="{{ $parent->icon }}"></i>
                                @endif
                                <span>{{ $parent->name }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="sidebar-background mb-3"></div>
</div>
<!-- LEFT SIDEBAR END -->