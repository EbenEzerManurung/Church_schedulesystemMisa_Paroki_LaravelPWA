{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Form Tambah User</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST" id="userForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name') }}" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" 
                           value="{{ old('email') }}" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror" 
                           placeholder="Kosongkan untuk menggunakan password default">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> 
                        Biarkan kosong untuk menggunakan password default: <strong>password</strong>
                    </p>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Level Akses <span class="text-red-500">*</span></label>
                    <select name="level_akses" id="level_akses" class="form-select @error('level_akses') border-red-500 @enderror" required>
                        <option value="">Pilih Level Akses</option>
                        @if(auth()->user()->isSuperAdmin())
                        <option value="super_admin" {{ old('level_akses') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        @endif
                        <option value="admin_keuskupan" {{ old('level_akses') == 'admin_keuskupan' ? 'selected' : '' }}>Admin Keuskupan</option>
                        <option value="admin_gereja" {{ old('level_akses') == 'admin_gereja' ? 'selected' : '' }}>Admin Gereja</option>
                        <option value="user" {{ old('level_akses') == 'user' ? 'selected' : '' }}>User Biasa</option>
                    </select>
                    @error('level_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group" id="keuskupan_group" style="{{ old('level_akses') == 'admin_keuskupan' ? 'display: block;' : 'display: none;' }}">
                    <label class="form-label">Keuskupan <span class="text-red-500">*</span></label>
                    <select name="keuskupan_id" id="keuskupan_id" class="form-select @error('keuskupan_id') border-red-500 @enderror">
                        <option value="">Pilih Keuskupan</option>
                        @foreach($keuskupans as $keuskupan)
                        <option value="{{ $keuskupan->id }}" {{ old('keuskupan_id') == $keuskupan->id ? 'selected' : '' }}>
                            {{ $keuskupan->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('keuskupan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group" id="gereja_group" style="{{ (old('level_akses') == 'admin_gereja' || old('level_akses') == 'user') ? 'display: block;' : 'display: none;' }}">
                    <label class="form-label">Gereja <span class="text-red-500">*</span></label>
                    <select name="gereja_id" id="gereja_id" class="form-select @error('gereja_id') border-red-500 @enderror">
                        <option value="">Pilih Gereja</option>
                        @foreach($gerejas as $gereja)
                        <option value="{{ $gereja->id }}" {{ old('gereja_id') == $gereja->id ? 'selected' : '' }}>
                            {{ $gereja->nama }} - {{ $gereja->lokasi }}
                        </option>
                        @endforeach
                    </select>
                    @error('gereja_id') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden input untuk memastikan gereja_id terkirim -->
                <input type="hidden" name="gereja_id_hidden" id="gereja_id_hidden" value="">

                <!-- Tugas Tetap -->
                <div class="form-group">
                    <label class="form-label">Tugas Tetap</label>
                    <select name="duty_id" class="form-select">
                        <option value="">-- Pilih Tugas Tetap --</option>
                        @foreach($duties as $duty)
                        <option value="{{ $duty->id }}" {{ old('duty_id') == $duty->id ? 'selected' : '' }}>
                            [{{ $duty->code ?? 'N/A' }}] {{ $duty->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Tugas pelayanan tetap yang akan diemban user ini
                    </p>
                    @error('duty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-textarea" rows="3">{{ old('address') }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('users.index') }}" class="btn btn-warning">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelAkses = document.getElementById('level_akses');
    const keuskupanGroup = document.getElementById('keuskupan_group');
    const gerejaGroup = document.getElementById('gereja_group');
    const keuskupanSelect = document.getElementById('keuskupan_id');
    const gerejaSelect = document.getElementById('gereja_id');
    const gerejaHidden = document.getElementById('gereja_id_hidden');
    const submitBtn = document.getElementById('submitBtn');
    
    function toggleFields() {
        // Reset required attributes
        if (keuskupanSelect) {
            keuskupanSelect.required = false;
            keuskupanSelect.disabled = false;
        }
        if (gerejaSelect) {
            gerejaSelect.required = false;
            gerejaSelect.disabled = false;
        }
        
        // Hide both groups first
        if (keuskupanGroup) {
            keuskupanGroup.style.display = 'none';
        }
        if (gerejaGroup) {
            gerejaGroup.style.display = 'none';
        }
        
        const selectedValue = levelAkses ? levelAkses.value : '';
        
        if (selectedValue === 'admin_keuskupan') {
            // Tampilkan Keuskupan
            if (keuskupanGroup) {
                keuskupanGroup.style.display = 'block';
            }
            if (keuskupanSelect) {
                keuskupanSelect.required = true;
                keuskupanSelect.disabled = false;
            }
            // Kosongkan gereja hidden
            if (gerejaHidden) {
                gerejaHidden.value = '';
            }
        } else if (selectedValue === 'admin_gereja' || selectedValue === 'user') {
            // Tampilkan Gereja
            if (gerejaGroup) {
                gerejaGroup.style.display = 'block';
            }
            if (gerejaSelect) {
                gerejaSelect.required = true;
                gerejaSelect.disabled = false;
            }
            
            // Jika admin gereja, pilih gereja yang sesuai dan disable
            @if(auth()->user()->isAdminGereja())
            if (gerejaSelect) {
                gerejaSelect.value = "{{ auth()->user()->gereja_id }}";
                gerejaSelect.disabled = true;
                gerejaSelect.required = true;
            }
            @endif
            
            // Update hidden input dengan value gereja_id
            if (gerejaHidden && gerejaSelect) {
                gerejaHidden.value = gerejaSelect.value;
            }
        }
        
        console.log('Level Akses:', selectedValue);
        console.log('Gereja ID:', gerejaSelect ? gerejaSelect.value : 'not found');
        console.log('Gereja Hidden:', gerejaHidden ? gerejaHidden.value : 'not found');
    }
    
    // Event listener untuk perubahan pada select gereja
    if (gerejaSelect) {
        gerejaSelect.addEventListener('change', function() {
            if (gerejaHidden) {
                gerejaHidden.value = this.value;
                console.log('Gereja Hidden updated:', this.value);
            }
        });
    }
    
    if (levelAkses) {
        levelAkses.addEventListener('change', toggleFields);
        toggleFields();
    }
    
    // Sebelum submit, pastikan gereja_id terisi
    document.getElementById('userForm').addEventListener('submit', function(e) {
        const selectedValue = levelAkses ? levelAkses.value : '';
        
        // Jika level_akses adalah admin_gereja atau user, pastikan gereja_id terisi
        if (selectedValue === 'admin_gereja' || selectedValue === 'user') {
            if (gerejaSelect) {
                // Enable select terlebih dahulu agar value-nya terkirim
                gerejaSelect.disabled = false;
                
                // Jika value kosong, coba ambil dari hidden
                if (!gerejaSelect.value && gerejaHidden) {
                    gerejaSelect.value = gerejaHidden.value;
                }
                
                // Jika masih kosong, tampilkan error
                if (!gerejaSelect.value) {
                    e.preventDefault();
                    alert('Silakan pilih Gereja terlebih dahulu.');
                    return false;
                }
            }
        }
    });
});
</script>

<style>
/* Style untuk field yang disabled */
select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.7;
}
</style>
@endsection