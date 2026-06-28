<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản trị HLA')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    @yield('extra_css')
</head>

<body class="admin-body admin-template-body">
    @php($adminLogo = asset('assets/img/logo.png'))

    <div id="overlay" class="overlay"></div>

    <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
        <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
            <i class="fas fa-bars"></i>
        </button>

        <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
            <i class="fas fa-bars"></i>
        </button>

        <div class="ms-auto">
            <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
                <li class="dropdown">
                    <a class="position-relative btn-icon btn-sm btn-light btn rounded-circle" data-bs-toggle="dropdown"
                        aria-expanded="false" href="#" role="button">
                        <i class="far fa-bell"></i>
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-2 ms-n2">
                            2
                            <span class="visually-hidden">Thông báo chưa đọc</span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">
                        <ul class="list-unstyled p-0 m-0">
                            <li class="p-3 border-bottom">
                                <div class="d-flex gap-3">
                                    <span class="avatar avatar-sm rounded-circle admin-avatar-circle">B</span>
                                    <div class="flex-grow-1 small">
                                        <p class="mb-0">Báo cáo doanh thu</p>
                                        <p class="mb-1">Kiểm tra doanh thu và sản phẩm bán chạy.</p>
                                        <div class="text-secondary">Vừa xong</div>
                                    </div>
                                </div>
                            </li>
                            <li class="p-3 border-bottom">
                                <div class="d-flex gap-3">
                                    <span class="avatar avatar-sm rounded-circle admin-avatar-circle">Đ</span>
                                    <div class="flex-grow-1 small">
                                        <p class="mb-0">Đơn hàng mới</p>
                                        <p class="mb-1">Theo dõi các đơn đang chờ xác nhận.</p>
                                        <div class="text-secondary">5 phút trước</div>
                                    </div>
                                </div>
                            </li>
                            <li class="px-4 py-3 text-center">
                                <a href="{{ route('admin.reports') }}" class="text-primary">Xem báo cáo</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="ms-3 dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar-sm rounded-circle admin-avatar-circle">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 220px;">
                        <div class="d-flex gap-3 align-items-center border-bottom px-3 py-3">
                            <span class="avatar avatar-md rounded-circle admin-avatar-circle">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <div>
                                <h4 class="mb-0 small fw-semibold">{{ auth()->user()->name }}</h4>
                                <p class="mb-0 small text-secondary">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <div class="p-3 d-flex flex-column gap-1 small lh-lg">
                            <a href="{{ route('admin.dashboard') }}"><span>Tổng quan</span></a>
                            <a href="{{ route('admin.orders.index') }}"><span>Đơn hàng</span></a>
                            <a href="{{ route('admin.customers.index') }}"><span>Khách hàng</span></a>
                            <a href="{{ route('admin.reports') }}"><span>Báo cáo</span></a>
                            <a href="{{ route('home') }}"><span>Cửa hàng</span></a>
                            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <aside id="sidebar" class="sidebar">
        <div class="logo-area">
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-logo d-inline-flex align-items-center">
                <img src="{{ $adminLogo }}" alt="HLA Wifi Shop">
                <span class="logo-text ms-2 brand-copy">
                    <strong>HLA Admin</strong>
                    <small>Wifi Shop</small>
                </span>
            </a>
        </div>

        <ul class="nav flex-column">
            <li class="px-4 py-2"><small class="nav-text">Chính</small></li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Tổng quan</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <i class="fas fa-box-open"></i>
                    <span class="nav-text">Sản phẩm</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}"
                    href="{{ route('admin.products.create') }}">
                    <i class="fas fa-plus"></i>
                    <span class="nav-text">Thêm sản phẩm</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
                    href="{{ route('admin.reports') }}">
                    <i class="fas fa-receipt"></i>
                    <span class="nav-text">Báo cáo</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                    href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="nav-text">Đơn hàng</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                    href="{{ route('admin.customers.index') }}">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Khách hàng</span>
                </a>
            </li>

            <li class="px-4 pt-4 pb-2"><small class="nav-text">Tài khoản</small></li>
            <li>
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-store"></i>
                    <span class="nav-text">Cửa hàng</span>
                </a>
            </li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link nav-link-button w-100 text-start">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text">Đăng xuất</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main id="content" class="content py-10">
        <div class="container-fluid">
            @include('base.flash')
            @yield('content')

            <div class="row">
                <div class="col-12">
                    <footer class="text-center py-2 mt-6 text-secondary">
                        <p class="mb-0">
                            Bản quyền © {{ now()->year }} Trang quản trị HLA.
                            <a href="{{ route('home') }}" class="text-primary">HLA Wifi Shop</a>
                        </p>
                    </footer>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    @yield('extra_js')
</body>

</html>
