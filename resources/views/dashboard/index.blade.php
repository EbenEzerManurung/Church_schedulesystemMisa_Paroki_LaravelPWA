@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- ============================================ -->
    <!-- GREETING CARD -->
    <!-- ============================================ -->
<div class="bg-gradient-to-r from-teal-500 to-cyan-700 rounded-2xl shadow-xl p-6 mb-8 text-white">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h2 class="text-2xl font-bold">
                    <i class="fas fa-wave-square mr-2"></i>
                    Welcome, {{ auth()->user()->name }}!
                </h2>
                <p class="text-blue-100 mt-1">
                    @if(auth()->user()->isAdmin())
                        <i class="fas fa-user-shield mr-1"></i> Anda login sebagai Administrator Super
                    @elseif(auth()->user()->isAdminKeuskupan())
                        <i class="fas fa-user-tie mr-1"></i> Anda login sebagai Administrator Keuskupan
                    @elseif(auth()->user()->level_akses === 'pic_group')
                        <i class="fas fa-users-cog mr-1"></i> Anda login sebagai PIC Group
                    @else
                        <i class="fas fa-user mr-1"></i> Anda login sebagai User
                    @endif
                </p>
            </div>
            <div class="mt-3 md:mt-0">
                <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg text-sm" id="clock">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <span id="dateTimeDisplay">{{ now()->format('l, d F Y H:i:s') }}</span>
                </span>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- STATISTIK CARDS - ADMIN SUPER -->
    <!-- ============================================ -->
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Keuskupan</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $stats->total_keuskapan ?? $stats->total_keuskupan ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-diocese text-2xl text-blue-500"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Gereja</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats->total_gereja ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-church text-2xl text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total User</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $stats->total_users ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-purple-500"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Jadwal</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $stats->total_schedules ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-2xl text-orange-500"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- STATISTIK PENUGASAN -->
    <!-- ============================================ -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-700 text-xs font-medium">Menunggu</p>
                    <p class="text-2xl font-bold text-yellow-800">{{ $stats->pending_assignments ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 text-xs font-medium">Diterima</p>
                    <p class="text-2xl font-bold text-green-800">{{ $stats->accepted_assignments ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-700 text-xs font-medium">Ditolak</p>
                    <p class="text-2xl font-bold text-red-800">{{ $stats->rejected_assignments ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-700 text-xs font-medium">Selesai</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $stats->completed_assignments ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-double text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-700 text-xs font-medium">Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats->total_assignments ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                    <i class="fas fa-tasks text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
<!-- FORM TAMBAH PENUGASAN - KHUSUS PIC GROUP -->
<!-- ============================================ -->
@php
    $user = auth()->user();
    $isPicGroup = $user->level_akses === 'pic_group';
    
    // Ambil data untuk PIC Group
    $schedules = collect();
    $duties = collect();
    $petugasList = collect();
    $schedule = null;
    $selectedScheduleId = null;
    
    if ($isPicGroup && $user->duty) {
        try {
            // Ambil schedules aktif
            $schedules = \App\Models\Schedule::where('status', 'active')
                ->orderByRaw("FIELD(day, 'sabtu', 'minggu')")
                ->orderBy('time')
                ->get();
            
            // Jika ada schedules, ambil yang pertama sebagai default
            if ($schedules->isNotEmpty()) {
                $schedule = $schedules->first();
                $selectedScheduleId = $schedule->id;
            }
            
            // Ambil duties - hanya duty milik user
            $duties = \App\Models\Duty::where('is_active', true)
                ->where('id', $user->duty_id)
                ->orderBy('name')
                ->get();
            
            // Ambil petugas dengan duty yang sama - TAMPILKAN SEMUA TERMASUK DIRI SENDIRI
            $petugasList = \App\Models\User::whereHas('duty', function($query) use ($user) {
                $query->where('id', $user->duty_id);
            })
            ->orderBy('name')
            ->get();
            
        } catch (\Exception $e) {
            // Jika error, biarkan kosong
            $schedules = collect();
            $duties = collect();
            $petugasList = collect();
            $schedule = null;
        }
    }
@endphp

    @if($isPicGroup && $user->duty)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 mb-8">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Penugasan Baru
                    </h3>
                    <p class="text-purple-100 text-sm mt-1 flex items-center gap-2">
                        <i class="fas fa-users-cog"></i> 
                        PIC Group: <span class="font-semibold">{{ $user->duty->name }}</span>
                        <span class="text-purple-200 ml-1">| Hanya dapat menugaskan ke anggota group</span>
                    </p>
                    <p class="text-green-200 text-sm mt-0.5">
                        <i class="fas fa-check-circle"></i> 
                        Petugas yang dipilih akan langsung berstatus <strong>Confirmed</strong>
                    </p>
                </div>
                <a href="{{ route('assignments.index') }}?duty_id={{ $user->duty_id }}" 
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i> Lihat Semua
                </a>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <form action="{{ route('assignments.store') }}" method="POST" id="assignmentForm">
                @csrf
                <input type="hidden" name="status" value="confirmed">
                <input type="hidden" name="is_pic_group" value="1">
                
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
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 @error('event_date') border-red-500 @enderror" required>
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
                            $selectedDutyId = $user->duty_id;
                        @endphp

                        <input type="hidden" id="hidden_duty_id" value="{{ $selectedDutyId }}">

                        <select name="duty_id" id="duty_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 @error('duty_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih Tugas --</option>
                            @foreach($duties as $duty)
                            <option value="{{ $duty->id }}" {{ $selectedDutyId == $duty->id ? 'selected' : '' }}>
                                [{{ $duty->code }}] {{ $duty->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('duty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        
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
                    </div>
                    
                    <!-- Petugas -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-users mr-2 text-blue-500"></i> Pilih Petugas Group <span class="text-red-500">*</span>
                        </label>
                        
                        @php
                            $filteredPetugas = $petugasList->filter(function($petugas) use ($user) {
                                return $petugas->duty_id == $user->duty_id;
                            });
                        @endphp

                        <div class="border-2 border-gray-200 rounded-xl p-3 bg-gray-50/50 max-h-60 overflow-y-auto" id="petugasContainer">
                            @if($filteredPetugas->count() > 0)
                                <!-- Tombol Select All / Unselect All -->
                                <div class="flex items-center justify-between p-2 mb-3 bg-purple-50 rounded-lg border border-purple-200">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <button type="button" id="selectAllBtn" class="text-xs text-purple-600 hover:text-purple-800 font-medium px-3 py-1 rounded hover:bg-purple-100 transition">
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

                            <div class="space-y-1">
                                @forelse($filteredPetugas as $petugas)
                                    @php
                                        $isChecked = false;
                                        if (old('user_id')) {
                                            if (is_array(old('user_id'))) {
                                                $isChecked = in_array($petugas->id, old('user_id'));
                                            } else {
                                                $isChecked = old('user_id') == $petugas->id;
                                            }
                                        }
                                        
                                        // Cek apakah ini user yang sedang login
                                        $isCurrentUser = $petugas->id == auth()->id();
                                    @endphp
                                    <label class="petugas-item flex items-center gap-3 p-2 rounded-lg hover:bg-purple-50 transition-all duration-200 cursor-pointer {{ $isChecked ? 'bg-purple-50 border-purple-200' : '' }}" 
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
                                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200 {{ $isChecked ? 'bg-purple-500 border-purple-500' : 'border-gray-300 hover:border-purple-400' }}">
                                                <i class="fas fa-check text-white text-xs transition-all duration-200 {{ $isChecked ? 'opacity-100 scale-100' : 'opacity-0 scale-75' }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-800 text-sm truncate">
                                                {{ $petugas->name }}
                                                @if($isCurrentUser)
                                                    <span class="text-xs text-purple-600 font-bold ml-1">(Saya)</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 truncate">
                                                @if($petugas->duty)
                                                    <span class="inline-flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 flex-shrink-0"></span>
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
                                        <div class="flex-shrink-0">
                                            <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full whitespace-nowrap">
                                                <i class="fas fa-check-circle mr-1"></i> Confirmed
                                            </span>
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-6 text-gray-500">
                                        <i class="fas fa-users text-3xl text-gray-300 mb-2 block"></i>
                                        <p class="text-sm">Belum ada petugas di group {{ $user->duty->name ?? '' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Tambahkan anggota ke group terlebih dahulu</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div id="selectedPetugasInfo" class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle text-blue-400"></i>
                            <span id="selectedCount">{{ old('user_id') ? (is_array(old('user_id')) ? count(old('user_id')) : 1) : 0 }}</span> petugas dipilih
                            <span class="text-green-600 ml-2">(Status langsung Confirmed)</span>
                            @if($filteredPetugas->count() > 0)
                                <span class="text-purple-600 ml-2">(Termasuk Anda jika dicentang)</span>
                            @endif
                        </div>
                        @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Catatan -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-2 text-blue-500"></i> Catatan
                        </label>
                        <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 @error('notes') border-red-500 @enderror" 
                                  placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- ===== STATUS KETERSEDIAAN ===== -->
                <div id="availability-status" class="hidden mt-4 p-4 rounded-lg"></div>

                <!-- Informasi -->
                <div class="mt-6 p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-100">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                            <i class="fas fa-info-circle text-white text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-purple-800">Informasi Penting untuk PIC Group:</h4>
                            <ul class="text-xs text-purple-700 mt-1 space-y-1">
                                <li class="text-green-600">• <strong>Status:</strong> Petugas langsung <strong>Confirmed</strong> tanpa perlu konfirmasi</li>
                                <li>• <strong>Jadwal Ibadah</strong> sudah tetap dan tidak dapat diubah</li>
                                <li>• <strong>Tanggal Pelaksanaan</strong> otomatis kelipatan +7 hari dari jadwal master</li>
                                <li class="text-purple-600">• <strong>PIC Group:</strong> Anda hanya dapat menugaskan ke anggota group {{ $user->duty->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <button type="reset" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition shadow-lg hover:shadow-purple-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i> Konfirmasi Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- JADWAL MISA SAYA (UNTUK USER BIASA) -->
    <!-- ============================================ -->
    @if(auth()->user()->isUser() || auth()->user()->level_akses === 'user_biasa')
    @php
        $today = \Carbon\Carbon::today();
        $currentUserId = auth()->user()->id;
        
        $allUpcoming = \App\Models\DutyAssignment::with(['schedule', 'duty', 'user'])
            ->whereDate('event_date', '>=', $today)
            ->whereIn('status', ['accepted', 'pending'])
            ->where('user_id', $currentUserId)
            ->orderBy('event_date', 'asc')
            ->get()
            ->filter(function($assignment) use ($today) {
                $daysDiff = $today->diffInDays(\Carbon\Carbon::parse($assignment->event_date));
                return $daysDiff <= 12;
            });
    @endphp
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300 mb-8">
        <div class="px-6 py-5 bg-gradient-to-r from-emerald-500 to-teal-600">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-white">
                        <i class="fas fa-calendar-check mr-2"></i> 
                        Jadwal Misa Saya
                    </h3>
                    <p class="text-emerald-100 text-lg mt-1">
                        <i class="fas fa-clock mr-1"></i> 12 hari ke depan
                    </p>
                </div>
                <span class="bg-white/20 text-white px-4 py-2 rounded-full text-lg font-bold">
                    {{ $allUpcoming->count() }} Jadwal
                </span>
            </div>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($allUpcoming->groupBy('schedule_id') as $scheduleId => $scheduleAssignments)
            @php
                $firstAssignment = $scheduleAssignments->first();
                $schedule = $firstAssignment->schedule;
                $duty = $firstAssignment->duty;
                
                $scheduleName = $schedule->display ?? $schedule->name ?? 'Jadwal Tidak Diketahui';
                $scheduleDay = ucfirst($schedule->day ?? '');
                $scheduleTime = $schedule->time ? \Carbon\Carbon::parse($schedule->time)->format('H:i') : '';
                
                $eventDate = $firstAssignment->event_date;
                $eventCarbon = \Carbon\Carbon::parse($eventDate);
                $dateFull = $eventCarbon->translatedFormat('l, d F Y');
                $daysDiff = $today->diffInDays($eventCarbon);
                $isToday = $eventCarbon->isToday();
                
                $dutyName = $duty->name ?? 'Tugas Tidak Diketahui';
                $maxPerson = $duty->max_person ?? 999;
                
                $totalAccepted = \App\Models\DutyAssignment::where('schedule_id', $scheduleId)
                    ->where('duty_id', $duty->id)
                    ->where('event_date', $eventDate)
                    ->where('status', 'accepted')
                    ->count();
                
                $acceptedUsers = \App\Models\DutyAssignment::where('schedule_id', $scheduleId)
                    ->where('duty_id', $duty->id)
                    ->where('event_date', $eventDate)
                    ->where('status', 'accepted')
                    ->with('user')
                    ->get()
                    ->pluck('user.name')
                    ->toArray();
                
                $isFull = $totalAccepted >= $maxPerson;
                $kuotaDisplay = $totalAccepted . '/' . ($maxPerson != 999 ? $maxPerson : '∞');
                
                if ($isFull) {
                    $kuotaBadge = 'bg-red-100 text-red-700';
                    $kuotaLabel = 'Penuh';
                } elseif ($totalAccepted > 0) {
                    $kuotaBadge = 'bg-green-100 text-green-700';
                    $kuotaLabel = 'Terisi';
                } else {
                    $kuotaBadge = 'bg-gray-100 text-gray-500';
                    $kuotaLabel = 'Kosong';
                }
                
                if ($isToday) {
                    $dayStatus = 'Hari ini';
                    $dayBadge = 'bg-red-100 text-red-700 animate-pulse';
                } elseif ($daysDiff <= 7) {
                    $dayStatus = $daysDiff . ' hari lagi';
                    $dayBadge = 'bg-yellow-100 text-yellow-700';
                } else {
                    $dayStatus = $eventCarbon->translatedFormat('d M Y');
                    $dayBadge = 'bg-blue-100 text-blue-700';
                }
            @endphp
            
            <div class="p-5 transition-all duration-200 hover:bg-gray-50/50 {{ $isToday ? 'bg-blue-50/30' : '' }}">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="flex-shrink-0 text-center min-w-[70px]">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex flex-col items-center justify-center text-white shadow-md">
                                <span class="text-xs font-semibold uppercase">{{ substr($scheduleDay, 0, 3) }}</span>
                                <span class="text-xl font-bold leading-none">{{ $eventCarbon->format('d') }}</span>
                            </div>
                            <div class="text-[10px] text-gray-500 mt-1 font-medium">
                                {{ $eventCarbon->translatedFormat('M') }}
                            </div>
                            @if($isToday)
                                <span class="text-[10px] text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded-full mt-0.5 inline-block">
                                    Hari Ini
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h4 class="font-bold text-gray-800 text-xl">
                                    {{ $scheduleName }}
                                </h4>
                                <span class="text-sm text-gray-500 flex items-center gap-1">
                                    <i class="far fa-clock"></i>
                                    {{ $scheduleTime }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <i class="far fa-calendar-alt text-gray-400"></i>
                                {{ $dateFull }}
                            </p>
                            
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-700">
                                    <i class="fas fa-tasks mr-1.5"></i>
                                    {{ $dutyName }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kuotaBadge }}">
                                    <i class="fas {{ $isFull ? 'fa-times-circle' : 'fa-check-circle' }} mr-1.5"></i>
                                    {{ $kuotaLabel }} ({{ $kuotaDisplay }})
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-start md:items-end gap-2 md:min-w-[200px]">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $dayBadge }}">
                            <i class="fas fa-calendar-day mr-1.5"></i>
                            {{ $dayStatus }}
                        </span>
                        
                        @if(count($acceptedUsers) > 0)
                            <div class="flex flex-wrap items-center gap-1 justify-end">
                                <span class="text-xs text-gray-500 mr-1">
                                    <i class="fas fa-users"></i> {{ count($acceptedUsers) }} orang:
                                </span>
                                @foreach($acceptedUsers as $petugas)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $petugas == auth()->user()->name ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        @if($petugas == auth()->user()->name)
                                            <i class="fas fa-check-circle text-emerald-500"></i>
                                        @else
                                            <i class="fas fa-user text-gray-400"></i>
                                        @endif
                                        {{ $petugas }}
                                        @if($petugas == auth()->user()->name)
                                            <span class="text-emerald-600 font-bold">(Saya)</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-sm text-gray-400 flex items-center gap-1">
                                <i class="fas fa-user-slash"></i> Belum ada yang menerima
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-5xl text-gray-400"></i>
                </div>
                <p class="text-gray-600 font-bold text-2xl">
                    Belum ada jadwal Misa
                </p>
                <p class="text-lg text-gray-400 mt-1">Anda belum mengambil tugas dalam 12 hari ke depan</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- AMBIL TUGAS PELAYAN - ONLY FOR USER ROLE -->
    <!-- ============================================ -->
    @if(auth()->user()->isUser() || auth()->user()->level_akses === 'user_biasa')
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 mb-8">
        <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-hand-paper text-emerald-600 text-2xl"></i>
                <h4 class="font-bold text-gray-700 text-2xl">Ambil Tugas Pelayan</h4>
                <span class="text-base text-gray-500 bg-white/50 px-3 py-1 rounded-full">
                    <i class="far fa-clock mr-1"></i> 
                    @php
                        try {
                            $availableSchedulesCount = \App\Models\Schedule::where('status', 'active')
                                ->orderByRaw("FIELD(day, 'sabtu', 'minggu')")
                                ->orderBy('time')
                                ->count();
                        } catch (\Exception $e) {
                            $availableSchedulesCount = 0;
                        }
                    @endphp
                    {{ min($availableSchedulesCount, 6) }} jadwal
                </span>
            </div>
            <button type="button" 
                    onclick="toggleAmbilTugas()" 
                    class="text-emerald-600 hover:text-emerald-800 text-lg font-bold transition-colors flex items-center gap-1.5 hover:gap-2.5 duration-200 group">
                <span id="ambilTugasText">Tampilkan Jadwal</span>
                <i id="ambilTugasIcon" class="fas fa-chevron-down text-base group-hover:translate-x-0.5 transition-transform"></i>
            </button>
        </div>
        
        <div id="ambilTugasContainer" class="hidden p-5 bg-gray-50/30">
            @php
                $availableSchedules = collect();
                $availableDuties = collect();
                $currentUserId = auth()->id();
                $userDutyId = null;
                $defaultEventDate = now()->addDays(7)->format('Y-m-d');
                $allUserAssignments = collect();
                
                $today = \Carbon\Carbon::today();
                $nextSaturday = $today->copy()->next('Saturday');
                $nextSunday = $today->copy()->next('Sunday');
                
                if ($today->isSaturday()) {
                    $nextSaturday = $today->copy();
                    $nextSunday = $today->copy()->addDay();
                }
                
                if ($today->isSunday()) {
                    $nextSunday = $today->copy();
                    $nextSaturday = $today->copy()->subDay();
                }
                
                try {
                    if (class_exists('App\Models\Duty')) {
                        $availableDuties = \App\Models\Duty::where('is_active', true)
                            ->orderBy('name')
                            ->get();
                        
                        $currentUser = auth()->user();
                        if ($currentUser && isset($currentUser->duty_id)) {
                            $userDutyId = $currentUser->duty_id;
                        }
                    }
                    
                    if (class_exists('App\Models\Schedule')) {
                        $availableSchedules = \App\Models\Schedule::where('status', 'active')
                            ->orderByRaw("FIELD(day, 'sabtu', 'minggu')")
                            ->orderBy('time')
                            ->take(6)
                            ->get();
                    }
                    
                    if (class_exists('App\Models\DutyAssignment') && $currentUserId) {
                        $allUserAssignments = \App\Models\DutyAssignment::where('user_id', $currentUserId)
                            ->whereIn('status', ['accepted', 'pending'])
                            ->get()
                            ->keyBy('schedule_id');
                    }
                } catch (\Exception $e) {
                    $availableSchedules = collect();
                    $availableDuties = collect();
                    $allUserAssignments = collect();
                }
            @endphp
            
            @if($availableSchedules->isNotEmpty() && $availableDuties->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($availableSchedules as $schedule)
                        @php
                            $dutyCounts = [];
                            $dutyUsers = [];
                            $hasAssignment = false;
                            $assignedDutyId = null;
                            
                            $scheduleDate = null;
                            if (strtolower($schedule->day) == 'sabtu') {
                                $scheduleDate = $nextSaturday;
                            } elseif (strtolower($schedule->day) == 'minggu') {
                                $scheduleDate = $nextSunday;
                            }
                            
                            try {
                                foreach($availableDuties as $duty) {
                                    $assignments = \App\Models\DutyAssignment::where('schedule_id', $schedule->id)
                                        ->where('duty_id', $duty->id)
                                        ->where('status', 'accepted')
                                        ->with('user')
                                        ->get();
                                    
                                    $dutyCounts[$duty->id] = $assignments->count();
                                    
                                    $dutyUsers[$duty->id] = $assignments->map(function($assignment) {
                                        return [
                                            'name' => $assignment->user->name ?? 'User Tidak Diketahui',
                                            'user_id' => $assignment->user_id,
                                            'is_me' => $assignment->user_id == auth()->id(),
                                        ];
                                    });
                                }
                                
                                $userAssignment = $allUserAssignments->get($schedule->id);
                                $hasAssignment = $userAssignment ? true : false;
                                $assignedDutyId = $userAssignment ? $userAssignment->duty_id : null;
                            } catch (\Exception $e) {
                                $dutyCounts = [];
                                $dutyUsers = [];
                                $hasAssignment = false;
                                $assignedDutyId = null;
                            }
                        @endphp
                        
                        <div class="bg-white rounded-xl p-5 border border-gray-200 hover:shadow-md transition {{ $hasAssignment ? 'ring-2 ring-emerald-400' : '' }}">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h5 class="font-bold text-gray-800 text-xl">
                                        {{ $schedule->name ?? $schedule->day . ' ' . $schedule->time }}
                                    </h5>
                                    <p class="text-base text-gray-500">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ ucfirst($schedule->day) }} • {{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}
                                    </p>
                                    @if($scheduleDate)
                                    <p class="text-sm text-gray-400 mt-1.5 bg-gray-50 px-3 py-1 rounded-full inline-flex items-center gap-1.5">
                                        <i class="far fa-calendar-check text-emerald-500"></i>
                                        <span class="font-medium">{{ $scheduleDate->translatedFormat('l, d F Y') }}</span>
                                    </p>
                                    @endif
                                </div>
                                @if($hasAssignment)
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm font-bold rounded-full flex items-center gap-1">
                                    <i class="fas fa-check-circle"></i> Sudah
                                </span>
                                @endif
                            </div>
                            
                            <div class="space-y-3">
                                @php $hasUserDuty = false; @endphp
                                
                                @foreach($availableDuties as $duty)
                                    @php
                                        if ($userDutyId && $userDutyId != $duty->id) continue;
                                        $hasUserDuty = true;
                                        
                                        $count = $dutyCounts[$duty->id] ?? 0;
                                        $isFull = $count >= $duty->max_person;
                                        $isAvailable = $count < $duty->max_person;
                                        
                                        $hasThisDuty = ($hasAssignment && $assignedDutyId == $duty->id);
                                        
                                        try {
                                            $hasThisDutyDb = \App\Models\DutyAssignment::where('schedule_id', $schedule->id)
                                                ->where('duty_id', $duty->id)
                                                ->where('user_id', $currentUserId)
                                                ->whereIn('status', ['accepted', 'pending'])
                                                ->exists();
                                        } catch (\Exception $e) {
                                            $hasThisDutyDb = false;
                                        }
                                        
                                        $hasThisDuty = $hasThisDuty || $hasThisDutyDb;
                                        $canTake = !$hasAssignment && $isAvailable && !$hasThisDuty;
                                    @endphp
                                    
                                    <div class="rounded-lg border {{ $hasThisDuty ? 'border-emerald-300 bg-emerald-50/30' : 'border-gray-200' }} overflow-hidden">
                                        <div class="flex items-center justify-between text-lg p-3 {{ $hasThisDuty ? 'bg-emerald-50/50' : 'bg-gray-50/50' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-700 text-lg">{{ $duty->name }}</span>
                                                <span class="text-base text-gray-500">
                                                    ({{ $count }}/{{ $duty->max_person }})
                                                </span>
                                                @if($isFull)
                                                    <span class="text-base text-red-500 font-bold">Penuh</span>
                                                @endif
                                            </div>
                                            
                                            @if($canTake && $schedule->id && $duty->id)
                                                <form action="{{ route('assignments.take') }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Ambil tugas {{ $duty->name }} untuk jadwal {{ $schedule->name }} ({{ $scheduleDate ? $scheduleDate->translatedFormat('d F Y') : '' }})?')">
                                                    @csrf
                                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                    <input type="hidden" name="duty_id" value="{{ $duty->id }}">
                                                    <input type="hidden" name="event_date" value="{{ $scheduleDate ? $scheduleDate->format('Y-m-d') : $defaultEventDate }}">
                                                    
                                                    <button type="submit" 
                                                            class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-base font-bold transition-colors flex items-center gap-1">
                                                        <i class="fas fa-hand-paper text-base"></i>
                                                        Ambil
                                                    </button>
                                                </form>
                                            @elseif($hasThisDuty || $hasAssignment)
                                                <span class="text-base text-gray-500 font-bold flex items-center gap-1">
                                                    <i class="fas fa-check-circle text-emerald-500"></i> Sudah
                                                </span>
                                            @elseif($isFull)
                                                <span class="text-base text-gray-500">Penuh</span>
                                            @else
                                                <span class="text-base text-gray-500">-</span>
                                            @endif
                                        </div>
                                        
                                        @if(isset($dutyUsers[$duty->id]) && $dutyUsers[$duty->id]->isNotEmpty())
                                        <div class="p-3 bg-white/50 border-t border-gray-100">
                                            <p class="text-base text-gray-600 mb-1.5">
                                                <i class="fas fa-check-circle text-emerald-500 mr-1"></i> 
                                                {{ $dutyUsers[$duty->id]->count() }} orang sudah menerima:
                                            </p>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($dutyUsers[$duty->id] as $userData)
                                                    <span class="inline-flex items-center gap-1 text-base px-3 py-1 rounded-full 
                                                        {{ $userData['is_me'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                                        @if($userData['is_me'])
                                                            <i class="fas fa-check-circle text-emerald-500"></i>
                                                        @else
                                                            <i class="fas fa-user text-gray-400"></i>
                                                        @endif
                                                        {{ $userData['name'] }}
                                                        @if($userData['is_me'])
                                                            <span class="text-emerald-600 font-bold">(Saya)</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @else
                                        <div class="p-3 bg-white/50 border-t border-gray-100">
                                            <p class="text-base text-gray-500">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Belum ada yang menerima
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if(!$hasUserDuty)
                                    <div class="text-center py-3 text-gray-500 text-base bg-gray-50 rounded-lg">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        @if($userDutyId)
                                            Tidak ada tugas yang sesuai untuk Anda
                                        @else
                                            Anda belum memiliki tugas. Silakan hubungi admin.
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-base text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Hanya menampilkan tugas yang sesuai dengan Tugas Anda
                    </p>
                    <p class="text-sm text-gray-400 mt-1">
                        <i class="fas fa-check-circle text-emerald-500 mr-1"></i>
                        Kuota dihitung dari petugas yang sudah <span class="font-bold">Menerima</span> (status: Diterima)
                    </p>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-5xl mb-2 block"></i>
                    <p class="text-lg">Belum ada jadwal atau tugas yang tersedia</p>
                    <p class="text-sm text-gray-400 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pastikan Anda telah memiliki tugas (Duty) dan ada jadwal aktif
                    </p>
                </div>
            @endif
        </div>
    </div>
    @endif

        <!-- ============================================ -->
    <!-- JADWAL 12 HARI TERDEKAT - KHUSUS PIC GROUP -->
    <!-- ============================================ -->
    @if($isPicGroup && $user->duty)
    @php
        $today = \Carbon\Carbon::today();
        $currentUserId = auth()->id();
        $userDutyId = $user->duty_id;
        
        // Ambil semua penugasan untuk duty group ini dalam 12 hari ke depan
        $allUpcomingGroup = \App\Models\DutyAssignment::with(['schedule', 'duty', 'user'])
            ->whereDate('event_date', '>=', $today)
            ->whereIn('status', ['accepted', 'confirmed'])
            ->whereHas('duty', function($query) use ($userDutyId) {
                $query->where('id', $userDutyId);
            })
            ->orderBy('event_date', 'asc')
            ->orderBy('schedule_id')
            ->get()
            ->filter(function($assignment) use ($today) {
                $daysDiff = $today->diffInDays(\Carbon\Carbon::parse($assignment->event_date));
                return $daysDiff <= 12;
            });
    @endphp

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow duration-300 mb-8">
        <!-- Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-purple-600 to-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-white">
                        <i class="fas fa-calendar-check mr-2"></i> 
                        Jadwal 12 Hari Terdekat
                    </h3>
                    <p class="text-purple-100 text-lg mt-1">
                        <i class="fas fa-users-cog mr-1"></i> 
                        Group: <span class="font-semibold">{{ $user->duty->name }}</span>
                        <span class="ml-2 bg-white/20 px-3 py-1 rounded-full text-base">
                            <i class="fas fa-check-circle mr-1"></i> Penugasan Group
                        </span>
                    </p>
                </div>
                <span class="bg-white/20 text-white px-4 py-2 rounded-full text-lg font-bold">
                    {{ $allUpcomingGroup->count() }} Penugasan
                </span>
            </div>
        </div>

        <!-- Daftar Penugasan Group -->
        <div class="divide-y divide-gray-100">
            @forelse($allUpcomingGroup->groupBy('event_date')->sortKeys() as $eventDate => $dateAssignments)
            @php
                $eventCarbon = \Carbon\Carbon::parse($eventDate);
                $dateFull = $eventCarbon->translatedFormat('l, d F Y');
                $daysDiff = $today->diffInDays($eventCarbon);
                $isToday = $eventCarbon->isToday();
                
                if ($isToday) {
                    $dayStatus = 'Hari ini';
                    $dayBadge = 'bg-red-100 text-red-700 animate-pulse';
                } elseif ($daysDiff <= 1) {
                    $dayStatus = 'Besok';
                    $dayBadge = 'bg-orange-100 text-orange-700';
                } elseif ($daysDiff <= 7) {
                    $dayStatus = $daysDiff . ' hari lagi';
                    $dayBadge = 'bg-yellow-100 text-yellow-700';
                } else {
                    $dayStatus = $eventCarbon->translatedFormat('d M Y');
                    $dayBadge = 'bg-blue-100 text-blue-700';
                }
                
                // Ambil schedule pertama untuk informasi hari
                $firstAssignment = $dateAssignments->first();
                $scheduleDay = ucfirst($firstAssignment->schedule->day ?? '');
                $scheduleTime = $firstAssignment->schedule->time ? \Carbon\Carbon::parse($firstAssignment->schedule->time)->format('H:i') : '';
            @endphp
            
            <div class="p-5 transition-all duration-200 hover:bg-purple-50/30 {{ $isToday ? 'bg-purple-50/30' : '' }}">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Left: Tanggal -->
                    <div class="flex items-start gap-4 flex-1">
                        <div class="flex-shrink-0 text-center min-w-[70px]">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex flex-col items-center justify-center text-white shadow-md">
                                <span class="text-xs font-semibold uppercase">{{ substr($eventCarbon->translatedFormat('l'), 0, 3) }}</span>
                                <span class="text-xl font-bold leading-none">{{ $eventCarbon->format('d') }}</span>
                            </div>
                            <div class="text-[10px] text-gray-500 mt-1 font-medium">
                                {{ $eventCarbon->translatedFormat('M') }}
                            </div>
                            @if($isToday)
                                <span class="text-[10px] text-red-500 font-bold bg-red-50 px-1.5 py-0.5 rounded-full mt-0.5 inline-block">
                                    Hari Ini
                                </span>
                            @endif
                        </div>
                        
                        <!-- Info Hari -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h4 class="font-bold text-gray-800 text-xl">
                                    {{ $dateFull }}
                                </h4>
                                <span class="text-sm text-gray-500 flex items-center gap-1">
                                    <i class="far fa-clock"></i>
                                    {{ $scheduleDay }} • {{ $scheduleTime }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <i class="fas fa-users text-purple-400"></i>
                                {{ $dateAssignments->count() }} penugasan
                            </p>
                            
                            <!-- Daftar Tugas per Schedule -->
                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                @foreach($dateAssignments->groupBy('schedule_id') as $scheduleId => $scheduleAssignments)
                                @php
                                    $schedule = $scheduleAssignments->first()->schedule;
                                    $scheduleName = $schedule->display ?? $schedule->name ?? 'Jadwal';
                                    $dutyName = $scheduleAssignments->first()->duty->name ?? '';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-700">
                                    <i class="fas fa-church mr-1.5"></i>
                                    {{ $scheduleName }}
                                    <span class="text-xs text-purple-500 ml-1">({{ $scheduleAssignments->count() }} org)</span>
                                </span>
                                @endforeach
                            </div>

                            <!-- Daftar Petugas -->
                            <div class="mt-3 flex flex-wrap items-center gap-1.5">
                                @foreach($dateAssignments as $assignment)
                                    @php
                                        $isCurrentUser = $assignment->user_id == $currentUserId;
                                        $userName = $assignment->user->name ?? 'User Tidak Diketahui';
                                        $status = $assignment->status;
                                        $statusColor = $status == 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
                                        $statusLabel = $status == 'confirmed' ? 'Confirmed' : 'Diterima';
                                        $dutyName = $assignment->duty->name ?? '';
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $isCurrentUser ? 'bg-purple-100 text-purple-700 border border-purple-300' : 'bg-gray-100 text-gray-600' }}">
                                        @if($isCurrentUser)
                                            <i class="fas fa-check-circle text-purple-500"></i>
                                        @else
                                            <i class="fas fa-user text-gray-400"></i>
                                        @endif
                                        {{ $userName }}
                                        @if($isCurrentUser)
                                            <span class="text-purple-600 font-bold">(Saya)</span>
                                        @endif
                                        <span class="text-xs {{ $statusColor }} px-1.5 py-0.5 rounded-full ml-0.5">
                                            {{ $statusLabel }}
                                        </span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right: Status Hari -->
                    <div class="flex flex-col items-start md:items-end gap-2 md:min-w-[120px]">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $dayBadge }}">
                            <i class="fas fa-calendar-day mr-1.5"></i>
                            {{ $dayStatus }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $dateAssignments->count() }} tugas
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center">
                <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-5xl text-purple-400"></i>
                </div>
                <p class="text-gray-600 font-bold text-2xl">
                    Belum ada jadwal
                </p>
                <p class="text-lg text-gray-400 mt-1">
                    Belum ada penugasan untuk group <span class="font-semibold text-purple-600">{{ $user->duty->name }}</span> dalam 12 hari ke depan
                </p>
                <a href="#tambah-penugasan" 
                   class="inline-flex items-center px-4 py-2 mt-4 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition duration-200 text-sm">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Penugasan
                </a>
            </div>
            @endforelse
        </div>
    </div>
    @endif
    
    <!-- ============================================ -->
    <!-- KALENDER LITURGI - TAMPIL UNTUK SEMUA ROLE -->
    <!-- ============================================ -->
    @if(isset($selectedLiturgi) && $selectedLiturgi)
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 mb-8">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center gap-2">
            <i class="fas fa-church text-blue-600 text-2xl"></i>
            <h4 class="font-bold text-gray-700 text-2xl">Kalender Liturgi</h4>
            <span class="text-base text-gray-500 bg-white/50 px-3 py-1 rounded-full">
                <i class="far fa-calendar-alt mr-1"></i>{{ isset($selectedDate) ? $selectedDate->translatedFormat('d M Y') : now()->translatedFormat('d M Y') }}
            </span>
        </div>
        <div class="p-6 bg-gray-50/30">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Keterangan Hari -->
                @if(isset($selectedLiturgi->keterangan_hari) && $selectedLiturgi->keterangan_hari)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-xl md:col-span-2 border-l-4 border-blue-500">
                    <div class="flex items-center gap-2 text-base text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        <i class="fas fa-feather-alt text-blue-500"></i>
                        <span>Keterangan Hari</span>
                    </div>
                    <p class="font-bold text-gray-800 text-2xl">{{ $selectedLiturgi->keterangan_hari }}</p>
                </div>
                @endif

                <!-- Warna Liturgi -->
                @if(isset($selectedLiturgi->warna_liturgi) && $selectedLiturgi->warna_liturgi)
                <div class="bg-white p-5 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 text-base text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        <i class="fas fa-palette text-purple-500"></i>
                        <span>Warna Liturgi</span>
                    </div>
                    <div class="flex items-center mt-1">
                        @php
                            $warnaMap = [
                                'putih' => '#FFFFFF',
                                'merah' => '#FF0000',
                                'hijau' => '#008000',
                                'ungu' => '#800080',
                                'kuning' => '#FFD700',
                                'krem' => '#F5DEB3',
                                'biru' => '#0000FF',
                                'hitam' => '#000000'
                            ];
                            $warnaHex = $warnaMap[strtolower($selectedLiturgi->warna_liturgi)] ?? '#ccc';
                            $isPutih = strtolower($selectedLiturgi->warna_liturgi) == 'putih';
                        @endphp
                        <div class="w-12 h-12 rounded-full mr-3 border-2 shadow-sm" 
                             style="background-color: {{ $warnaHex }}; {{ $isPutih ? 'border-color: #d1d5db;' : 'border-color: white;' }}"></div>
                        <p class="font-bold text-gray-800 text-2xl">{{ ucfirst($selectedLiturgi->warna_liturgi) }}</p>
                    </div>
                </div>
                @endif

                <!-- Bacaan Pertama -->
                @if(isset($selectedLiturgi->bacaan1) && $selectedLiturgi->bacaan1)
                <div class="bg-white p-5 rounded-xl border border-gray-100 md:col-span-2">
                    <div class="flex items-center gap-2 text-base text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        <i class="fas fa-book-open text-blue-500"></i>
                        <span>Bacaan Pertama</span>
                    </div>
                    <p class="text-gray-800 font-bold text-xl">{{ $selectedLiturgi->bacaan1 }}</p>
                </div>
                @endif

                <!-- Mazmur Tanggapan -->
                @if(isset($selectedLiturgi->mazmur_tanggapan) && $selectedLiturgi->mazmur_tanggapan)
                <div class="bg-white p-5 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 text-base text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        <i class="fas fa-music text-green-500"></i>
                        <span>Mazmur Tanggapan</span>
                    </div>
                    <p class="font-bold text-gray-800 text-xl">{{ $selectedLiturgi->mazmur_tanggapan }}</p>
                </div>
                @endif

                <!-- Bait Pengantar Injil -->
                @if(isset($selectedLiturgi->bait_pengarintarinjil) && $selectedLiturgi->bait_pengarintarinjil)
                <div class="bg-white p-5 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-2 text-base text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        <i class="fas fa-hands-praying text-purple-500"></i>
                        <span>Bait Pengantar Injil</span>
                    </div>
                    <p class="font-bold text-gray-800 text-xl">{{ $selectedLiturgi->bait_pengarintarinjil }}</p>
                </div>
                @endif

                <!-- Bacaan Injil -->
                @if(isset($selectedLiturgi->bacaan_injil) && $selectedLiturgi->bacaan_injil)
                <div class="bg-gradient-to-r from-red-50 to-pink-50 p-5 rounded-xl md:col-span-2 border-l-4 border-red-400">
                    <div class="flex items-center gap-2 text-base text-gray-500 uppercase tracking-wider font-semibold mb-1">
                        <i class="fas fa-cross text-red-500"></i>
                        <span>Bacaan Injil</span>
                    </div>
                    <p class="text-gray-800 font-bold text-xl">{{ $selectedLiturgi->bacaan_injil }}</p>
                </div>
                @endif

                <!-- Catatan Liturgi -->
                @if(isset($selectedLiturgi->catatan) && $selectedLiturgi->catatan)
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 p-5 rounded-xl md:col-span-2 border border-yellow-200">
                    <div class="flex items-center gap-2 text-base text-yellow-700 font-semibold mb-1">
                        <i class="fas fa-sticky-note"></i>
                        <span>Catatan Liturgi</span>
                    </div>
                    <p class="text-yellow-800 text-xl">{{ $selectedLiturgi->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- GRAFIK (HANYA UNTUK ADMIN SUPER) -->
    <!-- ============================================ -->
    @if(auth()->user()->isAdmin())
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i> Statistik Penugasan
                </h3>
                <span class="text-xs text-gray-400" id="chartUpdateTime">Update: {{ now()->format('H:i:s') }}</span>
            </div>
            <div class="h-64">
                <canvas id="assignmentChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-chart-pie mr-2 text-purple-500"></i> Distribusi Penugasan
                </h3>
                <span class="text-xs text-gray-400">Berdasarkan Status</span>
            </div>
            <div class="h-64">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-chart-line mr-2 text-green-500"></i> Penugasan per Gereja
            </h3>
            <span class="text-xs text-gray-400">Bulan Ini</span>
        </div>
        <div class="h-72">
            <canvas id="churchChart"></canvas>
        </div>
    </div>
    @endif
</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData ?? []);
    
    function updateDateTime() {
        const now = new Date();
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const dateTimeString = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
        const display = document.getElementById('dateTimeDisplay');
        if (display) display.textContent = dateTimeString;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    @if(auth()->user()->isAdmin())
    new Chart(document.getElementById('assignmentChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: chartData.labels ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Penugasan',
                data: chartData.assignmentData ?? [5, 8, 3, 7, 6, 4, 9],
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    new Chart(document.getElementById('distributionChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Menunggu', 'Diterima', 'Ditolak', 'Selesai'],
            datasets: [{
                data: [
                    {{ $stats->pending_assignments ?? 0 }},
                    {{ $stats->accepted_assignments ?? 0 }},
                    {{ $stats->rejected_assignments ?? 0 }},
                    {{ $stats->completed_assignments ?? 0 }}
                ],
                backgroundColor: ['#eab308', '#22c55e', '#ef4444', '#3b82f6'],
                borderWidth: 3,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            },
            cutout: '65%',
        }
    });

    new Chart(document.getElementById('churchChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: chartData.churchLabels ?? ['Gereja A', 'Gereja B', 'Gereja C', 'Gereja D'],
            datasets: [{
                label: 'Jumlah Penugasan',
                data: chartData.churchData ?? [12, 19, 8, 15],
                borderColor: 'rgba(34, 197, 94, 1)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
    @endif
});

@if(auth()->user()->isAdmin())
setInterval(function() {
    const updateTime = document.getElementById('chartUpdateTime');
    if (updateTime) {
        const now = new Date();
        updateTime.textContent = `Update: ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
    }
}, 1000);
@endif

function toggleAmbilTugas() {
    const container = document.getElementById('ambilTugasContainer');
    const text = document.getElementById('ambilTugasText');
    const icon = document.getElementById('ambilTugasIcon');
    
    if (container) {
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            if (text) text.textContent = 'Sembunyikan';
            if (icon) icon.className = 'fas fa-chevron-up text-base group-hover:translate-x-0.5 transition-transform';
        } else {
            container.classList.add('hidden');
            if (text) text.textContent = 'Tampilkan Jadwal';
            if (icon) icon.className = 'fas fa-chevron-down text-base group-hover:translate-x-0.5 transition-transform';
        }
    }
}
</script>

<!-- ============================================ -->
<!-- FORM SCRIPTS UNTUK PIC GROUP -->
<!-- ============================================ -->
@if(auth()->user()->level_akses === 'pic_group')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dutySelect = document.getElementById('duty_id');
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
    
    let checkTimeout;
    let isSubmitting = false;
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.petugas-checkbox:checked:not([style*="display: none"])').length;
        if (selectedCount) selectedCount.textContent = checked;
        if (selectedCountDisplay) selectedCountDisplay.textContent = checked;
    }
    
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
            checkBox.classList.add('bg-purple-500', 'border-purple-500');
            checkIcon.classList.remove('opacity-0', 'scale-75');
            checkIcon.classList.add('opacity-100', 'scale-100');
            item.classList.add('bg-purple-50', 'border-purple-200');
            item.classList.remove('hover:bg-purple-50');
        } else {
            checkBox.classList.remove('bg-purple-500', 'border-purple-500');
            checkBox.classList.add('border-gray-300', 'hover:border-purple-400');
            checkIcon.classList.remove('opacity-100', 'scale-100');
            checkIcon.classList.add('opacity-0', 'scale-75');
            item.classList.remove('bg-purple-50', 'border-purple-200');
            item.classList.add('hover:bg-purple-50');
        }
    }
    
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
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Konfirmasi Penugasan';
        }
        
        if (!schedule_id || !duty_id || !event_date || selectedUsers.length === 0) {
            if (availabilityStatus && selectedUsers.length === 0) {
                availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-gray-50 border border-gray-200 text-gray-600';
                availabilityStatus.innerHTML = '<i class="fas fa-info-circle mr-2"></i> Pilih minimal 1 petugas untuk ditugaskan.';
                availabilityStatus.classList.remove('hidden');
            }
            return;
        }
        
        if (availabilityStatus) {
            availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-gray-100 text-gray-700';
            availabilityStatus.classList.remove('hidden');
        }
        
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
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Konfirmasi Penugasan';
                }
            } else {
                availabilityStatus.className = 'mt-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700';
                availabilityStatus.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Semua petugas tersedia untuk ditugaskan.';
                availabilityStatus.classList.remove('hidden');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Konfirmasi Penugasan';
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
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Konfirmasi Penugasan';
            }
        });
    }
    
    // Event Listeners
    if (selectAllBtn) selectAllBtn.addEventListener('click', selectAll);
    if (unselectAllBtn) unselectAllBtn.addEventListener('click', unselectAll);
    
    // ============================================
    // GENERATE DATE OPTIONS ON PAGE LOAD
    // ============================================
    if (scheduleSelect) {
        // Fungsi untuk generate tanggal dengan retry jika select belum siap
        function generateDateWithRetry(retryCount) {
            retryCount = retryCount || 0;
            
            const selectedOption = scheduleSelect.options[scheduleSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                generateDateOptions(selectedOption);
                return;
            }
            
            // Jika belum ada option yang dipilih, coba dari hidden value
            const hiddenValue = scheduleHidden ? scheduleHidden.value : null;
            if (hiddenValue) {
                for (let i = 0; i < scheduleSelect.options.length; i++) {
                    if (scheduleSelect.options[i].value == hiddenValue) {
                        scheduleSelect.selectedIndex = i;
                        generateDateOptions(scheduleSelect.options[i]);
                        return;
                    }
                }
            }
            
            // Jika masih belum dan retry kurang dari 5, coba lagi
            if (retryCount < 5) {
                setTimeout(function() {
                    generateDateWithRetry(retryCount + 1);
                }, 300);
            }
        }
        
        // Jalankan generate tanggal dengan retry
        setTimeout(function() {
            generateDateWithRetry(0);
        }, 200);
        
        // Event change
        scheduleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (scheduleHidden) scheduleHidden.value = this.value;
            generateDateOptions(selectedOption);
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
    
    petugasCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectedCount();
        });
    });
    
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
    
    // Initialize - check availability after 500ms
    setTimeout(checkAvailability, 500);
});
</script>
@endif
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
        border-color: #d8b4fe;
    }
    
    .petugas-item.bg-purple-50 {
        border-color: #c084fc;
    }
    
    select:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    #petugasContainer {
        max-height: 250px;
        overflow-y: auto;
    }
    
    #petugasContainer::-webkit-scrollbar {
        width: 4px;
    }
    
    #petugasContainer::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #petugasContainer::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    #petugasContainer::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush
@endsection