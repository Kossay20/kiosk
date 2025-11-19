<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Detail Pesanan</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Ringkasan Order Kamu</h2>
            </div>
            <a href="{{ route('customer.orders.index') }}" class="hidden rounded-full border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:bg-gray-100 md:inline-flex">Kembali ke Menu</a>
        </div>
    </x-slot>

    @php
        $orderModel = \App\Models\Order::class;

        $statusLabels = [
            $orderModel::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            $orderModel::STATUS_AWAITING_CASHIER => 'Menunggu Kasir',
            $orderModel::STATUS_AWAITING_KITCHEN => 'Menunggu Dapur',
            $orderModel::STATUS_IN_KITCHEN => 'Sedang Dimasak',
            $orderModel::STATUS_READY_FOR_PICKUP => 'Siap Diambil',
            $orderModel::STATUS_COMPLETED => 'Selesai',
            $orderModel::STATUS_CANCELLED => 'Dibatalkan',
        ];

        $paymentStatusLabels = [
            $orderModel::PAYMENT_STATUS_PENDING => 'Belum Dibayar',
            $orderModel::PAYMENT_STATUS_PAID => 'Sudah Dibayar',
            $orderModel::PAYMENT_STATUS_FAILED => 'Pembayaran Gagal',
        ];

        $channelLabels = [
            $orderModel::CHANNEL_KIOSK => 'Kiosk',
            $orderModel::CHANNEL_CASHIER => 'Kasir',
            $orderModel::CHANNEL_MOBILE => 'Mobile',
        ];

        $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
        $paymentLabel = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
        $channelLabel = $order->payment_channel ? ($channelLabels[$order->payment_channel] ?? ucfirst($order->payment_channel)) : 'Belum dipilih';
    @endphp

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                <div class="rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nomor Antrian</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $order->queue_number }}</p>
                            <p class="mt-1 text-xs text-gray-400">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="rounded-2xl border border-gray-200 px-4 py-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Status</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $statusLabel }}</p>
                            </div>
                            <div class="rounded-2xl border border-gray-200 px-4 py-3 text-center">
                                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Pembayaran</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $paymentLabel }}</p>
                                <p class="text-xs text-gray-500">via {{ $channelLabel }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Menu</h3>
                    <p class="mt-1 text-sm text-gray-500">Jumlah item dan total harga ditampilkan di bawah ini.</p>

                    <div class="mt-6 space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-start justify-between rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                                <div>
                                    <p class="text-base font-semibold text-gray-900">{{ $item->menu->name }}</p>
                                    <p class="text-xs text-gray-500">Qty {{ $item->quantity }} &bull; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex flex-col gap-4 rounded-2xl bg-red-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600">Total Pembayaran</p>
                            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-sm text-red-500">
                            @if($order->requiresCashier())
                                Tunjukkan nomor ini ke kasir untuk menyelesaikan pembayaran.
                            @elseif($order->status === $orderModel::STATUS_READY_FOR_PICKUP)
                                Pesanan siap diambil. Datang ke counter pengambilan dengan nomor antrian ini.
                            @elseif($order->status === $orderModel::STATUS_COMPLETED)
                                Terima kasih! Pesanan sudah selesai diserahkan.
                            @elseif($order->status === $orderModel::STATUS_CANCELLED)
                                Pesanan dibatalkan. Hubungi kasir bila membutuhkan bantuan.
                            @else
                                Pesanan kamu sedang diproses oleh tim kasir dan dapur.
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white/95 p-6 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat</h3>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Dipesan oleh</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ auth()->user()?->name ?? 'Tamu' }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Diperbarui</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Kasir</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $order->cashier?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('customer.orders.status', $order) }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 px-6 py-3 text-sm font-semibold uppercase tracking-wider text-gray-600 transition hover:bg-gray-100">
                        Lihat Status Terkini
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>




