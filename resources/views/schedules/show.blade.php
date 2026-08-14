@extends('layouts.app')

@section('title', 'Detail Jadwal Ibadah')
@section('page-title', 'Detail Jadwal Ibadah')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt mr-2"></i> Detail Jadwal Ibadah
        </h1>
        <a href="{{ route('schedules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Hari</label>
                    <p class="text-lg font-semibold">{{ $schedule->day == 'sabtu' ? 'Sabtu' : 'Minggu' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Jam</label>
                    <p class="text-lg font-semibold">{{ date('H:i', strtotime($schedule->time)) }} WIB</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Nama Ibadah</label>
                    <p class="text-lg font-semibold">{{ $schedule->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $schedule->status_badge }}">
                            <i class="fas {{ $schedule->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $schedule->status_text }}
                        </span>
                    </p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Keterangan</label>
                    <p class="text-gray-700">{{ $schedule->description ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Dibuat pada</label>
                    <p class="text-gray-700">{{ $schedule->created_at ? $schedule->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Terakhir diupdate</label>
                    <p class="text-gray-700">{{ $schedule->updated_at ? $schedule->updated_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection