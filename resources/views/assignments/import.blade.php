@extends('layouts.app')

@section('title', 'Import Penugasan')
@section('page-title', 'Import Penugasan Massal')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-file-import mr-2 text-yellow-500"></i> Import Penugasan Massal
        </h1>
        <a href="{{ route('assignments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Import -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-600 to-orange-700">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-upload mr-2"></i> Upload File Excel
                    </h2>
                </div>
                <div class="p-6">
                    @if(session('import_failures'))
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                            <h4 class="text-red-700 font-bold mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i> 
                                {{ count(session('import_failures')) }} Data Gagal Diimport
                            </h4>
                            <div class="max-h-60 overflow-y-auto">
                                @foreach(session('import_failures') as $failure)
                                    <div class="text-sm text-red-600 border-b border-red-100 py-1">
                                        <span class="font-medium">Baris:</span>
                                        @if(is_array($failure['row']))
                                            {{ json_encode($failure['row']) }}
                                        @else
                                            {{ $failure['row'] ?? 'N/A' }}
                                        @endif
                                        <br>
                                        <span class="font-medium">Error:</span> {{ $failure['errors'] ?? 'Unknown error' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('assignments.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-file mr-1 text-gray-400"></i> Pilih File Excel/CSV <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-500 transition">
                                <input type="file" name="file" id="file" 
                                       class="hidden" 
                                       accept=".xlsx,.xls,.csv" 
                                       onchange="document.getElementById('fileLabel').innerHTML = this.files[0].name">
                                <label for="file" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-600" id="fileLabel">
                                        <span class="text-yellow-500 font-semibold">Klik untuk pilih file</span> atau drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Format: .xlsx, .xls, .csv (Maks: 10MB)
                                    </p>
                                </label>
                            </div>
                            @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-upload mr-2"></i> Upload & Import
                            </button>
                            <a href="{{ route('assignments.template') }}" 
                               class="w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-download mr-2"></i> Download Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Petunjuk -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-700">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-info-circle mr-2"></i> Petunjuk
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">1. Download Template</h3>
                        <p class="text-sm text-gray-600">Gunakan template yang sudah disediakan untuk format yang benar.</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">2. Isi Data</h3>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li><span class="text-red-500">*</span> <strong>Tanggal Penugasan</strong>: format <code>d/m/Y</code></li>
                            <li><span class="text-red-500">*</span> <strong>Jadwal Ibadah</strong>: nama jadwal di database</li>
                            <li><span class="text-red-500">*</span> <strong>Tugas Pelayanan</strong>: nama tugas di database</li>
                            <li><span class="text-red-500">*</span> <strong>Email Petugas</strong>: email terdaftar di database</li>
                            <li><strong>Catatan</strong>: opsional</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">3. Upload</h3>
                        <p class="text-sm text-gray-600">Upload file dan sistem akan memproses semua data sekaligus.</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Tips:</strong> Pastikan semua data valid sebelum upload. Data yang error akan ditampilkan di halaman ini.
                        </p>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <p class="text-sm text-green-700">
                            <i class="fas fa-check-circle mr-1"></i>
                            <strong>Keunggulan:</strong> Bisa import puluhan bahkan ratusan penugasan sekaligus!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Template -->
    <div class="mt-6 bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-600 to-gray-800">
            <h2 class="text-xl font-bold text-white">
                <i class="fas fa-table mr-2"></i> Contoh Format Template
            </h2>
        </div>
        <div class="overflow-x-auto p-4">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-yellow-100">Tanggal Penugasan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-yellow-100">Jadwal Ibadah</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-yellow-100">Tugas Pelayanan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-yellow-100">Email Petugas</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-sm">25/12/2024</td>
                        <td class="px-4 py-2 text-sm">Misa Sabtu Sore</td>
                        <td class="px-4 py-2 text-sm">Misdinar</td>
                        <td class="px-4 py-2 text-sm">jhonny@gmail.com</td>
                        <td class="px-4 py-2 text-sm">Penugasan Natal</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm">25/12/2024</td>
                        <td class="px-4 py-2 text-sm">Misa Sabtu Sore</td>
                        <td class="px-4 py-2 text-sm">Khotbah</td>
                        <td class="px-4 py-2 text-sm">budi@gmail.com</td>
                        <td class="px-4 py-2 text-sm">-</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm">01/01/2025</td>
                        <td class="px-4 py-2 text-sm">Misa Minggu Pagi I</td>
                        <td class="px-4 py-2 text-sm">Lektor</td>
                        <td class="px-4 py-2 text-sm">siti@gmail.com</td>
                        <td class="px-4 py-2 text-sm">Tahun Baru</td>
                    </tr>
                </tbody>
            </table>
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Kolom dengan latar <span class="bg-yellow-100 px-1">kuning</span> adalah kolom wajib diisi
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Drag and drop
    const dropZone = document.querySelector('.border-dashed');
    const fileInput = document.getElementById('file');
    const fileLabel = document.getElementById('fileLabel');

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-yellow-500', 'bg-yellow-50');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-yellow-500', 'bg-yellow-50');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-yellow-500', 'bg-yellow-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileLabel.innerHTML = files[0].name;
        }
    });
</script>
@endpush
@endsection