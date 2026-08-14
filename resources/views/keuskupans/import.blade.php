@extends('layouts.app')

@section('title', 'Import Data Keuskupan')
@section('page-title', 'Import Data Keuskupan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-upload mr-2"></i> Import Data Keuskupan
        </h1>
        <a href="{{ route('keuskupans.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

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
                                <br>
                                <small class="text-gray-600">Data: {{ json_encode($failure->values()) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    <div>
                        <p class="font-bold">Petunjuk Import Data:</p>
                        <ul class="list-disc list-inside mt-2 text-sm">
                            <li>File harus berformat .xlsx, .xls, atau .csv</li>
                            <li>Ukuran file maksimal 5MB</li>
                            <li>Kolom yang wajib diisi: <strong>kode</strong> dan <strong>nama_keuskupan</strong></li>
                            <li>Kode keuskupan harus unik (tidak boleh sama dengan data yang sudah ada)</li>
                            <li>Status: "Aktif" atau "Nonaktif" (atau 1/0)</li>
                            <li>Download template untuk melihat format yang benar</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <a href="{{ route('keuskupans.template') }}" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-download mr-2"></i> Download Template Import
                </a>
            </div>

            <form action="{{ route('keuskupans.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        File Excel/CSV <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">
                                    XLSX, XLS, atau CSV (MAX. 5MB)
                                </p>
                            </div>
                            <input type="file" name="file" id="file" class="hidden" accept=".xlsx,.xls,.csv" required>
                        </label>
                    </div>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('keuskupans.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-upload mr-2"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Menampilkan nama file yang dipilih
    document.getElementById('file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const label = document.querySelector('label[for="file"]');
            label.innerHTML = `
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <i class="fas fa-file-excel text-3xl text-green-500 mb-2"></i>
                    <p class="mb-2 text-sm text-gray-700 font-semibold">${fileName}</p>
                    <p class="text-xs text-gray-500">Klik untuk mengganti file</p>
                </div>
            `;
        }
    });
</script>
@endpush
@endsection