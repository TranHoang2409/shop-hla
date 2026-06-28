@extends('layouts.frontend')

@section('title', 'Danh mục sản phẩm')

@section('content')
    @php
        $priceRanges = [
            ['label' => 'Dưới 1 triệu', 'min' => '', 'max' => '1000000'],
            ['label' => 'Từ 1 triệu đến 3 triệu', 'min' => '1000000', 'max' => '3000000'],
            ['label' => 'Từ 3 triệu đến 5 triệu', 'min' => '3000000', 'max' => '5000000'],
            ['label' => 'Trên 5 triệu', 'min' => '5000000', 'max' => ''],
        ];

        $baseQuery = collect($filters)
            ->reject(fn ($value, $key) => $value === '' || $key === 'category')
            ->all();
    @endphp

    <style>
        .catalog-page {
            background: #ffffff;
        }

        .catalog-breadcrumb {
            display: flex;
            gap: 0.45rem;
            align-items: center;
            color: #64748b;
            font-size: 0.82rem;
        }

        .catalog-breadcrumb a {
            color: #0f172a;
            text-decoration: none;
        }

        .catalog-layout {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            gap: 28px;
            align-items: start;
        }

        .catalog-sidebar {
            display: grid;
            gap: 18px;
        }

        .catalog-filter-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #ffffff;
            overflow: hidden;
        }

        .catalog-filter-title {
            margin: 0;
            padding: 16px 16px 10px;
            color: #1f2a6d;
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .catalog-category-list,
        .catalog-price-list {
            display: grid;
            gap: 0;
            margin: 0;
            padding: 0 0 10px;
            list-style: none;
        }

        .catalog-category-list a,
        .catalog-price-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 42px;
            padding: 8px 16px;
            color: #111827;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .catalog-category-list a:hover,
        .catalog-category-list a.is-active,
        .catalog-price-list a:hover,
        .catalog-price-list a.is-active {
            color: #1f2a6d;
            background: #f4f6fb;
        }

        .catalog-category-list small {
            min-width: 1.55rem;
            height: 1.55rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #eef2ff;
            color: #1f2a6d;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .catalog-price-list a {
            justify-content: flex-start;
            gap: 10px;
            color: #475569;
            font-weight: 500;
        }

        .catalog-price-list span {
            width: 12px;
            height: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: #ffffff;
            flex: 0 0 auto;
        }

        .catalog-price-list a.is-active span {
            border-color: #1f2a6d;
            box-shadow: inset 0 0 0 3px #ffffff;
            background: #1f2a6d;
        }

        .catalog-search-form {
            padding: 0 16px 16px;
            display: grid;
            gap: 10px;
        }

        .catalog-content {
            min-width: 0;
        }

        .catalog-topbar {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 14px;
            margin-bottom: 24px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 8px;
        }

        .catalog-sort-form {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .catalog-sort-form select {
            width: 170px;
            border: 0;
            background-color: transparent;
            color: #111827;
            font-size: 0.86rem;
            box-shadow: none;
        }

        .catalog-products-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 28px 24px;
        }

        .catalog-product-card {
            position: relative;
            min-width: 0;
            background: #ffffff;
        }

        .catalog-product-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 2;
            padding: 4px 8px;
            background: #1f2a6d;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .catalog-product-image {
            width: 100%;
            aspect-ratio: 4 / 3;
            display: block;
            object-fit: contain;
            background: #ffffff;
        }

        .catalog-product-body {
            padding-top: 14px;
        }

        .catalog-product-category {
            color: #64748b;
            font-size: 0.78rem;
            line-height: 1.35;
        }

        .catalog-product-title {
            min-height: 2.5rem;
            margin: 6px 0 4px;
            color: #111827;
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.4;
            text-transform: uppercase;
        }

        .catalog-product-title a {
            color: inherit;
            text-decoration: none;
        }

        .catalog-product-title a:hover {
            color: #1f2a6d;
        }

        .catalog-product-price {
            display: block;
            color: #111827;
            font-size: 0.84rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .catalog-product-contact {
            color: #dc2626;
            font-size: 0.84rem;
            text-decoration: none;
        }

        @media (max-width: 1199.98px) {
            .catalog-products-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .catalog-layout {
                grid-template-columns: 1fr;
            }

            .catalog-sidebar {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .catalog-sidebar,
            .catalog-products-grid {
                grid-template-columns: 1fr;
            }

            .catalog-topbar {
                align-items: stretch;
                flex-direction: column;
            }

            .catalog-sort-form {
                justify-content: space-between;
            }
        }
    </style>

    <section class="catalog-page">
        <div class="container py-4 py-lg-5">
            <nav class="catalog-breadcrumb mb-5">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span>›</span>
                <span>Danh sách sản phẩm</span>
            </nav>

            <div class="catalog-layout">
                <aside class="catalog-sidebar">
                    <div class="catalog-filter-box">
                        <h2 class="catalog-filter-title">Danh mục</h2>
                        <ul class="catalog-category-list">
                            <li>
                                <a href="{{ route('shop', $baseQuery) }}" class="{{ $filters['category'] === '' ? 'is-active' : '' }}">
                                    <span>Tất cả sản phẩm</span>
                                    <small>{{ array_sum($categoryCounts->toArray()) }}</small>
                                </a>
                            </li>
                            @foreach ($categories as $categoryValue => $categoryLabel)
                                <li>
                                    <a href="{{ route('shop', array_merge($baseQuery, ['category' => $categoryValue])) }}"
                                        class="{{ $filters['category'] === $categoryValue ? 'is-active' : '' }}">
                                        <span>{{ $categoryLabel }}</span>
                                        <small>{{ (int) ($categoryCounts[$categoryValue] ?? 0) }}</small>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="catalog-filter-box">
                        <h2 class="catalog-filter-title">Tìm kiếm</h2>
                        <form method="GET" class="catalog-search-form">
                            @if ($filters['category'] !== '')
                                <input type="hidden" name="category" value="{{ $filters['category'] }}">
                            @endif
                            <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control"
                                placeholder="Tên, SKU hoặc mô tả">
                            <select name="stock" class="form-select">
                                <option value="">Tất cả tồn kho</option>
                                <option value="in_stock" @selected($filters['stock'] === 'in_stock')>Còn hàng</option>
                                <option value="out_of_stock" @selected($filters['stock'] === 'out_of_stock')>Hết hàng</option>
                            </select>
                            <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                            <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
                            <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                            <button class="btn btn-success">Lọc sản phẩm</button>
                            @if (array_filter($filters))
                                <a href="{{ route('shop') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                            @endif
                        </form>
                    </div>

                    <div class="catalog-filter-box">
                        <h2 class="catalog-filter-title">Giá sản phẩm</h2>
                        <ul class="catalog-price-list">
                            @foreach ($priceRanges as $range)
                                @php
                                    $isActivePrice = $filters['min_price'] === $range['min'] && $filters['max_price'] === $range['max'];
                                    $priceQuery = collect($filters)
                                        ->reject(fn ($value, $key) => $value === '' || in_array($key, ['min_price', 'max_price'], true))
                                        ->merge([
                                            'min_price' => $range['min'],
                                            'max_price' => $range['max'],
                                        ])
                                        ->reject(fn ($value) => $value === '')
                                        ->all();
                                @endphp
                                <li>
                                    <a href="{{ route('shop', $priceQuery) }}" class="{{ $isActivePrice ? 'is-active' : '' }}">
                                        <span></span>
                                        {{ $range['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <div class="catalog-content">
                    <div class="catalog-topbar">
                        <div class="small text-dark">
                            Hiển thị: {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} / {{ $products->total() }}
                        </div>

                        <form method="GET" class="catalog-sort-form">
                            @foreach ($filters as $filterKey => $filterValue)
                                @if ($filterKey !== 'sort' && $filterValue !== '')
                                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filterValue }}">
                                @endif
                            @endforeach
                            <label class="small text-dark" for="catalogSort">Sắp xếp</label>
                            <select id="catalogSort" name="sort" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option value="">Mới nhất</option>
                                <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Giá tăng dần</option>
                                <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Giá giảm dần</option>
                                <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Tên A-Z</option>
                                <option value="stock_desc" @selected($filters['sort'] === 'stock_desc')>Tồn kho nhiều</option>
                            </select>
                        </form>
                    </div>

                    <div class="catalog-products-grid">
                        @forelse ($products as $product)
                            <article class="catalog-product-card">
                                <span class="catalog-product-badge">{{ $product->stock > 0 ? 'Nổi bật' : 'Hết hàng' }}</span>
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ asset($product->image) }}" class="catalog-product-image"
                                        alt="{{ $product->name }}">
                                </a>
                                <div class="catalog-product-body">
                                    <div class="catalog-product-category">{{ $product->categoryName() }}</div>
                                    <h2 class="catalog-product-title">
                                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                    </h2>
                                    <span class="catalog-product-price">{{ number_format((float) $product->price) }} VNĐ</span>
                                    <a href="{{ route('products.show', $product) }}" class="catalog-product-contact">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="alert alert-warning mb-0">
                                Không tìm thấy sản phẩm phù hợp với bộ lọc hiện tại.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
