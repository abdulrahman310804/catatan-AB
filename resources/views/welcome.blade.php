<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Keuangan</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen font-sans">
    <div class="text-center px-4">
        <h1 class="text-5xl font-extrabold text-gray-800 mb-4">
            Catatan<span class="text-blue-600">Keuangan</span>
        </h1>
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            Kelola hutang, pantau target harian, dan catat tabunganmu dalam satu tempat.
        </p>
        
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Masuk
            </a>
            <a href="{{ route('register') }}" class="bg-white text-blue-600 border border-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                Daftar
            </a>
        </div>
    </div>
</body>
</html>