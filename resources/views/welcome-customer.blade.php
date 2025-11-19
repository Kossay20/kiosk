<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to McDonald's Kiosk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffbc0d 0%, #ff8c00 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/2 bg-gradient-to-br from-red-600 to-red-800 p-12 text-white">
                <div class="flex justify-center mb-8">
                    <div class="bg-white p-4 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 8a2 2 0 114 0 2 2 0 01-4 0zm2 6a4 4 0 00-3.446 2h6.892A4 4 0 0010 14z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-center mb-4">McDonald's Kiosk</h1>
                <p class="text-xl text-center mb-8">Order Self-Service</p>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="mr-4 bg-white bg-opacity-20 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p>Pilih menu favorit Anda</p>
                    </div>
                    <div class="flex items-center">
                        <div class="mr-4 bg-white bg-opacity-20 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p>Bayar di kasir</p>
                    </div>
                    <div class="flex items-center">
                        <div class="mr-4 bg-white bg-opacity-20 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p>Nikmati makanan Anda</p>
                    </div>
                </div>
            </div>
            <div class="md:w-1/2 p-12 flex flex-col justify-center">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang!</h2>
                <p class="text-gray-600 mb-8">Silakan mulai pesanan Anda dengan mengklik tombol di bawah ini.</p>
                <div class="mb-8">
                    <a href="{{ route('customer.orders.create.guest') }}" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-full text-center text-xl transition duration-300 transform hover:scale-105">
                        Mulai Pesan Sekarang
                    </a>
                </div>
                <div class="text-center">
                    <p class="text-gray-500 text-sm">Butuh bantuan? Hubungi staff kami</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
