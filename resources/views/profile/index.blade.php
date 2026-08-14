{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-circle mr-2"></i> Profil Saya
        </h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Profil dengan Upload Foto -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="text-center py-6 bg-gradient-to-r from-blue-500 to-blue-700">
                    <div class="relative inline-block">
                        <!-- Avatar / Foto Profil -->
                        <div id="avatarContainer" class="cursor-pointer group">
                            @if($user->photo)
                                <img id="profileImage" src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" 
                                     class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover">
                            @else
                                <div id="profileInitial" class="w-28 h-28 rounded-full bg-white flex items-center justify-center mx-auto shadow-lg">
                                    <span class="text-4xl font-bold text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <!-- Overlay untuk upload -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <i class="fas fa-camera text-white text-2xl"></i>
                            </div>
                        </div>
                        
                        <!-- Form Upload Foto (Hidden) -->
                        <form id="uploadPhotoForm" action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden">
                        </form>
                    </div>
                    
                    <h3 class="text-white font-bold mt-3">{{ $user->name }}</h3>
                    <p class="text-blue-100 text-sm">
                        @php
                            $roleLabels = [
                                'super_admin' => 'Super Admin',
                                'admin_keuskupan' => 'Admin Keuskupan',
                                'admin_gereja' => 'Admin Gereja',
                                'user' => 'User Biasa',
                            ];
                        @endphp
                        {{ $roleLabels[$user->level_akses] ?? $user->level_akses }}
                    </p>
                </div>
                
                <div class="p-4 border-t">
                    <div class="text-sm text-gray-600">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-envelope w-5 text-gray-400"></i>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        @if($user->phone)
                        <div class="flex items-center mb-2">
                            <i class="fas fa-phone w-5 text-gray-400"></i>
                            <span>{{ $user->phone }}</span>
                        </div>
                        @endif
                        <div class="flex items-center">
                            <i class="fas fa-calendar w-5 text-gray-400"></i>
                            <span>Bergabung: {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tombol Hapus Foto -->
                @if($user->photo)
                <div class="p-4 border-t">
                    <form action="{{ route('profile.photo.remove') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-red-600 hover:text-red-700 text-sm flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i> Hapus Foto
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Form Edit Profil -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="card-header bg-gray-50 border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-edit mr-2"></i> Edit Profil
                    </h3>
                </div>
                <div class="card-body p-6">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                <input type="text" name="phone" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       value="{{ old('phone', $user->phone) }}">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <textarea name="address" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                          rows="3">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form Ganti Password -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="card-header bg-gray-50 border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-key mr-2"></i> Ganti Password
                    </h3>
                </div>
                <div class="card-body p-6">
                    <form action="{{ route('profile.change-password') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                                <input type="password" name="current_password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <input type="password" name="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                                <i class="fas fa-sync-alt mr-2"></i> Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informasi Organisasi -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mt-6">
                <div class="card-header bg-gray-50 border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-building mr-2"></i> Informasi Organisasi
                    </h3>
                </div>
                <div class="card-body p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-500">Keuskupan</label>
                            <p class="font-semibold">
                                @if($user->keuskupan)
                                    <i class="fas fa-diocese mr-1 text-blue-500"></i>
                                    {{ $user->keuskupan->name }}
                                @else
                                    <span class="text-gray-500">- Tidak terdaftar -</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-500">Gereja</label>
                            <p class="font-semibold">
                                @if($user->gereja)
                                    <i class="fas fa-church mr-1 text-green-500"></i>
                                    {{ $user->gereja->nama }}
                                @else
                                    <span class="text-gray-500">- Tidak terdaftar -</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crop Image (Opsional untuk crop foto) -->
<div id="cropModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900">Crop Foto Profil</h3>
            <div class="mt-4">
                <img id="cropImage" src="" alt="Crop Image" class="w-full">
            </div>
            <div class="flex justify-center gap-3 mt-4">
                <button onclick="closeCropModal()" class="px-4 py-2 bg-gray-300 rounded-md">Batal</button>
                <button id="cropConfirmBtn" class="px-4 py-2 bg-blue-500 text-white rounded-md">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Upload foto profil
    document.getElementById('avatarContainer').addEventListener('click', function() {
        document.getElementById('photoInput').click();
    });
    
    document.getElementById('photoInput').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            // Preview image before upload
            const reader = new FileReader();
            reader.onload = function(e) {
                // Optional: Show crop modal here
                document.getElementById('uploadPhotoForm').submit();
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection