@extends('layouts.app')

@section('title', 'Edit Menu & Hak Akses')
@section('page-title', 'Edit Menu & Hak Akses')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Form Edit Menu -->
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">Edit Informasi Menu</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('/permissions/menu/' . $menu->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-4">
                    <div class="form-group">
                        <label class="form-label">Nama Menu <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" 
                               value="{{ old('name', $menu->name) }}" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">URL <span class="text-red-500">*</span></label>
                        <input type="text" name="url" class="form-input @error('url') border-red-500 @enderror" 
                               value="{{ old('url', $menu->url) }}" required>
                        @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Icon (Font Awesome)</label>
                        <input type="text" name="icon" class="form-input" value="{{ old('icon', $menu->icon) }}" placeholder="fa-circle">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Parent Menu</label>
                        <select name="parent_id" class="form-select">
                            <option value="">- Root Menu -</option>
                            @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Order / Urutan</label>
                        <input type="number" name="order" class="form-input" value="{{ old('order', $menu->order) }}">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Status Menu</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $menu->is_active) == '1' ? 'selected' : '' }}>Aktif (Ditampilkan)</option>
                            <option value="0" {{ old('is_active', $menu->is_active) == '0' ? 'selected' : '' }}>Nonaktif (Disembunyikan)</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('permissions.index') }}" class="btn btn-warning">Batal</a>
                    <button type="submit" class="btn btn-primary">Update Menu</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Hak Akses per Role -->
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">Edit Hak Akses untuk Menu "{{ $menu->name }}"</h3>
        </div>
        <div class="card-body">
            <form action="{{ url('/permissions/menu/' . $menu->id . '/access') }}" method="POST">
                @csrf
                @method('PUT')
                
                <p class="text-sm text-gray-600 mb-4">
                    <i class="fas fa-info-circle text-blue-500"></i> 
                    Centang checkbox di bawah untuk memberikan akses ke role tertentu.
                </p>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th class="text-center w-24">View <span class="text-xs text-gray-500">(Lihat Menu)</span></th>
                                <th class="text-center w-24">Create <span class="text-xs text-gray-500">(Tambah)</span></th>
                                <th class="text-center w-24">Edit <span class="text-xs text-gray-500">(Ubah)</span></th>
                                <th class="text-center w-24">Delete <span class="text-xs text-gray-500">(Hapus)</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td class="font-medium">
                                    {{ ucfirst($role->name) }}
                                    @if($role->name == 'admin')
                                        <span class="text-xs text-blue-500">(Super User)</span>
                                    @elseif($role->name == 'pastor')
                                        <span class="text-xs text-green-500">(Pengelola)</span>
                                    @else
                                        <span class="text-xs text-gray-500">(Jemaat)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][can_view]" 
                                           value="1" {{ $menu->hasAccess($role, 'view') ? 'checked' : '' }}
                                           class="form-checkbox h-5 w-5 text-blue-600 rounded"
                                           {{ $role->name == 'admin' ? 'disabled' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][can_create]" value="1"
                                           {{ $menu->hasAccess($role, 'create') ? 'checked' : '' }}
                                           class="form-checkbox h-5 w-5 text-green-600 rounded"
                                           {{ $role->name == 'admin' ? 'disabled' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][can_edit]" value="1"
                                           {{ $menu->hasAccess($role, 'edit') ? 'checked' : '' }}
                                           class="form-checkbox h-5 w-5 text-yellow-600 rounded"
                                           {{ $role->name == 'admin' ? 'disabled' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="permissions[{{ $role->id }}][can_delete]" value="1"
                                           {{ $menu->hasAccess($role, 'delete') ? 'checked' : '' }}
                                           class="form-checkbox h-5 w-5 text-red-600 rounded"
                                           {{ $role->name == 'admin' ? 'disabled' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mt-4 text-sm text-yellow-700">
                    <i class="fas fa-lock mr-2"></i> 
                    Role <strong>Admin</strong> memiliki akses penuh secara otomatis dan tidak dapat diubah.
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Informasi Tambahan -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Informasi Menu</h3>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-gray-500 text-sm">ID Menu</p>
                <p class="font-medium">{{ $menu->id }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Dibuat Pada</p>
                <p class="font-medium">{{ $menu->created_at ? $menu->created_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Terakhir Update</p>
                <p class="font-medium">{{ $menu->updated_at ? $menu->updated_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">URL Preview</p>
                <p class="font-medium text-blue-600">{{ url($menu->url) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Preview Hak Akses -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="font-semibold text-gray-800">Ringkasan Hak Akses Menu "{{ $menu->name }}"</h3>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th class="text-center">View</th>
                        <th class="text-center">Create</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="font-medium">{{ ucfirst($role->name) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $menu->hasAccess($role, 'view') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $menu->hasAccess($role, 'view') ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $menu->hasAccess($role, 'create') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $menu->hasAccess($role, 'create') ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $menu->hasAccess($role, 'edit') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $menu->hasAccess($role, 'edit') ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $menu->hasAccess($role, 'delete') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $menu->hasAccess($role, 'delete') ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection