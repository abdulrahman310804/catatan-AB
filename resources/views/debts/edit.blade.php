@extends('layouts.app')

@section('title', 'Edit Hutang')
@section('headerTitle', 'Edit Hutang')
@section('headerSubtitle', $debt->name)

@section('content')

    <div class="max-w-2xl mx-auto">

        <a href="{{ route('debts.show', $debt->id) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-sm mb-4 md-ripple">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <div class="bg-white rounded-2xl md-shadow p-6">

            <h2 class="text-lg font-bold text-gray-800 mb-5">Edit: {{ $debt->name }}</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('debts.update', $debt->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Hutang <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $debt->name) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Platform <span class="text-red-500">*</span></label>
                    <select name="platform_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                        @foreach($platforms as $p)
                            <option value="{{ $p->id }}" {{ old('platform_id', $debt->platform_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Hutang <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="total_amount" value="{{ old('total_amount', $debt->total_amount) }}" required min="1" step="1000"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Sudah dibayar: Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', $debt->start_date) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jatuh Tempo <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date', $debt->due_date) }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">{{ old('notes', $debt->notes) }}</textarea>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition md-ripple">
                        <i class="fa-solid fa-check mr-2"></i> Update
                    </button>
                    <a href="{{ route('debts.show', $debt->id) }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition md-ripple">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection