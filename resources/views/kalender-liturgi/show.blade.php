{{-- resources/views/kalender-liturgi/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Detail Kalender Liturgi')
@section('page-title', 'Detail Kalender Liturgi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-info-circle mr-2 text-purple-600"></i> Detail Kalender Liturgi
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('kalender-liturgi.edit', $kalender->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('kalender-liturgi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Utama -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-day mr-2"></i> Informasi Liturgi
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Tanggal</label>
                            <p class="font-semibold text-gray-800 text-lg">
                                {{ $kalender->tanggal->format('d F Y') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $kalender->tanggal->format('l') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Keterangan Hari</label>
                            <p class="font-semibold text-gray-800">{{ $kalender->keterangan_hari }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Warna Liturgi</label>
                            <p>
                                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $kalender->warna_liturgi_badge }}">
                                    {{ ucfirst($kalender->warna_liturgi) }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Status</label>
                            <p>
                                @if($kalender->is_active)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-clock mr-2"></i> Informasi Sistem
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Dibuat Pada</label>
                        <p class="text-gray-700">{{ $kalender->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Diperbarui Pada</label>
                        <p class="text-gray-700">{{ $kalender->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bacaan Liturgi -->
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-bible mr-2 text-red-600"></i> Bacaan Liturgi
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    @if($kalender->bacaan1)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-book mr-1 text-blue-600"></i> Bacaan 1
                        </label>
                        <p class="text-gray-800 mt-1">{{ $kalender->bacaan1 }}</p>
                    </div>
                    @endif

                    @if($kalender->mazmur_tanggapan)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-music mr-1 text-green-600"></i> Mazmur Tanggapan
                        </label>
                        <p class="text-gray-800 mt-1">{{ $kalender->mazmur_tanggapan }}</p>
                    </div>
                    @endif

                    @if($kalender->bait_pengantarinjil)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-hands-praying mr-1 text-purple-600"></i> Bait Pengantar Injil
                        </label>
                        <p class="text-gray-800 mt-1">{{ $kalender->bait_pengantarinjil }}</p>
                    </div>
                    @endif

                    @if($kalender->bacaan_injil)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-cross mr-1 text-red-600"></i> Bacaan Injil
                        </label>
                        <p class="text-gray-800 mt-1">{{ $kalender->bacaan_injil }}</p>
                    </div>
                    @endif

                    @if($kalender->catatan)
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <label class="text-sm font-semibold text-yellow-700">
                            <i class="fas fa-sticky-note mr-1"></i> Catatan
                        </label>
                        <p class="text-gray-700 mt-1">{{ $kalender->catatan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection