@extends('layouts.app')

@section('title', 'Tugas Pelayanan Saya')
@section('page-title', 'Tugas Pelayanan Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Tugas Tetap User -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-blue-50 border-b">
            <h3 class="text-lg font-semibold text-blue-800">
                <i class="fas fa-tasks mr-2"></i> Tugas Tetap Anda
            </h3>
        </div>
        <div class="p-6">
            @if(auth()->user()->duty)
                <div class="flex items-center gap-4">
                    <div class="bg-purple-100 p-4 rounded-full">
                        <i class="fas fa-crosshairs text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Tugas Pelayanan</p>
                        <p class="text-xl font-bold text-gray-800">
                            [{{ auth()->user()->duty->code }}] {{ auth()->user()->duty->name }}
                        </p>
                        <p class="text-gray-500 text-sm mt-1">{{ auth()->user()->duty->description }}</p>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-yellow-800">⚠️ Anda belum memiliki tugas tetap. Silakan hubungi admin.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Tugas Menunggu Konfirmasi -->
    @if($pendingAssignments->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-yellow-50 border-b">
            <h3 class="text-lg font-semibold text-yellow-800">
                <i class="fas fa-clock mr-2"></i> Menunggu Konfirmasi Anda
            </h3>
            <p class="text-sm text-yellow-600">Silakan konfirmasi kesediaan Anda untuk tugas berikut</p>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($pendingAssignments as $assignment)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                <i class="fas fa-calendar mr-1"></i> {{ $assignment->schedule->display ?? $assignment->schedule->name }}
                            </span>
                        </div>
                        <p class="text-gray-700">
                            <i class="fas fa-tasks mr-2 text-gray-400"></i>
                            Tugas: <strong>{{ $assignment->duty->name }}</strong>
                        </p>
                        <p class="text-gray-500 text-sm mt-1">
                            <i class="fas fa-info-circle mr-1"></i> 
                            Dibuat: {{ $assignment->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($assignment->notes)
                        <p class="text-sm text-gray-500 mt-2 bg-gray-50 p-2 rounded">
                            <i class="fas fa-sticky-note mr-1"></i> {{ $assignment->notes }}
                        </p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('assignments.accept', $assignment) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg transition flex items-center gap-2">
                                <i class="fas fa-check"></i> Terima
                            </button>
                        </form>
                        <button onclick="openRejectModal({{ $assignment->id }})" class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="fas fa-times"></i> Tidak Bersedia
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tugas yang Diterima -->
    @if($acceptedAssignments->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-green-50 border-b">
            <h3 class="text-lg font-semibold text-green-800">
                <i class="fas fa-check-circle mr-2"></i> Tugas yang Telah Diterima
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($acceptedAssignments as $assignment)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                            {{ $assignment->schedule->display ?? $assignment->schedule->name }}
                        </span>
                        <p class="text-gray-700 mt-1">
                            <i class="fas fa-tasks mr-1"></i> {{ $assignment->duty->name }}
                        </p>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                        <i class="fas fa-check-circle mr-1"></i> Diterima
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tugas Selesai -->
    @if($completedAssignments->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="px-6 py-4 bg-blue-50 border-b">
            <h3 class="text-lg font-semibold text-blue-800">
                <i class="fas fa-check-double mr-2"></i> Riwayat Tugas Selesai
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($completedAssignments as $assignment)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                            {{ $assignment->schedule->display ?? $assignment->schedule->name }}
                        </span>
                        <p class="text-gray-600 text-sm mt-1">{{ $assignment->duty->name }}</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        <i class="fas fa-check-double mr-1"></i> Selesai
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tugas Ditolak -->
    @if($rejectedAssignments->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-red-50 border-b">
            <h3 class="text-lg font-semibold text-red-800">
                <i class="fas fa-times-circle mr-2"></i> Riwayat Tugas Ditolak
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($rejectedAssignments as $assignment)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                            {{ $assignment->schedule->display ?? $assignment->schedule->name }}
                        </span>
                        <p class="text-gray-600 text-sm mt-1">{{ $assignment->duty->name }}</p>
                        @if($assignment->rejection_reason)
                        <p class="text-xs text-red-500 mt-1">Alasan: {{ $assignment->rejection_reason }}</p>
                        @endif
                    </div>
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Jika tidak ada tugas sama sekali -->
    @if($pendingAssignments->isEmpty() && $acceptedAssignments->isEmpty() && $completedAssignments->isEmpty() && $rejectedAssignments->isEmpty())
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-12 text-center">
            <i class="fas fa-calendar-check text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada penugasan untuk Anda</p>
            <p class="text-gray-400 text-sm mt-2">Admin akan memberikan penugasan sesuai jadwal</p>
        </div>
    </div>
    @endif
</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-times-circle text-red-500 mr-2"></i> Alasan Tidak Bersedia
            </h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan (Opsional)</label>
                <textarea name="rejection_reason" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-red-500 focus:border-red-500" 
                          placeholder="Berikan alasan ketidaksediaan Anda..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-paper-plane mr-1"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(assignmentId) {
        const form = document.getElementById('rejectForm');
        form.action = '/assignments/' + assignmentId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
    
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endsection