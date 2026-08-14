@extends('layouts.app')

@section('title', 'Statistik Keuskupan')
@section('page-title', 'Statistik Keuskupan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-chart-bar mr-2"></i> Statistik Keuskupan
        </h1>
        <a href="{{ route('keuskupans.show', $keuskupan->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <div class="text-blue-500 text-3xl mb-2">
                    <i class="fas fa-building"></i>
                </div>
                <div class="text-2xl font-bold">{{ $statistics['total_churches'] ?? 0 }}</div>
                <div class="text-gray-500">Total Gereja</div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <div class="text-green-500 text-3xl mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="text-2xl font-bold">{{ $statistics['active_churches'] ?? 0 }}</div>
                <div class="text-gray-500">Gereja Aktif</div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <div class="text-purple-500 text-3xl mb-2">
                    <i class="fas fa-users"></i>
                </div>
                <div class="text-2xl font-bold">{{ $statistics['total_users'] ?? 0 }}</div>
                <div class="text-gray-500">Total User</div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 text-center">
                <div class="text-yellow-500 text-3xl mb-2">
                    <i class="fas fa-church"></i>
                </div>
                <div class="text-2xl font-bold">{{ $statistics['admin_gereja'] ?? 0 }}</div>
                <div class="text-gray-500">Admin Gereja</div>
            </div>
        </div>
    </div>

    <!-- Detail Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-pie mr-2"></i> Detail User
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Admin Keuskupan</span>
                        <span class="font-semibold text-lg">{{ $statistics['admin_keuskupan'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Admin Gereja</span>
                        <span class="font-semibold text-lg">{{ $statistics['admin_gereja'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">User Biasa</span>
                        <span class="font-semibold text-lg">{{ $statistics['regular_users'] ?? 0 }}</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center font-bold">
                            <span>Total User</span>
                            <span class="text-xl text-blue-600">{{ $statistics['total_users'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-church mr-2"></i> Daftar Gereja
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse(($statistics['churches_data'] ?? []) as $gereja)
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <div>
                            <span class="font-medium">{{ $gereja->nama }}</span>
                            <br><small class="text-gray-500">Kode: {{ $gereja->kode }}</small>
                        </div>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                            {{ $gereja->users_count ?? 0 }} User
                        </span>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">Belum ada data gereja</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection