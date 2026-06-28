<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $cart = session('cart', []);

        if ($cart === []) {
            return redirect()
                ->route('shop')
                ->with('warning', 'Giỏ hàng đang trống.');
        }

        return view('frontend.checkout.index', [
            'cart' => $cart,
            'user' => $request->user(),
            'paymentMethods' => Order::paymentMethods(),
            'bankTransfer' => config('payment.bank_transfer'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:' . implode(',', array_keys(Order::paymentMethods()))],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session('cart', []);

        if ($cart === []) {
            throw ValidationException::withMessages([
                'cart' => 'Giỏ hàng đang trống.',
            ]);
        }

        $order = DB::transaction(function () use ($cart, $request, $validated) {
            $products = Product::query()
                ->whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $lines = [];

            foreach ($cart as $productId => $item) {
                $product = $products->get((int) $productId);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'Một sản phẩm trong giỏ hàng không còn khả dụng.',
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "Sản phẩm {$product->name} không đủ tồn kho.",
                    ]);
                }

                $lineTotal = (float) $product->price * $item['quantity'];
                $subtotal += $lineTotal;
                $lines[] = [$product, $item['quantity'], $lineTotal];
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => Order::STATUS_PENDING,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === Order::PAYMENT_METHOD_BANK_TRANSFER
                    ? Order::PAYMENT_STATUS_AWAITING_TRANSFER
                    : Order::PAYMENT_STATUS_UNPAID,
                'subtotal' => $subtotal,
                'shipping_fee' => 0,
                'total' => $subtotal,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'note' => $validated['note'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($lines as [$product, $quantity, $lineTotal]) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock', $quantity);
            }

            return $order;
        });

        $request->user()->update([
            'name' => $validated['customer_name'],
            'phone' => $validated['customer_phone'],
            'address' => $validated['shipping_address'],
        ]);

        session()->forget('cart');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $order->isBankTransfer()
                ? 'Đặt hàng thành công. Vui lòng chuyển khoản theo thông tin trên đơn hàng.'
                : 'Đặt hàng thành công. Đơn hàng của bạn đã được ghi nhận.');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'HLA-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
