@extends('layouts.app')

@section('title', 'Beranda - Catatan Keuangan')
@section('headerTitle', 'Beranda')
@section('headerSubtitle', 'Ringkasan keuanganmu')

@section('content')

    {{-- Sapaan --}}
    <div class="mb-5">
        <h2 class="text-xl lg:text-2xl font-bold text-gray-800">Hai, {{ $user->name ?? 'User' }}! 👋</h2>
        <p class="text-gray-500 text-sm mt-1">Semangat, sedikit lagi lunas!</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="flex lg:grid lg:grid-cols-4 gap-3 overflow-x-auto pb-2 mb-5 -mx-4 px-4 lg:mx-0 lg:px-0">

        {{-- Total Hutang --}}
        <div class="min-w-[200px] lg:min-w-0 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-4 text-white md-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs opacity-90">Total Hutang</p>
                    <p class="text-xl font-bold mt-1">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                </div>
                <i class="fa-solid fa-hand-holding-dollar opacity-80"></i>
            </div>
            <p class="text-xs opacity-80 mt-3">{{ $jumlahHutangAktif }} hutang aktif</p>
        </div>

        {{-- Target Harian --}}
        <div class="min-w-[200px] lg:min-w-0 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 text-white md-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs opacity-90">Target Harian</p>
                    <p class="text-xl font-bold mt-1">Rp {{ number_format($targetHarian, 0, ',', '.') }}</p>
                </div>
                <i class="fa-solid fa-bullseye opacity-80"></i>
            </div>
            <p class="text-xs opacity-80 mt-3">Untuk semua hutang</p>
        </div>

        {{-- Tabungan --}}
        <div class="min-w-[200px] lg:min-w-0 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 text-white md-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs opacity-90">Tabungan</p>
                    <p class="text-xl font-bold mt-1">Rp {{ number_format($tabungan, 0, ',', '.') }}</p>
                </div>
                <i class="fa-solid fa-piggy-bank opacity-80"></i>
            </div>
            <p class="text-xs opacity-80 mt-3">{{ $tabunganPersen }}% dari total hutang</p>
        </div>

        {{-- Sudah Dibayar --}}
        <div class="min-w-[200px] lg:min-w-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white md-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs opacity-90">Sudah Dibayar</p>
                    <p class="text-xl font-bold mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <i class="fa-solid fa-circle-check opacity-80"></i>
            </div>
            <p class="text-xs opacity-80 mt-3">{{ $progressPersen }}% selesai</p>
        </div>

    </div>

    {{-- Progress Pelunasan --}}
    <div class="bg-white rounded-2xl md-shadow p-5 mb-5">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-bold text-gray-800">Progress Pelunasan</h3>
            <span class="text-sm font-bold text-blue-600">{{ $progressPersen }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full" style="width: {{ $progressPersen }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-2">
            <span>Rp {{ number_format($totalPaid, 0, ',', '.') }} terbayar</span>
            <span>Total: Rp {{ number_format($totalOriginal, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Daftar Hutang Aktif --}}
        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-gray-800">Hutang Aktif</h3>
                <button class="text-sm font-semibold text-blue-600 md-ripple">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>

            <div class="space-y-3">

                @forelse($debts as $debt)
                    @php
                        // Warna badge berdasarkan sisa hari
                        if ($debt->days_left < 0) {
                            $badgeClass = 'bg-red-100 text-red-600';
                            $badgeText = 'Terlambat ' . abs($debt->days_left) . ' hari';
                            $barClass = 'bg-red-500';
                        } elseif ($debt->days_left == 0) {
                            $badgeClass = 'bg-red-100 text-red-600';
                            $badgeText = 'Hari ini!';
                            $barClass = 'bg-red-500';
                        } elseif ($debt->days_left == 1) {
                            $badgeClass = 'bg-red-100 text-red-600';
                            $badgeText = 'Besok!';
                            $barClass = 'bg-red-500';
                        } elseif ($debt->days_left <= 7) {
                            $badgeClass = 'bg-orange-100 text-orange-600';
                            $badgeText = $debt->days_left . ' hari lagi';
                            $barClass = 'bg-orange-500';
                        } else {
                            $badgeClass = 'bg-green-100 text-green-600';
                            $badgeText = $debt->days_left . ' hari lagi';
                            $barClass = 'bg-green-500';
                        }
                    @endphp

                    <div class="bg-white rounded-2xl md-shadow p-4 md-ripple">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                                     style="background-color: {{ $debt->platform_color ?? '#3B82F6' }}20; color: {{ $debt->platform_color ?? '#3B82F6' }}">
                                    <i class="fa-solid {{ $debt->platform_icon ?? 'fa-credit-card' }}"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $debt->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $debt->platform_name ?? 'Tanpa Platform' }} •
                                        {{ \Carbon\Carbon::parse($debt->due_date)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs {{ $badgeClass }} px-2 py-1 rounded-full font-semibold whitespace-nowrap">
                                {{ $badgeText }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                            <div class="{{ $barClass }} h-2 rounded-full" style="width: {{ $debt->progress_percent }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Sisa: <strong class="text-gray-800">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</strong></span>
                            <span class="text-gray-500">Harian: <strong class="text-gray-800">Rp {{ number_format($debt->daily_target, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl md-shadow p-8 text-center">
                        <i class="fa-solid fa-face-smile text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">Belum ada hutang aktif. Mantap! 🎉</p>
                    </div>
                @endforelse

            </div>
        </div>

        {{-- Sidebar Kanan: Pengingat --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl md-shadow p-5">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">🔔 Pengingat</h3>
                <div class="space-y-2">
                    @forelse($reminders as $reminder)
                        <div class="flex gap-3 p-3 bg-red-50 rounded-xl border border-red-100">
                            <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-semibold text-red-700">{{ $reminder->title }}</p>
                                <p class="text-xs text-red-500 mt-0.5">{{ $reminder->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-3">Tidak ada pengingat 🎉</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection