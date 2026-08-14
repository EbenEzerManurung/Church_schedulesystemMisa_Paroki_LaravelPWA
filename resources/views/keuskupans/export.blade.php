@extends('layouts.app')

@section('title', 'Export Data Keuskupan')
@section('page-title', 'Export Data Keuskupan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-download mr-2"></i> Export Data Keuskupan
        </h1>
        <a href="{{ route('keuskupans.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Pilihan Export -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Pilihan Export</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Export Semua Data -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                        <div class="text-center">
                            <i class="fas fa-database text-4xl text-blue-500 mb-3"></i>
                            <h3 class="font-semibold text-gray-800 mb-2">Export Semua Data</h3>
                            <p class="text-sm text-gray-600 mb-4">Export seluruh data keuskupan yang tersedia</p>
                            <form action="{{ route('keuskupans.export-all') }}" method="GET">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full">
                                    <i class="fas fa-download mr-2"></i> Export All
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Export dengan Filter -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                        <div class="text-center">
                            <i class="fas fa-filter text-4xl text-green-500 mb-3"></i>
                            <h3 class="font-semibold text-gray-800 mb-2">Export dengan Filter</h3>
                            <p class="text-sm text-gray-600 mb-4">Export data berdasarkan filter tertentu</p>
                            <button onclick="showFilterModal()" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg w-full">
                                <i class="fas fa-sliders-h mr-2"></i> Filter & Export
                            </button>
                        </div>
                    </div>

                    <!-- Export Template Kosong -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                        <div class="text-center">
                            <i class="fas fa-file-excel text-4xl text-yellow-500 mb-3"></i>
                            <h3 class="font-semibold text-gray-800 mb-2">Download Template</h3>
                            <p class="text-sm text-gray-600 mb-4">Download template Excel untuk import data</p>
                            <a href="{{ route('keuskupans.template') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg inline-block w-full text-center">
                                <i class="fas fa-download mr-2"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Data yang Akan Diexport -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Preview Data Keuskapan</h2>
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
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($keuskupans ?? [] as $index => $keuskupan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $keuskupan->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $keuskupan->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $keuskupan->email ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $keuskupan->phone ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $keuskupan->gerejas_count ?? 0 }} Gereja
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($keuskupan->is_active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-database fa-3x mb-3 text-gray-300"></i>
                                    <p>Belum ada data keuskupan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(isset($keuskupans) && method_exists($keuskupans, 'links'))
                <div class="mt-4">
                    {{ $keuskupans->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Data Sebelum Export</h3>
            <form id="filterForm" action="{{ route('keuskupans.export-filtered') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="filter_search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           name="search" 
                           id="filter_search" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Cari berdasarkan nama atau kode...">
                </div>
                
                <div class="mb-4">
                    <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="filter_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="filter_format" class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                    <select name="format" id="filter_format" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="xlsx">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeFilterModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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

@push('scripts')
<script>
    function showFilterModal() {
        document.getElementById('filterModal').classList.remove('hidden');
    }
    
    function closeFilterModal() {
        document.getElementById('filterModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('filterModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFilterModal();
        }
    });
</script>
@endpush
@endsection