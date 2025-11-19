<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Dapur</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Monitor Pesanan</h2>
            </div>
            <button onclick="location.reload()" class="hidden rounded-full border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-600 transition hover:bg-gray-100 md:inline-flex">
                Refresh
            </button>
        </div>
    </x-slot>

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-3xl bg-white/95 p-5 shadow-xl ring-1 ring-red-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Menunggu Dapur</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $queuedOrders->count() }}</p>
                    <p class="mt-1 text-xs text-gray-500">Order sudah dibayar, siap dimasak.</p>
                </div>
                <div class="rounded-3xl bg-white/95 p-5 shadow-xl ring-1 ring-red-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Sedang Dimasak</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $inProgressOrders->count() }}</p>
                    <p class="mt-1 text-xs text-gray-500">Pantau progres tiap pesanan.</p>
                </div>
                <div class="rounded-3xl bg-white/95 p-5 shadow-xl ring-1 ring-red-100 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Siap Diambil</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $readyOrders->count() }}</p>
                    <p class="mt-1 text-xs text-gray-500">Serahkan ke counter pengambilan.</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <section class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Menunggu Dapur</h3>
                    <p class="mt-1 text-xs text-gray-500">Order lunas dan siap dimasak.</p>
                    <div class="mt-4 space-y-3">
                        @forelse($queuedOrders as $order)
                            <article class="rounded-2xl border border-gray-100 bg-white px-4 py-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-bold text-gray-900">{{ $order->queue_number }}</p>
                                    <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                                </div>
                                <p class="text-xs text-gray-500">Item: {{ $order->items->sum('quantity') }}</p>
                                <p class="text-sm font-semibold text-gray-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </article>
                        @empty
                            <p class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 text-center text-xs text-gray-500">Belum ada order.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Sedang Dimasak</h3>
                    <p class="mt-1 text-xs text-gray-500">Fokus siapkan pesanan ini.</p>
                    <div class="mt-4 space-y-3">
                        @forelse($inProgressOrders as $order)
                            <article class="rounded-2xl border border-gray-100 bg-white px-4 py-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-bold text-orange-600">{{ $order->queue_number }}</p>
                                    <span class="text-xs text-gray-500">Mulai {{ $order->updated_at->format('H:i') }}</span>
                                </div>
                                <p class="text-xs text-gray-500">Item: {{ $order->items->sum('quantity') }}</p>
                                <p class="text-sm font-semibold text-gray-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </article>
                        @empty
                            <p class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 text-center text-xs text-gray-500">Belum ada order.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                    <h3 class="text-lg font-semibold text-gray-900">Siap Diambil</h3>
                    <p class="mt-1 text-xs text-gray-500">Serahkan ke counter pengambilan.</p>
                    <div class="mt-4 space-y-3">
                        @forelse($readyOrders as $order)
                            <article class="rounded-2xl border border-gray-100 bg-white px-4 py-4 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <p class="text-lg font-bold text-green-600">{{ $order->queue_number }}</p>
                                    <span class="text-xs text-gray-500">Selesai {{ $order->updated_at->format('H:i') }}</span>
                                </div>
                                <p class="text-xs text-gray-500">Item: {{ $order->items->sum('quantity') }}</p>
                                <p class="text-sm font-semibold text-gray-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </article>
                        @empty
                            <p class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 text-center text-xs text-gray-500">Belum ada order.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <p class="mt-8 text-center text-xs text-gray-500">Gunakan tombol refresh untuk memutakhirkan daftar atau aktifkan auto-refresh di perangkat dapur.</p>
        </div>
    </div>
</x-app-layout>
