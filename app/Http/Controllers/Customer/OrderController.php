<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $menus = Menu::all();

        return auth()->check()
            ? view('customer.order.index', compact('menus'))
            : view('customer.order.guest-order', compact('menus'));
    }

    public function create()
    {
        return $this->index();
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $items = $this->prepareItems($validated['items']);
        $itemsForView = $items->map(fn ($item) => [
            'menu' => $item['menu'],
            'quantity' => $item['quantity'],
            'total' => $item['line_total'],
        ]);
        $totalPrice = $items->sum('line_total');

        if (auth()->check()) {
            return view('customer.order.checkout', [
                'items' => $itemsForView,
                'totalPrice' => $totalPrice,
            ]);
        }

        return view('customer.order.guest-checkout', [
            'items' => $itemsForView,
            'totalPrice' => $totalPrice,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:0',
            'payment_channel' => ['required', Rule::in(Order::paymentChannels())],
        ]);

        $order = $this->persistOrder($validated['items'], $validated['payment_channel']);

        $route = auth()->check() ? 'customer.orders.status' : 'customer.orders.status.guest';
        $message = $order->requiresCashier()
            ? 'Order berhasil dibuat. Silakan menuju kasir untuk pembayaran.'
            : 'Order berhasil dibuat. Pesanan Anda segera diteruskan ke dapur.';

        return redirect()->route($route, $order)->with('success', $message);
    }

    public function storeGuest(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:0',
            'payment_channel' => ['required', Rule::in(Order::paymentChannels())],
        ]);

        $order = $this->persistOrder($validated['items'], $validated['payment_channel']);

        $message = $order->requiresCashier()
            ? 'Order berhasil dibuat. Silakan menuju kasir untuk pembayaran.'
            : 'Order berhasil dibuat. Pesanan Anda segera diteruskan ke dapur.';

        return redirect()->route('customer.orders.status.guest', $order)->with('success', $message);
    }

    public function show(Order $order)
    {
        $order->load('items.menu');

        return auth()->check()
            ? view('customer.order.show', compact('order'))
            : view('customer.order.guest-show', compact('order'));
    }

    public function status(Order $order)
    {
        return view('customer.order.status', compact('order'));
    }

    private function prepareItems(array $items): Collection
    {
        $filtered = collect($items)->mapWithKeys(function ($item, $key) {
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($quantity <= 0) {
                return [];
            }

            $menuId = (int) ($item['menu_id'] ?? $key);

            return [
                $menuId => [
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                ],
            ];
        });

        if ($filtered->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Silakan pilih minimal satu menu.',
            ]);
        }

        $menus = Menu::whereIn('id', $filtered->keys())->get()->keyBy('id');

        return $filtered->map(function ($item, $menuId) use ($menus) {
            $menu = $menus->get($menuId);

            if (!$menu) {
                throw ValidationException::withMessages([
                    'items' => 'Menu tidak ditemukan.',
                ]);
            }

            $quantity = $item['quantity'];

            return [
                'menu' => $menu,
                'quantity' => $quantity,
                'line_total' => $menu->price * $quantity,
            ];
        });
    }

    private function persistOrder(array $items, string $paymentChannel): Order
    {
        $preparedItems = $this->prepareItems($items);

        return DB::transaction(function () use ($preparedItems, $paymentChannel) {
            $totalPrice = $preparedItems->sum('line_total');

            $lastOrderId = Order::lockForUpdate()->latest('id')->value('id') ?? 0;
            $queueNumber = 'ORD-' . str_pad($lastOrderId + 1, 3, '0', STR_PAD_LEFT);

            $status = $paymentChannel === Order::CHANNEL_CASHIER
                ? Order::STATUS_AWAITING_CASHIER
                : Order::STATUS_AWAITING_KITCHEN;

            $paymentStatus = $paymentChannel === Order::CHANNEL_CASHIER
                ? Order::PAYMENT_STATUS_PENDING
                : Order::PAYMENT_STATUS_PAID;

            $order = Order::create([
                'queue_number' => $queueNumber,
                'total_price' => $totalPrice,
                'status' => $status,
                'ordering_channel' => Order::CHANNEL_KIOSK,
                'payment_channel' => $paymentChannel,
                'payment_status' => $paymentStatus,
            ]);

            $preparedItems->each(function ($item) use ($order) {
                $order->items()->create([
                    'menu_id' => $item['menu']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['menu']->price,
                ]);
            });

            $paymentAttributes = [
                'status' => $paymentStatus === Order::PAYMENT_STATUS_PAID
                    ? Payment::STATUS_PAID
                    : Payment::STATUS_UNPAID,
                'amount' => $totalPrice,
                'method' => null,
            ];

            if ($paymentChannel === Order::CHANNEL_KIOSK) {
                $paymentAttributes['method'] = Payment::METHOD_CARD;
            } elseif ($paymentChannel === Order::CHANNEL_MOBILE) {
                $paymentAttributes['method'] = Payment::METHOD_QRIS;
            }

            $order->payment()->create($paymentAttributes);

            return $order;
        });
    }
}
