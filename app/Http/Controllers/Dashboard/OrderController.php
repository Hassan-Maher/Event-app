<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->get();

        $totalOrders   = $orders->count();
        $acceptedOrders = $orders->where('status', 'accepted')->count();
        $rejectedOrders = $orders->where('status', 'rejected')->count();
        $pendingOrders  = $orders->where('status', 'waiting')->count();

        return view('admin.orders', compact(
            'orders',
            'totalOrders',
            'acceptedOrders',
            'rejectedOrders',
            'pendingOrders'
        ));
    }

    public function show($order_id)
    {
        $order = Order::with(['user' , 'items' , 'items.product' , 'items.package'])->findOrFail($order_id);

        return view('admin.orderShow' , compact('order'));
    }
}
