<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-red-500">Self Order Kiosk</p>
                <h2 class="text-2xl font-bold leading-tight text-gray-900">Pilih Menu Favoritmu</h2>
            </div>
            <div class="hidden text-sm text-gray-500 md:flex md:items-center md:gap-6">
                <span>1. Pilih menu</span>
                <span>2. Masuk checkout</span>
                <span>3. Selesaikan pembayaran</span>
            </div>
        </div>
    </x-slot>

    <div class="bg-gradient-to-br from-yellow-50 via-white to-red-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form id="order-form" action="{{ route('customer.orders.checkout') }}" method="POST" class="grid grid-cols-1 gap-8 xl:grid-cols-3">
                @csrf
                <div class="space-y-6 xl:col-span-2">
                    <div class="rounded-3xl bg-white/90 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Menu Tersedia</h3>
                            <p class="text-sm text-gray-500">Ketuk + untuk menambahkan ke keranjang</p>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($menus as $menu)
                                <div
                                    class="group relative flex h-full flex-col justify-between rounded-2xl border border-transparent bg-white/80 p-5 shadow-sm transition hover:-translate-y-1 hover:border-red-400 hover:shadow-lg"
                                    data-menu-card
                                    data-menu-id="{{ $menu->id }}"
                                    data-menu-name="{{ $menu->name }}"
                                    data-menu-price="{{ $menu->price }}"
                                >
                                    <div class="space-y-3">
                                        <div class="flex items-start justify-between">
                                            <p class="text-lg font-semibold text-gray-900">{{ $menu->name }}</p>
                                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase text-red-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $menu->description ?? 'Menu enak untuk menemani harimu.' }}</p>
                                    </div>

                                    <div class="mt-6 flex items-center justify-between">
                                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-xl font-bold text-gray-500 transition hover:border-gray-300 hover:bg-gray-100" data-action="decrease" aria-label="Kurangi {{ $menu->name }}">
                                            &minus;
                                        </button>
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-medium uppercase text-gray-400">Jumlah</span>
                                            <span class="text-2xl font-bold text-gray-900" data-quantity-display>0</span>
                                        </div>
                                        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-red-500 text-xl font-bold text-white shadow transition hover:bg-red-600" data-action="increase" aria-label="Tambah {{ $menu->name }}">
                                            +
                                        </button>
                                    </div>

                                    <input type="hidden" name="items[{{ $menu->id }}][menu_id]" value="{{ $menu->id }}">
                                    <input type="hidden" name="items[{{ $menu->id }}][quantity]" id="quantity-{{ $menu->id }}" value="0">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-6">
                    <div class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pesanan</h3>
                        <p class="mt-1 text-sm text-gray-500">Pesanan akan segera diproses setelah pembayaran dikonfirmasi.</p>

                        <div id="summary-empty-state" class="mt-6 rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
                            Belum ada item di keranjang.
                        </div>

                        <div id="summary-items" class="mt-6 space-y-4 text-sm text-gray-700"></div>

                        <div class="mt-6 flex items-center justify-between rounded-2xl bg-red-50 px-4 py-3">
                            <span class="text-sm font-medium text-red-600">Total sementara</span>
                            <span class="text-2xl font-bold text-red-600">Rp <span id="total-price">0</span></span>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white/95 p-6 shadow-xl ring-1 ring-red-100 backdrop-blur">
                        <h3 class="text-lg font-semibold text-gray-900">Siap Checkout?</h3>
                        <p class="mt-2 text-sm text-gray-500">Tekan tombol di bawah ini untuk melihat ringkasan akhir dan pilih metode pembayaran.</p>
                        <button type="submit" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-red-500 px-6 py-3 text-sm font-semibold uppercase tracking-wider text-white shadow transition hover:bg-red-600 focus:outline-none focus:ring-4 focus:ring-red-200">
                            Lanjut ke Checkout
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                        <p class="mt-3 text-center text-xs text-gray-400">Pastikan jumlah pesanan sudah sesuai sebelum lanjut.</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuCards = document.querySelectorAll('[data-menu-card]');
            const totalPriceElement = document.getElementById('total-price');
            const orderForm = document.getElementById('order-form');
            const summaryItems = document.getElementById('summary-items');
            const summaryEmptyState = document.getElementById('summary-empty-state');

            function updateSummary() {
                let total = 0;
                summaryItems.innerHTML = '';

                menuCards.forEach((card) => {
                    const menuId = card.dataset.menuId;
                    const quantityInput = document.getElementById(`quantity-${menuId}`);
                    const quantityDisplay = card.querySelector('[data-quantity-display]');
                    const quantity = parseInt(quantityInput.value, 10) || 0;
                    const price = parseFloat(card.dataset.menuPrice) || 0;

                    if (quantityDisplay) {
                        quantityDisplay.textContent = quantity;
                    }

                    if (quantity > 0) {
                        total += quantity * price;

                        const row = document.createElement('div');
                        row.className = 'flex items-center justify-between rounded-xl border border-gray-100 bg-white px-4 py-3 shadow-sm';
                        row.innerHTML = `
                            <div>
                                <p class="font-semibold text-gray-900">${card.dataset.menuName}</p>
                                <p class="text-xs text-gray-500">${quantity} x Rp ${price.toLocaleString('id-ID')}</p>
                            </div>
                            <span class="text-sm font-semibold text-gray-800">Rp ${(quantity * price).toLocaleString('id-ID')}</span>
                        `;
                        summaryItems.appendChild(row);
                    }
                });

                totalPriceElement.textContent = total.toLocaleString('id-ID');
                summaryEmptyState.classList.toggle('hidden', summaryItems.children.length > 0);
            }

            menuCards.forEach((card) => {
                const menuId = card.dataset.menuId;
                const quantityInput = document.getElementById(`quantity-${menuId}`);
                const increaseBtn = card.querySelector('[data-action="increase"]');
                const decreaseBtn = card.querySelector('[data-action="decrease"]');

                increaseBtn.addEventListener('click', () => {
                    quantityInput.value = parseInt(quantityInput.value || '0', 10) + 1;
                    updateSummary();
                });

                decreaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(quantityInput.value || '0', 10);
                    if (currentValue > 0) {
                        quantityInput.value = currentValue - 1;
                        updateSummary();
                    }
                });
            });

            orderForm.addEventListener('submit', function (event) {
                let hasItems = false;

                menuCards.forEach((card) => {
                    const menuId = card.dataset.menuId;
                    const quantityInput = document.getElementById(`quantity-${menuId}`);
                    if (parseInt(quantityInput.value || '0', 10) > 0) {
                        hasItems = true;
                    }
                });

                if (!hasItems) {
                    event.preventDefault();
                    alert('Silakan pilih minimal satu menu untuk dipesan.');
                }
            });

            updateSummary();
        });
    </script>
</x-app-layout>
