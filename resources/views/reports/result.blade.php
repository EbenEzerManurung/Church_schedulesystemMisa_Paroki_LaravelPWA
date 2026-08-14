@extends('layouts.app')

@section('title', 'Hasil Laporan')
@section('page-title', 'Hasil Laporan Pelayanan')

@section('content')
<!-- Statistik -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_assignments'] }}</p>
            <p class="text-sm text-gray-500">Total Penugasan</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['total_available'] }}</p>
            <p class="text-sm text-gray-500">Bersedia</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-red-600">{{ $stats['total_unavailable'] }}</p>
            <p class="text-sm text-gray-500">Tidak Bersedia</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_replaced'] }}</p>
            <p class="text-sm text-gray-500">Digantikan</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_pending'] }}</p>
            <p class="text-sm text-gray-500">Menunggu</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-500">Petugas Aktif</p>
        </div>
    </div>
</div>

<!-- Filter Info -->
<div class="card mb-6">
    <div class="card-body">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-filter mr-2"></i>
                    @if($filters['date_from'] && $filters['date_to'])
                        Periode: {{ date('d/m/Y', strtotime($filters['date_from'])) }} - {{ date('d/m/Y', strtotime($filters['date_to'])) }}
                    @elseif($filters['date_from'])
                        Dari: {{ date('d/m/Y', strtotime($filters['date_from'])) }} - Sekarang
                    @elseif($filters['date_to'])
                        Sampai: {{ date('d/m/Y', strtotime($filters['date_to'])) }}
                    @else
                        Semua Data
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-1">Total {{ $data->count() }} data ditemukan</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('reports.generate', array_merge($filters, ['export' => 1])) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Detail Data Penugasan</h3>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal Ibadah</th>
                        <th>Ibadah</th>
                        <th>Tugas</th>
                        <th>Petugas</th>
                        <th>Status Ketersediaan</th>
                        <th>Alasan</th>
                        <th>Usulan Pengganti</th>
                        <th>Status Penugasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                    <tr>
                        <td>{{ $row['Tanggal Ibadah'] }}</td>
                        <td>{{ $row['Ibadah'] }}</td>
                        <td>{{ $row['Tugas'] }}</td>
                        <td>{{ $row['Petugas'] }}</td>
                        <td>
                            @php
                                $badgeClass = match($row['Status Ketersediaan']) {
                                    'Bersedia' => 'bg-green-100 text-green-800',
                                    'Tidak Bersedia' => 'bg-red-100 text-red-800',
                                    'Digantikan' => 'bg-blue-100 text-blue-800',
                                    'Menunggu Konfirmasi' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $row['Status Ketersediaan'] }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($row['Alasan (Jika Tidak Bersedia)'], 50) }}</td>
                        <td>{{ $row['Usulan Pengganti'] }}</td>
                        <td>
                            <span class="badge bg-gray-100 text-gray-800">{{ $row['Status Penugasan'] }}</span>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fas fa-chart-bar text-4xl mb-2 block"></i>
                                Tidak ada data untuk periode yang dipilih
                            </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection