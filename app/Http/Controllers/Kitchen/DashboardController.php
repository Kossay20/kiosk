<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $queuedOrders = Order::with('items')
            ->where('status', Order::STATUS_AWAITING_KITCHEN)
            ->orderBy('created_at')
            ->get();

        $inProgressOrders = Order::with('items')
            ->where('status', Order::STATUS_IN_KITCHEN)
            ->orderBy('updated_at')
            ->get();

        $readyOrders = Order::with('items')
            ->where('status', Order::STATUS_READY_FOR_PICKUP)
            ->orderBy('updated_at')
            ->get();

        return view('kitchen.dashboard', compact('queuedOrders', 'inProgressOrders', 'readyOrders'));
    }
}
