@extends('layouts.app')

@section('title', 'Import Tugas Pelayanan')
@section('page-title', 'Import Tugas Pelayanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-file-import mr-2 text-yellow-500"></i> Import Tugas Pelayanan
        </h1>
        <a href="{{ route('duties.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
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
                                        <span class="font-medium">Baris {{ $failure->row() }}:</span>
                                        @foreach($failure->errors() as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('duties.import') }}" method="POST" enctype="multipart/form-data">
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
                                        Format: .xlsx, .xls, .csv (Maks: 5MB)
                                    </p>
                                </label>
                            </div>
                            @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                <i class="fas fa-upload mr-2"></i> Upload & Import
                            </button>
                            <a href="{{ route('duties.template') }}" 
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
                            <li><span class="text-red-500">*</span> <strong>nama</strong>: Nama tugas (wajib)</li>
                            <li><strong>kode</strong>: Kode tugas (opsional)</li>
                            <li><strong>deskripsi</strong>: Deskripsi tugas (opsional)</li>
                            <li><strong>min_person</strong>: Minimum petugas (default: 1)</li>
                            <li><strong>max_person</strong>: Maksimum petugas (opsional)</li>
                            <li><strong>status</strong>: Aktif/Nonaktif (default: Aktif)</li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">3. Upload</h3>
                        <p class="text-sm text-gray-600">Upload file dan sistem akan memproses semua data sekaligus.</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>Tips:</strong> Pastikan semua data valid sebelum upload.
                        </p>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <p class="text-sm text-green-700">
                            <i class="fas fa-check-circle mr-1"></i>
                            <strong>Keunggulan:</strong> Bisa import puluhan tugas sekaligus!
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
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-yellow-100">kode</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase bg-yellow-100">nama</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">deskripsi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">min_person</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">max_person</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 text-sm">DUTY001</td>
                        <td class="px-4 py-2 text-sm font-medium">Lektor</td>
                        <td class="px-4 py-2 text-sm">Membacakan bacaan pertama, kedua, dan doa umat</td>
                        <td class="px-4 py-2 text-sm">2</td>
                        <td class="px-4 py-2 text-sm">4</td>
                        <td class="px-4 py-2 text-sm"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm">DUTY002</td>
                        <td class="px-4 py-2 text-sm font-medium">Pemazmur</td>
                        <td class="px-4 py-2 text-sm">Membawakan mazmur tanggapan</td>
                        <td class="px-4 py-2 text-sm">1</td>
                        <td class="px-4 py-2 text-sm">2</td>
                        <td class="px-4 py-2 text-sm"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 text-sm">DUTY003</td>
                        <td class="px-4 py-2 text-sm font-medium">Misdinar</td>
                        <td class="px-4 py-2 text-sm">Membantu imam selama perayaan Misa</td>
                        <td class="px-4 py-2 text-sm">3</td>
                        <td class="px-4 py-2 text-sm">6</td>
                        <td class="px-4 py-2 text-sm"><span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Aktif</span></td>
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

    if (dropZone) {
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
    }
</script>
@endpush
@endsection