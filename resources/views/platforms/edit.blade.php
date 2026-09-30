@extends('layouts.app')

@section('title', 'Edit Platform')
@section('headerTitle', 'Edit Platform')
@section('headerSubtitle', 'Ubah data platform')

@section('content')

    <div class="max-w-2xl mx-auto">

        <a href="{{ route('platforms.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-sm mb-4 md-ripple">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <div class="bg-white rounded-2xl md-shadow p-6">

            <h2 class="text-lg font-bold text-gray-800 mb-5">Edit: {{ $platform->name }}</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('platforms.update', $platform->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Platform <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $platform->name) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                        @foreach(['paylater' => 'PayLater', 'bank' => 'Bank', 'ewallet' => 'E-Wallet', 'personal' => 'Personal', 'lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $platform->type) == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Warna</label>
                        <input type="color" name="color" value="{{ old('color', $platform->color ?? '#3B82F6') }}"
                               class="w-full h-12 rounded-xl border border-gray-200 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ikon</label>
                        <select name="icon" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                            @foreach(['fa-credit-card' => '💳 Kartu', 'fa-building-columns' => '🏦 Bank', 'fa-wallet' => '👛 Dompet', 'fa-user' => '👤 Orang', 'fa-bag-shopping' => '🛍️ Belanja', 'fa-mobile-screen' => '📱 HP'] as $val => $label)
                                <option value="{{ $val }}" {{ old('icon', $platform->icon) == $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition md-ripple">
                        <i class="fa-solid fa-check mr-2"></i> Update
                    </button>
                    <a href="{{ route('platforms.index') }}"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition md-ripple">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection