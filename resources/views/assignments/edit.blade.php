@extends('layouts.app')

@section('title', 'Edit Penugasan')
@section('page-title', 'Edit Penugasan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit mr-2 text-yellow-500"></i> Edit Penugasan
        </h1>
        <a href="{{ route('assignments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <form action="{{ route('assignments.update', $assignment) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jadwal Ibadah (Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1 text-gray-400"></i> Jadwal Ibadah
                    </label>
                    <input type="text" class="w-full rounded-lg border-gray-300 bg-gray-100 py-2 px-3" 
                           value="{{ $assignment->schedule->display ?? $assignment->schedule->name ?? 'Jadwal tidak ditemukan' }}" readonly disabled>
                </div>

                <!-- ===== TAMBAHKAN: TANGGAL PENUGASAN (Readonly) ===== -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-1 text-gray-400"></i> Tanggal Penugasan
                    </label>
                    <input type="date" name="event_date" 
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('event_date') border-red-500 @enderror" 
                           value="{{ old('event_date', $assignment->event_date ? $assignment->event_date->format('Y-m-d') : '') }}" required>
                    @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Tugas Pelayanan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tasks mr-1 text-gray-400"></i> Tugas Pelayanan <span class="text-red-500">*</span>
                    </label>
                    <select name="duty_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('duty_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih Tugas --</option>
                        @foreach($duties as $duty)
                        <option value="{{ $duty->id }}" {{ old('duty_id', $assignment->duty_id) == $duty->id ? 'selected' : '' }}>
                            [{{ $duty->code }}] {{ $duty->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('duty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Petugas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1 text-gray-400"></i> Petugas <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('user_id') border-red-500 @enderror" required>
                        <option value="">-- Pilih Petugas --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" data-duty="{{ $user->duty_id }}" {{ old('user_id', $assignment->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} 
                            @if($user->duty) - [{{ $user->duty->code }}] {{ $user->duty->name }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-flag-checkered mr-1 text-gray-400"></i> Status
                    </label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                        <option value="pending" {{ old('status', $assignment->status) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="accepted" {{ old('status', $assignment->status) == 'accepted' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ old('status', $assignment->status) == 'rejected' ? 'selected' : '' }}>Ditolak</option>

                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-1 text-gray-400"></i> Catatan
                    </label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-500 @enderror" 
                              placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes', $assignment->notes) }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Informasi -->
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-800">Catatan:</h4>
                        <ul class="text-xs text-yellow-700 mt-1 space-y-1">
                            <li>• Mengubah status akan mempengaruhi notifikasi ke petugas</li>
                            <li>• Status "Diterima" berarti petugas sudah mengkonfirmasi kesediaan</li>
                            <li>• Status "Ditolak" berarti petugas tidak bersedia</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('assignments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-yellow-600 transition">
                    <i class="fas fa-save mr-2"></i> Update Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user_id');
    const allUserOptions = Array.from(userSelect.options);
    const currentUserId = "{{ old('user_id', $assignment->user_id) }}";
    
    // Simpan data user dengan duty_id
    const userData = allUserOptions
        .filter(opt => opt.value !== '')
        .map(opt => ({
            value: opt.value,
            text: opt.text,
            dutyId: opt.getAttribute('data-duty')
        }));
    
    // Filter user berdasarkan duty yang dipilih
    function filterUsersByDuty(dutyId) {
        // Simpan current value
        const currentValue = userSelect.value;
        
        userSelect.innerHTML = '';
        
        if (!dutyId) {
            const msgOption = document.createElement('option');
            msgOption.value = '';
            msgOption.textContent = '-- Pilih Tugas Dulu --';
            userSelect.appendChild(msgOption);
            return;
        }
        
        const filteredUsers = userData.filter(u => u.dutyId == dutyId);
        
        if (filteredUsers.length === 0) {
            const noOption = document.createElement('option');
            noOption.value = '';
            noOption.textContent = '-- Tidak ada petugas untuk tugas ini --';
            userSelect.appendChild(noOption);
            return;
        }
        
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = '-- Pilih Petugas --';
        userSelect.appendChild(defaultOpt);
        
        filteredUsers.forEach(user => {
            const option = document.createElement('option');
            option.value = user.value;
            option.textContent = user.text;
            userSelect.appendChild(option);
        });
        
        // Set kembali value yang dipilih
        if (currentValue) {
            userSelect.value = currentValue;
        }
    }
    
    // Event listener saat duty berubah
    const dutySelect = document.querySelector('select[name="duty_id"]');
    if (dutySelect) {
        dutySelect.addEventListener('change', function() {
            filterUsersByDuty(this.value);
        });
        
        // Trigger filter saat load dengan duty_id yang ada
        const currentDutyId = dutySelect.value;
        if (currentDutyId) {
            filterUsersByDuty(currentDutyId);
        }
    }
});
</script>
@endpush
@endsection