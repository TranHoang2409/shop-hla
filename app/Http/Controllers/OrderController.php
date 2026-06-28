<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return view('frontend.orders.index', [
            'orders' => auth()->user()->orders()->latest('placed_at')->paginate(10),
        ]);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load(['items', 'user']);

        return view('frontend.orders.show', [
            'order' => $order,
            'bankTransfer' => config('payment.bank_transfer'),
        ]);
    }
}
