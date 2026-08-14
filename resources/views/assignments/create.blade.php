@extends('layouts.app')

@section('title', 'Tambah Penugasan')
@section('page-title', 'Tambah Penugasan Baru')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle mr-2 text-blue-500"></i> Tambah Penugasan
            </h1>
            @php
                $user = auth()->user();
                $isPicGroup = $user->level_akses === 'pic_group';
            @endphp

            @if($isPicGroup && $user->duty)
                <p class="text-sm text-purple-600 mt-1">
                    <i class="fas fa-users-cog mr-1 text-purple-400"></i> 
                    PIC Group: <span class="font-semibold">{{ $user->duty->name }}</span>
                    <span class="text-gray-400 ml-2">| Hanya dapat menugaskan ke anggota group</span>
                </p>
                <p class="text-sm text-green-600 mt-1">
                    <i class="fas fa-check-circle mr-1 text-green-400"></i> 
                    Petugas yang dipilih akan langsung berstatus <strong>Confirmed</strong>
                </p>
            @endif

            @if(isset($selectedDuty) && $selectedDuty)
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-tasks mr-1 text-blue-400"></i> 
                    Menambahkan petugas untuk: 
                    <span class="font-semibold text-blue-600">{{ $selectedDuty->name }}</span>
                    @if($selectedDuty->code)
                        <span class="text-gray-400">({{ $selectedDuty->code }})</span>
                    @endif
                </p>
            @endif
        </div>
        <a href="{{ route('assignments.index', request()->has('duty_id') ? ['duty_id' => request('duty_id')] : []) }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <form action="{{ route('assignments.store') }}" method="POST" class="p-6" id="assignmentForm">
            @csrf
            
            <!-- Hidden field untuk status -->
            <input type="hidden" name="status" value="{{ $isPicGroup ? 'confirmed' : 'pending' }}">
            @if($isPicGroup)
                <input type="hidden" name="is_pic_group" value="1">
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Jadwal Ibadah -->
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        <i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Jadwal Ibadah <span class="text-red-500">*</span>
    </label>

   <!-- Informasi jadwal (hanya nama jadwal) -->
<div class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2 text-gray-700 cursor-default">
    @php
        $selectedSchedule = $schedule ?? null;
        if (!$selectedSchedule && old('schedule_id')) {
            $selectedSchedule = $schedules->firstWhere('id', old('schedule_id'));
        }
        if (!$selectedSchedule && $schedules->isNotEmpty()) {
            $selectedSchedule = $schedules->first();
        }
    @endphp
    @if($selectedSchedule)
        {{ $selectedSchedule->name }}
    @else
        <span class="text-gray-400">Belum ada jadwal dipilih</span>
    @endif
