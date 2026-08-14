@extends('layouts.app')

@section('title', 'Detail Gereja - ' . ($gereja->nama ?? ''))
@section('page-title', 'Detail Gereja')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-church mr-2 text-purple-500"></i> 
            {{ $gereja->nama ?? 'Gereja tidak ditemukan' }}
        </h1>
        <div class="flex space-x-2">
            <a href="{{ route('gerejas.edit', $gereja->id) }}" 
               class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('gerejas.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detail Gereja -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-700">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-info-circle mr-2"></i> Detail Gereja
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Kode Gereja</label>
                            <p class="text-lg font-semibold text-gray-800">
                                <span class="px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $gereja->kode ?? '-' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nama Gereja</label>
                            <p class="text-lg font-semibold text-gray-800">{{ $gereja->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Keuskupan</label>
                            <p class="text-lg font-semibold text-gray-800">
                                <a href="{{ route('keuskupans.show', $gereja->keuskupan_id) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $gereja->keuskupan->name ?? '-' }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="text-lg font-semibold">
                                @if($gereja->is_active)
                                    <span class="px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Lokasi</label>
                            <p class="text-lg font-semibold text-gray-800">{{ $gereja->lokasi ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-lg font-semibold text-gray-800">
                                @if($gereja->email)
                                    <a href="mailto:{{ $gereja->email }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $gereja->email }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Telepon</label>
                            <p class="text-lg font-semibold text-gray-800">
                                @if($gereja->telepon)
                                    <a href="tel:{{ $gereja->telepon }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $gereja->telepon }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-500">Deskripsi</label>
                            <p class="text-gray-700 mt-1">{{ $gereja->description ?? 'Tidak ada deskripsi' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tanggal Dibuat</label>
                            <p class="text-gray-700">{{ $gereja->created_at ? $gereja->created_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Terakhir Update</label>
                            <p class="text-gray-700">{{ $gereja->updated_at ? $gereja->updated_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-teal-700">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-chart-bar mr-2"></i> Statistik
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Total Pengguna</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_users'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Pengguna Aktif</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $statistics['active_users'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Admin Gereja</p>
                                    <p class="text-2xl font-bold text-yellow-600">{{ $statistics['admin_count'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-shield text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">Pengguna Biasa</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['regular_users'] ?? 0 }}</p>
                                </div>
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aksi Cepat -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-cyan-700">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-bolt mr-2"></i> Aksi Cepat
                    </h2>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('gerejas.edit', $gereja->id) }}" 
                       class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit Gereja
                    </a>
                    <a href="{{ route('gerejas.members', $gereja->id) }}" 
                       class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-users mr-2"></i> Lihat Anggota
                    </a>
                    <a href="{{ route('gerejas.statistics', $gereja->id) }}" 
                       class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-chart-pie mr-2"></i> Statistik Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection