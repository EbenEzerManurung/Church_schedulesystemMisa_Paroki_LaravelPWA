@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Form Edit User</h3>
    </div>
    <div class="card-body">
        {{-- PERBAIKAN 1: Form action dengan parameter eksplisit --}}
        <form action="{{ route('users.update', ['user' => $user->id]) }}" method="POST">
            @csrf
            {{-- PERBAIKAN 2: Method PUT dengan cara yang lebih aman --}}
            @method('PUT')
            
            {{-- PERBAIKAN 3: Tambahkan debug sementara (opsional) --}}
            {{-- <div class="bg-yellow-100 p-2 mb-4 text-xs">
                <p><strong>Debug:</strong></p>
                <p>User ID: {{ $user->id }}</p>
                <p>Route: {{ route('users.update', ['user' => $user->id]) }}</p>
            </div> --}}
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" 
                           value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label class="form-label">Password <span class="text-gray-400 text-xs">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="form-input @error('password') border-red-500 @enderror">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Phone -->
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}">
                </div>
                
                <!-- Role dengan name="level_akses" -->
                <div class="form-group">
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="level_akses" class="form-select @error('level_akses') border-red-500 @enderror" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('level_akses', $user->level_akses) == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                        @endforeach
                    </select>
                    @error('level_akses') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Status -->
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                
                <!-- PERBAIKAN 4: Field Duty dengan selected yang lebih baik -->
                <div class="form-group">
                    <label class="form-label">Group Tugas</label>
                    <select name="duty_id" class="form-select @error('duty_id') border-red-500 @enderror">
                        <option value="">Pilih Group Tugas</option>
                        @foreach($duties as $duty)
                            <option value="{{ $duty->id }}" 
                                {{ (old('duty_id', $user->duty_id) == $duty->id) ? 'selected' : '' }}>
                                {{ $duty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('duty_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Alamat -->
                <div class="form-group md:col-span-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-textarea" rows="3">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>
            
            <!-- PERBAIKAN 5: Hidden fields untuk keuskupan dan gereja -->
            @if(auth()->user()->isAdminKeuskupan() || auth()->user()->isAdminGereja())
                <input type="hidden" name="gereja_id" value="{{ old('gereja_id', $user->gereja_id) }}">
                <input type="hidden" name="keuskupan_id" value="{{ old('keuskupan_id', $user->keuskupan_id) }}">
            @endif
            
            {{-- PERBAIKAN 6: Tambahkan hidden field untuk Super Admin --}}
            @if(auth()->user()->isSuperAdmin())
                <input type="hidden" name="keuskupan_id" value="{{ old('keuskupan_id', $user->keuskupan_id) }}">
                <input type="hidden" name="gereja_id" value="{{ old('gereja_id', $user->gereja_id) }}">
            @endif
            
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('users.index') }}" class="btn btn-warning">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- PERBAIKAN 7: Script untuk debugging (opsional) --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitted');
                console.log('Action:', this.action);
                console.log('Method:', this.method);
                
                // Cek apakah ada _method
                const methodInput = this.querySelector('input[name="_method"]');
                if (methodInput) {
                    console.log('_method value:', methodInput.value);
                }
            });
        }
    });
</script>
@endpush
@endsection