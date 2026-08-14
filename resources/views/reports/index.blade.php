@extends('layouts.app')

@section('title', 'Laporan Pelayanan')
@section('page-title', 'Laporan Pelayanan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.generate') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-input" value="{{ old('date_from', date('Y-m-01')) }}">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk data dari awal</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-input" value="{{ old('date_to', date('Y-m-d')) }}">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk data sampai hari ini</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('reports.export-all') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Semua Data
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Informasi Laporan</h3>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-blue-600 text-sm">Fitur Laporan</p>
                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                    <li><i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Filter berdasarkan rentang tanggal</li>
                    <li><i class="fas fa-file-excel mr-2 text-green-500"></i> Export ke Excel dengan filter yang dipilih</li>
                    <li><i class="fas fa-download mr-2 text-blue-500"></i> Export semua data tanpa filter</li>
                    <li><i class="fas fa-chart-pie mr-2 text-purple-500"></i> Statistik lengkap</li>
                </ul>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-600 text-sm">Data yang Dilaporkan</p>
                <ul class="text-sm text-gray-600 mt-2 space-y-1">
                    <li><i class="fas fa-tasks mr-2"></i> Semua penugasan pelayanan</li>
                    <li><i class="fas fa-user-check mr-2"></i> Status ketersediaan petugas</li>
                    <li><i class="fas fa-exchange-alt mr-2"></i> Data penggantian tugas</li>
                    <li><i class="fas fa-chart-line mr-2"></i> Statistik keseluruhan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection