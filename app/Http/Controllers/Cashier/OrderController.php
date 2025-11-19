<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::whereNotIn('status', [
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])
            ->orderBy('created_at')
            ->get();

        return view('cashier.order.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        $order->load('items.menu', 'payment');

        return view('cashier.order.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Order::STATUS_AWAITING_CASHIER,
                Order::STATUS_AWAITING_KITCHEN,
                Order::STATUS_IN_KITCHEN,
                Order::STATUS_READY_FOR_PICKUP,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])],
            'payment_method' => ['nullable', Rule::in(Payment::methods())],
        ]);

        if (
            $order->payment_status !== Order::PAYMENT_STATUS_PAID
            && !in_array($validated['status'], [Order::STATUS_AWAITING_CASHIER, Order::STATUS_CANCELLED], true)
            && empty($validated['payment_method'])
        ) {
            throw ValidationException::withMessages([
                'status' => 'Pesanan harus ditandai lunas sebelum dikirim ke dapur.',
            ]);
        }

        DB::transaction(function () use ($order, $validated) {
            $order->status = $validated['status'];
            $order->cashier_id = auth()->id();

            if (!empty($validated['payment_method'])) {
                $order->payment_status = Order::PAYMENT_STATUS_PAID;
                $order->payment_channel = Order::CHANNEL_CASHIER;

                $order->payment()->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'method' => $validated['payment_method'],
                        'status' => Payment::STATUS_PAID,
                        'amount' => $order->total_price,
                    ]
                );
            }

            if ($order->payment_status === Order::PAYMENT_STATUS_PAID
                && $order->status === Order::STATUS_AWAITING_CASHIER) {
                $order->status = Order::STATUS_AWAITING_KITCHEN;
            }

            $order->save();
        });

        return redirect()->route('cashier.orders.index')->with('success', 'Order berhasil diperbarui');
    }

    public function show(Order $order)
    {
        $order->load('items.menu', 'cashier', 'payment');

        return view('cashier.order.show', compact('order'));
    }
}
