{{-- resources/views/kalender-liturgi/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Kalender Liturgi Hari')
@section('page-title', 'Kalender Liturgi Hari')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt mr-2 text-purple-600"></i> Kalender Liturgi Hari
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('kalender-liturgi.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Tambah Data
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

    @if(session('warning'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow">
            <p>{{ session('warning') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" 
                           placeholder="Keterangan hari / bacaan..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" 
                           value="{{ request('tanggal') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Liturgi</label>
                    <select name="warna_liturgi" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Semua Warna</option>
                        @foreach($warnaList as $warna)
                            <option value="{{ $warna }}" {{ request('warna_liturgi') == $warna ? 'selected' : '' }}>
                                {{ ucfirst($warna) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 col-span-4">
                    <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('kalender-liturgi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                    <div class="ml-auto flex gap-2 flex-wrap">
                        <!-- Tombol Export -->
                        {{-- <a href="{{ route('kalender-liturgi.export.form') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-file-export"></i> Export
                        </a> --}}
                        
                        <!-- Tombol Import -->
                        <a href="{{ route('kalender-liturgi.import.form') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-file-import"></i> Import
                        </a>
                        
                        <!-- Tombol Template -->
                        {{-- <a href="{{ route('kalender-liturgi.template') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-download"></i> Template
                        </a> --}}
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warna Liturgi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bacaan 1</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bacaan Injil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kalender as $index => $item)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $kalender->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="font-medium text-gray-900">{{ $item->tanggal->format('d F Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $item->tanggal->format('l') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $item->keterangan_hari }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $item->warna_liturgi_badge }}">
                                {{ ucfirst($item->warna_liturgi) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $item->bacaan1 ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $item->bacaan_injil ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('kalender-liturgi.show', $item->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-sm">
                                    <i class="fas fa-eye"></i> Show
                                </a>
                                <a href="{{ route('kalender-liturgi.edit', $item->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('kalender-liturgi.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                <form action="{{ route('kalender-liturgi.toggle-status', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-sm">
                                        <i class="fas fa-toggle-{{ $item->is_active ? 'on' : 'off' }}"></i> 
                                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-alt fa-4x mb-4 text-gray-300"></i>
                                <p class="text-lg">Tidak ada data kalender liturgi</p>
                                <p class="text-sm text-gray-400 mt-1">Silakan tambahkan data baru atau import dari file</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan {{ $kalender->firstItem() ?? 0 }} - {{ $kalender->lastItem() ?? 0 }} 
                    dari {{ $kalender->total() }} data
                </div>
                @if($kalender->hasPages())
                <div>
                    {{ $kalender->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection