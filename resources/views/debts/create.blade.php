@extends('layouts.app')

@section('title', 'Tambah Hutang')
@section('headerTitle', 'Tambah Hutang')
@section('headerSubtitle', 'Catat hutang baru')

@section('content')

    <div class="max-w-2xl mx-auto">

        <a href="{{ route('debts.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-sm mb-4 md-ripple">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <div class="bg-white rounded-2xl md-shadow p-6">

            <h2 class="text-lg font-bold text-gray-800 mb-5">Informasi Hutang</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($platforms->isEmpty())
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl mb-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Kamu belum punya platform. <a href="{{ route('platforms.create') }}" class="font-semibold underline">Tambah platform dulu</a>.
                    </p>
                </div>
            @endif

            <form action="{{ route('debts.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Hutang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Hutang HP Samsung, Pinjam Budi"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Platform <span class="text-red-500">*</span>
                    </label>
                    <select name="platform_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        <option value="">-- Pilih Platform --</option>
                        @foreach($platforms as $p)
                            <option value="{{ $p->id }}" {{ old('platform_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Total Hutang <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="total_amount" id="totalAmount" value="{{ old('total_amount') }}" required min="1" step="1000"
                               placeholder="1000000"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="startDate" value="{{ old('start_date', date('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jatuh Tempo <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="due_date" id="dueDate" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none transition">
                    </div>
                </div>

                {{-- Preview Target Harian --}}
                <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-700 font-semibold">Target Harian (otomatis)</p>
                            <p class="text-xs text-blue-500 mt-0.5">Berdasarkan sisa hari sampai jatuh tempo</p>
                        </div>
                        <p class="text-lg font-bold text-blue-700" id="previewTarget">Rp 0</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="3" placeholder="Opsional..."
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none transition">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit" {{ $platforms->isEmpty() ? 'disabled' : '' }}
                            class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition md-ripple disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-check mr-2"></i> Simpan Hutang
                    </button>
                    <a href="{{ route('debts.index') }}"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition md-ripple">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

    <script>
        const totalAmount = document.getElementById('totalAmount');
        const dueDate = document.getElementById('dueDate');
        const previewTarget = document.getElementById('previewTarget');

        function hitungTarget() {
            const total = parseFloat(totalAmount.value) || 0;
            const due = new Date(dueDate.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            due.setHours(0, 0, 0, 0);

            const diffDays = Math.max(Math.ceil((due - today) / (1000 * 60 * 60 * 24)), 1);
            const target = Math.round(total / diffDays);

            previewTarget.textContent = 'Rp ' + target.toLocaleString('id-ID');
        }

        totalAmount.addEventListener('input', hitungTarget);
        dueDate.addEventListener('change', hitungTarget);
        hitungTarget();
    </script>

@endsection