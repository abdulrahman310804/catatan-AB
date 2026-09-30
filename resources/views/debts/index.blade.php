@extends('layouts.app')

@section('title', 'Hutang - Catatan Keuangan')
@section('headerTitle', 'Hutang')
@section('headerSubtitle', 'Kelola semua hutangmu')

@section('content')

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="bg-white rounded-2xl md-shadow p-4">
            <p class="text-xs text-gray-500">Total Hutang</p>
            <p class="text-lg font-bold text-gray-800 mt-1">Rp {{ number_format($stats['total'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl md-shadow p-4">
            <p class="text-xs text-gray-500">Sisa Aktif</p>
            <p class="text-lg font-bold text-red-600 mt-1">Rp {{ number_format($stats['sisa'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl md-shadow p-4">
            <p class="text-xs text-gray-500">Sudah Dibayar</p>
            <p class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($stats['terbayar'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl md-shadow p-4">
            <p class="text-xs text-gray-500">Hutang Aktif</p>
            <p class="text-lg font-bold text-blue-600 mt-1">{{ $stats['jumlah_aktif'] }} item</p>
        </div>
    </div>

    {{-- Filter + Tombol Tambah --}}
    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach(['semua' => 'Semua', 'aktif' => 'Aktif', 'overdue' => 'Terlambat', 'lunas' => 'Lunas'] as $key => $label)
                <a href="{{ route('debts.index', ['filter' => $key]) }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap md-ripple
                          {{ $filter === $key ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }} md-shadow">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <a href="{{ route('debts.create') }}" class="bg-blue-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-blue-700 transition md-ripple flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span class="hidden sm:inline">Tambah Hutang</span>
        </a>
    </div>

    {{-- Daftar Hutang --}}
    <div class="space-y-3">
        @forelse($debts as $debt)
            @php
                $daysLeft = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($debt->due_date)->startOfDay(), false);

                if ($debt->status === 'paid') {
                    $badgeClass = 'bg-green-100 text-green-600';
                    $badgeText = 'Lunas ✓';
                    $barClass = 'bg-green-500';
                } elseif ($daysLeft < 0) {
                    $badgeClass = 'bg-red-100 text-red-600';
                    $badgeText = 'Terlambat ' . abs($daysLeft) . ' hari';
                    $barClass = 'bg-red-500';
                } elseif ($daysLeft == 0) {
                    $badgeClass = 'bg-red-100 text-red-600';
                    $badgeText = 'Hari ini!';
                    $barClass = 'bg-red-500';
                } elseif ($daysLeft == 1) {
                    $badgeClass = 'bg-red-100 text-red-600';
                    $badgeText = 'Besok!';
                    $barClass = 'bg-red-500';
                } elseif ($daysLeft <= 7) {
                    $badgeClass = 'bg-orange-100 text-orange-600';
                    $badgeText = $daysLeft . ' hari lagi';
                    $barClass = 'bg-orange-500';
                } else {
                    $badgeClass = 'bg-green-100 text-green-600';
                    $badgeText = $daysLeft . ' hari lagi';
                    $barClass = 'bg-green-500';
                }

                $progressPercent = $debt->total_amount > 0 ? round(($debt->paid_amount / $debt->total_amount) * 100) : 0;
            @endphp

            <div class="bg-white rounded-2xl md-shadow p-4 md-ripple hover:border-blue-300 border border-transparent transition">
                <div class="flex justify-between items-start gap-3 mb-3">
                    <a href="{{ route('debts.show', $debt->id) }}" class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $debt->platform_color ?? '#3B82F6' }}20; color: {{ $debt->platform_color ?? '#3B82F6' }}">
                            <i class="fa-solid {{ $debt->platform_icon ?? 'fa-credit-card' }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $debt->name }}</p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $debt->platform_name ?? 'Tanpa Platform' }} •
                                {{ \Carbon\Carbon::parse($debt->due_date)->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </a>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span class="text-xs {{ $badgeClass }} px-2 py-1 rounded-full font-semibold whitespace-nowrap">
                            {{ $badgeText }}
                        </span>
                        <a href="{{ route('debts.show', $debt->id) }}" class="text-xs text-blue-600 font-semibold">
                            Detail <i class="fa-solid fa-arrow-right ml-0.5"></i>
                        </a>
                    </div>
                </div>

                <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                    <div class="{{ $barClass }} h-2 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                </div>

                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">
                        Terbayar: <strong class="text-green-600">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</strong>
                    </span>
                    <span class="text-gray-500">
                        Sisa: <strong class="text-red-600">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</strong>
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl md-shadow p-10 text-center">
                <i class="fa-solid fa-hand-holding-dollar text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-700 font-semibold">Belum ada hutang</p>
                <p class="text-sm text-gray-500 mt-1 mb-4">Tambah hutang pertamamu untuk mulai tracking!</p>
                <a href="{{ route('debts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Hutang
                </a>
            </div>
        @endforelse
    </div>

@endsection