{{-- resources/views/gerejas/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Gereja')
@section('page-title', 'Tambah Gereja Baru')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center">
            @if(isset($keuskupan))
                <h3 class="text-lg font-semibold">Form Tambah Gereja - {{ $keuskupan->name }}</h3>
                <a href="{{ route('keuskupans.gerejas', $keuskupan->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            @else
                <h3 class="text-lg font-semibold">Form Tambah Gereja</h3>
                <a href="{{ route('gerejas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        {{-- PERBAIKAN: gunakan route gerejas.store --}}
        <form action="{{ route('gerejas.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label">Nama Gereja <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" class="form-input @error('nama') border-red-500 @enderror" 
                           value="{{ old('nama') }}" required placeholder="Contoh: Gereja Katedral Bogor">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Keuskupan <span class="text-red-500">*</span></label>
                    @if(isset($keuskupan))
                        <input type="text" class="form-input" value="{{ $keuskupan->name }}" readonly disabled>
                        <input type="hidden" name="keuskupan_id" value="{{ $keuskupan->id }}">
                    @else
                        <select name="keuskupan_id" class="form-select @error('keuskupan_id') border-red-500 @enderror" required>
                            <option value="">Pilih Keuskupan</option>
                            @foreach($keuskupans as $k)
                            <option value="{{ $k->id }}" {{ old('keuskupan_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->name }}
                            </option>
                            @endforeach
                        </select>
                    @endif
                    @error('keuskupan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi" class="form-input @error('lokasi') border-red-500 @enderror" 
                           value="{{ old('lokasi') }}" required placeholder="Contoh: Jl. Kapten Muslihat">
                    @error('lokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-input" value="{{ old('telepon') }}" placeholder="(0251) 1234567">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="gereja@example.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pastor Kepala</label>
                    <input type="text" name="pastor" class="form-input" value="{{ old('pastor') }}" placeholder="Nama Pastor">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Jumlah Umat</label>
                    <input type="number" name="jumlah_umat" class="form-input" value="{{ old('jumlah_umat', 0) }}" min="0">
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-textarea" rows="3" placeholder="Alamat lengkap gereja">{{ old('alamat_lengkap') }}</textarea>
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-textarea" rows="2" placeholder="Deskripsi singkat tentang gereja">{{ old('deskripsi') }}</textarea>
                </div>
            </div>
            
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle"></i>
                <strong>Informasi:</strong> Kode gereja akan digenerate secara otomatis.
            </div>
            
            <div class="mt-4 flex justify-end gap-2">
                @if(isset($keuskupan))
                    <a href="{{ route('keuskupans.gerejas', $keuskupan->id) }}" class="btn btn-warning">Batal</a>
                @else
                    <a href="{{ route('gerejas.index') }}" class="btn btn-warning">Batal</a>
                @endif
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Gereja
                </button>
            </div>
        </form>
    </div>
</div>
@endsection