<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest('placed_at');

        if ($search = trim((string) $request->string('q'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_email', 'like', '%' . $search . '%');
            });
        }

        $status = $request->string('status')->toString();

        if ($status && array_key_exists($status, Order::statuses())) {
            $query->where('status', $status);
        }

        return view('backend.orders.index', [
            'orders' => $query->paginate(12)->withQueryString(),
            'statuses' => Order::statuses(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'status' => array_key_exists($status, Order::statuses()) ? $status : '',
            ],
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);

        return view('backend.orders.show', [
            'order' => $order,
            'statuses' => Order::statuses(),
            'bankTransfer' => config('payment.bank_transfer'),
        ]);
    }

    public function confirmPayment(Order $order)
    {
        if (! $order->isBankTransfer()) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('warning', 'Đơn hàng này không dùng phương thức chuyển khoản.');
        }

        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Thanh toán của đơn hàng đã được xác nhận trước đó.');
        }

        $order->update([
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'paid_at' => now(),
            'payment_confirmed_at' => now(),
            'status' => $order->status === Order::STATUS_PENDING
                ? Order::STATUS_CONFIRMED
                : $order->status,
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Đã xác nhận thanh toán. Đơn hàng được chuyển sang xử lý.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::statuses()))],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        if ($newStatus === $oldStatus) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Trạng thái đơn hàng không thay đổi.');
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {
            $order->load('items');

            if ($oldStatus !== Order::STATUS_CANCELLED && $newStatus === Order::STATUS_CANCELLED) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            if ($oldStatus === Order::STATUS_CANCELLED && $newStatus !== Order::STATUS_CANCELLED) {
                foreach ($order->items as $item) {
                    if (! $item->product_id) {
                        continue;
                    }

                    $product = Product::query()->lockForUpdate()->find($item->product_id);

                    if (! $product || ! $product->is_active || $product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'status' => "Không thể khôi phục đơn vì sản phẩm {$item->product_name} không còn đủ tồn kho.",
                        ]);
                    }

                    $product->decrement('stock', $item->quantity);
                }
            }

            $order->update([
                'status' => $newStatus,
                'delivered_at' => $newStatus === Order::STATUS_DELIVERED ? now() : null,
            ]);
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }
}
