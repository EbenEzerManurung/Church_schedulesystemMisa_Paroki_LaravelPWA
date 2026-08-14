{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user mr-2"></i> Detail User
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('users.edit', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi User -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="card-header bg-gray-50 border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-info-circle mr-2"></i> Informasi User
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-500">Nama Lengkap</label>
                            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Email</label>
                            <p class="text-gray-700">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Level Akses</label>
                            <p>
                                @php
                                    $roleColors = [
                                        'super_admin' => 'bg-purple-100 text-purple-800',
                                        'admin_keuskupan' => 'bg-blue-100 text-blue-800',
                                        'admin_gereja' => 'bg-green-100 text-green-800',
                                        'user' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $roleIcons = [
                                        'super_admin' => 'fa-crown',
                                        'admin_keuskupan' => 'fa-diocese',
                                        'admin_gereja' => 'fa-church',
                                        'user' => 'fa-user',
                                    ];
                                    $roleLabels = [
                                        'super_admin' => 'Super Admin',
                                        'admin_keuskupan' => 'Admin Keuskupan',
                                        'admin_gereja' => 'Admin Gereja',
                                        'user' => 'User Biasa',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->level_akses] ?? 'bg-gray-100' }}">
                                    <i class="fas {{ $roleIcons[$user->level_akses] ?? 'fa-user' }} mr-1"></i>
                                    {{ $roleLabels[$user->level_akses] ?? $user->level_akses }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">No. Telepon</label>
                            <p class="text-gray-700">{{ $user->phone ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-500">Alamat</label>
                            <p class="text-gray-700">{{ $user->address ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Status</label>
                            <p>
                                @if($user->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Nonaktif
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Tanggal Dibuat</label>
                            <p class="text-gray-700">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Terakhir Diupdate</label>
                            <p class="text-gray-700">{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Organisasi -->
        <div>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="card-header bg-gray-50 border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-building mr-2"></i> Informasi Organisasi
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-gray-500">Keuskupan</label>
                            @if($user->keuskupan)
                                <p class="font-semibold text-gray-800">
                                    <i class="fas fa-diocese mr-1 text-blue-500"></i>
                                    {{ $user->keuskupan->name }}
                                </p>
                            @else
                                <p class="text-gray-500 italic">- Tidak terdaftar di keuskupan -</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Gereja</label>
                            @if($user->gereja)
                                <p class="font-semibold text-gray-800">
                                    <i class="fas fa-church mr-1 text-green-500"></i>
                                    {{ $user->gereja->nama }}
                                </p>
                                @if($user->gereja->lokasi)
                                    <p class="text-xs text-gray-500 mt-1">{{ $user->gereja->lokasi }}</p>
                                @endif
                            @else
                                <p class="text-gray-500 italic">- Tidak terdaftar di gereja -</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Reset Password -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="card-header bg-gray-50 border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-key mr-2"></i> Keamanan
                    </h3>
                </div>
                <div class="card-body p-6">
                    <button type="button" 
                            onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->name }}')"
                            class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-sync-alt mr-2"></i> Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-key text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Reset Password</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Reset password untuk user <strong id="resetUserName"></strong>?
                </p>
                <p class="text-xs text-red-500 mt-2">
                    <i class="fas fa-warning"></i> Password baru akan dikirim ke email user.
                </p>
            </div>
            <form id="resetPasswordForm" method="POST" class="mt-4">
                @csrf
                <input type="password" name="password" placeholder="Password Baru" class="w-full px-3 py-2 border rounded-md mb-2" required>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="w-full px-3 py-2 border rounded-md" required>
                <div class="flex justify-center gap-3 mt-4">
                    <button type="button" onclick="closeResetModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-700">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let resetUserId = null;
    
    function openResetPasswordModal(id, name) {
        resetUserId = id;
        document.getElementById('resetUserName').innerText = name;
        document.getElementById('resetPasswordForm').action = '/users/' + id + '/reset-password';
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }
    
    function closeResetModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
        resetUserId = null;
    }
    
    // Close modal when clicking outside
    document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeResetModal();
        }
    });
</script>
@endsection