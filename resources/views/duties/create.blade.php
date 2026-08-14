@extends('layouts.app')

@section('title', 'Tambah Tugas Pelayanan')
@section('page-title', 'Tambah Tugas Pelayanan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-plus-circle mr-2 text-blue-500"></i> Tambah Tugas Pelayanan
        </h1>
        <a href="{{ route('duties.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <form action="{{ route('duties.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Tugas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-barcode mr-1 text-gray-400"></i> Kode Tugas
                    </label>
                    <input type="text" 
                           name="code" 
                           value="{{ old('code', $generatedCode ?? '') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                           placeholder="AUTO GENERATED">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Biarkan kosong untuk generate otomatis
                    </p>
                    @error('code') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Nama Tugas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1 text-gray-400"></i> Nama Tugas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Contoh: Lektor, Pemazmur, Misdinar"
                           required>
                    @error('name') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-1 text-gray-400"></i> Deskripsi
                </label>
                <textarea name="description" 
                          rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                          placeholder="Jelaskan tugas dan tanggung jawab pelayanan ini...">{{ old('description') }}</textarea>
                @error('description') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ===== TAMBAHKAN: MIN & MAX PERSON ===== -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-1 text-gray-400"></i> Jumlah Minimum Petugas <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="min_person" 
                           value="{{ old('min_person', 1) }}"
                           min="0"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('min_person') border-red-500 @enderror"
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Jumlah minimum petugas yang dibutuhkan
                    </p>
                    @error('min_person') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-users mr-1 text-gray-400"></i> Jumlah Maksimum Petugas
                    </label>
                    <input type="number" 
                           name="max_person" 
                           value="{{ old('max_person') }}"
                           min="0"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('max_person') border-red-500 @enderror"
                           placeholder="Kosongkan jika tidak ada batas">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i> Jumlah maksimum petugas (kosongkan jika tidak ada batas)
                    </p>
                    @error('max_person') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-power-off mr-1 text-gray-400"></i> Status
                </label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1" class="form-radio text-blue-500" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i> Aktif
                        </span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0" class="form-radio text-red-500" {{ old('is_active') == '0' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-times-circle text-red-500 mr-1"></i> Nonaktif
                        </span>
                    </label>
                </div>
                @error('is_active') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Informasi Tambahan -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">Tips Pengisian:</h4>
                        <ul class="text-xs text-blue-700 mt-1 space-y-1">
                            <li>• Gunakan nama tugas yang jelas dan mudah dipahami</li>
                            <li>• <strong>Minimum Petugas:</strong> Jumlah minimal yang dibutuhkan untuk tugas ini</li>
                            <li>• <strong>Maksimum Petugas:</strong> Jumlah maksimal (kosongkan jika tidak ada batas)</li>
                            <li>• Status akan otomatis berubah: <span class="bg-yellow-200 px-1">Membutuhkan Petugas</span> → <span class="bg-green-200 px-1">Cukup</span> → <span class="bg-red-200 px-1">Full</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('duties.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200">
                    <i class="fas fa-save mr-2"></i> Simpan Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection