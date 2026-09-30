@extends('layouts.app')

@section('title', 'Platform - Catatan Keuangan')
@section('headerTitle', 'Platform')
@section('headerSubtitle', 'Kelola daftar platform hutangmu')

@section('content')

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl">
            <i class="fa-solid fa-circle-check"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header + Tombol Tambah --}}
    <div class="flex justify-between items-center mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Platform</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $platforms->count() }} platform terdaftar</p>
        </div>
        <a href="{{ route('platforms.create') }}" class="bg-blue-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-blue-700 transition md-ripple flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span class="hidden sm:inline">Tambah</span>
        </a>
    </div>

    {{-- Grid Platform --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        @forelse($platforms as $platform)
            <div class="bg-white rounded-2xl md-shadow p-5 md-ripple">

                {{-- Header Kartu --}}
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg"
                             style="background-color: {{ $platform->color }}20; color: {{ $platform->color }}">
                            <i class="fa-solid {{ $platform->icon ?? 'fa-credit-card' }}"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $platform->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ $platform->type }}</p>
                        </div>
                    </div>

                    {{-- Dropdown Aksi --}}
                    <div class="relative" x-data="{ open: false }">
                        <details class="group">
                            <summary class="list-none cursor-pointer w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center">
                                <i class="fa-solid fa-ellipsis-vertical text-gray-500"></i>
                            </summary>
                            <div class="absolute right-0 top-full mt-1 bg-white rounded-xl md-shadow border border-gray-100 py-1 w-32 z-10">
                                <a href="{{ route('platforms.edit', $platform->id) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fa-solid fa-pen mr-2 text-blue-500"></i> Edit
                                </a>
                                <form action="{{ route('platforms.destroy', $platform->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus platform {{ $platform->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fa-solid fa-trash mr-2"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>

                {{-- Statistik --}}
                <div class="flex justify-between text-xs pt-3 border-t border-gray-100">
                    <div>
                        <p class="text-gray-500">Hutang</p>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $platform->debts_count }} item</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500">Total Sisa</p>
                        <p class="font-bold text-gray-800 mt-0.5">
                            Rp {{ number_format($platform->total_debt ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl md-shadow p-10 text-center">
                <i class="fa-solid fa-credit-card text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-700 font-semibold">Belum ada platform</p>
                <p class="text-sm text-gray-500 mt-1 mb-4">Yuk tambahkan platform pertama kamu!</p>
                <a href="{{ route('platforms.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Platform
                </a>
            </div>
        @endforelse

    </div>

@endsection