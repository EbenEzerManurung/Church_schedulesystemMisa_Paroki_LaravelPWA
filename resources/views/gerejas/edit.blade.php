{{-- resources/views/gerejas/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Gereja')
@section('page-title', 'Edit Gereja')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Form Edit Gereja - {{ $gereja->nama }}</h3>
            <a href="{{ route('keuskupans.gerejas', $gereja->keuskupan_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('gerejas.update', $gereja->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label">Nama Gereja <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" class="form-input @error('nama') border-red-500 @enderror" 
                           value="{{ old('nama', $gereja->nama) }}" required placeholder="Contoh: Gereja Katedral Bogor">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kode Gereja</label>
                    <input type="text" class="form-input" value="{{ $gereja->kode }}" readonly disabled>
                    <p class="text-xs text-gray-500 mt-1">Kode tidak dapat diubah</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Keuskupan <span class="text-red-500">*</span></label>
                    <select name="keuskupan_id" class="form-select @error('keuskupan_id') border-red-500 @enderror" required>
                        <option value="">Pilih Keuskupan</option>
                        @foreach($keuskupans as $k)
                        <option value="{{ $k->id }}" {{ old('keuskupan_id', $gereja->keuskupan_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('keuskupan_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi" class="form-input @error('lokasi') border-red-500 @enderror" 
                           value="{{ old('lokasi', $gereja->lokasi) }}" required placeholder="Contoh: Jl. Kapten Muslihat">
                    @error('lokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-input" value="{{ old('telepon', $gereja->telepon) }}" placeholder="(0251) 1234567">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $gereja->email) }}" placeholder="gereja@example.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Pastor Kepala</label>
                    <input type="text" name="pastor" class="form-input" value="{{ old('pastor', $gereja->pastor) }}" placeholder="Nama Pastor">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Jumlah Umat</label>
                    <input type="number" name="jumlah_umat" class="form-input" value="{{ old('jumlah_umat', $gereja->jumlah_umat) }}" min="0">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', $gereja->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $gereja->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-textarea" rows="3" placeholder="Alamat lengkap gereja">{{ old('alamat_lengkap', $gereja->alamat_lengkap) }}</textarea>
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-textarea" rows="2" placeholder="Deskripsi singkat tentang gereja">{{ old('deskripsi', $gereja->deskripsi) }}</textarea>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('keuskupans.gerejas', $gereja->keuskupan_id) }}" class="btn btn-warning">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Gereja
                </button>
            </div>
        </form>
    </div>
</div>
@endsection