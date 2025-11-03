@extends('adminlte::page')

@section('title', 'قائمة الطلبات')

@section('content_header')
    <h1 class="text-center mb-4" style="font-size: 2rem;">📦  Orders Data</h1>
@endsection

@section('content')
<div class="container">

    {{-- 🔹 الكروت الإحصائية --}}
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h5 class="text-primary"> Total Orders</h5>
                    <h2>{{ $totalOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h5 class="text-success">Success Orders </h5>
                    <h2>{{ $acceptedOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <h5 class="text-warning"> Pending Orders </h5>
                    <h2>{{ $pendingOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <h5 class="text-danger"> Failed Orders </h5>
                    <h2>{{ $rejectedOrders }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 جدول الطلبات --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0">
            <h4 class="mb-0 text-muted">🧾  Orders List</h4>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>👤  Customer Nmae</th>
                        <th>📞 Phone</th>
                        <th>💰  Main Price</th>
                        <th>🎟️ Offer</th>
                        <th>💳  Final Price</th>
                        <th>💵  Payment Method</th>
                        <th>📊 Status</th>
                        <th> Email Of user</th>
                        <th>⚙️ Operations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->customer_phone }}</td>
                            <td>{{ number_format($order->price, 2) }} ج.م</td>
                            <td>
                                @if($order->offer)
                                    {{ number_format($order->offer, 2) }} ج.م
                                @else
                                    <span class="text-muted">لا يوجد</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">{{ number_format($order->final_price, 2) }} ج.م</td>
                            <td>{{ ucfirst($order->payment_method) }}</td>
                            <td> {{ $order->status }}</td>
                            <td> {{ $order->user->email }}</td>
                                    <td>
                                <a href="{{ route('order.show' , $order->id) }}" class="btn btn-outline-primary btn-sm">
                                    عرض التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted py-3">🚫 لا توجد طلبات حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
