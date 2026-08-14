@extends('layouts.app')

@section('title', 'Export Data Gereja')
@section('page-title', 'Export Data Gereja')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-download mr-2"></i> Export Data Gereja
        </h1>
        <a href="{{ route('gerejas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <i class="fas fa-database text-4xl text-blue-500 mb-3"></i>
            <h3 class="font-semibold text-lg mb-2">Export Semua Data</h3>
            <p class="text-gray-600 text-sm mb-4">Export seluruh data gereja</p>
            <a href="{{ route('gerejas.export.all') }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-download mr-2"></i> Export All
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <i class="fas fa-filter text-4xl text-green-500 mb-3"></i>
            <h3 class="font-semibold text-lg mb-2">Export dengan Filter</h3>
            <p class="text-gray-600 text-sm mb-4">Export data berdasarkan filter</p>
            <button onclick="showFilterModal()" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-sliders-h mr-2"></i> Filter & Export
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <i class="fas fa-file-excel text-4xl text-yellow-500 mb-3"></i>
            <h3 class="font-semibold text-lg mb-2">Download Template</h3>
            <p class="text-gray-600 text-sm mb-4">Template untuk import data</p>
            <a href="{{ route('gerejas.template') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-download mr-2"></i> Download Template
            </a>
        </div>
    </div>

    <!-- Preview Data -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-lg">Preview Data Gereja</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nama Gereja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Keuskupan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gerejas as $index => $gereja)
                    <tr>
                        <td class="px-6 py-4">{{ $gerejas->firstItem() + $index }}</td>
                        <td class="px-6 py-4">{{ $gereja->kode }}</td>
                        <td class="px-6 py-4">{{ $gereja->nama }}</td>
                        <td class="px-6 py-4">{{ $gereja->keuskupan->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $gereja->lokasi }}</td>
                        <td class="px-6 py-4">
                            @if($gereja->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">Belum ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($gerejas->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $gerejas->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Filter -->
<div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Filter Data</h3>
        <form action="{{ route('gerejas.export.filtered') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Keuskupan</label>
                <select name="keuskupan_id" class="w-full border rounded-lg p-2">
                    <option value="">Semua Keuskupan</option>
                    @foreach($keuskupans as $keuskupan)
                        <option value="{{ $keuskupan->id }}">{{ $keuskupan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Pencarian</label>
                <input type="text" name="search" placeholder="Cari nama/kode/lokasi..." class="w-full border rounded-lg p-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full border rounded-lg p-2">
                    <option value="">Semua</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Format</label>
                <select name="format" class="w-full border rounded-lg p-2">
                    <option value="xlsx">Excel (.xlsx)</option>
                    <option value="csv">CSV (.csv)</option>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeFilterModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Export</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showFilterModal() {
        document.getElementById('filterModal').classList.remove('hidden');
    }
    function closeFilterModal() {
        document.getElementById('filterModal').classList.add('hidden');
    }
</script>
@endsection