@extends('layouts.app')

@section('title', 'Anggota di ' . $keuskupan->name)

@section('page-title', 'Anggota di ' . $keuskupan->name)

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Daftar Anggota/Umat</h3>
            <div class="flex gap-2">
                <a href="{{ route('users.create') }}?keuskupan={{ $keuskupan->code }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </a>
                <a href="{{ route('keuskupans.show', $keuskupan) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($members->count() > 0)
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Gereja</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $index => $member)
                    <tr>
                        <td>{{ $members->firstItem() + $index }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->church_name ?? '-' }}</td>
                        <td>
                            @php $role = $member->getRoleNames()->first(); @endphp
                            <span class="badge {{ $role == 'super_admin' ? 'bg-purple-100 text-purple-800' : 
                                                  ($role == 'admin_keuskupan' ? 'bg-blue-100 text-blue-800' : 
                                                  ($role == 'admin_gereja' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ $role ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $member->is_active ? 'badge-approved' : 'badge-cancelled' }}">
                                {{ $member->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('users.show', $member) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $members->links() }}
        @else
        <p class="text-center text-gray-500 py-4">Belum ada anggota dalam keuskupan ini.</p>
        @endif
    </div>
</div>
@endsection