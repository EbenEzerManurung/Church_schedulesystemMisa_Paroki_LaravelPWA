{{-- resources/views/keuskupans/gerejas.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Gereja')
@section('page-title', 'Daftar Gereja - ' . $keuskupan->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-church mr-2"></i> Daftar Gereja
            <span class="text-lg text-gray-600 ml-2">({{ $keuskupan->name }})</span>
        </h1>
        <div class="flex space-x-3">
            {{-- PERBAIKAN: route('gerejas.create') bukan route('gereja.create') --}}
            <a href="{{ route('gerejas.create', ['keuskupan_id' => $keuskupan->id]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Tambah Gereja
            </a>
            <a href="{{ route('keuskupans.show', $keuskupan->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($gerejas->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Gereja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pastor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Umat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($gerejas as $index => $gereja)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gerejas->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $gereja->kode }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $gereja->nama }}</div>
                            @if($gereja->email)
                                <div class="text-xs text-gray-500">{{ $gereja->email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gereja->lokasi }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $gereja->pastor ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ number_format($gereja->jumlah_umat) }} umat
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($gereja->is_active)
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
                                <a href="{{ route('gerejas.edit', $gereja->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 p-2 rounded transition"
                                   title="Edit Gereja">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        onclick="confirmDelete({{ $gereja->id }}, '{{ $gereja->nama }}')"
                                        class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-2 rounded transition"
                                        title="Hapus Gereja">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $gereja->id }}" 
                                      action="{{ route('gerejas.destroy', $gereja->id) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $gerejas->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <div class="flex flex-col items-center">
                <i class="fas fa-church fa-4x mb-4 text-gray-300"></i>
                <p class="text-lg text-gray-500">Belum ada gereja di keuskupan ini</p>
                <a href="{{ route('gerejas.create', ['keuskupan_id' => $keuskupan->id]) }}" 
                   class="mt-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Gereja Pertama
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if(confirm('Apakah Anda yakin ingin menghapus gereja "' + name + '"?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>

<style>
.transition {
    transition: all 0.2s ease;
}
</style>
@endsection