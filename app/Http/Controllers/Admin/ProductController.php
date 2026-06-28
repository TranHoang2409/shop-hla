<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $category = $request->string('category')->toString();

        $query->search($request->string('q')->toString());
        $query->inCategory($category);

        if (! Product::isValidCategory($category)) {
            $category = '';
        }

        match ($request->string('status')->toString()) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => null,
        };

        match ($request->string('stock')->toString()) {
            'low' => $query->whereBetween('stock', [1, 5]),
            'out' => $query->where('stock', '<=', 0),
            'available' => $query->where('stock', '>', 0),
            default => null,
        };

        return view('backend.products.index', [
            'products' => $query->latest()->paginate(12)->withQueryString(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'category' => $category,
                'status' => $request->string('status')->toString(),
                'stock' => $request->string('stock')->toString(),
            ],
            'categories' => Product::CATEGORIES,
            'categoryOptions' => Product::categoryOptions(),
        ]);
    }

    public function create()
    {
        return view('backend.products.form', [
            'product' => new Product([
                'category' => Product::CATEGORY_ROUTER,
                'is_active' => true,
                'stock' => 0,
            ]),
            'formAction' => route('admin.products.store'),
            'formMethod' => 'POST',
            'categories' => Product::CATEGORIES,
            'categoryOptions' => Product::categoryOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $data['image'] = $this->resolveImagePath($request);

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo.');
    }

    public function edit(Product $product)
    {
        return view('backend.products.form', [
            'product' => $product,
            'formAction' => route('admin.products.update', $product),
            'formMethod' => 'PUT',
            'categories' => Product::CATEGORIES,
            'categoryOptions' => Product::categoryOptions(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product);
        $newImage = $this->resolveImagePath($request, $product->image);

        if ($newImage !== $product->image) {
            $this->deleteManagedImage($product->image);
        }

        $data['image'] = $newImage;
        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);

            return redirect()
                ->route('admin.products.index')
                ->with('warning', 'Sản phẩm đã có đơn hàng nên được chuyển sang trạng thái ngừng bán thay vì xóa.');
        }

        $this->deleteManagedImage($product->image);
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(array_keys(Product::CATEGORIES))],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_path' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['image'], $validated['image_path']);

        return $validated;
    }

    private function resolveImagePath(Request $request, ?string $currentImage = null): string
    {
        if ($request->hasFile('image')) {
            $directory = public_path('uploads/products');
            File::ensureDirectoryExists($directory);

            $filename = now()->format('YmdHis') . '-' . $request->file('image')->hashName();
            $request->file('image')->move($directory, $filename);

            return 'uploads/products/' . $filename;
        }

        if (filled($request->input('image_path'))) {
            return trim((string) $request->input('image_path'));
        }

        return $currentImage ?: 'assets/img/banner_img_01.jpg';
    }

    private function deleteManagedImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'uploads/products/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
