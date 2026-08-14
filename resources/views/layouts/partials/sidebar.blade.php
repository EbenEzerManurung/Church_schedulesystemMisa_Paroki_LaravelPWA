<aside class="sidebar" :class="{ 'mobile-open': mobileOpen }">
    <div class="sidebar-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-church text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white tracking-wide">Church Schedule System</h2>
                    {{-- <p class="text-xs text-gray-400">Church Schedule System</p> --}}
                </div>
            </div>
            <button @click="mobileOpen = false" class="lg:hidden text-gray-400 hover:text-white transition duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    
    <!-- User Info Section dengan Foto Profil -->
    <div class="sidebar-user-info">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-300 to-teal-500 flex items-center justify-center shadow-md overflow-hidden">
                @php
                    use Illuminate\Support\Facades\Storage;
                @endphp
                @if(auth()->user()->photo && Storage::disk('public')->exists(auth()->user()->photo))
                    <img src="{{ Storage::url(auth()->user()->photo) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-white font-bold text-lg">{{ substr(auth()->user()->name, 0, 1) }}</span>
                @endif
            </div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">
                    @if(auth()->user()->isSuperAdmin())
                        <i class="fas fa-crown mr-1 text-yellow-400"></i> Super Admin
                    @elseif(auth()->user()->isAdminKeuskupan())
                        <i class="fas fa-diocese mr-1 text-teal-400"></i> Admin Keuskupan
                    @elseif(auth()->user()->isAdminGereja())
                        <i class="fas fa-church mr-1 text-green-400"></i> Admin Gereja
                    @elseif(auth()->user()->level_akses === 'pic_group')
                        <i class="fas fa-users-cog mr-1 text-purple-400"></i> PIC Group
                        @if(auth()->user()->duty)
                            <span class="text-xs text-purple-300 ml-1">({{ auth()->user()->duty->name }})</span>
                        @endif
                    @else
                        <i class="fas fa-user mr-1 text-gray-400"></i> User
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Tampilkan Keuskupan menggunakan relasi -->
        @if(auth()->user()->keuskupan)
        <div class="sidebar-user-church">
            <i class="fas fa-diocese mr-2 text-teal-400 w-4"></i>
            <span>{{ auth()->user()->keuskupan->name }}</span>
        </div>
        @endif
        
        <!-- Tampilkan Gereja menggunakan relasi -->
        @if(auth()->user()->gereja)
        <div class="sidebar-user-church">
            <i class="fas fa-church mr-2 text-green-400 w-4"></i>
            <span>{{ auth()->user()->gereja->nama }}</span>
        </div>
        @endif

        <!-- Tampilkan Duty Group untuk PIC Group -->
        @if(auth()->user()->level_akses === 'pic_group' && auth()->user()->duty)
        <div class="sidebar-user-church" style="color: #a78bfa;">
            <i class="fas fa-tasks mr-2 text-purple-400 w-4"></i>
            <span>Duty Group: {{ auth()->user()->duty->name }}</span>
        </div>
        @endif
    </div>
    
    <nav class="px-3 mt-4">
        @php
            use App\Models\Duty; // Tambahkan import model Duty
            
            $user = auth()->user();
            $isPicGroup = $user->level_akses === 'pic_group';
            $isUser = $user->level_akses === 'user' || $user->level_akses === 'user_biasa';
            $userDutyId = $user->duty_id;
            
            // Definisikan URL untuk PIC Group
            $dashboardUrl = '/dashboard';
            $keuskupanUrl = '/keuskupans';
            $gerejaUrl = '/gerejas';
            $jadwalUrl = '/schedules';
            $tugasUrl = '/duties';
            
            // Hanya tampilkan penugasan jika user bukan user biasa
            $penugasanUrl = ($isPicGroup) ? '/assignments?duty_id=' . $userDutyId : '/assignments';
            $ketersediaanUrl = '/availability';
            $userManagementUrl = ($isPicGroup) ? '/users?duty_id=' . $userDutyId : '/users';
            $permissionsUrl = '/permissions';
            $laporanUrl = '/assignments/export/form';
            $profileUrl = '/profile';
            $exportUrl = '/export-database';
            
            // Definisikan semua menu
            $allMenus = [
                ['name' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'url' => $dashboardUrl, 'order' => 1, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user']],
                ['name' => 'Keuskupan', 'icon' => 'fa-church', 'url' => $keuskupanUrl, 'order' => 2, 'roles' => ['super_admin']],
                ['name' => 'Gereja', 'icon' => 'fa-building', 'url' => $gerejaUrl, 'order' => 3, 'roles' => ['super_admin', 'admin_keuskupan']],
                ['name' => 'Jadwal', 'icon' => 'fa-calendar-alt', 'url' => $jadwalUrl, 'order' => 4, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group']],
                ['name' => 'Tugas', 'icon' => 'fa-tasks', 'url' => $tugasUrl, 'order' => 5, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group']],
                // PENUGASAN - HANYA UNTUK ADMIN & PIC GROUP, TIDAK UNTUK USER BIASA
                ['name' => 'Penugasan', 'icon' => 'fa-user-check', 'url' => $penugasanUrl, 'order' => 6, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group'], 'sub_menus' => true],
                ['name' => 'Ketersediaan', 'icon' => 'fa-clock', 'url' => $ketersediaanUrl, 'order' => 7, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user']],
                ['name' => 'User Management', 'icon' => 'fa-users', 'url' => $userManagementUrl, 'order' => 8, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group']],
                ['name' => 'Permissions', 'icon' => 'fa-lock', 'url' => $permissionsUrl, 'order' => 9, 'roles' => ['super_admin']],
                ['name' => 'Laporan', 'icon' => 'fa-chart-bar', 'url' => $laporanUrl, 'order' => 10, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group']],
                ['name' => 'Profile', 'icon' => 'fa-user-circle', 'url' => $profileUrl, 'order' => 11, 'roles' => ['super_admin', 'admin_keuskupan', 'admin_gereja', 'pic_group', 'user']],
                ['name' => 'Export', 'icon' => 'fa-download', 'url' => $exportUrl, 'order' => 12, 'roles' => ['super_admin']],
            ];
            
            // Filter menu berdasarkan role user - USER BIASA TIDAK DAPAT MELIHAT PENUGASAN
            $menus = array_filter($allMenus, function($menu) use ($user, $isUser) {
                // Jika user biasa, skip menu Penugasan
                if ($isUser && $menu['name'] === 'Penugasan') {
                    return false;
                }
                
                if ($user->isSuperAdmin()) {
                    return in_array('super_admin', $menu['roles']);
                }
                if ($user->isAdminKeuskupan()) {
                    return in_array('admin_keuskupan', $menu['roles']);
                }
                if ($user->isAdminGereja()) {
                    return in_array('admin_gereja', $menu['roles']);
                }
                if ($user->level_akses === 'pic_group') {
                    return in_array('pic_group', $menu['roles']);
                }
                if ($user->isUser() || $isUser) {
                    return in_array('user', $menu['roles']);
                }
                return false;
            });
            
            // Urutkan berdasarkan order
            usort($menus, function($a, $b) {
                return $a['order'] <=> $b['order'];
            });
            
            // Ambil data duties untuk sub-menu - HANYA UNTUK ADMIN & PIC GROUP
            $duties = collect();
            if (!$isUser) {
                $dutiesQuery = Duty::where('is_active', true)->orderBy('name');
                
                // Jika PIC Group, hanya tampilkan duty_id yang sama
                if ($isPicGroup && $userDutyId) {
                    $dutiesQuery->where('id', $userDutyId);
                }
                $duties = $dutiesQuery->get();
            }
            
            // Cek apakah di halaman penugasan
            $isAssignmentsPage = request()->routeIs('assignments.*');
            $selectedDutyId = request()->get('duty_id');
            $isCreatePage = request()->routeIs('assignments.create');
        @endphp
        
        @foreach($menus as $menu)
            @php
                $currentUrl = request()->url();
                $menuUrl = url($menu['url']);
                $isActive = $currentUrl == $menuUrl || (strpos($currentUrl, $menu['url']) === 0 && $menu['url'] != '/');
                $hasSubMenus = isset($menu['sub_menus']) && $menu['sub_menus'] === true;
                
                // Cek apakah menu Penugasan aktif (termasuk sub-menunya)
                $isPenugasanActive = $isActive || ($isAssignmentsPage && $menu['name'] == 'Penugasan');
            @endphp
            
            @if($hasSubMenus)
                <!-- Menu Penugasan dengan Sub-menu - HANYA UNTUK ADMIN & PIC GROUP -->
                @if(!$isUser)
                <div x-data="{ open: {{ $isPenugasanActive ? 'true' : 'false' }} }" class="sidebar-menu-group">
                    <!-- Tombol Penugasan -->
                    <button @click="open = !open" 
                            class="sidebar-nav-item w-full text-left {{ $isPenugasanActive ? 'active' : '' }}"
                            style="justify-content: space-between;">
                        <span class="flex items-center gap-2">
                            <i class="fas {{ $menu['icon'] }}"></i>
                            <span>{{ $menu['name'] }}</span>
                            @if($isPenugasanActive)
                                <span class="sidebar-badge-active">
                                    <i class="fas fa-circle text-[5px] mr-1"></i>
                                    Aktif
                                </span>
                            @endif
                        </span>
                        <i class="fas fa-chevron-down transition-transform duration-300 text-[10px]" 
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Sub-menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="sidebar-sub-menu">
                        
                        <!-- Link: Semua Penugasan -->
                        @php
                            $allAssignmentsUrl = $isPicGroup ? '/assignments?duty_id=' . $userDutyId : route('assignments.index');
                            $isAllAssignments = request()->routeIs('assignments.index') && !request()->has('duty_id');
                        @endphp
                        <a href="{{ $allAssignmentsUrl }}" 
                           class="sidebar-sub-nav-item {{ $isAllAssignments ? 'active' : '' }}">
                            <i class="fas fa-list-ul mr-3"></i>
                            <span>Semua Penugasan</span>
                            @if($isAllAssignments)
                                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-teal-400 shadow-glow"></span>
                            @endif
                        </a>
                        
                        <!-- Divider -->
                        <div class="sidebar-sub-divider">
                            <span>Master Tugas</span>
                        </div>
                        
                        <!-- Sub-menu per duty -->
                        @foreach($duties as $duty)
                            @php
                                $dutyUrl = '/assignments?duty_id=' . $duty->id;
                                $isDutyActive = request()->get('duty_id') == $duty->id;
                                $isCreateDuty = $isCreatePage && request()->get('duty_id') == $duty->id;
                                $isDutySelected = $isDutyActive || $isCreateDuty;
                            @endphp
                            <div class="sidebar-duty-item">
                                <a href="{{ $dutyUrl }}" 
                                   class="sidebar-sub-nav-item {{ $isDutyActive ? 'active' : '' }}">
                                    <i class="fas fa-circle text-[6px] mr-3"></i>
                                    <span class="flex-1">{{ $duty->name }}</span>
                                    @if($duty->code)
                                        <span class="sidebar-duty-code">{{ $duty->code }}</span>
                                    @endif
                                    @if($isDutyActive)
                                        <span class="ml-2 w-1.5 h-1.5 rounded-full bg-teal-400 shadow-glow"></span>
                                    @endif
                                </a>
                                
                                <!-- Tombol Tambah Petugas -->
                                <div class="sidebar-sub-action">
                                    <a href="{{ route('assignments.create') }}?duty_id={{ $duty->id }}" 
                                       class="sidebar-sub-add-btn {{ $isDutySelected ? 'active' : '' }}">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Tambah Petugas
                                        @if($isDutySelected)
                                            <span class="sidebar-badge-new">Pilih</span>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @else
                <!-- Menu tanpa Sub-menu -->
                <a href="{{ url($menu['url']) }}" 
                   class="sidebar-nav-item {{ $isActive ? 'active' : '' }}">
                    <i class="fas {{ $menu['icon'] }}"></i>
                    <span>{{ $menu['name'] }}</span>
                    @if($isActive)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-white shadow-glow-white"></span>
                    @endif
                </a>
            @endif
        @endforeach
        
        <div class="border-t border-gray-700/50 my-4"></div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-nav-item w-full text-left hover:bg-red-600/20 transition duration-200">
                <i class="fas fa-sign-out-alt text-red-400"></i>
                <span class="text-red-300">Logout</span>
            </button>
        </form>
    </nav>
</aside>

<!-- Mobile overlay -->
<div x-show="mobileOpen" 
     x-transition.opacity.duration.300ms
     @click="mobileOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden"
     style="display: none;"></div>

@push('styles')
<style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        width: 280px;
        background: linear-gradient(180deg, #40e0d0 0%, #2bb8ab 100%);
        z-index: 50;
        transition: transform 0.3s ease-in-out;
        transform: translateX(-100%);
        overflow-y: auto;
        scrollbar-width: thin;
    }
    
    .sidebar::-webkit-scrollbar {
        width: 3px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(45, 212, 191, 0.95);
        border-radius: 10px;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(94, 234, 212, 0.94);
    }
    
    @media (min-width: 1024px) {
        .sidebar {
            transform: translateX(0);
        }
    }
    
    .sidebar.mobile-open {
        transform: translateX(0);
    }
    
    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(37, 224, 245, 0.86);
        background: rgba(29, 210, 241, 0.82);
        backdrop-filter: blur(10px);
    }
    
    .sidebar-user-info {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-user-name {
        font-weight: 600;
        color: #f1f5f9;
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }
    
    .sidebar-user-role {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.125rem;
        letter-spacing: 0.2px;
    }
    
    .sidebar-user-church {
        font-size: 0.75rem;
        color: #2dd4bf;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        opacity: 0.85;
        letter-spacing: 0.2px;
    }
    
    /* ===== NAVIGATION ITEMS ===== */
    .sidebar-nav-item {
        display: flex;
        align-items: center;
        padding: 0.7rem 1rem;
        margin: 0.2rem 0;
        border-radius: 0.5rem;
        color: #94a3b8;
        transition: all 0.25s ease;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        letter-spacing: 0.2px;
    }
    
    .sidebar-nav-item i {
        width: 1.75rem;
        font-size: 1rem;
        transition: transform 0.2s ease;
    }
    
    .sidebar-nav-item:hover {
        background: rgba(45, 212, 191, 0.08);
        color: #e2e8f0;
    }
    
    .sidebar-nav-item.active {
        background: linear-gradient(90deg, rgba(45, 212, 191, 0.2) 0%, rgba(45, 212, 191, 0.05) 100%);
        color: #5eead4;
        border-left: 3px solid #2dd4bf;
    }
    
    .sidebar-nav-item.active i {
        color: #2dd4bf;
    }
    
    /* ===== BADGE AKTIF ===== */
    .sidebar-badge-active {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: rgba(45, 212, 191, 0.2);
        color: #2dd4bf;
        font-size: 0.6rem;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        margin-left: 0.5rem;
        border: 1px solid rgba(45, 212, 191, 0.15);
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    
    /* ===== SUB MENU ===== */
    .sidebar-sub-menu {
        margin-left: 0.75rem;
        margin-top: 0.25rem;
        padding: 0.2rem 0;
        border-left: 1px solid rgba(45, 212, 191, 0.1);
        padding-left: 0.75rem;
    }
    
    .sidebar-sub-nav-item {
        display: flex;
        align-items: center;
        padding: 0.45rem 0.75rem;
        margin: 0.1rem 0;
        border-radius: 0.375rem;
        color: #94a3b8;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        font-weight: 450;
        text-decoration: none;
        cursor: pointer;
        gap: 0.25rem;
        position: relative;
        letter-spacing: 0.2px;
    }
    
    .sidebar-sub-nav-item:hover {
        color: #e2e8f0;
        background: rgb(20, 184, 166);
    }
    
    .sidebar-sub-nav-item.active {
        color: #2dd4bf;
        background: rgba(45, 212, 191, 0.92);
        font-weight: 500;
    }
    
    .sidebar-sub-nav-item i {
        width: 1.25rem;
        font-size: 0.7rem;
        flex-shrink: 0;
        opacity: 0.5;
        color: #64748b;
    }
    
    .sidebar-sub-nav-item.active i {
        opacity: 1;
        color: #2dd4bf;
    }
    
    /* ===== DIVIDER ===== */
    .sidebar-sub-divider {
        display: flex;
        align-items: center;
        padding: 0.4rem 0.75rem;
        margin: 0.3rem 0;
        color: #475569;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    
    .sidebar-sub-divider::after {
        content: '';
        flex: 1;
        margin-left: 0.75rem;
        height: 1px;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%);
    }
    
    .sidebar-sub-divider span {
        background: rgba(0, 0, 0, 0.08);
        padding: 0 0.5rem;
        color: #0f766e;
        font-size: 0.6rem;
        letter-spacing: 0.5px;
    }
    
    /* ===== DUTY ITEMS ===== */
    .sidebar-duty-item {
        position: relative;
    }
    
    .sidebar-duty-item .sidebar-sub-nav-item {
        padding-right: 0.5rem;
    }
    
    .sidebar-duty-code {
        font-size: 0.65rem;
        color: #475569;
        background: rgba(255, 255, 255, 0.04);
        padding: 0.1rem 0.5rem;
        border-radius: 0.25rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    }
    
    .sidebar-sub-nav-item.active .sidebar-duty-code {
        color: #2dd4bf;
        background: rgba(22, 55, 241, 0.95);
    }
    
    /* ===== ADD BUTTON ===== */
    .sidebar-sub-action {
        padding-left: 2.25rem;
        padding-top: 0.1rem;
        padding-bottom: 0.2rem;
        animation: fadeSlideDown 0.3s ease-out;
    }
    
    @keyframes fadeSlideDown {
        from {
            opacity: 0;
            transform: translateY(-3px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .sidebar-sub-add-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.8rem;
        border-radius: 0.375rem;
        color: #34d399;
        font-size: 0.78rem;
        text-decoration: none;
        transition: all 0.25s ease;
        background: rgba(52, 211, 153, 0.05);
        border: 1px solid rgba(52, 211, 153, 0.08);
        gap: 0.25rem;
        font-weight: 500;
        letter-spacing: 0.2px;
    }
    
    .sidebar-sub-add-btn:hover {
        background: rgba(52, 211, 153, 0.1);
        color: #6ee7b7;
        border-color: rgba(52, 211, 153, 0.2);
    }
    
    .sidebar-sub-add-btn.active {
        background: rgba(52, 211, 153, 0.12);
        border-color: rgba(52, 211, 153, 0.25);
        color: #6ee7b7;
    }
    
    .sidebar-sub-add-btn i {
        font-size: 0.7rem;
        transition: transform 0.2s ease;
    }
    
    .sidebar-sub-add-btn:hover i {
        transform: scale(1.1);
    }
    
    .sidebar-badge-new {
        background: rgba(52, 211, 153, 0.15);
        color: #34d399;
        font-size: 0.55rem;
        padding: 0.05rem 0.4rem;
        border-radius: 9999px;
        margin-left: 0.25rem;
        border: 1px solid rgba(52, 211, 153, 0.1);
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    
    /* ===== SHADOW GLOW ===== */
    .shadow-glow {
        box-shadow: 0 0 8px rgba(45, 212, 191, 0.4);
    }
    
    .shadow-glow-white {
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
    }
    
    /* ===== TRANSITIONS ===== */
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>
@endpush
