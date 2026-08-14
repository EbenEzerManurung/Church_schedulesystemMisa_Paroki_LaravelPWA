{{-- resources/views/availability/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Ketersediaan')
@section('page-title', 'Edit Ketersediaan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2 text-yellow-500"></i> Edit Ketersediaan
        </h1>
        <a href="{{ route('availability.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-info-circle mr-2"></i> Informasi Penugasan
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-500">Tanggal Penugasan</label>
                    <p class="font-semibold text-gray-800">
                        {{ $assignment->event_date ? \Carbon\Carbon::parse($assignment->event_date)->format('d F Y') : '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-500">Jadwal</label>
                    <p class="font-semibold text-gray-800">
                        {{ $assignment->schedule->display ?? $assignment->schedule->name ?? '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-500">Tugas</label>
                    <p class="font-semibold text-gray-800">
                        {{ $assignment->duty->name ?? '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-500">Status Saat Ini</label>
                    <p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $assignment->status_badge }}">
                            <i class="fas {{ $assignment->status_icon }} mr-1"></i>
                            {{ $assignment->status_label }}
                        </span>
                        @if($assignment->availability_status == 'available')
                            <span class="ml-2 text-xs text-green-600 font-medium">
                                <i class="fas fa-check-circle"></i> Bersedia
                            </span>
                        @elseif($assignment->availability_status == 'unavailable')
                            <span class="ml-2 text-xs text-red-600 font-medium">
                                <i class="fas fa-times-circle"></i> Tidak Bersedia
                            </span>
                        @else
                            <span class="ml-2 text-xs text-yellow-600 font-medium">
                                <i class="fas fa-clock"></i> Belum Konfirmasi
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Informasi Status -->
            @if($assignment->status == 'accepted')
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda saat ini <strong>BERSEDIA</strong> untuk tugas ini. Anda dapat mengubah pilihan di bawah.
                    </p>
                </div>
            @elseif($assignment->status == 'rejected')
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda saat ini <strong>TIDAK BERSEDIA</strong> untuk tugas ini. Anda dapat mengubah pilihan di bawah.
                    </p>
                    @if($assignment->unavailable_reason)
                        <p class="text-sm text-red-600 mt-1">
                            <strong>Alasan sebelumnya:</strong> {{ $assignment->unavailable_reason }}
                        </p>
                    @endif
                </div>
            @else
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda saat ini <strong>BELUM KONFIRMASI</strong> untuk tugas ini. Silakan pilih status di bawah.
                    </p>
                </div>
            @endif

            <form action="{{ route('availability.update', $assignment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Status Ketersediaan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-circle mr-1 text-gray-400"></i> Status Ketersediaan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Pilihan 1: Bersedia -->
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition 
                                   {{ old('availability_status', $assignment->availability_status) == 'available' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                                <input type="radio" name="availability_status" value="available" 
                                       class="h-4 w-4 text-green-600 focus:ring-green-500"
                                       {{ old('availability_status', $assignment->availability_status) == 'available' ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i> Bersedia
                                    </span>
                                    <span class="text-xs text-gray-500">Saya bersedia untuk tugas ini</span>
                                </div>
                            </label>

                            <!-- Pilihan 2: Tidak Bersedia -->
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition 
                                   {{ old('availability_status', $assignment->availability_status) == 'unavailable' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                                <input type="radio" name="availability_status" value="unavailable" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500"
                                       {{ old('availability_status', $assignment->availability_status) == 'unavailable' ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-red-700">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Bersedia
                                    </span>
                                    <span class="text-xs text-gray-500">Saya tidak bersedia untuk tugas ini</span>
                                </div>
                            </label>

                            <!-- Pilihan 3: Menunggu / Belum Konfirmasi -->
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition 
                                   {{ old('availability_status', $assignment->availability_status) == 'pending' || old('availability_status', $assignment->availability_status) == null ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200' }}">
                                <input type="radio" name="availability_status" value="pending" 
                                       class="h-4 w-4 text-yellow-600 focus:ring-yellow-500"
                                       {{ old('availability_status', $assignment->availability_status) == 'pending' || old('availability_status', $assignment->availability_status) == null ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-yellow-700">
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    </span>
                                    <span class="text-xs text-gray-500">Belum memutuskan (pending)</span>
                                </div>
                            </label>
                        </div>
                        @error('availability_status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pilih "Menunggu" jika Anda belum bisa memutuskan. Admin akan menunggu konfirmasi Anda.
                        </p>
                    </div>

                    <!-- Alasan Tidak Bersedia -->
                    <div id="unavailableReason" class="{{ old('availability_status', $assignment->availability_status) == 'unavailable' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-comment mr-1 text-gray-400"></i> Alasan Tidak Bersedia <span class="text-red-500">*</span>
                        </label>
                        <textarea name="unavailable_reason" rows="3" 
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('unavailable_reason') border-red-500 @enderror"
                                  placeholder="Jelaskan alasan mengapa Anda tidak bersedia...">{{ old('unavailable_reason', $assignment->unavailable_reason) }}</textarea>
                        @error('unavailable_reason')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-1 text-gray-400"></i> Catatan Tambahan
                        </label>
                        <textarea name="notes" rows="2" 
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes', $assignment->notes) }}</textarea>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Perubahan status:</strong>
                        </p>
                        <ul class="text-xs text-blue-600 mt-1 space-y-0.5 ml-4 list-disc">
                            <li><strong>Bersedia</strong> → Anda menerima tugas ini</li>
                            <li><strong>Tidak Bersedia</strong> → Anda menolak tugas ini (harus isi alasan)</li>
                            <li><strong>Menunggu</strong> → Kembali ke status pending, belum ada keputusan</li>
                        </ul>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('availability.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="availability_status"]');
    const unavailableReason = document.getElementById('unavailableReason');
    
    function toggleUnavailableReason() {
        const selected = document.querySelector('input[name="availability_status"]:checked');
        if (selected && selected.value === 'unavailable') {
            unavailableReason.classList.remove('hidden');
        } else {
            unavailableReason.classList.add('hidden');
        }
    }
    
    radioButtons.forEach(function(radio) {
        radio.addEventListener('change', toggleUnavailableReason);
    });
    
    // Initial state
    toggleUnavailableReason();
});
</script>
@endpush
@endsection