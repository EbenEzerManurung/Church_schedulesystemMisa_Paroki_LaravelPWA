@extends('layouts.app')

@section('title', 'Daftar Keuskupan')
@section('page-title', 'Manajemen Keuskupan')

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
                    <a href="{{ route('keuskupans.export.all') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-file-excel mr-2"></i> Export Semua Data
                    </a>
                    <button onclick="showExportFilter()" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-filter mr-2"></i> Export Hasil Filter
                    </button>
                </div>
            </div>
            
            <!-- Tombol Import -->
            <a href="{{ route('keuskupans.import.form') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-upload mr-2"></i> Import
            </a>
            
            <!-- Tombol Tambah -->
            <a href="{{ route('keuskupans.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i> Tambah Keuskupan
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
                    <ul class="list-disc list-inside mt-2">
                        @foreach(session('import_failures') as $failure)
                            <li>
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

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Keuskupan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Gereja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($keuskupans as $index => $keuskupan)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keuskupans->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $keuskupan->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $keuskupan->name }}</div>
                            @if($keuskupan->description)
                                <div class="text-xs text-gray-500">{{ Str::limit($keuskupan->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keuskupan->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $keuskupan->phone ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $keuskupan->gerejas_count ?? $keuskupan->churches_count ?? 0 }} Gereja
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($keuskupan->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <!-- Tombol Lihat Detail -->
                                <a href="{{ route('keuskupans.show', $keuskupan->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-2 rounded transition duration-200"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Tombol Edit -->
                                <a href="{{ route('keuskupans.edit', $keuskupan->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 p-2 rounded transition duration-200"
                                   title="Edit Keuskupan">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Tombol Lihat Gereja -->
                                <a href="{{ route('keuskupans.gerejas', $keuskupan->id) }}" 
                                   class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 p-2 rounded transition duration-200"
                                   title="Lihat Gereja">
                                    <i class="fas fa-church"></i>
                                </a>
                                
                                <!-- Tombol Hapus -->
                                @if(auth()->user()->isSuperAdmin())
                                <button type="button" 
                                        onclick="confirmDelete({{ $keuskupan->id }}, '{{ $keuskupan->name }}')"
                                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-2 rounded transition duration-200"
                                        title="Hapus Keuskupan">
                                    <i class="fas fa-trash"></i>
                                </button>
                                
                                <!-- Form Delete (Hidden) -->
                                <form id="delete-form-{{ $keuskupan->id }}" 
                                      action="{{ route('keuskupans.destroy', $keuskupan->id) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-church fa-4x mb-4 text-gray-300"></i>
                                <p class="text-lg">Belum ada data keuskupan</p>
                                <a href="{{ route('keuskupans.create') }}" class="mt-4 text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-plus mr-1"></i> Tambah Keuskupan Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($keuskupans) && $keuskupans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $keuskupans->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Export Filter -->
<div id="exportFilterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Export Data dengan Filter</h3>
            <form id="exportFilterForm" action="{{ route('keuskupans.export.filtered') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="filter_search" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Cari berdasarkan nama atau kode...">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="filter_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                    <select name="format" id="filter_format" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="xlsx">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeExportFilterModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                </div>
            </form>
        </div>
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
                    Apakah Anda yakin ingin menghapus keuskupan <strong id="deleteName"></strong>?
                </p>
                <p class="text-xs text-red-500 mt-2">
                    <i class="fas fa-warning"></i> Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            <div class="flex justify-center gap-3 mt-4">
                <button onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                    Batal
                </button>
                <button id="confirmDeleteBtn" 
                        class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-700 transition duration-200">
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
    
    // Close modal when clicking outside
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
</style>
@endpush
@endsection