<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Kasir</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Detail Order {{ $order->queue_number }}</h2>
            </div>
            <a href="{{ route('cashier.orders.index') }}" class="hidden rounded-full border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:bg-gray-100 md:inline-flex">Kembali</a>
        </div>
    </x-slot>

    @php
        use App\Models\Order;
        use App\Models\Payment;

        $statusLabels = [
            Order::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            Order::STATUS_AWAITING_CASHIER => 'Menunggu Kasir',
            Order::STATUS_AWAITING_KITCHEN => 'Menunggu Dapur',
            Order::STATUS_IN_KITCHEN => 'Sedang Dimasak',
            Order::STATUS_READY_FOR_PICKUP => 'Siap Diambil',
            Order::STATUS_COMPLETED => 'Selesai',
            Order::STATUS_CANCELLED => 'Dibatalkan',
        ];

        $paymentStatusLabels = [
            Order::PAYMENT_STATUS_PENDING => ['label' => 'Belum Dibayar', 'classes' => 'bg-yellow-100 text-yellow-800'],
            Order::PAYMENT_STATUS_PAID => ['label' => 'Sudah Dibayar', 'classes' => 'bg-green-100 text-green-800'],
            Order::PAYMENT_STATUS_FAILED => ['label' => 'Pembayaran Gagal', 'classes' => 'bg-red-100 text-red-800'],
        ];

        $paymentMethodLabels = [
            Payment::METHOD_CASH => 'Cash',
            Payment::METHOD_CARD => 'Kartu (EDC)',
            Payment::METHOD_QRIS => 'QRIS',
        ];

        $statusInfo = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
        $paymentInfo = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst($order->payment_status), 'classes' => 'bg-gray-100 text-gray-700'];
        $paymentMethod = $order->payment?->method ? ($paymentMethodLabels[$order->payment->method] ?? ucfirst($order->payment->method)) : '-';
    @endphp

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div class="rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nomor Antrian</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $order->queue_number }}</p>
                            <p class="mt-1 text-xs text-gray-400">Masuk: {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $paymentInfo['classes'] }}">{{ $paymentInfo['label'] }}</span>
                            <span class="rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">Status: {{ $statusInfo }}</span>
                            <span class="rounded-full border border-gray-200 px-3 py-1 text-xs text-gray-500">Kasir: {{ $order->cashier?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Item Pesanan</h3>
                    <div class="mt-4 space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->menu->name }}</p>
                                    <p class="text-xs text-gray-500">Qty {{ $item->quantity }} &bull; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-between rounded-2xl bg-red-50 px-4 py-3">
                        <span class="text-sm font-medium text-red-600">Total</span>
                        <span class="text-2xl font-bold text-red-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pembayaran</h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Metode</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $paymentMethod }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Status Pembayaran</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $paymentInfo['label'] }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Terakhir Diperbarui</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('cashier.orders.edit', $order) }}" class="inline-flex items-center justify-center rounded-full bg-red-500 px-6 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow transition hover:bg-red-600">Perbarui Order</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
