<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'McDonald\'s Kiosk') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, #ffbc0d 0%, #ff8c00 100%);
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const quantityInputs = document.querySelectorAll('.quantity-input');
                const increaseButtons = document.querySelectorAll('.increase-quantity');
                const decreaseButtons = document.querySelectorAll('.decrease-quantity');
                const totalPriceElement = document.getElementById('total-price');
                
                // Fungsi untuk menghitung total harga
                function calculateTotal() {
                    let total = 0;
                    quantityInputs.forEach(input => {
                        const quantity = parseInt(input.value) || 0;
                        const price = parseFloat(input.dataset.price) || 0;
                        total += quantity * price;
                    });
                    totalPriceElement.textContent = total.toLocaleString('id-ID');
                }
                
                // Event listener untuk tombol tambah
                increaseButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const menuId = this.dataset.menuId;
                        const input = document.getElementById(`quantity-${menuId}`);
                        input.value = parseInt(input.value) + 1;
                        calculateTotal();
                    });
                });
                
                // Event listener untuk tombol kurang
                decreaseButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const menuId = this.dataset.menuId;
                        const input = document.getElementById(`quantity-${menuId}`);
                        if (parseInt(input.value) > 0) {
                            input.value = parseInt(input.value) - 1;
                            calculateTotal();
                        }
                    });
                });
                
                // Validasi form sebelum submit
                document.getElementById('order-form').addEventListener('submit', function(e) {
                    let hasItems = false;
                    quantityInputs.forEach(input => {
                        if (parseInt(input.value) > 0) {
                            hasItems = true;
                        }
                    });
                    
                    if (!hasItems) {
                        e.preventDefault();
                        alert('Silakan pilih minimal satu menu untuk dipesan.');
                    }
                });
                
                // Hitung total awal
                calculateTotal();
            });
        </script>
    </body>
</html>