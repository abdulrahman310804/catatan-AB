@extends('layouts.app')

@section('title', 'Tambah Platform')
@section('headerTitle', 'Tambah Platform')
@section('headerSubtitle', 'Buat platform baru untuk mencatat hutang')

@section('content')

    <div class="max-w-2xl mx-auto">

        {{-- Tombol Kembali --}}
        <a href="{{ route('platforms.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-sm mb-4 md-ripple">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        {{-- Form --}}
        <div class="bg-white rounded-2xl md-shadow p-6">

            <h2 class="text-lg font-bold text-gray-800 mb-5">Informasi Platform</h2>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('platforms.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Platform <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Kredivo, BCA, Budi (Teman)"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                </div>

                {{-- Tipe --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tipe Platform <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="paylater" {{ old('type') == 'paylater' ? 'selected' : '' }}>PayLater (Kredivo, Shopee, dll)</option>
                        <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Personal (Teman/Keluarga)</option>
                        <option value="lainnya" {{ old('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Warna --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Warna</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" id="colorInput" value="{{ old('color', '#3B82F6') }}"
                                   class="w-14 h-12 rounded-xl border border-gray-200 cursor-pointer">
                            <input type="text" id="colorText" value="{{ old('color', '#3B82F6') }}"
                                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none text-sm"
                                   readonly>
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ikon</label>
                        <select name="icon" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 outline-none">
                            <option value="fa-credit-card">💳 Kartu Kredit</option>
                            <option value="fa-building-columns">🏦 Bank</option>
                            <option value="fa-wallet">👛 Dompet</option>
                            <option value="fa-user">👤 Orang</option>
                            <option value="fa-bag-shopping">🛍️ Belanja</option>
                            <option value="fa-mobile-screen">📱 HP</option>
                        </select>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-2">Preview:</p>
                    <div class="flex items-center gap-3">
                        <div id="previewIcon" class="w-12 h-12 rounded-xl flex items-center justify-center text-lg"
                             style="background-color: #3B82F620; color: #3B82F6">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div>
                            <p id="previewName" class="font-bold text-gray-800">Nama Platform</p>
                            <p id="previewType" class="text-xs text-gray-500">Tipe</p>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-3 pt-3">
                    <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition md-ripple">
                        <i class="fa-solid fa-check mr-2"></i> Simpan
                    </button>
                    <a href="{{ route('platforms.index') }}"
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition md-ripple">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

    <script>
        // Sinkron warna
        const colorInput = document.getElementById('colorInput');
        const colorText = document.getElementById('colorText');
        const previewIcon = document.getElementById('previewIcon');

        colorInput.addEventListener('input', (e) => {
            colorText.value = e.target.value;
            previewIcon.style.backgroundColor = e.target.value + '20';
            previewIcon.style.color = e.target.value;
        });

        // Preview nama & tipe
        document.querySelector('input[name="name"]').addEventListener('input', (e) => {
            document.getElementById('previewName').textContent = e.target.value || 'Nama Platform';
        });
        document.querySelector('select[name="type"]').addEventListener('change', (e) => {
            document.getElementById('previewType').textContent = e.target.value || 'Tipe';
        });
    </script>

@endsection