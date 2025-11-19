<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Status Pesanan</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Pantau Pesanan Kamu</h2>
            </div>
            <a href="{{ route('customer.orders.index') }}" class="hidden rounded-full border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:bg-gray-100 md:inline-flex">Buat Pesanan Baru</a>
        </div>
    </x-slot>

    @php
        $orderModel = \App\Models\Order::class;

        $statusFlow = [
            $orderModel::STATUS_PENDING_PAYMENT,
            $orderModel::STATUS_AWAITING_CASHIER,
            $orderModel::STATUS_AWAITING_KITCHEN,
            $orderModel::STATUS_IN_KITCHEN,
            $orderModel::STATUS_READY_FOR_PICKUP,
            $orderModel::STATUS_COMPLETED,
        ];

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

        $paymentChannelLabels = [
            $orderModel::CHANNEL_KIOSK => 'Kiosk',
            $orderModel::CHANNEL_CASHIER => 'Kasir',
            $orderModel::CHANNEL_MOBILE => 'Mobile',
        ];

        $statusMessages = [
            $orderModel::STATUS_PENDING_PAYMENT => 'Pesanan menunggu konfirmasi pembayaran.',
            $orderModel::STATUS_AWAITING_CASHIER => 'Silakan menuju kasir dengan nomor antrian Anda.',
            $orderModel::STATUS_AWAITING_KITCHEN => 'Pembayaran terkonfirmasi. Pesanan segera diteruskan ke dapur.',
            $orderModel::STATUS_IN_KITCHEN => 'Tim dapur sedang menyiapkan pesanan kamu.',
            $orderModel::STATUS_READY_FOR_PICKUP => 'Pesanan siap diambil di counter pengambilan.',
            $orderModel::STATUS_COMPLETED => 'Selamat menikmati! Pesanan telah selesai.',
            $orderModel::STATUS_CANCELLED => 'Pesanan dibatalkan. Hubungi kasir jika membutuhkan bantuan.',
        ];

        $activeIndex = array_search($order->status, $statusFlow, true);
        $paymentLabel = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
        $channelLabel = $order->payment_channel ? ($paymentChannelLabels[$order->payment_channel] ?? ucfirst($order->payment_channel)) : 'Belum dipilih';
        $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
        $statusMessage = $statusMessages[$order->status] ?? '';

        $primaryActionLabel = match ($order->status) {
            $orderModel::STATUS_PENDING_PAYMENT, $orderModel::STATUS_AWAITING_CASHIER => 'Pergi ke Kasir',
            $orderModel::STATUS_READY_FOR_PICKUP => 'Ambil Pesanan',
            $orderModel::STATUS_COMPLETED => 'Buat Pesanan Baru',
            default => 'Refresh Status',
        };

        $primaryActionUrl = match ($order->status) {
            $orderModel::STATUS_COMPLETED => route('customer.orders.index'),
            default => url()->current(),
        };

        $primaryActionIsRefresh = ! in_array($order->status, [$orderModel::STATUS_COMPLETED], true);
    @endphp

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white/95 p-8 shadow-2xl ring-1 ring-red-100 backdrop-blur">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nomor Antrian</p>
                        <p class="text-4xl font-black tracking-tight text-gray-900">{{ $order->queue_number }}</p>
                        <p class="mt-1 text-xs text-gray-400">Dibuat {{ $order->created_at->diffForHumans() }} • Terakhir diperbarui {{ $order->updated_at->format('d M H:i') }}</p>
                    </div>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="rounded-2xl border border-red-100 bg-red-50 px-5 py-4 text-center shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Status Pesanan</p>
                            <p class="text-xl font-bold text-red-700">{{ $statusLabel }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 text-center shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Pembayaran</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $paymentLabel }}</p>
                            <p class="text-xs text-gray-500">via {{ $channelLabel }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">Progress Pesanan</p>
                        <button
                            @if($primaryActionIsRefresh)
                                onclick="window.location.reload()"
                            @endif
                            class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:border-red-200 hover:text-red-600"
                        >
                            @if($primaryActionIsRefresh)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M4 14a8 8 0 0113.856-5.856M20 10a8 8 0 01-13.856 5.856" />
                                </svg>
                            @endif
                            {{ $primaryActionLabel }}
                        </button>
                    </div>

                    <div class="mt-4">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="h-1 w-full rounded-full bg-gray-200"></div>
                            </div>
                            <div class="relative flex justify-between">
                                @foreach($statusFlow as $index => $step)
                                    @php
                                        $isActive = $activeIndex !== false && $index <= $activeIndex;
                                        $isComplete = $activeIndex !== false && $index < $activeIndex;
                                    @endphp
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 {{ $isComplete ? 'border-red-500 bg-red-500 text-white' : ($isActive ? 'border-red-500 bg-white text-red-600' : 'border-gray-200 bg-white text-gray-400') }}">
                                        @if($isComplete)
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-6 grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                            @foreach($statusFlow as $index => $step)
                                @php
                                    $isActive = $activeIndex !== false && $index <= $activeIndex;
                                    $stepLabel = $statusLabels[$step];
                                @endphp
                                <div class="rounded-2xl border {{ $isActive ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' }} px-4 py-3 text-center shadow-sm">
                                    <p class="text-xs font-semibold uppercase tracking-widest {{ $isActive ? 'text-red-500' : 'text-gray-400' }}">Langkah {{ $index + 1 }}</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $stepLabel }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 lg:grid-cols-3">
                    <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Instruksi</p>
                        <p class="mt-2 text-sm text-gray-600">{{ $statusMessage }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Total Pembayaran</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Status pembayaran: {{ $paymentLabel }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Butuh bantuan?</p>
                        <p class="mt-2 text-sm text-gray-600">Jika status tidak berubah setelah beberapa menit, hubungi kasir terdekat dan tunjukkan nomor antrian di atas.</p>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700">Ringkasan Item</h3>
                        <span class="text-xs text-gray-400">Total item: {{ $order->items->sum('quantity') }}</span>
                    </div>
                    <div class="mt-3 space-y-3">
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
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 px-6 py-3 text-sm font-semibold uppercase tracking-wider text-gray-600 transition hover:bg-gray-100">
                        Pesan Lagi
                    </a>
                    <button onclick="window.location.reload()" class="mt-3 inline-flex items-center justify-center rounded-full bg-red-500 px-6 py-3 text-sm font-semibold uppercase tracking-wider text-white shadow transition hover:bg-red-600">
                        Refresh Status
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
