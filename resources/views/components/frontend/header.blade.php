@php
    $cartCount = collect(session('cart', []))->sum('quantity');
    $productCategories = \App\Models\Product::CATEGORIES;
@endphp
<style>
    .topbar-header {
        background: #f7f7f7;
        padding: 8px 0;
        font-size: 14px;
    }

    .language-section a {
        margin-right: 8px;
    }

    .language-section img {
        width: 24px;
        height: 16px;
    }

    .social-section {
        margin-left: auto;
    }

    .social-section ul {
        display: flex;
        gap: 14px;
    }

    .social-section li {
        list-style: none;
    }

    .social-section a,
    .social-section a i {
        color: #333 !important;
        transition: all .3s ease;
    }


    .main-header {
        background: #fff;
        padding: 20px 0;
        border-bottom: 1px solid #eee;
        z-index: 99;
    }

    .sticky-menu {
        position: sticky;
        top: 0;
    }

    .header-main-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
    }

    .logo-section a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #111;
        text-decoration: none;
    }

    .logo-section img {
        height: 65px;
        width: auto;
        object-fit: contain;
        transition: all .3s ease;
    }

    /* Hover nhẹ */
    .logo-section img:hover {
        transform: scale(1.03);
    }

    /* Canh logo đẹp hơn */
    .logo-section a {
        display: flex;
        align-items: center;
    }

    .main-header {
        height: 90px;
        padding: 0;
        display: flex;
        align-items: center;
    }

    .header-main-inner {
        height: 90px;
        display: flex;
        align-items: center;
    }

    /* Logo to */
    .logo-section img {
        height: 80px;
        width: auto;
        object-fit: contain;
    }

    /* Không cho logo kéo giãn menu */
    .logo-section {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        overflow: visible;
    }

    /* Mobile */
    @media (max-width: 991px) {
        .logo-section img {
            height: 50px;
        }
    }


    .brand-mark {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #198754;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .brand-copy strong {
        display: block;
        font-size: 20px;
    }

    .brand-copy small {
        display: block;
        font-size: 12px;
        color: #777;
    }

    .menu-section>ul {
        display: flex;
        align-items: center;
        gap: 28px;
        margin: 0;
        padding: 0;
    }

    .menu-section li {
        list-style: none;
        position: relative;
    }

    .menu-section a {
        color: #222;
        font-weight: 600;
        text-decoration: none;
    }

    .menu-section a.active,
    .menu-section a:hover {
        color: #198754;
    }

    .has-child:hover>.entry-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .entry-menu {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 230px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
        padding: 10px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px);
        transition: .25s;
        z-index: 100;
    }

    .entry-menu li a {
        display: block;
        padding: 10px 18px;
        font-weight: 500;
    }

    .action-header {
        gap: 12px;
    }

    .btn-action-header {
        transition: all .3s ease;
    }

    .btn-action-header:hover {
        color: #198754 !important;
        border-color: #198754;
    }

    .btn-action-header {
        width: 42px;
        height: 42px;
        border: 1px solid #eee;
        background: #fff;
        color: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        text-decoration: none;
    }

    .items-number {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #dc3545;
        color: #fff;
        font-size: 11px;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-section {
        position: relative;
    }

    .form-dropdown {
        position: absolute;
        right: 0;
        top: 100%;
        width: 330px;
        background: #fff;
        padding: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
        display: none;
        z-index: 100;
    }

    .form-dropdown::before {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        top: -10px;
        height: 10px;
    }

    .search-section:hover .form-dropdown,
    .search-section:focus-within .form-dropdown {
        display: block;
    }

    .btn-submit {
        background: #198754;
        color: #fff;
    }

    .policy-bar {
        background: #198754;
        color: #fff;
        padding: 10px 0;
    }

    .policy-list {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        text-align: center;
        gap: 15px;
        font-size: 14px;
    }

    .btn-menu-mobile {
        display: none;
    }

    @media (max-width: 991px) {
        .header-main-inner {
            flex-wrap: wrap;
        }

        .btn-menu-mobile {
            display: block;
            border: none;
            background: transparent;
            font-size: 22px;
        }

        .menu-section {
            width: 100%;
            display: none;
        }

        .menu-container:hover .menu-section {
            display: block;
        }

        .menu-section>ul {
            display: block;
            background: #fff;
            padding: 15px 0;
        }

        .menu-section li a {
            display: block;
            padding: 10px 0;
        }

        .entry-menu {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            padding-left: 15px;
        }

        .policy-list {
            grid-template-columns: 1fr;
        }

        .form-dropdown {
            right: -60px;
            width: 280px;
        }
    }
</style>
<header class="hla-header">
    {{-- TOPBAR --}}
    <div class="topbar-header">
        <div class="container">
            <div class="entry-topbar d-flex justify-content-between align-items-center">
                <div class="social-section">
                    <ul class="list-inline mb-0">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN HEADER --}}
    <div class="main-header sticky-menu">
        <div class="container">
            <div class="header-main-inner">
                <div class="logo-section">
                    <a href="{{ route('home') }}"><img src="{{ asset('assets/img/logo.png') }}" alt="Logo HLA Wifi Shop" /></a>
                </div>

                <div class="menu-container">
                    <button class="btn-menu-mobile" type="button">
                        <i class="fas fa-bars"></i>
                    </button>

                    <nav class="menu-section">
                        <ul>
                            <li>
                                <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    Trang chủ
                                </a>
                            </li>

                            <li>
                                <a class="{{ request()->routeIs('about') ? 'active' : '' }}"
                                    href="{{ route('about') }}">
                                    Về chúng tôi
                                </a>
                            </li>

                            <li class="has-child">
                                <a class="{{ request()->routeIs('shop', 'products.show') ? 'active' : '' }}"
                                    href="{{ route('shop') }}">
                                    Sản phẩm <i class="fas fa-angle-down"></i>
                                </a>

                                <ul class="entry-menu dropdown">
                                    @foreach ($productCategories as $categoryValue => $categoryLabel)
                                        <li><a href="{{ route('shop', ['category' => $categoryValue]) }}">{{ $categoryLabel }}</a></li>
                                    @endforeach
                                </ul>
                            </li>

                            <li>
                                <a class="{{ request()->routeIs('warranty') ? 'active' : '' }}"
                                    href="{{ route('warranty') }}">
                                    Bảo hành
                                </a>
                            </li>

                            <li>
                                <a class="{{ request()->routeIs('contact') ? 'active' : '' }}"
                                    href="{{ route('contact') }}">
                                    Liên hệ
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="action-header d-flex align-items-center">
                    @auth
                        <div class="user-section dropdown">
                            <button class="btn-action-header dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                @if (auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                                @endif

                                <li><a class="dropdown-item" href="{{ route('account.edit') }}">Tài khoản</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item text-danger">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="btn-action-header" href="{{ route('login') }}" title="Tài khoản">
                            <i class="fas fa-user"></i>
                        </a>
                    @endauth

                    <div class="search-section">
                        <a class="btn-action-header btn-search-toggle" href="javascript:;" title="Tìm kiếm">
                            <i class="fas fa-search"></i>
                        </a>

                        <div class="form-dropdown">
                            <form action="{{ route('shop') }}" method="GET">
                                <div class="input-group">
                                    <input name="q" value="{{ request('q') }}" placeholder="Từ khóa tìm kiếm"
                                        type="text" class="form-control">
                                    <button class="btn btn-submit" type="submit">Tìm kiếm</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <a class="btn-mini-cart btn-action-header" href="{{ route('cart.index') }}" title="Giỏ hàng">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="items-number">{{ $cartCount }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- POLICY BAR --}}
    <div class="policy-bar">
        <div class="container">
            <div class="policy-list">
                <div>Miễn phí vận chuyển đơn từ 500.000 VNĐ</div>
                <div>Bảo hành rõ ràng</div>
                <div>Hỗ trợ cấu hình cơ bản</div>
                <div>Giao nhanh nội thành</div>
            </div>
        </div>
    </div>
</header>
