@extends('layouts.app')

@section('title', 'Statistik Gereja - ' . ($gereja->nama ?? ''))
@section('page-title', 'Statistik Gereja')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-chart-pie mr-2 text-green-500"></i> 
            Statistik {{ $gereja->nama ?? 'Gereja' }}
        </h1>
        <a href="{{ route('gerejas.show', $gereja->id) }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Pengguna</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistics['total_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pengguna Aktif</p>
                    <p class="text-2xl font-bold text-green-600">{{ $statistics['active_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Admin Gereja</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $statistics['admin_count'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pengguna Biasa</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['regular_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Sederhana -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-bar mr-2 text-blue-500"></i> Distribusi Pengguna
        </h3>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Admin Gereja</span>
                    <span>{{ $statistics['admin_count'] ?? 0 }} orang</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    @php
                        $total = ($statistics['total_users'] ?? 1);
                        $adminPercent = ($statistics['admin_count'] ?? 0) / $total * 100;
                    @endphp
                    <div class="bg-yellow-500 h-4 rounded-full" style="width: {{ $adminPercent }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Pengguna Biasa</span>
                    <span>{{ $statistics['regular_users'] ?? 0 }} orang</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    @php
                        $regularPercent = ($statistics['regular_users'] ?? 0) / $total * 100;
                    @endphp
                    <div class="bg-purple-500 h-4 rounded-full" style="width: {{ $regularPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection