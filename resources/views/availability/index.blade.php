@extends('layouts.app')

@section('title', 'Ketersediaan')
@section('page-title', 'Manajemen Ketersediaan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-calendar-check mr-2"></i> 
            @auth
                @if(auth()->user()->level_akses === 'user')
                    Ketersediaan Saya
                @else
                    Ketersediaan Petugas
                @endif
            @endauth
        </h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" name="search" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Nama petugas / tugas..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Penugasan</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ request('start_date') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ request('end_date') }}">
                </div>
                <div class="flex items-end gap-2 col-span-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('availability.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Penugasan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $index => $assignment)
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $assignments->firstItem() + $index }}
                        </td>

                        <!-- ===== TANGGAL PENUGASAN ===== -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($assignment->event_date)
                                <div class="font-medium text-gray-800 text-sm">
                                    {{ \Carbon\Carbon::parse($assignment->event_date)->translatedFormat('d F Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($assignment->event_date)->translatedFormat('l') }}
                                    @if($assignment->schedule && $assignment->schedule->time)
                                        • {{ \Carbon\Carbon::parse($assignment->schedule->time)->format('H:i') }}
                                    @endif
                                </div>
                                <!-- ===== STATUS WAKTU ===== -->
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $eventDate = \Carbon\Carbon::parse($assignment->event_date);
                                @endphp
                                
                                @if($eventDate->lt($today))
                                    <!-- SUDAH LEWAT -->
                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        <i class="fas fa-clock mr-1"></i> Sudah Lewat
                                    </div>
                                @elseif($eventDate->eq($today))
                                    <!-- SEDANG BERLANGSUNG -->
                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 animate-pulse">
                                        <i class="fas fa-hourglass-half mr-1"></i> Sedang Berlangsung
                                    </div>
                                @else
                                    <!-- AKAN DATANG -->
                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-calendar-alt mr-1"></i> Akan Datang
                                    </div>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        
                        <!-- JADWAL -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $assignment->schedule->display ?? $assignment->schedule->name ?? 'Jadwal tidak tersedia' }}
                            </span>
                        </td>
                        
                        <!-- TUGAS -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div class="font-medium">{{ $assignment->duty->name ?? 'Tugas tidak tersedia' }}</div>
                            @if($assignment->duty)
                            <div class="text-xs text-gray-500">{{ $assignment->duty->code ?? '' }}</div>
                            @endif
                        </td>
                        
                        <!-- PETUGAS -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $assignment->user->name ?? 'Petugas tidak tersedia' }}
                            @if($assignment->user)
                            <div class="text-xs text-gray-500">{{ $assignment->user->email }}</div>
                            @endif
                        </td>
                        
                        <!-- STATUS -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'accepted' => 'fa-check-circle',
                                        'rejected' => 'fa-times-circle',
                                        'completed' => 'fa-check-double',
                                        'cancelled' => 'fa-ban',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'accepted' => 'Diterima',
                                        'rejected' => 'Ditolak',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                    $status = $assignment->status ?? 'pending';
                                    $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                    $iconClass = $statusIcons[$status] ?? 'fa-question-circle';
                                    $label = $statusLabels[$status] ?? ucfirst($status);
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colorClass }}">
                                    <i class="fas {{ $iconClass }} mr-1"></i>
                                    {{ $label }}
                                </span>
                            </div>
                            <!-- ===== STATUS KETERSEDIAAN ===== -->
                            @php
                                $availabilityStatus = $assignment->availability_status ?? 'pending';
                            @endphp
                            @if($availabilityStatus == 'available')
                                <div class="mt-1 text-xs text-green-600 font-medium">
                                    <i class="fas fa-check-circle"></i> Bersedia
                                </div>
                            @elseif($availabilityStatus == 'unavailable')
                                <div class="mt-1 text-xs text-red-600 font-medium">
                                    <i class="fas fa-times-circle"></i> Tidak Bersedia
                                </div>
                            @else
                                <div class="mt-1 text-xs text-yellow-600 font-medium">
                                    <i class="fas fa-clock"></i> Belum Konfirmasi
                                </div>
                            @endif
                        </td>
                        
                        <!-- AKSI -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-wrap gap-2">
                                @auth
                                    @php
                                        $user = auth()->user();
                                        $isAdminRole = in_array($user->level_akses, ['super_admin', 'admin_keuskupan', 'admin_gereja']);
                                        $isUserRole = $user->level_akses === 'user';
                                        $isOwner = $assignment->user_id == $user->id;
                                        $canEdit = $isAdminRole || ($isUserRole && $isOwner);
                                    @endphp
                                    
                                    <!-- TOMBOL SHOW -->
                                    <a href="{{ route('availability.show', $assignment) }}" 
                                       class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-sm"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Show
                                    </a>
                                    
                                    @if($canEdit && $assignment->status !== 'completed' && $assignment->status !== 'cancelled')
                                        <a href="{{ route('availability.edit', $assignment) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1 text-sm"
                                           title="Edit Ketersediaan">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-check fa-4x mb-4 text-gray-300"></i>
                                <p class="text-lg">Tidak ada data ketersediaan</p>
                                <p class="text-sm text-gray-400 mt-1">
                                    @auth
                                        @if(auth()->user()->level_akses === 'user')
                                            Belum ada penugasan untuk Anda
                                        @else
                                            Belum ada data penugasan
                                        @endif
                                    @endauth
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t bg-gray-50">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Menampilkan {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} 
                    dari {{ $assignments->total() }} data
                </div>
                @if($assignments->hasPages())
                <div>
                    {{ $assignments->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection