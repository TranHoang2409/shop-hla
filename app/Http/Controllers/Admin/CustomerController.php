<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->latest();

        if ($search = trim((string) $request->string('q'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        return view('backend.customers.index', [
            'customers' => $query->paginate(12)->withQueryString(),
            'filters' => ['q' => $request->string('q')->toString()],
        ]);
    }

    public function show(User $customer)
    {
        abort_unless($customer->role === User::ROLE_CUSTOMER, 404);

        $orders = $customer->orders()
            ->latest('placed_at')
            ->paginate(10);

        $summary = [
            'orders' => $customer->orders()->count(),
            'active_orders' => $customer->orders()
                ->whereNotIn('status', [Order::STATUS_DELIVERED, Order::STATUS_CANCELLED])
                ->count(),
            'delivered_orders' => $customer->orders()
                ->where('status', Order::STATUS_DELIVERED)
                ->count(),
            'total_spent' => (float) $customer->orders()
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->sum('total'),
        ];

        return view('backend.customers.show', [
            'customer' => $customer,
            'orders' => $orders,
            'summary' => $summary,
        ]);
    }
}
