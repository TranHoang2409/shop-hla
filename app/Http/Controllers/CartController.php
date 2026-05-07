<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Vui lòng đăng nhập!');
        }
        return view('pages/cart');
    }

    public function add($id)
    {
        $product = Product::findOrFail($id);

        $cart = session()->get('capages/cartrt', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1,
            ];
        }

        session()->put('pages/cart', $cart);

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    public function update(Request $request)
    {
        $cart = session()->get('capages/cartrt', []);
        $id = $request->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = max(1, (int) $request->quantity);
            session()->put('pages/cart', $cart);
        }

        return redirect()->back()->with('success', 'Đã cập nhật giỏ hàng!');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('pages/cart', []);
        $id = $request->id;

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('pages/cart', $cart);
        }

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    public function clear()
    {
        session()->forget('cart');

        return redirect()->back()->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}
