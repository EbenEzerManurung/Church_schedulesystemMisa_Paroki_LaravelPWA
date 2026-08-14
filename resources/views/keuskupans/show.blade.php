{{-- resources/views/keuskupans/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Keuskupan')
@section('page-title', 'Detail Keuskupan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-church mr-2"></i> Detail Keuskupan
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('keuskupans.edit', $keuskupan->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('keuskupans.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Keuskupan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="card-header bg-gray-50 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-info-circle mr-2"></i> Informasi Keuskupan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-500">Kode Keuskupan</label>
                            <p class="font-semibold">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">{{ $keuskupan->code }}</span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Nama Keuskupan</label>
                            <p class="font-semibold">{{ $keuskupan->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Email</label>
                            <p>{{ $keuskupan->email ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Telepon</label>
                            <p>{{ $keuskupan->phone ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-500">Alamat</label>
                            <p>{{ $keuskupan->address ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-500">Deskripsi</label>
                            <p>{{ $keuskupan->description ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="card-header bg-gray-50 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-pie mr-2"></i> Statistik
                    </h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Gereja</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_churches'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-church text-3xl text-blue-400"></i>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Gereja Aktif</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $statistics['active_churches'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl text-green-400"></i>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total User</p>
                                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['total_users'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-users text-3xl text-purple-400"></i>
                            </div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Pastor</p>
                                    <p class="text-2xl font-bold text-yellow-600">{{ $statistics['total_priests'] ?? 0 }}</p>
                                </div>
                                <i class="fas fa-pray text-3xl text-yellow-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Lainnya -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="card-header bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-building mr-2"></i> Manajemen Gereja
                </h3>
            </div>
            <div class="card-body">
                <p class="text-gray-600 mb-4">Kelola daftar gereja yang berada di bawah keuskupan ini.</p>
                {{-- PERBAIKAN: gunakan route keuskupans.gerejas --}}
                <a href="{{ route('keuskupans.gerejas', $keuskupan->id) }}" class="btn btn-primary w-full">
                    <i class="fas fa-church mr-2"></i> Lihat Gereja
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="card-header bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-users mr-2"></i> Manajemen Anggota
                </h3>
            </div>
            <div class="card-body">
                <p class="text-gray-600 mb-4">Kelola semua user yang terdaftar di keuskupan ini.</p>
                <a href="{{ route('keuskupans.members', $keuskupan->id) }}" class="btn btn-primary w-full">
                    <i class="fas fa-users mr-2"></i> Lihat Anggota
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="card-header bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-bar mr-2"></i> Statistik
                </h3>
            </div>
            <div class="card-body">
                <p class="text-gray-600 mb-4">Lihat statistik lengkap keuskupan ini.</p>
                <a href="{{ route('keuskupans.statistics', $keuskupan->id) }}" class="btn btn-primary w-full">
                    <i class="fas fa-chart-bar mr-2"></i> Statistik Lengkap
                </a>
            </div>
        </div>
    </div>
</div>
@endsection