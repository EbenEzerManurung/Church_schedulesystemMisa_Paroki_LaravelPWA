@extends('layouts.app')

@section('title', 'Daftar Tugas Pelayanan')
@section('page-title', 'Manajemen Tugas Pelayanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Tombol Aksi -->
    <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <div class="flex flex-wrap gap-2">
            <!-- Tombol Export -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center shadow-md">
                    <i class="fas fa-download mr-2"></i> Export
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-10 border">
                    <a href="{{ route('duties.export.all') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
                        <i class="fas fa-file-excel text-green-500 w-5 mr-3"></i> Export Semua Data
                    </a>
                    <button onclick="showExportFilter()" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-b-lg">
                        <i class="fas fa-filter text-blue-500 w-5 mr-3"></i> Export Hasil Filter
                    </button>
                </div>
            </div>

            <!-- Tombol Import -->
            <a href="{{ route('duties.import.form') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center shadow-md">
                <i class="fas fa-upload mr-2"></i> Import
            </a>

            <!-- Tombol Tambah -->
            @if($hasAccess)
            <a href="{{ route('duties.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 inline-flex items-center shadow-md">
                <i class="fas fa-plus mr-2"></i> Tambah Tugas
            </a>
            @endif
        </div>
        
        <div class="text-sm text-gray-500 flex items-center gap-2 bg-white/80 px-3 py-1.5 rounded-lg border border-gray-200/50 shadow-sm">
            <i class="fas fa-clock text-indigo-400 text-xs"></i>
            <span class="font-mono tabular-nums font-medium text-gray-700">
                {{ now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
            </span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('import_failures'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-lg shadow-sm">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle mr-3 mt-1 text-yellow-500"></i>
                <div>
                    <p class="font-bold mb-2">Detail kegagalan import:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(session('import_failures') as $failure)
                            <li class="text-sm">
                                Baris {{ $failure->row() }}: 
                                @foreach($failure->errors() as $error)
                                    {{ $error }}
                                @endforeach
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Informasi Filter PIC Group -->
    @php
        $user = auth()->user();
        $isPicGroup = $user->level_akses === 'pic_group';
        $filterDutyId = request()->get('duty_id', $isPicGroup ? $user->duty_id : null);
    @endphp

    @if($isPicGroup && $user->duty)
    <div class="bg-purple-50 border-l-4 border-purple-500 text-purple-700 p-4 mb-6 rounded-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-users-cog mr-3 text-purple-500"></i>
            <div>
                <p class="font-semibold">Mode PIC Group: {{ $user->duty->name }}</p>
                <p class="text-sm text-purple-600 mt-0.5">
                    Anda hanya melihat tugas yang terkait dengan duty group Anda.
                    <span class="text-xs bg-purple-100 px-2 py-0.5 rounded-full ml-2">
                        <i class="fas fa-filter mr-1"></i> Filter aktif
                    </span>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Card Tabel -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-search text-gray-400 mr-1"></i> Cari Tugas
                    </label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Cari kode atau nama tugas..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-filter text-gray-400 mr-1"></i> Status
                    </label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <!-- Hidden field untuk duty_id jika PIC Group -->
                @if($isPicGroup && $user->duty_id)
                <input type="hidden" name="duty_id" value="{{ $user->duty_id }}">
                @endif
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg transition duration-200 flex items-center shadow-sm">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <a href="{{ route('duties.index') }}?{{ $isPicGroup ? 'duty_id=' . $user->duty_id : '' }}" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg transition duration-200 flex items-center shadow-sm">
                        <i class="fas fa-redo mr-2"></i> Reset
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Tugas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Min / Max</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($duties as $index => $duty)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            {{ $duties->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                <i class="fas fa-hashtag mr-1 text-xs"></i> {{ $duty->code }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-gray-800">{{ $duty->name }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 max-w-xs">
                            <div class="line-clamp-2" title="{{ $duty->description }}">
                                {{ $duty->description ?? '-' }}
                            </div>
                        </td>
                        
                        <!-- KOLOM MIN / MAX -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ $duty->min_person ?? 1 }}
                                <span class="text-gray-400"> /</span>
                                {{ $duty->max_person ?? '∞' }}
                            </span>
                        </td>
                        
                        <!-- STATUS -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $duty->status_badge }}">
                                <i class="fas {{ $duty->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1.5"></i>
                                {{ $duty->status_text }}
                            </span>
                        </td>
                        
                        <!-- AKSI -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if($hasAccess)
                            <div class="flex items-center justify-center space-x-1.5">
                                <a href="{{ route('duties.edit', $duty) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 rounded-lg transition duration-200"
                                   title="Edit Tugas">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <button type="button" 
                                        onclick="confirmDelete({{ $duty->id }}, '{{ $duty->name }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 rounded-lg transition duration-200"
                                        title="Hapus Tugas">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                                <form id="delete-form-{{ $duty->id }}" 
                                      action="{{ route('duties.destroy', $duty) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-tasks text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-600">Belum ada data tugas pelayanan</p>
                                <p class="text-sm text-gray-400 mt-1">
                                    @if($isPicGroup && $user->duty)
                                        Tidak ada tugas yang terkait dengan duty group {{ $user->duty->name }}
                                    @else
                                        Silakan tambah tugas pelayanan terlebih dahulu
                                    @endif
                                </p>
                                @if($hasAccess)
                                <a href="{{ route('duties.create') }}" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg transition duration-200 inline-flex items-center shadow-sm">
                                    <i class="fas fa-plus mr-2"></i> Tambah Tugas Pertama
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan <span class="font-medium">{{ $duties->firstItem() ?? 0 }}</span> - 
                    <span class="font-medium">{{ $duties->lastItem() ?? 0 }}</span> 
                    dari <span class="font-medium">{{ $duties->total() }}</span> data
                    @if($isPicGroup && $user->duty)
                    <span class="text-purple-500 ml-2">
                        <i class="fas fa-filter"></i> Filter: {{ $user->duty->name }}
                    </span>
                    @endif
                </div>
                @if($duties->hasPages())
                <div>
                    {{ $duties->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
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
        <form action="{{ route('duties.export.filtered') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" name="search" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="Cari berdasarkan kode atau nama...">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                <select name="format" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="xlsx">Excel (.xlsx)</option>
                    <option value="csv">CSV (.csv)</option>
                </select>
            </div>
            @if($isPicGroup && $user->duty_id)
            <input type="hidden" name="duty_id" value="{{ $user->duty_id }}">
            @endif
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeExportFilterModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus tugas <strong id="deleteName" class="text-red-600"></strong>?
                </p>
                <p class="text-xs text-red-500 mt-2">
                    <i class="fas fa-warning mr-1"></i> Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="flex justify-center gap-3 mt-6">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-trash mr-1"></i> Ya, Hapus!
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
    .transition {
        transition: all 0.2s ease;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection