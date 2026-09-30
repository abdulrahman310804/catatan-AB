<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Catatan Keuangan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .md-shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .md-ripple { transition: all 0.2s ease; }
        .md-ripple:active { transform: scale(0.97); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed lg:relative inset-y-0 left-0 z-40 w-64 bg-white md-shadow transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        <div class="h-16 flex items-center px-5 border-b border-gray-100">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="ml-3 font-bold text-gray-800">Catatan<span class="text-blue-600">Ku</span></span>
        </div>

        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            <p class="text-xs font-semibold text-gray-400 px-3 pt-3 pb-2 uppercase">Menu Utama</p>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium' }} md-ripple">
                <i class="fa-solid fa-house w-5 text-center"></i>
                <span>Beranda</span>
            </a>

            <a href="{{ route('platforms.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl {{ request()->routeIs('platforms.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium' }} md-ripple">
                <i class="fa-solid fa-credit-card w-5 text-center"></i>
                <span>Platform</span>
            </a>

            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl {{ request()->routeIs('debts.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium' }} md-ripple">
                <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                <span>Hutang</span>
            </a>

            <a href="{{ route('debts.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium md-ripple">
                <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i>
                <span>Bayar Hutang</span>
            </a>

            <p class="text-xs font-semibold text-gray-400 px-3 pt-4 pb-2 uppercase">Keuangan</p>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium md-ripple">
                <i class="fa-solid fa-bullseye w-5 text-center"></i>
                <span>Target Harian</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium md-ripple">
                <i class="fa-solid fa-piggy-bank w-5 text-center"></i>
                <span>Tabungan</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium md-ripple">
                <i class="fa-solid fa-chart-line w-5 text-center"></i>
                <span>Laporan</span>
            </a>

            <p class="text-xs font-semibold text-gray-400 px-3 pt-4 pb-2 uppercase">Lainnya</p>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium md-ripple">
                <i class="fa-solid fa-bell w-5 text-center"></i>
                <span>Pengingat</span>
                @if(($layoutReminderCount ?? 0) > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $layoutReminderCount }}</span>
                @endif
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-blue-600 font-medium md-ripple">
                <i class="fa-solid fa-user w-5 text-center"></i>
                <span>Profil</span>
            </a>
        </nav>

        <div class="p-3 border-t border-gray-100">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-gray-50">
                <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr($layoutUser->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $layoutUser->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $layoutUser->email ?? '-' }}</p>
                </div>
                <i class="fa-solid fa-right-from-bracket text-gray-400 hover:text-red-500 cursor-pointer"></i>
            </div>
        </div>
    </aside>

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="h-16 bg-white md-shadow flex items-center px-4 lg:px-6 gap-3 z-20">
            <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center md-ripple">
                <i class="fa-solid fa-bars text-gray-700"></i>
            </button>
            <div>
                <h1 class="font-bold text-gray-800 text-lg">@yield('headerTitle', 'Beranda')</h1>
                <p class="text-xs text-gray-500">@yield('headerSubtitle', 'Ringkasan keuanganmu')</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center md-ripple relative">
                    <i class="fa-solid fa-bell text-gray-600"></i>
                    @if(($layoutReminderCount ?? 0) > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 lg:p-6 pb-24 lg:pb-6">
            @yield('content')
        </main>

        <!-- BOTTOM NAV (khusus mobile) -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 flex justify-around items-center py-2 z-20">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-500' }} px-3 py-1">
                <i class="fa-solid fa-house text-lg"></i>
                <span class="text-[10px] mt-0.5 font-medium">Beranda</span>
            </a>

            <a href="{{ route('debts.index') }}" class="flex flex-col items-center {{ request()->routeIs('debts.*') ? 'text-blue-600' : 'text-gray-500' }} px-3 py-1">
                <i class="fa-solid fa-hand-holding-dollar text-lg"></i>
                <span class="text-[10px] mt-0.5 font-medium">Hutang</span>
            </a>

            <a href="{{ route('debts.create') }}" class="-mt-6 flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white md-shadow md-ripple">
                <i class="fa-solid fa-plus text-xl"></i>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-500 px-3 py-1">
                <i class="fa-solid fa-piggy-bank text-lg"></i>
                <span class="text-[10px] mt-0.5 font-medium">Tabungan</span>
            </a>

            <a href="#" class="flex flex-col items-center text-gray-500 px-3 py-1">
                <i class="fa-solid fa-user text-lg"></i>
                <span class="text-[10px] mt-0.5 font-medium">Profil</span>
            </a>
        </nav>

    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

</body>
</html>