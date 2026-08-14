@extends('layouts.app')

@section('title', 'Daftar Penugasan')
@section('page-title', 'Manajemen Penugasan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-2">
            <!-- Tombol Export -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center">
                    <i class="fas fa-download mr-2"></i> Export
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <a href="{{ route('assignments.export.form') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file-export mr-2"></i> Halaman Export
                    </a>
                    <a href="{{ route('assignments.export.all') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file-excel mr-2"></i> Export Semua Data
                    </a>
                    <button onclick="showExportFilter()" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-filter mr-2"></i> Export Hasil Filter
                    </button>
                </div>
            </div>

            <!-- Tombol Import -->
            <a href="{{ route('assignments.import.form') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-upload mr-2"></i> Import
            </a>
        </div>
    </div>

    {{-- @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif --}}

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('import_failures'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle mr-3 mt-1"></i>
                <div>
                    <p class="font-bold">Detail kegagalan import:</p>
                    <ul class="list-disc list-inside mt-2 text-sm">
                        @foreach(session('import_failures') as $failure)
                            <li class="mb-1">
                                <span class="font-medium">Baris:</span>
                                @if(is_array($failure['row']))
                                    {{ json_encode($failure['row']) }}
                                @else
                                    {{ $failure['row'] ?? 'N/A' }}
                                @endif
                                <br>
                                <span class="font-medium">Error:</span> {{ $failure['errors'] ?? 'Unknown error' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="p-4 border-b bg-gray-50">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="Nama petugas / tugas..." value="{{ request('search') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tugas</label>
                <select name="duty_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Tugas</option>
                    @foreach($duties as $duty)
                        <option value="{{ $duty->id }}" {{ request('duty_id') == $duty->id ? 'selected' : '' }}>
                            [{{ $duty->code }}] {{ $duty->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                       value="{{ request('start_date') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                       value="{{ request('end_date') }}">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('assignments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Penugasan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Min / Max</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ketersediaan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($assignments as $index => $assignment)
                @php
                    // ===== HITUNG KETERSEDIAAN PER TANGGAL & JADWAL =====
                    $duty = $assignment->duty;
                    $schedule = $assignment->schedule;
                    $eventDate = $assignment->event_date;
                    
                    // Hitung jumlah petugas yang sudah ACCEPTED untuk duty, schedule, dan tanggal yang sama
                    $totalAccepted = \App\Models\DutyAssignment::where('duty_id', $duty->id)
                        ->where('schedule_id', $schedule->id)
                        ->where('event_date', $eventDate)
                        ->where('status', 'accepted')
                        ->count();
                    
                    // Hitung total petugas (accepted + pending)
                    $totalAssigned = \App\Models\DutyAssignment::where('duty_id', $duty->id)
                        ->where('schedule_id', $schedule->id)
                        ->where('event_date', $eventDate)
                        ->whereIn('status', ['accepted', 'pending'])
                        ->count();
                    
                    $minPerson = $duty->min_person ?? 1;
                    $maxPerson = $duty->max_person ?? 999;
                    
                    // Status Ketersediaan
                    if ($totalAccepted >= $maxPerson) {
                        $availabilityStatus = 'penuh';
                        $availabilityLabel = 'Penuh';
                        $availabilityBadge = 'bg-red-100 text-red-700';
                        $availabilityIcon = 'fa-times-circle';
                        $availabilityMessage = 'Kuota sudah penuh (' . $totalAccepted . '/' . $maxPerson . ')';
                    } elseif ($totalAccepted >= $minPerson) {
                        $availabilityStatus = 'cukup';
                        $availabilityLabel = 'Cukup';
                        $availabilityBadge = 'bg-green-100 text-green-700';
                        $availabilityIcon = 'fa-check-circle';
                        $availabilityMessage = 'Kuota terpenuhi (' . $totalAccepted . '/' . $maxPerson . ')';
                    } elseif ($totalAccepted > 0) {
                        $availabilityStatus = 'kurang';
                        $availabilityLabel = 'Kurang';
                        $availabilityBadge = 'bg-yellow-100 text-yellow-700';
                        $availabilityIcon = 'fa-exclamation-triangle';
                        $availabilityMessage = 'Butuh ' . ($minPerson - $totalAccepted) . ' orang lagi (min ' . $minPerson . ')';
                    } else {
                        $availabilityStatus = 'kosong';
                        $availabilityLabel = 'Kosong';
                        $availabilityBadge = 'bg-gray-100 text-gray-500';
                        $availabilityIcon = 'fa-user-slash';
                        $availabilityMessage = 'Belum ada petugas (0/' . $maxPerson . ')';
                    }
                @endphp
                <tr class="hover:bg-gray-50 transition duration-200">
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        {{ $assignments->firstItem() + $index }}
                    </td>
                    
                    <!-- TANGGAL PENUGASAN -->
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($assignment->event_date)
                            <div class="font-medium text-gray-800 text-sm">
                                {{ $assignment->event_date->translatedFormat('d F Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $assignment->event_date->translatedFormat('l') }}
                                @if($assignment->schedule && $assignment->schedule->time)
                                    • {{ \Carbon\Carbon::parse($assignment->schedule->time)->format('H:i') }}
                                @endif
                            </div>
                            <!-- STATUS WAKTU -->
                            @php
                                $today = \Carbon\Carbon::today();
                                $eventDateCarbon = \Carbon\Carbon::parse($assignment->event_date);
                            @endphp
                            
                            @if($eventDateCarbon->lt($today))
                                <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-clock mr-1"></i> Sudah Lewat
                                </div>
                            @elseif($eventDateCarbon->eq($today))
                                <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 animate-pulse">
                                    <i class="fas fa-hourglass-half mr-1"></i> Sedang Berlangsung
                                </div>
                            @else
                                <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-calendar-alt mr-1"></i> Akan Datang
                                </div>
                            @endif
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                    
                    <!-- JADWAL -->
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ $assignment->schedule->display ?? $assignment->schedule->name ?? '-' }}
                        </span>
                    </td>
                    
                    <!-- TUGAS -->
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                        <div class="font-medium">{{ $assignment->duty->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $assignment->duty->code ?? '' }}</div>
                    </td>
                    
                    <!-- KOLOM MIN / MAX -->
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <span class="text-sm font-medium text-gray-700">
                            {{ $minPerson }}
                            <span class="text-gray-400"> - </span>
                            {{ $maxPerson != 999 ? $maxPerson : '∞' }}
                        </span>
                    </td>
                    
                    <!-- KOLOM KETERSEDIAAN (AKURAT PER TANGGAL & JADWAL) -->
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <div class="inline-flex flex-col items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $availabilityBadge }}" title="{{ $availabilityMessage }}">
                                <i class="fas {{ $availabilityIcon }} mr-1.5"></i>
                                {{ $availabilityLabel }}
                            </span>
                            <span class="text-xs text-gray-400 mt-1">
                                {{ $totalAccepted }}/{{ $maxPerson != 999 ? $maxPerson : '∞' }}
                                @if($totalAssigned > $totalAccepted)
                                    <span class="text-yellow-500">(+{{ $totalAssigned - $totalAccepted }} pending)</span>
                                @endif
                            </span>
                        </div>
                    </td>
                    
                    <!-- PETUGAS -->
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $assignment->user->name ?? '-' }}
                    </td>
                    
                    <!-- STATUS PENUGASAN -->
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $assignment->status_badge }}">
                            <i class="fas {{ $assignment->status_icon }} mr-1"></i>
                            {{ $assignment->status_label }}
                        </span>
                    </td>
                    
                    <!-- AKSI -->
                    <td class="px-4 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-1.5">
                            <a href="{{ route('assignments.show', $assignment) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 rounded-lg transition"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('assignments.edit', $assignment) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition"
                               title="Edit Penugasan">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <button type="button" 
                                    onclick="confirmDelete({{ $assignment->id }}, '{{ $assignment->duty->name ?? 'Tugas' }} - {{ $assignment->user->name ?? 'User' }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 rounded-lg transition"
                                    title="Hapus Penugasan">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                            <form id="delete-form-{{ $assignment->id }}" 
                                  action="{{ route('assignments.destroy', $assignment) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-tasks fa-4x mb-4 text-gray-300"></i>
                            <p class="text-lg">Belum ada data penugasan</p>
                            <a href="{{ route('assignments.create') }}" class="mt-4 text-blue-500 hover:text-blue-700">
                                <i class="fas fa-plus mr-1"></i> Tambah Penugasan Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($assignments->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} 
                dari {{ $assignments->total() }} data
            </div>
            <div>
                {{ $assignments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Export Filter -->
<div id="exportFilterModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-filter text-blue-500 mr-2"></i> Export Data dengan Filter
            </h3>
            <button onclick="closeExportFilterModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('assignments.export.filtered') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" name="search" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="Cari berdasarkan nama petugas atau tugas...">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Penugasan</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Konfirmasi</option>
                    <option value="accepted">Diterima</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                <select name="format" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="xlsx">Excel (.xlsx)</option>
                    <option value="csv">CSV (.csv)</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeExportFilterModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus penugasan <strong id="deleteName"></strong>?
                </p>
                <p class="text-xs text-red-500 mt-2">
                    <i class="fas fa-warning"></i> Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="flex justify-center gap-3 mt-4">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Batal
                </button>
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-700">
                    Ya, Hapus!
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let deleteId = null;
    
    function confirmDelete(id, name) {
        deleteId = id;
        document.getElementById('deleteName').innerText = name;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }
    
    function showExportFilter() {
        document.getElementById('exportFilterModal').classList.remove('hidden');
    }
    
    function closeExportFilterModal() {
        document.getElementById('exportFilterModal').classList.add('hidden');
    }
    
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteId) {
            document.getElementById('delete-form-' + deleteId).submit();
        }
    });
    
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    document.getElementById('exportFilterModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeExportFilterModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush
@endsection