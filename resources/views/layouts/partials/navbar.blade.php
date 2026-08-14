{{-- resources/views/layouts/partials/navbar.blade.php --}}

@php
    $user = auth()->user();
    
    // Hitung pending assignments berdasarkan role
    if ($user->isSuperAdmin()) {
        // Super Admin: lihat semua pending
        $pendingCount = App\Models\DutyAssignment::where('status', 'pending')->count();
        $roleLabel = 'Super Admin';
        $roleIcon = 'fa-user-shield';
        $roleColor = 'text-purple-600';
        // ADMIN: ke halaman index dengan filter pending
        $notificationLink = route('assignments.index', ['status' => 'pending']);
    } elseif ($user->isAdminKeuskupan()) {
        // Admin Keuskupan: lihat pending di keuskupannya
        $pendingCount = App\Models\DutyAssignment::where('status', 'pending')
            ->whereHas('user', function($q) use ($user) {
                $q->where('keuskupan_id', $user->keuskupan_id);
            })->count();
        $roleLabel = 'Admin Keuskupan';
        $roleIcon = 'fa-user-tie';
        $roleColor = 'text-blue-600';
        // ADMIN: ke halaman index dengan filter pending
        $notificationLink = route('assignments.index', ['status' => 'pending']);
    } elseif ($user->isAdminGereja()) {
        // Admin Gereja: lihat pending di gerejanya
        $pendingCount = App\Models\DutyAssignment::where('status', 'pending')
            ->whereHas('user', function($q) use ($user) {
                $q->where('gereja_id', $user->gereja_id);
            })->count();
        $roleLabel = 'Admin Gereja';
        $roleIcon = 'fa-user-cog';
        $roleColor = 'text-green-600';
        // ADMIN: ke halaman index dengan filter pending
        $notificationLink = route('assignments.index', ['status' => 'pending']);
    } else {
        // USER BIASA: lihat pending miliknya sendiri
        $pendingCount = App\Models\DutyAssignment::where('status', 'pending')
            ->where('user_id', $user->id)
            ->count();
        $roleLabel = 'User';
        $roleIcon = 'fa-user';
        $roleColor = 'text-gray-600';
        // USER: ke halaman "Tugas Saya" (my assignments)
        $notificationLink = route('assignments.my');
    }
@endphp

<nav class="navbar">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <!-- Tombol Toggle Sidebar -->
            <button @click="sidebarOpen = !sidebarOpen; if(window.innerWidth < 1024) mobileOpen = !mobileOpen" 
                    class="text-gray-600 hover:text-gray-900 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <!-- Page Title -->
            <h1 class="text-xl font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>
        
        <div class="flex items-center space-x-4">
            <!-- Notifikasi - Count Assignment Pending -->
            <a href="{{ $notificationLink }}" 
               class="relative text-gray-600 hover:text-gray-900 focus:outline-none transition-colors duration-200"
               title="{{ $pendingCount }} penugasan pending menunggu konfirmasi">
                <i class="fas fa-bell text-xl"></i>
                @if($pendingCount > 0)
                <span class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold px-1 animate-pulse-badge">
                    {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                </span>
                @endif
            </a>
            
            <!-- Dropdown User -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md overflow-hidden">
                        @if(auth()->user()->photo && Storage::disk('public')->exists(auth()->user()->photo))
                            <img src="{{ Storage::url(auth()->user()->photo) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <span class="text-gray-700 text-sm hidden md:inline">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-gray-500 text-xs hidden md:inline"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" 
                     class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 border"
                     x-cloak>
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md overflow-hidden flex-shrink-0">
                                @if(auth()->user()->photo && Storage::disk('public')->exists(auth()->user()->photo))
                                    <img src="{{ Storage::url(auth()->user()->photo) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-1">
                            <i class="fas {{ $roleIcon }} {{ $roleColor }} text-xs"></i>
                            <span class="text-xs {{ $roleColor }} font-medium">{{ $roleLabel }}</span>
                            @if($pendingCount > 0)
                            <span class="ml-auto text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                                {{ $pendingCount }} pending
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2 w-4 text-gray-400"></i> Profil Saya
                    </a>
                    <a href="{{ route('profile.index') }}#password" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-key mr-2 w-4 text-gray-400"></i> Ganti Password
                    </a>
                    
                    <!-- My Assignments untuk User Biasa -->
                    @if(!$user->isSuperAdmin() && !$user->isAdminKeuskupan() && !$user->isAdminGereja())
                    <a href="{{ route('assignments.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-tasks mr-2 w-4 text-gray-400"></i> Tugas Saya
                        @if($pendingCount > 0)
                        <span class="ml-auto text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                            {{ $pendingCount }}
                        </span>
                        @endif
                    </a>
                    @endif
                    
                    <!-- Admin Menu untuk Admin -->
                    @if($user->isSuperAdmin() || $user->isAdminKeuskupan() || $user->isAdminGereja())
                    <a href="{{ route('assignments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-clipboard-list mr-2 w-4 text-gray-400"></i> Semua Penugasan
                        @if($pendingCount > 0)
                        <span class="ml-auto text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                            {{ $pendingCount }}
                        </span>
                        @endif
                    </a>
                    @endif
                    
                    <hr class="my-1">
                    
                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2 w-4"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

@push('styles')
<style>
    .navbar {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        padding: 0.75rem 1.5rem;
        position: sticky;
        top: 0;
        z-index: 999;
    }
    
    [x-cloak] {
        display: none !important;
    }
    
    /* Animasi pulse untuk badge notifikasi */
    @keyframes pulse-badge {
        0%, 100% { 
            transform: scale(1); 
        }
        50% { 
            transform: scale(1.15); 
        }
    }
    
    .animate-pulse-badge {
        animation: pulse-badge 1.5s ease-in-out infinite;
    }
    
    /* Hover effect untuk bell icon */
    .navbar .fa-bell {
        transition: transform 0.2s ease;
    }
    
    .navbar .fa-bell:hover {
        transform: scale(1.1);
    }
</style>
@endpush