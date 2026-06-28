@extends('layouts.backend')

@php($isCreate = $formMethod === 'POST')

@section('title', $isCreate ? 'Thêm sản phẩm' : 'Cập nhật sản phẩm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h1 class="fs-3 mb-1">{{ $isCreate ? 'Thêm sản phẩm' : 'Cập nhật sản phẩm' }}</h1>
                    <p class="mb-0">Nhập thông tin bán hàng, danh mục và tồn kho của sản phẩm.</p>
                </div>
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Về danh sách sản phẩm</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (!$isCreate)
                            @method($formMethod)
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productName" class="form-label">Tên sản phẩm</label>
                                <input id="productName" type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $product->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productSKU" class="form-label">SKU</label>
                                <input id="productSKU" type="text" name="sku"
                                    class="form-control @error('sku') is-invalid @enderror"
                                    value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productCategory" class="form-label">Danh mục sản phẩm</label>
                                <select id="productCategory" name="category"
                                    class="form-select @error('category') is-invalid @enderror" required>
                                    @foreach ($categoryOptions as $categoryValue => $category)
                                        <option value="{{ $categoryValue }}" @selected(old('category', $product->category) === $categoryValue)>
                                            {{ $category['label'] }} - {{ $category['description'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Chọn đúng nhóm để menu, bộ lọc và sản phẩm liên quan hiển thị chính
                                    xác.</div>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productPrice" class="form-label">Giá bán</label>
                                <input id="productPrice" type="number" name="price"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $product->price) }}" placeholder="0" min="0"
                                    step="1000" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productStock" class="form-label">Số lượng tồn</label>
                                <input id="productStock" type="number" name="stock"
                                    class="form-control @error('stock') is-invalid @enderror"
                                    value="{{ old('stock', $product->stock) }}" placeholder="0" min="0" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="productImage" class="form-label">Ảnh sản phẩm</label>
                            <input id="productImage" type="file" name="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="productImagePath" class="form-label">Đường dẫn ảnh</label>
                            <input id="productImagePath" type="text" name="image_path"
                                class="form-control @error('image_path') is-invalid @enderror"
                                value="{{ old('image_path', $product->image) }}"
                                placeholder="assets/img/banner_img_01.jpg">
                            <div class="form-text">Có thể upload ảnh mới hoặc giữ đường dẫn ảnh hiện tại.</div>
                            @error('image_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Mô tả</label>
                            <textarea id="productDescription" name="description" class="form-control @error('description') is-invalid @enderror"
                                rows="4" placeholder="Mô tả ngắn gọn về nhu cầu sử dụng, tốc độ, vùng phủ sóng...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input id="productStatus" class="form-check-input" type="checkbox" name="is_active"
                                value="1" @checked(old('is_active', $product->is_active))>
                            <label class="form-check-label" for="productStatus">Hiển thị ngoài cửa hàng</label>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit"
                                class="btn btn-primary">{{ $isCreate ? 'Thêm sản phẩm' : 'Lưu thay đổi' }}</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
