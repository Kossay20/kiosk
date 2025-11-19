<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Checkout</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Konfirmasi Pesanan</h2>
            </div>
            <div class="hidden text-sm text-gray-500 md:flex md:items-center md:gap-4">
                <span class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-green-500 text-xs font-bold text-white">1</span>
                    Pilih Menu
                </span>
                <span class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">2</span>
                    Checkout
                </span>
                <span class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-bold text-gray-500">3</span>
                    Pembayaran
                </span>
            </div>
        </div>
    </x-slot>

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Menu</h3>
                        <p class="mt-1 text-sm text-gray-500">Pastikan jumlah pesanan dan catatan sudah tepat sebelum memilih metode pembayaran.</p>

                        <div class="mt-6 space-y-4">
                            @foreach($items as $item)
                                <div class="flex items-start justify-between rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm">
                                    <div>
                                        <p class="text-base font-semibold text-gray-900">{{ $item['menu']->name }}</p>
                                        <p class="text-xs text-gray-500">Qty {{ $item['quantity'] }} &bull; Rp {{ number_format($item['menu']->price, 0, ',', '.') }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center justify-between rounded-2xl bg-red-50 px-5 py-3">
                            <span class="text-sm font-medium text-red-600">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-red-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6">
                    <div class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                        <h3 class="text-lg font-semibold text-gray-900">Pilih Metode Pembayaran</h3>
                        <p class="mt-1 text-sm text-gray-500">Pilih salah satu opsi di bawah ini. Anda dapat mengubah metode sebelum konfirmasi.</p>

                        <form action="{{ route('customer.orders.store') }}" method="POST" class="mt-6 space-y-5">
                            @csrf
                            @foreach($items as $item)
                                <input type="hidden" name="items[{{ $item['menu']->id }}][menu_id]" value="{{ $item['menu']->id }}">
                                <input type="hidden" name="items[{{ $item['menu']->id }}][quantity]" value="{{ $item['quantity'] }}">
                            @endforeach

                            <div class="space-y-4">
                                <div class="payment-option group flex cursor-pointer items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 transition hover:border-red-400" data-payment="cashier">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 8h6m-1 13H10a2 2 0 01-2-2v-4h8v4a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-900">Bayar di Kasir</p>
                                        <p class="text-sm text-gray-500">Tunai atau kartu di loket kasir. Order masuk ke kasir dan dapur setelah pembayaran.</p>
                                    </div>
                                </div>

                                <div class="payment-option group flex cursor-pointer items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 transition hover:border-red-400" data-payment="kiosk">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-900">Bayar di Kiosk</p>
                                        <p class="text-sm text-gray-500">Gunakan kartu di kiosk ini, pesanan otomatis ke dapur begitu pembayaran sukses.</p>
                                    </div>
                                </div>

                                <div class="payment-option group flex cursor-pointer items-center gap-4 rounded-2xl border border-gray-200 bg-white p-4 transition hover:border-red-400" data-payment="mobile">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-semibold text-gray-900">Mobile Payment</p>
                                        <p class="text-sm text-gray-500">Scan QRIS dari aplikasi favorit. Pesanan langsung diteruskan ke dapur.</p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="payment_channel" id="payment_channel" value="">

                            <div class="space-y-3">
                                <button type="submit" id="confirm-order-btn" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-red-500 px-6 py-3 text-sm font-semibold uppercase tracking-wider text-white shadow transition hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-200" disabled>
                                    Konfirmasi &amp; Buat Order
                                </button>
                                <a href="{{ route('customer.orders.index') }}" class="inline-flex w-full items-center justify-center rounded-full border border-gray-300 px-6 py-3 text-sm font-semibold uppercase tracking-wider text-gray-600 transition hover:bg-gray-100">
                                    Kembali Pilih Menu
                                </a>
                                <p class="text-center text-xs text-gray-400">Dengan menekan konfirmasi, Anda menyetujui total pembayaran di atas.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentChannelInput = document.getElementById('payment_channel');
            const confirmOrderBtn = document.getElementById('confirm-order-btn');

            paymentOptions.forEach((option) => {
                option.addEventListener('click', function () {
                    paymentOptions.forEach((opt) => {
                        opt.classList.remove('border-red-400', 'ring-2', 'ring-red-200');
                    });

                    this.classList.add('border-red-400', 'ring-2', 'ring-red-200');
                    paymentChannelInput.value = this.getAttribute('data-payment');
                    confirmOrderBtn.disabled = false;
                });
            });

            const checkoutForm = document.querySelector('form');
            checkoutForm.addEventListener('submit', function (event) {
                if (!paymentChannelInput.value) {
                    event.preventDefault();
                    alert('Silakan pilih metode pembayaran terlebih dahulu.');
                    return;
                }

                if (paymentChannelInput.value !== 'cashier') {
                    const proceed = confirm('Pastikan Anda siap menyelesaikan pembayaran sekarang. Lanjutkan?');
                    if (!proceed) {
                        event.preventDefault();
                    }
                }
            });
        });
    </script>
</x-app-layout>
