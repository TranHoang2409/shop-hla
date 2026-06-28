<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index()
    {
        return view('frontend.cart.index', [
            'cart' => session('cart', []),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::active()->findOrFail($validated['product_id']);

        if ($product->stock < 1) {
            throw ValidationException::withMessages([
                'cart' => 'Sản phẩm hiện đã hết hàng.',
            ]);
        }

        $cart = session('cart', []);
        $currentQuantity = $cart[$product->id]['quantity'] ?? 0;
        $newQuantity = $currentQuantity + $validated['quantity'];

        if ($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'cart' => 'Số lượng vượt quá tồn kho hiện tại của sản phẩm.',
            ]);
        }

        $cart[$product->id] = [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => $product->image,
            'stock' => $product->stock,
            'quantity' => $newQuantity,
        ];

        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);

        if (! isset($cart[$product->id])) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Sản phẩm không còn trong giỏ hàng.');
        }

        if (! $product->is_active || $validated['quantity'] > $product->stock) {
            throw ValidationException::withMessages([
                'cart' => 'Không thể cập nhật số lượng do tồn kho không đủ hoặc sản phẩm đã ngừng bán.',
            ]);
        }

        $cart[$product->id]['quantity'] = $validated['quantity'];
        $cart[$product->id]['stock'] = $product->stock;
        session()->put('cart', $cart);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Giỏ hàng đã được cập nhật.');
    }

    public function destroy(Product $product)
    {
        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }
}
