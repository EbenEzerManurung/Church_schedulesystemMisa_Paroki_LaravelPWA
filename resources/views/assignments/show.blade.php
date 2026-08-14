@extends('layouts.app')

@section('title', 'Detail Penugasan')
@section('page-title', 'Detail Penugasan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-info-circle mr-2 text-blue-500"></i> Detail Penugasan
        </h1>
        <div class="flex space-x-2">
            <a href="{{ route('assignments.edit', $assignment) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('assignments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
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
                        <i class="fas fa-info-circle mr-2"></i> Informasi Penugasan
                    </h3>
                </div>
                   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-50 p-4 rounded-lg">
            <label class="text-sm text-gray-500">Tanggal Ibadah</label>
            <p class="font-semibold text-gray-800 text-lg">
                {{ $assignment->event_date ? \Carbon\Carbon::parse($assignment->event_date)->format('d F Y') : 'Tanggal belum ditentukan' }}
            </p>
        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Jadwal Ibadah</label>
                            <p class="font-semibold text-gray-800">
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                    {{ $assignment->schedule->schedule_type_display }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Tugas Pelayanan</label>
                            <p class="font-semibold text-gray-800">
                                [{{ $assignment->duty->code }}] {{ $assignment->duty->name }}
                            </p>
                            @if($assignment->duty->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $assignment->duty->description }}</p>
                            @endif
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Petugas</label>
                            <p class="font-semibold text-gray-800">{{ $assignment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $assignment->user->email }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Status</label>
                            <p>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $assignment->status_badge }}">
                                    <i class="fas {{ $assignment->status_icon }} mr-1"></i>
                                    {{ $assignment->status_label }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Dibuat Pada</label>
                            <p class="text-gray-700">{{ $assignment->created_at ? $assignment->created_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        @if($assignment->responded_at)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Direspon Pada</label>
                            <p class="text-gray-700">{{ $assignment->responded_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($assignment->notes)
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                            <label class="text-sm text-gray-500">Catatan Admin</label>
                            <p class="text-gray-700">{{ $assignment->notes }}</p>
                        </div>
                        @endif
                        @if($assignment->rejection_reason)
                        <div class="md:col-span-2 bg-red-50 p-4 rounded-lg border border-red-200">
                            <label class="text-sm text-red-600 font-semibold">
                                <i class="fas fa-info-circle mr-1"></i> Alasan Penolakan
                            </label>
                            <p class="text-red-700 mt-1">{{ $assignment->rejection_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

  
        <!-- ============================================ -->
<!-- SECTION LITURGI - Berdasarkan Tanggal Penugasan (event_date) -->
<!-- ============================================ -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-700">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-church mr-2"></i> Kalender Liturgi
            </h3>
            <span class="text-xs text-blue-100">
                Tanggal Penugasan: {{ $assignment->event_date ? \Carbon\Carbon::parse($assignment->event_date)->format('d F Y') : 'Tanggal tidak tersedia' }}
            </span>
        </div>
    </div>
    <div class="p-6">
        @if(isset($liturgi) && $liturgi)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Keterangan Hari -->
            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                <label class="text-sm text-gray-500">Keterangan Hari</label>
                <p class="font-bold text-gray-800 text-lg">{{ $liturgi->keterangan_hari }}</p>
            </div>

            <!-- Warna Liturgi -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <label class="text-sm text-gray-500">Warna Liturgi</label>
                <div class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $liturgi->warna_liturgi_badge }}">
                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $liturgi->warna_liturgi }};"></span>
                        {{ ucfirst($liturgi->warna_liturgi) }}
                    </span>
                </div>
            </div>

            <!-- Status Liturgi -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <label class="text-sm text-gray-500">Status Liturgi</label>
                <div class="mt-1">
                    @if($liturgi->is_active)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Bacaan 1 -->
            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                <label class="text-sm text-gray-500">
                    <i class="fas fa-book mr-1 text-blue-600"></i> Bacaan Pertama
                </label>
                <p class="text-gray-800 font-medium mt-1">{{ $liturgi->bacaan1 }}</p>
            </div>

            <!-- Mazmur Tanggapan -->
            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                <label class="text-sm text-gray-500">
                    <i class="fas fa-music mr-1 text-green-600"></i> Mazmur Tanggapan
                </label>
                <p class="text-gray-800 font-medium mt-1">{{ $liturgi->mazmur_tanggapan }}</p>
            </div>

            <!-- Bait Pengantar Injil -->
            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                <label class="text-sm text-gray-500">
                    <i class="fas fa-hands-praying mr-1 text-purple-600"></i> Bait Pengantar Injil
                </label>
                <p class="text-gray-800 font-medium mt-1">{{ $liturgi->bait_pengantarinjil }}</p>
            </div>

            <!-- Bacaan Injil -->
            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                <label class="text-sm text-gray-500">
                    <i class="fas fa-cross mr-1 text-red-600"></i> Bacaan Injil
                </label>
                <p class="text-gray-800 font-medium mt-1">{{ $liturgi->bacaan_injil }}</p>
            </div>

            @if($liturgi->catatan)
            <div class="bg-yellow-50 p-4 rounded-lg md:col-span-2 border border-yellow-200">
                <label class="text-sm text-yellow-700 font-semibold">
                    <i class="fas fa-sticky-note mr-1"></i> Catatan Liturgi
                </label>
                <p class="text-yellow-800 mt-1">{{ $liturgi->catatan }}</p>
            </div>
            @endif
        </div>

        <!-- Link ke Detail Liturgi -->
        <div class="mt-4 text-right">
            <a href="{{ route('kalender-liturgi.show', $liturgi->id) }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                <i class="fas fa-external-link-alt mr-1"></i> Lihat Detail Liturgi
            </a>
        </div>
        @else
        <div class="text-center py-8">
            <div class="text-6xl text-gray-300 mb-4">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <p class="text-gray-500 text-lg font-medium">Data Liturgi Tidak Tersedia</p>
            <p class="text-gray-400 text-sm mt-1">
                Belum ada data liturgi untuk tanggal penugasan 
                <strong>{{ $assignment->event_date ? \Carbon\Carbon::parse($assignment->event_date)->format('d F Y') : 'ini' }}</strong>
            </p>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 flex justify-center gap-3">
              
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
        <!-- Informasi Organisasi -->
        <div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-building mr-2"></i> Informasi Keuskupan & Gereja
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-gray-500">Keuskupan</label>
                            <p class="font-semibold text-gray-800">
                                {{ $assignment->user->keuskupan->name ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Gereja</label>
                            <p class="font-semibold text-gray-800">
                                {{ $assignment->user->gereja->nama ?? '-' }}
                            </p>
                            @if($assignment->user->gereja)
                                <p class="text-sm text-gray-500 mt-1">{{ $assignment->user->gereja->lokasi }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection