{{-- resources/views/kalender-liturgi/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Kalender Liturgi')
@section('page-title', 'Edit Kalender Liturgi Hari')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2 text-yellow-600"></i> Edit Kalender Liturgi
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('kalender-liturgi.show', $kalender->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-eye mr-2"></i> Lihat Detail
            </a>
            <a href="{{ route('kalender-liturgi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <div class="font-bold">Terjadi kesalahan:</div>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <form action="{{ route('kalender-liturgi.update', $kalender->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-day mr-1 text-purple-600"></i> Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('tanggal') border-red-500 @enderror" 
                               value="{{ old('tanggal', $kalender->tanggal->format('Y-m-d')) }}" required>
                        @error('tanggal')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan Hari -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag mr-1 text-blue-600"></i> Keterangan Hari <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="keterangan_hari" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('keterangan_hari') border-red-500 @enderror" 
                               placeholder="Contoh: Hari Minggu Biasa, Hari Raya Natal" 
                               value="{{ old('keterangan_hari', $kalender->keterangan_hari) }}" required>
                        @error('keterangan_hari')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Warna Liturgi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-palette mr-1 text-pink-600"></i> Warna Liturgi <span class="text-red-500">*</span>
                        </label>
                        <select name="warna_liturgi" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('warna_liturgi') border-red-500 @enderror" required>
                            <option value="">Pilih Warna Liturgi</option>
                            @foreach($warnaList as $warna)
                                <option value="{{ $warna }}" {{ old('warna_liturgi', $kalender->warna_liturgi) == $warna ? 'selected' : '' }}>
                                    {{ ucfirst($warna) }}
                                </option>
                            @endforeach
                        </select>
                        @error('warna_liturgi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Preview Warna -->
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-sm text-gray-600">Preview:</span>
                            <span id="colorPreview" class="px-3 py-1 rounded-full text-sm font-medium {{ $kalender->warna_liturgi_badge }}">
                                {{ ucfirst($kalender->warna_liturgi) }}
                            </span>
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on mr-1 text-green-600"></i> Status
                        </label>
                        <div class="flex items-center space-x-4 pt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_active" value="1" 
                                       class="form-radio text-purple-600" {{ old('is_active', $kalender->is_active) == '1' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-check-circle text-green-600"></i> Aktif
                                </span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_active" value="0" 
                                       class="form-radio text-red-600" {{ old('is_active', $kalender->is_active) == '0' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-times-circle text-red-600"></i> Tidak Aktif
                                </span>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bacaan Liturgi -->
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-bible mr-2 text-red-600"></i> Bacaan Liturgi
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Bacaan 1 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-book mr-1 text-blue-600"></i> Bacaan 1
                            </label>
                            <textarea name="bacaan1" rows="2" 
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('bacaan1') border-red-500 @enderror" 
                                      placeholder="Contoh: 1 Kor 1:1-9">{{ old('bacaan1', $kalender->bacaan1) }}</textarea>
                            @error('bacaan1')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mazmur Tanggapan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-music mr-1 text-green-600"></i> Mazmur Tanggapan
                            </label>
                            <textarea name="mazmur_tanggapan" rows="2" 
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('mazmur_tanggapan') border-red-500 @enderror" 
                                      placeholder="Contoh: Mzm 1:1-2,3,4,6">{{ old('mazmur_tanggapan', $kalender->mazmur_tanggapan) }}</textarea>
                            @error('mazmur_tanggapan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bait Pengantar Injil -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hands-praying mr-1 text-purple-600"></i> Bait Pengantar Injil
                            </label>
                            <textarea name="bait_pengantarinjil" rows="2" 
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('bait_pengantarinjil') border-red-500 @enderror" 
                                      placeholder="Contoh: Alleluia, Alleluia...">{{ old('bait_pengantarinjil', $kalender->bait_pengantarinjil) }}</textarea>
                            @error('bait_pengantarinjil')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bacaan Injil -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cross mr-1 text-red-600"></i> Bacaan Injil
                            </label>
                            <textarea name="bacaan_injil" rows="2" 
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('bacaan_injil') border-red-500 @enderror" 
                                      placeholder="Contoh: Mat 4:12-17">{{ old('bacaan_injil', $kalender->bacaan_injil) }}</textarea>
                            @error('bacaan_injil')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-1 text-yellow-600"></i> Catatan
                            </label>
                            <textarea name="catatan" rows="2" 
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('catatan') border-red-500 @enderror" 
                                      placeholder="Catatan tambahan...">{{ old('catatan', $kalender->catatan) }}</textarea>
                            @error('catatan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Sistem -->
                <div class="mt-8 border-t pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Dibuat Pada:</span>
                            {{ $kalender->created_at->format('d F Y H:i:s') }}
                        </div>
                        <div>
                            <span class="font-medium">Terakhir Diperbarui:</span>
                            {{ $kalender->updated_at->format('d F Y H:i:s') }}
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="mt-8 border-t pt-6 flex justify-end gap-3">
                    <a href="{{ route('kalender-liturgi.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i> Perbarui
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Preview warna liturgi
    document.querySelector('select[name="warna_liturgi"]').addEventListener('change', function() {
        const preview = document.getElementById('colorPreview');
        const selectedColor = this.value;
        
        if (selectedColor) {
            const colorMap = {
                'putih': 'bg-white text-gray-800 border border-gray-300',
                'merah': 'bg-red-600 text-white',
                'ungu': 'bg-purple-600 text-white',
                'hijau': 'bg-green-600 text-white',
                'kuning': 'bg-yellow-500 text-white',
                'hitam': 'bg-gray-800 text-white',
                'pink': 'bg-pink-500 text-white',
                'biru': 'bg-blue-600 text-white'
            };
            
            preview.className = 'px-3 py-1 rounded-full text-sm font-medium ' + (colorMap[selectedColor] || 'bg-gray-200 text-gray-700');
            preview.textContent = selectedColor.charAt(0).toUpperCase() + selectedColor.slice(1);
        } else {
            preview.className = 'px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-700';
            preview.textContent = 'Belum dipilih';
        }
    });
</script>
@endpush
@endsection