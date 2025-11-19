<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Kasir</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Antrian Pesanan</h2>
            </div>
            <div class="text-sm text-gray-500">{{ now()->format('d M Y, H:i') }}</div>
        </div>
    </x-slot>

    @php
        use App\Models\Order;

        $paymentStatusLabels = [
            Order::PAYMENT_STATUS_PENDING => ['label' => 'Belum Dibayar', 'classes' => 'bg-yellow-100 text-yellow-800'],
            Order::PAYMENT_STATUS_PAID => ['label' => 'Sudah Dibayar', 'classes' => 'bg-green-100 text-green-800'],
            Order::PAYMENT_STATUS_FAILED => ['label' => 'Gagal', 'classes' => 'bg-red-100 text-red-800'],
        ];

        $channelLabels = [
            Order::CHANNEL_KIOSK => ['label' => 'Kiosk', 'classes' => 'bg-green-100 text-green-700'],
            Order::CHANNEL_CASHIER => ['label' => 'Kasir', 'classes' => 'bg-blue-100 text-blue-700'],
            Order::CHANNEL_MOBILE => ['label' => 'Mobile', 'classes' => 'bg-purple-100 text-purple-700'],
        ];

        $statusLabels = [
            Order::STATUS_AWAITING_CASHIER => ['label' => 'Menunggu Kasir', 'classes' => 'text-blue-600'],
            Order::STATUS_AWAITING_KITCHEN => ['label' => 'Menunggu Dapur', 'classes' => 'text-indigo-600'],
            Order::STATUS_IN_KITCHEN => ['label' => 'Sedang Dimasak', 'classes' => 'text-orange-600'],
            Order::STATUS_READY_FOR_PICKUP => ['label' => 'Siap Diambil', 'classes' => 'text-green-600'],
            Order::STATUS_COMPLETED => ['label' => 'Selesai', 'classes' => 'text-green-600'],
            Order::STATUS_CANCELLED => ['label' => 'Dibatalkan', 'classes' => 'text-red-600'],
            Order::STATUS_PENDING_PAYMENT => ['label' => 'Menunggu Pembayaran', 'classes' => 'text-yellow-600'],
        ];
    @endphp

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kelola pembayaran dan kirim pesanan ke dapur setelah pembayaran terkonfirmasi.</p>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="rounded-full bg-red-100 px-3 py-1 font-semibold text-red-600">{{ $orders->count() }} pesanan aktif</span>
                    <button onclick="location.reload()" class="inline-flex items-center gap-2 rounded-full border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:bg-gray-100">
                        Refresh
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M4 14a8 8 0 0113.856-5.856M20 10a8 8 0 01-13.856 5.856" />
                        </svg>
                    </button>
                </div>
            </div>

            @if($orders->isEmpty())
                <div class="rounded-3xl border border-dashed border-gray-200 bg-white/70 p-10 text-center shadow-sm">
                    <p class="text-lg font-semibold text-gray-700">Belum ada pesanan aktif.</p>
                    <p class="mt-2 text-sm text-gray-500">Pesanan baru akan muncul di sini setelah pelanggan checkout.</p>
                </div>
            @else
                <div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                    @foreach($orders as $order)
                        @php
                            $paymentInfo = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst($order->payment_status), 'classes' => 'bg-gray-100 text-gray-700'];
                            $channelInfo = $order->payment_channel ? ($channelLabels[$order->payment_channel] ?? ['label' => ucfirst($order->payment_channel), 'classes' => 'bg-gray-100 text-gray-700']) : ['label' => 'Tidak diketahui', 'classes' => 'bg-gray-100 text-gray-700'];
                            $statusInfo = $statusLabels[$order->status] ?? ['label' => ucfirst(str_replace('_', ' ', $order->status)), 'classes' => 'text-gray-600'];
                        @endphp
                        <div class="flex h-full flex-col justify-between rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Nomor Antrian</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $order->queue_number }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $channelInfo['classes'] }}">{{ $channelInfo['label'] }}</span>
                                </div>

                                <div class="rounded-2xl border border-gray-100 bg-white px-4 py-3 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-600">Total</span>
                                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-gray-500">Order dibuat</span>
                                        <span class="text-gray-700">{{ $order->created_at->format('d/m H:i') }}</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $paymentInfo['classes'] }}">{{ $paymentInfo['label'] }}</span>
                                    <span class="text-sm font-semibold {{ $statusInfo['classes'] }}">{{ $statusInfo['label'] }}</span>
                                </div>

                                <div class="rounded-2xl border border-gray-100 bg-white px-4 py-3 text-xs text-gray-500">
                                    <p>{{ $order->items->sum('quantity') }} item &bull; Kasir: {{ $order->cashier?->name ?? 'Belum diproses' }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-col gap-2">
                                <a href="{{ route('cashier.orders.edit', $order) }}" class="inline-flex items-center justify-center rounded-full bg-red-500 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-white shadow transition hover:bg-red-600">
                                    Proses Pembayaran / Status
                                </a>
                                <a href="{{ route('cashier.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 px-5 py-3 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:bg-gray-100">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>



