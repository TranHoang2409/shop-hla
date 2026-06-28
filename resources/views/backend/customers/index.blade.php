@extends('layouts.backend')

@section('title', 'Quản lý khách hàng')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="fs-3 mb-1">Khách hàng</h1>
                <p class="mb-0">Tra cứu thông tin khách hàng và lịch sử mua hàng</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form class="row g-2 mb-4">
                <div class="col-md-10">
                    <input type="text" name="q" class="form-control" value="{{ $filters['q'] }}"
                        placeholder="Tìm tên, email hoặc số điện thoại">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">Lọc</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Khách hàng</th>
                            <th>Liên hệ</th>
                            <th class="text-center">Đơn hàng</th>
                            <th class="text-end">Tổng mua</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $customer->name }}</div>
                                    <small class="text-muted">Tham gia {{ $customer->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div>{{ $customer->phone ?: 'Chưa có số điện thoại' }}</div>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </td>
                                <td class="text-center">{{ number_format((int) $customer->orders_count) }}</td>
                                <td class="text-end">{{ number_format((float) ($customer->orders_sum_total ?? 0)) }} VNĐ</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                        class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Không tìm thấy khách hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
@endsection
