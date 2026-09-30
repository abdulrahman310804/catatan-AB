@extends('layouts.app')

@section('title', $debt->name)
@section('headerTitle', 'Detail Hutang')
@section('headerSubtitle', $debt->name)

@section('content')

    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="max-w-3xl mx-auto">

        <a href="{{ route('debts.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-sm mb-4 md-ripple">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        {{-- Header Hutang --}}
        <div class="bg-white rounded-2xl md-shadow p-5 mb-5">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-xl"
                         style="background-color: {{ $debt->platform_color ?? '#3B82F6' }}20; color: {{ $debt->platform_color ?? '#3B82F6' }}">
                        <i class="fa-solid {{ $debt->platform_icon ?? 'fa-credit-card' }}"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800 text-lg">{{ $debt->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $debt->platform_name }} • {{ $debt->status === 'paid' ? 'Lunas' : 'Aktif' }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('debts.edit', $debt->id) }}" class="w-9 h-9 rounded-xl hover:bg-gray-100 flex items-center justify-center md-ripple">
                        <i class="fa-solid fa-pen text-blue-500"></i>
                    </a>
                    <form action="{{ route('debts.destroy', $debt->id) }}" method="POST" onsubmit="return confirm('Yakin hapus hutang ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-9 h-9 rounded-xl hover:bg-gray-100 flex items-center justify-center md-ripple">
                            <i class="fa-solid fa-trash text-red-500"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500">Total Hutang</p>
                    <p class="font-bold text-gray-800 mt-1">Rp {{ number_format($debt->total_amount, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl">
                    <p class="text-xs text-green-600">Sudah Dibayar</p>
                    <p class="font-bold text-green-700 mt-1">Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-xl">
                    <p class="text-xs text-red-600">Sisa Hutang</p>
                    <p class="font-bold text-red-700 mt-1">Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <p class="text-xs text-blue-600">Target Harian</p>
                    <p class="font-bold text-blue-700 mt-1">Rp {{ number_format($debt->daily_target, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Progress --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-gray-500">Progress Pelunasan</span>
                    <span class="text-xs font-bold text-blue-600">{{ $debt->progress_percent }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all" style="width: {{ $debt->progress_percent }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>Mulai: {{ \Carbon\Carbon::parse($debt->start_date)->translatedFormat('d M Y') }}</span>
                    <span>Jatuh tempo:
                        <strong class="{{ $debt->days_left < 0 ? 'text-red-600' : ($debt->days_left <= 3 ? 'text-orange-600' : 'text-gray-700') }}">
                            {{ \Carbon\Carbon::parse($debt->due_date)->translatedFormat('d M Y') }}
                            ({{ $debt->days_left >= 0 ? $debt->days_left . ' hari' : 'Terlambat ' . abs($debt->days_left) . ' hari' }})
                        </strong>
                    </span>
                </div>
            </div>

            @if($debt->notes)
                <div class="mt-4 p-3 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Catatan:</p>
                    <p class="text-sm text-gray-700">{{ $debt->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Form Bayar Hutang --}}
        @if($debt->status === 'active')
            <div class="bg-white rounded-2xl md-shadow p-5 mb-5">
                <h3 class="font-bold text-gray-800 mb-4">
                    <i class="fa-solid fa-money-bill-wave text-green-600 mr-2"></i>
                    Bayar Hutang
                </h3>

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('debts.pay', $debt->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Bayar <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                                <input type="number" name="amount" required min="1" max="{{ $debt->remaining_amount }}" step="1000"
                                       value="{{ min(100000, $debt->remaining_amount) }}"
                                       class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maks: Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Bayar <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bayar dari Dompet</label>
                            <select name="wallet_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                                <option value="">-- Tidak dipilih --</option>
                                @foreach($wallets as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }} (Rp {{ number_format($w->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Metode</label>
                            <select name="method" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                                <option value="transfer">Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                        <input type="text" name="notes" placeholder="Opsional..."
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition md-ripple">
                        <i class="fa-solid fa-check mr-2"></i> Catat Pembayaran
                    </button>
                </form>
            </div>
        @endif

        {{-- Riwayat Pembayaran --}}
        <div class="bg-white rounded-2xl md-shadow p-5">
            <h3 class="font-bold text-gray-800 mb-4">
                <i class="fa-solid fa-clock-rotate-left text-gray-500 mr-2"></i>
                Riwayat Pembayaran ({{ $payments->count() }})
            </h3>

            @forelse($payments as $p)
                <div class="flex justify-between items-center p-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($p->payment_date)->translatedFormat('d M Y') }}
                                @if($p->wallet_name) • {{ $p->wallet_name }} @endif
                                @if($p->notes) • {{ $p->notes }} @endif
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 text-sm py-4">Belum ada pembayaran.</p>
            @endforelse
        </div>

    </div>

@endsection