@extends('layouts.app')

@section('title', 'Kelola Akses Menu')
@section('page-title', 'Kelola Hak Akses Menu')

@section('content')
<div class="space-y-6">
    <!-- Header dengan Tombol Tambah Menu -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Daftar Menu & Hak Akses per Role</h3>
                <button type="button" onclick="showAddMenuModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Menu Baru
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Tabel Menu -->
            <div class="overflow-x-auto mb-8">
                <h4 class="font-semibold text-gray-700 mb-3">Menu yang Tersedia</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Menu</th>
                            <th>URL</th>
                            <th>Icon</th>
                            <th>Parent</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                        <tr class="{{ $menu->is_active ? '' : 'bg-gray-100' }}">
                            <td class="font-medium">{{ $menu->name }} 
                                @if(!$menu->is_active) <span class="text-xs text-red-500">(Nonaktif)</span> @endif
                            </td>
                            <td><code class="text-sm">{{ $menu->url }}</code></td>
                            <td><i class="fas {{ $menu->icon }}"></i> {{ $menu->icon }}</td>
                            <td>{{ $menu->parent->name ?? '-' }}</td>
                            <td class="text-center">{{ $menu->order }}</td>
                            <td>
                                <span class="badge {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="flex space-x-2">
                                    <button onclick="editMenu({{ $menu->id }})" class="text-yellow-600 hover:text-yellow-800" title="Edit Menu">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(!$menu->children->count())
                                    <form action="{{ url('/permissions/menu/' . $menu->id) }}" method="POST" 
                                          onsubmit="return confirm('Hapus menu {{ $menu->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus Menu">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($menu->children->count())
                            @foreach($menu->children as $child)
                            <tr class="bg-gray-50 {{ $child->is_active ? '' : 'bg-gray-100' }}">
                                <td class="pl-8">↳ {{ $child->name }}
                                    @if(!$child->is_active) <span class="text-xs text-red-500">(Nonaktif)</span> @endif
                                </td>
                                <td><code class="text-sm">{{ $child->url }}</code></td>
                                <td><i class="fas {{ $child->icon }}"></i> {{ $child->icon }}</td>
                                <td>{{ $child->parent->name ?? '-' }}</td>
                                <td class="text-center">{{ $child->order }}</td>
                                <td>
                                    <span class="badge {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button onclick="editMenu({{ $child->id }})" class="text-yellow-600 hover:text-yellow-800" title="Edit Menu">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ url('/permissions/menu/' . $child->id) }}" method="POST" 
                                              onsubmit="return confirm('Hapus menu {{ $child->name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus Menu">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Hak Akses per Role -->
            <h4 class="font-semibold text-gray-700 mb-3 mt-6">Hak Akses per Role</h4>
            @foreach($roles as $role)
            <div class="border rounded-lg overflow-hidden mt-4">
                <div class="bg-gray-100 px-4 py-3 border-b">
                    <div class="flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">
                            <i class="fas fa-user-tag mr-2"></i> Role: {{ ucfirst($role->name) }}
                        </h4>
                        <form action="{{ url('/permissions/reset') }}" method="POST" class="inline" 
                              onsubmit="return confirm('Reset hak akses ke default untuk role {{ $role->name }}? Semua pengaturan akan kembali ke awal.')">
                            @csrf
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="fas fa-undo"></i> Reset ke Default
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-4">
                    <form action="{{ url('/permissions') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role->id }}">
                        
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="w-64">Menu</th>
                                        <th class="text-center w-24">View <span class="text-xs text-gray-500">(Lihat)</span></th>
                                        <th class="text-center w-24">Create <span class="text-xs text-gray-500">(Tambah)</span></th>
                                        <th class="text-center w-24">Edit <span class="text-xs text-gray-500">(Ubah)</span></th>
                                        <th class="text-center w-24">Delete <span class="text-xs text-gray-500">(Hapus)</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menus as $menu)
                                    <tr>
                                        <td class="font-medium">{{ $menu->name }}
                                            <span class="text-xs text-gray-400 block">{{ $menu->url }}</span>
                                        </td>
                                        <td class="text-center">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="permissions[{{ $menu->id }}][can_view]" 
                                                       value="1" {{ isset($permissions[$role->id][$menu->id]['can_view']) && $permissions[$role->id][$menu->id]['can_view'] ? 'checked' : '' }}
                                                       class="form-checkbox h-5 w-5 text-blue-600 rounded">
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $menu->id }}][can_create]" value="1"
                                                   {{ isset($permissions[$role->id][$menu->id]['can_create']) && $permissions[$role->id][$menu->id]['can_create'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-green-600 rounded">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $menu->id }}][can_edit]" value="1"
                                                   {{ isset($permissions[$role->id][$menu->id]['can_edit']) && $permissions[$role->id][$menu->id]['can_edit'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-yellow-600 rounded">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $menu->id }}][can_delete]" value="1"
                                                   {{ isset($permissions[$role->id][$menu->id]['can_delete']) && $permissions[$role->id][$menu->id]['can_delete'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-red-600 rounded">
                                        </td>
                                    </tr>
                                    @foreach($menu->children as $child)
                                    <tr class="bg-gray-50">
                                        <td class="pl-8">↳ {{ $child->name }}
                                            <span class="text-xs text-gray-400 block pl-4">{{ $child->url }}</span>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $child->id }}][can_view]" 
                                                   value="1" {{ isset($permissions[$role->id][$child->id]['can_view']) && $permissions[$role->id][$child->id]['can_view'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-blue-600 rounded">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $child->id }}][can_create]" value="1"
                                                   {{ isset($permissions[$role->id][$child->id]['can_create']) && $permissions[$role->id][$child->id]['can_create'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-green-600 rounded">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $child->id }}][can_edit]" value="1"
                                                   {{ isset($permissions[$role->id][$child->id]['can_edit']) && $permissions[$role->id][$child->id]['can_edit'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-yellow-600 rounded">
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $child->id }}][can_delete]" value="1"
                                                   {{ isset($permissions[$role->id][$child->id]['can_delete']) && $permissions[$role->id][$child->id]['can_delete'] ? 'checked' : '' }}
                                                   class="form-checkbox h-5 w-5 text-red-600 rounded">
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Hak Akses untuk {{ ucfirst($role->name) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div id="addMenuModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Tambah Menu Baru</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ url('/permissions/add-menu') }}" method="POST" class="px-6 py-4">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Menu <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">URL <span class="text-red-500">*</span></label>
                    <input type="text" name="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="/nama-menu" required>
                    <p class="text-xs text-gray-500 mt-1">Contoh: /dashboard, /users, /laporan</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Icon (Font Awesome)</label>
                    <input type="text" name="icon" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="fa-circle">
                    <p class="text-xs text-gray-500 mt-1">Contoh: fa-dashboard, fa-users, fa-chart-bar</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Parent Menu</label>
                    <select name="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">- Root Menu (Menu Utama) -</option>
                        @foreach($menus as $menu)
                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Order / Urutan</label>
                    <input type="number" name="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="0">
                    <p class="text-xs text-gray-500 mt-1">Semakin kecil angka, semakin atas posisi menu</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tambah Menu</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showAddMenuModal() {
        document.getElementById('addMenuModal').classList.remove('hidden');
        document.getElementById('addMenuModal').classList.add('flex');
    }
    
    function closeModal() {
        document.getElementById('addMenuModal').classList.add('hidden');
        document.getElementById('addMenuModal').classList.remove('flex');
    }
    
    function editMenu(menuId) {
        window.location.href = '/permissions/menu/' + menuId + '/edit';
    }
    
    // Klik di luar modal untuk menutup
    document.getElementById('addMenuModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
@endsection