@extends('layouts.backend')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="min-w-0">
                    <h1 class="fs-3 mb-1">Quản lý sản phẩm</h1>
                    <p class="mb-0 text-muted">Quản lý danh mục, giá bán, tồn kho và trạng thái hiển thị của sản phẩm.</p>
                </div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm sản phẩm</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form class="admin-filter-bar mb-3">
                <div>
                    <label class="form-label small text-muted">Từ khóa</label>
                    <input type="text" name="q" class="form-control" value="{{ $filters['q'] }}"
                        placeholder="Tên, SKU, danh mục...">
                </div>

                <div>
                    <label class="form-label small text-muted">Danh mục</label>
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $categoryValue => $categoryLabel)
                            <option value="{{ $categoryValue }}" @selected($filters['category'] === $categoryValue)>
                                {{ $categoryLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label small text-muted">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="active" @selected($filters['status'] === 'active')>Đang bán</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Ngừng bán</option>
                    </select>
                </div>

                <div>
                    <label class="form-label small text-muted">Tồn kho</label>
                    <select name="stock" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="available" @selected($filters['stock'] === 'available')>Còn hàng</option>
                        <option value="low" @selected($filters['stock'] === 'low')>Sắp hết hàng</option>
                        <option value="out" @selected($filters['stock'] === 'out')>Hết hàng</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            </form>

            <div class="card admin-products-table-card">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover admin-products-table">
                        <thead class="table-light border-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-center">Tồn kho</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="align-middle">
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="admin-product-cell text-decoration-none text-dark">
                                            <img src="{{ asset($product->image ?: 'assets/img/banner_img_01.jpg') }}"
                                                alt="{{ $product->name }}" class="avatar avatar-md rounded">
                                            <span>
                                                <strong>{{ $product->name }}</strong>
                                                <small>{{ $product->sku ?: 'Chưa có SKU' }}</small>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="admin-product-category-cell">
                                            <span>{{ $product->categoryName() }}</span>
                                            <small>{{ $product->categoryDescription() }}</small>
                                        </div>
                                    </td>
                                    <td class="text-end admin-product-price">{{ number_format((float) $product->price) }} VNĐ</td>
                                    <td class="text-center">
                                        <span class="admin-stock-pill {{ $product->stock <= 5 ? 'is-low' : '' }}">
                                            {{ number_format((int) $product->stock) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="admin-status-pill {{ $product->is_active ? 'is-active' : 'is-inactive' }}">
                                            {{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-product-actions">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="admin-table-action" aria-label="Sửa sản phẩm">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-table-action is-danger" aria-label="Xóa sản phẩm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="admin-table-footer">
                    <span>Tổng: {{ $products->total() }} sản phẩm</span>
                    <div class="admin-table-pagination">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