</div>

    <!-- Select asli disembunyikan, tetap untuk keperluan JS (generate tanggal) -->
    <select name="schedule_id_display" id="schedule_id" 
            class="hidden" 
            disabled>
        <option value="">-- Pilih Jadwal Ibadah --</option>
        @foreach($schedules as $s)
        @php
            $masterDateFormatted = $s->master_date ? \Carbon\Carbon::parse($s->master_date)->format('Y-m-d') : '';
            $masterDateDisplay = $s->master_date ? \Carbon\Carbon::parse($s->master_date)->format('d/m/Y') : '';
        @endphp
        <option value="{{ $s->id }}" 
                data-day="{{ $s->day }}"
                data-time="{{ $s->time }}"
                data-master-date="{{ $masterDateFormatted }}"
                {{ old('schedule_id', $schedule->id ?? '') == $s->id ? 'selected' : '' }}>
            {{ $s->name }} 
            @if($s->master_date)
                ({{ ucfirst($s->day) }}, {{ $masterDateDisplay }} {{ $s->time }})
            @else
                ({{ ucfirst($s->day) }}, {{ $s->time }})
            @endif
        </option>
        @endforeach
    </select>

    <!-- Hidden input untuk mengirim schedule_id ke server -->
    <input type="hidden" name="schedule_id" id="schedule_id_hidden" value="{{ old('schedule_id', $schedule->id ?? '') }}">

    <p class="text-xs text-gray-500 mt-1">
        <i class="fas fa-info-circle text-blue-400"></i> 
        Jadwal ibadah sudah ditentukan dan tidak dapat diubah
    </p>
    @error('schedule_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

                <!-- Tanggal Pelaksanaan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-2 text-blue-500"></i> Tanggal Pelaksanaan <span class="text-red-500">*</span>
                    </label>
                    <select name="event_date" id="event_date" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('event_date') border-red-500 @enderror" required>
                        <option value="">-- Pilih Tanggal Pelaksanaan --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle text-blue-400"></i> 
                        Tanggal otomatis kelipatan +7 hari dari jadwal master
                    </p>
                    @error('event_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
             <!-- Tugas Pelayanan -->
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        <i class="fas fa-tasks mr-2 text-blue-500"></i> Tugas Pelayanan <span class="text-red-500">*</span>
    </label>
    
    @php
        $selectedDutyId = null;
        
        if (old('duty_id') && old('duty_id') != '' && old('duty_id') != 'null') {
            $selectedDutyId = old('duty_id');
        } elseif (session('selected_duty_id') && session('selected_duty_id') != '' && session('selected_duty_id') != 'null') {
            $selectedDutyId = session('selected_duty_id');
        } elseif (request()->has('duty_id') && request('duty_id') != '' && request('duty_id') != 'null') {
            $selectedDutyId = request('duty_id');
        } elseif (isset($dutyId) && $dutyId != '' && $dutyId != 'null') {
            $selectedDutyId = $dutyId;
        } elseif (isset($selectedDuty) && $selectedDuty) {
            $selectedDutyId = $selectedDuty->id;
        }
        
        // FILTER DUTY UNTUK PIC GROUP
        $filteredDuties = $duties;
        if ($isPicGroup && $user->duty_id) {
            $filteredDuties = $duties->filter(function($duty) use ($user) {
                return $duty->id == $user->duty_id;
            });
        }
        
        $selectedDutyName = '';
        $selectedDutyCode = '';
        if ($selectedDutyId) {
            foreach ($filteredDuties as $duty) {
                if ($duty->id == $selectedDutyId) {
                    $selectedDutyName = $duty->name;
                    $selectedDutyCode = $duty->code;
                    break;
                }
            }
        }
    @endphp

    <input type="hidden" id="hidden_duty_id" value="{{ $selectedDutyId }}">
    <input type="hidden" id="session_duty_id" value="{{ session('selected_duty_id') }}">

    {{-- 🔥 SOLUSI UTAMA: Hidden input untuk kirim data, Select dinonaktifkan --}}
    <input type="hidden" name="duty_id" value="{{ $selectedDutyId }}">

    <select 
        name="duty_id_display" 
        id="duty_id" 
        class="w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed focus:ring-0 @error('duty_id') border-red-500 @enderror" 
        disabled
    >
        <option value="">-- Pilih Tugas --</option>
        @foreach($filteredDuties as $duty)
        <option value="{{ $duty->id }}" 
            {{ ($selectedDutyId == $duty->id) ? 'selected' : '' }}>
            [{{ $duty->code }}] {{ $duty->name }}
        </option>
        @endforeach
    </select>
    
    {{-- Pesan error tetap muncul dari validasi Laravel --}}
    @error('duty_id') 
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
    @enderror
    
    @if($isPicGroup && $user->duty)
        <div class="mt-2 flex items-center gap-2 p-2.5 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg border border-purple-200">
            <div class="w-7 h-7 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="fas fa-check text-white text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-xs font-medium text-gray-700">
                    Tugas Anda: 
                    <strong class="text-purple-700">{{ $user->duty->name }}</strong>
                    @if($user->duty->code)
                        <span class="text-gray-500">({{ $user->duty->code }})</span>
                    @endif
                </span>
            </div>
        </div>
    @endif
</div>
                
                <!-- Petugas -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-users mr-2 text-blue-500"></i> 
                        @if($isPicGroup)
                            Pilih Petugas Group
                        @else
                            Pilih Petugas
                        @endif
                        <span class="text-red-500">*</span>
                    </label>
                    
                    @php
                        // FILTER PETUGAS UNTUK PIC GROUP
                        $filteredPetugas = $petugasList ?? collect();
                        
                        if ($isPicGroup && $user->duty_id) {
                            $filteredPetugas = $filteredPetugas->filter(function($petugas) use ($user) {
                                return $petugas->duty_id == $user->duty_id;
                            });
                        }
                    @endphp

                    <!-- TANPA SCROLL - Tampilkan semua -->
                    <div class="border-2 border-gray-200 rounded-xl p-3 bg-gray-50/50" id="petugasContainer">
                        @if($isPicGroup && $filteredPetugas->count() > 0)
                            <!-- Tombol Select All / Unselect All -->
                            <div class="flex items-center justify-between p-2 mb-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="button" id="selectAllBtn" class="text-xs text-blue-600 hover:text-blue-800 font-medium px-3 py-1 rounded hover:bg-blue-100 transition">
                                        <i class="fas fa-check-double mr-1"></i> Pilih Semua
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" id="unselectAllBtn" class="text-xs text-gray-500 hover:text-gray-700 font-medium px-3 py-1 rounded hover:bg-gray-200 transition">
                                        <i class="fas fa-times mr-1"></i> Hapus Semua
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-gray-600">
                                        <span id="selectedCountDisplay">0</span> / {{ $filteredPetugas->count() }} dipilih
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- DAFTAR PETUGAS - Tanpa scroll, tampilkan semua -->
                        <div class="space-y-1">
                            @forelse($filteredPetugas as $petugas)
                                @php
                                    // Cek old value
                                    $isChecked = false;
                                    if (old('user_id')) {
                                        if (is_array(old('user_id'))) {
                                            $isChecked = in_array($petugas->id, old('user_id'));
                                        } else {
                                            $isChecked = old('user_id') == $petugas->id;
                                        }
                                    }
                                @endphp
                                <label class="petugas-item flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 transition-all duration-200 cursor-pointer {{ $isChecked ? 'bg-blue-50 border-blue-200' : '' }}" 
                                       data-duty-id="{{ $petugas->duty_id }}">
                                    <div class="relative flex-shrink-0">
                                        <input type="checkbox" 
                                               name="user_id[]" 
                                               value="{{ $petugas->id }}"
                                               class="petugas-checkbox hidden"
                                               data-name="{{ $petugas->name }}"
                                               data-duty="{{ $petugas->duty ? $petugas->duty->name : '-' }}"
                                               data-duty-id="{{ $petugas->duty_id }}"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200 {{ $isChecked ? 'bg-blue-500 border-blue-500' : 'border-gray-300 hover:border-blue-400' }}">
                                            <i class="fas fa-check text-white text-xs transition-all duration-200 {{ $isChecked ? 'opacity-100 scale-100' : 'opacity-0 scale-75' }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-800 text-sm truncate">{{ $petugas->name }}</div>
                                        <div class="text-xs text-gray-500 truncate">
                                            @if($petugas->duty)
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                                                    {{ $petugas->duty->name }}
                                                    @if($petugas->duty->code)
                                                        <span class="text-gray-400">({{ $petugas->duty->code }})</span>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-400">Belum ada tugas</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($isPicGroup)
                                        <div class="flex-shrink-0">
                                            <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                <i class="fas fa-check-circle mr-1"></i> Confirmed
                                            </span>
                                        </div>
                                    @endif
                                </label>
                            @empty
                                <div class="text-center py-6 text-gray-500">
                                    <i class="fas fa-users text-3xl text-gray-300 mb-2 block"></i>
                                    <p class="text-sm">
                                        @if($isPicGroup)
                                            Belum ada petugas di group {{ $user->duty->name ?? '' }}
                                        @else
                                            Belum ada petugas untuk tugas ini
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        @if($isPicGroup)
                                            Tambahkan anggota ke group terlebih dahulu
                                        @else
                                            Tambahkan petugas terlebih dahulu
                                        @endif
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <div id="selectedPetugasInfo" class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle text-blue-400"></i>
                        <span id="selectedCount">{{ old('user_id') ? (is_array(old('user_id')) ? count(old('user_id')) : 1) : 0 }}</span> petugas dipilih
                        @if($isPicGroup)
                            <span class="text-green-600 ml-2">(Status langsung Confirmed)</span>
                        @endif
                    </div>
                    @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2 text-blue-500"></i> Catatan
                    </label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 @error('notes') border-red-500 @enderror" 
                              placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- ===== STATUS KETERSEDIAAN ===== -->
            <div id="availability-status" class="hidden mt-4 p-4 rounded-lg"></div>

            <!-- Informasi -->
            <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                        <i class="fas fa-info-circle text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">Informasi Penting:</h4>
                        <ul class="text-xs text-blue-700 mt-1 space-y-1">
                            @if($isPicGroup)
                                <li class="text-green-600">• <strong>PIC Group:</strong> Pilih petugas yang akan ditugaskan</li>
                                <li class="text-green-600">• <strong>Status:</strong> Langsung <strong>Confirmed</strong> tanpa perlu konfirmasi</li>
                            @else
                                <li>• Penugasan akan berstatus <strong>Menunggu Konfirmasi</strong> sampai petugas merespon</li>
                                <li>• Petugas akan melihat tugas ini di halaman "Tugas Pelayanan Saya"</li>
                                <li>• Petugas bisa menerima atau menolak tugas yang diberikan</li>
                            @endif
                            <li>• <strong>Jadwal Ibadah</strong> sudah tetap dan tidak dapat diubah</li>
                            <li>• <strong>Tanggal Pelaksanaan</strong> otomatis kelipatan +7 hari dari jadwal master</li>
                            @if($isPicGroup)
                            <li class="text-purple-600">• <strong>PIC Group:</strong> Anda hanya dapat menugaskan ke anggota group {{ $user->duty->name ?? '' }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('assignments.index', request()->has('duty_id') ? ['duty_id' => request('duty_id')] : []) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition shadow-lg hover:shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i> 
                    @if($isPicGroup)
                        Konfirmasi Penugasan
                    @else
                        Simpan Penugasan
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dutySelect = document.getElementById('duty_id');
    const hiddenDutyId = document.getElementById('hidden_duty_id');
    const sessionDutyId = document.getElementById('session_duty_id');
    const scheduleSelect = document.getElementById('schedule_id');
    const scheduleHidden = document.getElementById('schedule_id_hidden');
    const eventDateSelect = document.getElementById('event_date');
    const submitBtn = document.getElementById('submitBtn');
    const availabilityStatus = document.getElementById('availability-status');
    const form = document.getElementById('assignmentForm');
    const petugasItems = document.querySelectorAll('.petugas-item');
    const selectedCount = document.getElementById('selectedCount');
    const selectedCountDisplay = document.getElementById('selectedCountDisplay');
    const petugasCheckboxes = document.querySelectorAll('.petugas-checkbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const unselectAllBtn = document.getElementById('unselectAllBtn');
    
    // Cek apakah user adalah PIC Group
    const isPicGroup = {{ $isPicGroup ? 'true' : 'false' }};
    
    let checkTimeout;
    let isSubmitting = false;
    
    // Update selected count
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.petugas-checkbox:checked:not([style*="display: none"])').length;
        if (selectedCount) {
            selectedCount.textContent = checked;
        }
        if (selectedCountDisplay) {
            selectedCountDisplay.textContent = checked;
        }
    }
    
    // Select All
    function selectAll() {
        petugasItems.forEach(item => {
            if (item.style.display !== 'none') {
                const checkbox = item.querySelector('.petugas-checkbox');
                if (checkbox) {
                    checkbox.checked = true;
                    const checkBox = item.querySelector('.w-5.h-5');
                    const checkIcon = item.querySelector('.fa-check');
                    updateCheckboxUI(checkbox, checkBox, checkIcon, item);
                }
            }
        });
        updateSelectedCount();
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(checkAvailability, 300);
    }
    
    // Unselect All
    function unselectAll() {
        petugasItems.forEach(item => {
            if (item.style.display !== 'none') {
                const checkbox = item.querySelector('.petugas-checkbox');
                if (checkbox) {
                    checkbox.checked = false;
                    const checkBox = item.querySelector('.w-5.h-5');
                    const checkIcon = item.querySelector('.fa-check');
                    updateCheckboxUI(checkbox, checkBox, checkIcon, item);
                }
            }
        });
        updateSelectedCount();
        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(checkAvailability, 300);
    }
    
    // ============================================
    // GENERATE DATE OPTIONS
    // ============================================
    function generateDateOptions(scheduleOption) {
        if (!scheduleOption || !scheduleOption.value) {
            eventDateSelect.innerHTML = '<option value="">-- Silakan pilih jadwal terlebih dahulu --</option>';
            return;
        }
        
        const masterDateStr = scheduleOption.getAttribute('data-master-date');
        const day = scheduleOption.getAttribute('data-day');
        const time = scheduleOption.getAttribute('data-time');
        
        if (!masterDateStr || masterDateStr === '' || masterDateStr === 'null') {
            eventDateSelect.innerHTML = '<option value="">-- Data master_date tidak tersedia --</option>';
            return;
        }
        
        const dateParts = masterDateStr.split('-');
        if (dateParts.length !== 3) {
            eventDateSelect.innerHTML = '<option value="">-- Format master_date tidak valid --</option>';
            return;
        }
        
        const year = parseInt(dateParts[0]);
        const month = parseInt(dateParts[1]) - 1;
        const dayNum = parseInt(dateParts[2]);
        
        const baseDate = new Date(year, month, dayNum);
        if (isNaN(baseDate.getTime())) {
            eventDateSelect.innerHTML = '<option value="">-- Format master_date tidak valid --</option>';
            return;
        }
        
        const options = [];
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let currentDate = new Date(baseDate);
        let foundValid = false;
        let count = 0;
        const maxWeeks = 52;
        
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const timeStr = time || '00:00:00';
        const timeParts = timeStr.split(':');
        const hour = parseInt(timeParts[0]) || 0;
        const minute = parseInt(timeParts[1]) || 0;
        
        while (count < maxWeeks) {
            if (currentDate >= today) {
                const yearStr = currentDate.getFullYear();
                const monthStr = String(currentDate.getMonth() + 1).padStart(2, '0');
                const dayStr = String(currentDate.getDate()).padStart(2, '0');
                const dateStr = `${yearStr}-${monthStr}-${dayStr}`;
                
                const dayName = dayNames[currentDate.getDay()];
                const dateDisplay = `${dayName}, ${currentDate.getDate()} ${monthNames[currentDate.getMonth()]} ${yearStr}`;
                const timeDisplay = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                
                options.push({
                    value: dateStr,
                    text: `${dateDisplay} (${timeDisplay})`
                });
                foundValid = true;
            }
            
            currentDate.setDate(currentDate.getDate() + 7);
            count++;
        }
        
        if (!foundValid) {
            eventDateSelect.innerHTML = '<option value="">-- Tidak ada jadwal yang tersedia --</option>';
            return;
        }
        
        let html = '<option value="">-- Pilih Tanggal Pelaksanaan --</option>';
        options.forEach(opt => {
            const selected = (opt.value === '{{ old("event_date") }}') ? 'selected' : '';
            html += `<option value="${opt.value}" ${selected}>${opt.text}</option>`;
        });
        eventDateSelect.innerHTML = html;
    }
    
    // ============================================
    // FILTER PETUGAS BERDASARKAN DUTY
    // ============================================
    function filterPetugasByDuty(dutyId) {
        if (!dutyId || dutyId === '0' || dutyId === '' || dutyId === 'null') {
            petugasItems.forEach(item => {
                item.style.display = 'flex';
            });
            updateSelectedCount();
            return;
        }
        
        petugasItems.forEach(item => {
            const itemDutyId = item.getAttribute('data-duty-id');
            if (itemDutyId == dutyId) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
                const checkbox = item.querySelector('.petugas-checkbox');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                    const checkBox = item.querySelector('.w-5.h-5');
                    const checkIcon = item.querySelector('.fa-check');
                    updateCheckboxUI(checkbox, checkBox, checkIcon, item);
                }
            }
        });
        updateSelectedCount();
    }
    
    // ============================================
    // PETUGAS CHECKBOX HANDLER
    // ============================================
    petugasItems.forEach(item => {
        const checkbox = item.querySelector('.petugas-checkbox');
        const checkBox = item.querySelector('.w-5.h-5');
        const checkIcon = item.querySelector('.fa-check');
        
        item.addEventListener('click', function(e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            
            checkbox.checked = !checkbox.checked;
            updateCheckboxUI(checkbox, checkBox, checkIcon, item);
            updateSelectedCount();
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAvailability, 300);
        });
        
        checkbox.addEventListener('change', function() {
            updateCheckboxUI(this, checkBox, checkIcon, item);
            updateSelectedCount();
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAvailability, 300);
        });
    });
    
    function updateCheckboxUI(checkbox, checkBox, checkIcon, item) {
        if (checkbox.checked) {
            checkBox.classList.remove('border-gray-300', 'hover:border-blue-400');
            checkBox.classList.add('bg-blue-500', 'border-blue-500');
            checkIcon.classList.remove('opacity-0', 'scale-75');
            checkIcon.classList.add('opacity-100', 'scale-100');
            item.classList.add('bg-blue-50', 'border-blue-200');
            item.classList.remove('hover:bg-blue-50');
        } else {
            checkBox.classList.remove('bg-blue-500', 'border-blue-500');
            checkBox.classList.add('border-gray-300', 'hover:border-blue-400');
            checkIcon.classList.remove('opacity-100', 'scale-100');
            checkIcon.classList.add('opacity-0', 'scale-75');
            item.classList.remove('bg-blue-50', 'border-blue-200');
            item.classList.add('hover:bg-blue-50');
        }
    }
    
    // ============================================
    // CHECK AVAILABILITY
    // ============================================
    function checkAvailability() {
        const schedule_id = scheduleHidden?.value;
        const duty_id = dutySelect?.value;
        const event_date = eventDateSelect?.value;
        const selectedUsers = document.querySelectorAll('.petugas-checkbox:checked:not([style*="display: none"])');
        
        if (availabilityStatus) {
            availabilityStatus.className = 'hidden mt-4 p-4 rounded-lg';
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> ' + (isPicGroup ? 'Konfirmasi Penugasan' : 'Simpan Penugasan');
        }
        
        if (!schedule_id || !duty_id || !event_date || selectedUsers.length === 0) {
            if (availabilityStatus && selectedUsers.length === 0) {
                availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-gray-50 border border-gray-200 text-gray-600';
                availabilityStatus.innerHTML = '<i class="fas fa-info-circle mr-2"></i> Pilih minimal 1 petugas untuk ditugaskan.';
                availabilityStatus.classList.remove('hidden');
            }
            return;
        }
        
   
        // Cek satu per satu
        let checks = [];
        selectedUsers.forEach(checkbox => {
            checks.push(
                $.ajax({
                    url: '{{ route("assignments.check-duplicate") }}',
                    method: 'POST',
                    data: {
                        schedule_id: schedule_id,
                        duty_id: duty_id,
                        user_id: checkbox.value,
                        event_date: event_date,
                        _token: '{{ csrf_token() }}'
                    }
                })
            );
        });
        
        $.when.apply($, checks).done(function() {
            let messages = [];
            let hasError = false;
            
            arguments.forEach((response, index) => {
                if (response[0] && !response[0].valid) {
                    const userName = selectedUsers[index].getAttribute('data-name') || 'User';
                    messages.push(`⚠️ ${userName}: ${response[0].message}`);
                    hasError = true;
                }
            });
            
            if (hasError) {
                availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-700';
                availabilityStatus.innerHTML = messages.join('<br>');
                availabilityStatus.classList.remove('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> ' + (isPicGroup ? 'Konfirmasi Penugasan' : 'Simpan Penugasan');
                }
            } else {
                availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700';
                availabilityStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Semua petugas tersedia untuk ditugaskan.';
                availabilityStatus.classList.remove('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> ' + (isPicGroup ? 'Konfirmasi Penugasan' : 'Simpan Penugasan');
                }
            }
        }).fail(function() {
            if (availabilityStatus) {
                availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-700';
                availabilityStatus.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i> Gagal memeriksa ketersediaan. Silakan coba lagi.';
                availabilityStatus.classList.remove('hidden');
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> ' + (isPicGroup ? 'Konfirmasi Penugasan' : 'Simpan Penugasan');
            }
        });
    }
    
    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    // Select All / Unselect All
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', selectAll);
    }
    if (unselectAllBtn) {
        unselectAllBtn.addEventListener('click', unselectAll);
    }
    
    if (scheduleSelect) {
        const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            generateDateOptions(selectedOption);
        } else {
            const hiddenValue = scheduleHidden ? scheduleHidden.value : null;
            if (hiddenValue) {
                for (let i = 0; i < scheduleSelect.options.length; i++) {
                    if (scheduleSelect.options[i].value == hiddenValue) {
                        generateDateOptions(scheduleSelect.options[i]);
                        break;
                    }
                }
            }
        }
        
        scheduleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (scheduleHidden) {
                scheduleHidden.value = this.value;
            }
            generateDateOptions(selectedOption);
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAvailability, 300);
        });
    }
    
    if (dutySelect) {
        dutySelect.addEventListener('change', function() {
            filterPetugasByDuty(this.value);
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAvailability, 300);
        });
    }
    
    if (eventDateSelect) {
        eventDateSelect.addEventListener('change', function() {
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(checkAvailability, 300);
        });
    }
    
    // Update count when checkbox changes
    petugasCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectedCount();
        });
    });
    
    // ============================================
    // CEGAH DOUBLE SUBMIT
    // ============================================
    if (form) {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            const selectedPetugas = document.querySelectorAll('.petugas-checkbox:checked:not([style*="display: none"])');
            if (selectedPetugas.length === 0) {
                e.preventDefault();
                if (availabilityStatus) {
                    availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700';
                    availabilityStatus.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Silakan pilih minimal 1 petugas.';
                    availabilityStatus.classList.remove('hidden');
                }
                return false;
            }
            
            if (!eventDateSelect || !eventDateSelect.value) {
                e.preventDefault();
                if (availabilityStatus) {
                    availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700';
                    availabilityStatus.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i> Silakan pilih tanggal pelaksanaan.';
                    availabilityStatus.classList.remove('hidden');
                }
                return false;
            }
            
            isSubmitting = true;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
            }
            return true;
        });
    }
    
    // ============================================
    // INITIALIZE
    // ============================================
    
    // Set initial duty filter
    const hiddenDutyValue = hiddenDutyId ? hiddenDutyId.value : '';
    const sessionDutyValue = sessionDutyId ? sessionDutyId.value : '';
    const urlParams = new URLSearchParams(window.location.search);
    const dutyIdFromUrl = urlParams.get('duty_id');
    const serverDutyId = "{{ old('duty_id', request('duty_id')) }}";
    
    let initialDutyId = sessionDutyValue || hiddenDutyValue || dutyIdFromUrl || serverDutyId;
    
    if (dutySelect && initialDutyId && initialDutyId !== '' && initialDutyId !== 'null' && initialDutyId !== '0') {
        const optionExists = Array.from(dutySelect.options).some(opt => opt.value == initialDutyId);
        if (optionExists) {
            dutySelect.value = initialDutyId;
            filterPetugasByDuty(initialDutyId);
        }
    }
    
    if (initialDutyId && initialDutyId !== '' && initialDutyId !== 'null' && initialDutyId !== '0') {
        filterPetugasByDuty(initialDutyId);
        updateSelectedCount();
    } else {
        petugasItems.forEach(item => {
            item.style.display = 'flex';
        });
        updateSelectedCount();
    }
    
    // Auto check availability setelah load
    setTimeout(checkAvailability, 500);
});
</script>
@endpush

@push('styles')
<style>
    .hidden {
        display: none !important;
    }
    
    .petugas-item {
        border: 2px solid transparent;
        transition: all 0.2s ease;
        display: flex;
    }
    
    .petugas-item:hover {
        border-color: #bfdbfe;
    }
    
    .petugas-item.bg-blue-50 {
        border-color: #93c5fd;
    }
    
    .info-selected {
        animation: fadeSlideIn 0.4s ease-out;
    }
    
    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    select:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    /* Hilangkan scroll dan tampilkan semua */
    #petugasContainer {
        max-height: none !important;
        overflow-y: visible !important;
    }
</style>
@endpush
@endsection