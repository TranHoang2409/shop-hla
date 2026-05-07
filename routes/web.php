<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;





Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/shop', function () {
    $products = Product::all();
    return view('pages.shop', compact('products'));
})->name('shop');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/product-detail/{id}', function ($id) {
    $product = Product::findOrFail($id);
    return view('pages.product-detail', compact('product'));
})->name('product.detail');

Route::post('/add-to-cart', function (Request $request) {
    $cart = session()->get('cart', []);

    $id = $request->id;

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            'name' => $request->name,
            'price' => $request->price,
            'image' => $request->image,
            'quantity' => 1,
        ];
    }

    session()->put('cart', $cart);

    return redirect()->route('cart')->with('success', 'Đã thêm sản phẩm vào giỏ hàng');
})->name('cart.add');

Route::get('/cart', function () {
    return view('pages.cart');
})->name('cart');

Route::post('/cart/update', function (Request $request) {
    $cart = session()->get('cart', []);

    if (isset($cart[$request->id])) {
        $cart[$request->id]['quantity'] = max(1, $request->quantity);
        session()->put('cart', $cart);
    }

    return redirect()->route('cart');
})->name('cart.update');

Route::post('/cart/remove', function (Request $request) {
    $cart = session()->get('cart', []);

    if (isset($cart[$request->id])) {
        unset($cart[$request->id]);
        session()->put('cart', $cart);
    }

    return redirect()->route('cart');
})->name('cart.remove');

Route::post('/cart/clear', function () {
    session()->forget('cart');

    return redirect()->route('cart');
})->name('cart.clear');


Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/warranty', function () {
    return view('warranty');
})->name('warranty');
