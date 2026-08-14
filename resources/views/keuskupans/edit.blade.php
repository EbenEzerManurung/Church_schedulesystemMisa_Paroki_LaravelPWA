@extends('layouts.app')

@section('title', 'Edit Keuskupan')

@section('page-title', 'Edit Keuskupan')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold">Form Edit Keuskupan</h3>
            <a href="{{ route('keuskupans.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('keuskupans.update', $keuskupan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Kode Keuskupan <span class="text-red-500">*</span></label>
                    <input type="text" name="code" class="form-input @error('code') border-red-500 @enderror" 
                           value="{{ old('code', $keuskupan->code) }}" required>
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nama Keuskupan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $keuskupan->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input @error('email') border-red-500 @enderror" 
                           value="{{ old('email', $keuskupan->email) }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-input @error('phone') border-red-500 @enderror" 
                           value="{{ old('phone', $keuskupan->phone) }}">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-textarea @error('address') border-red-500 @enderror" 
                              rows="3">{{ old('address', $keuskupan->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group md:col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-textarea" rows="2">{{ old('description', $keuskupan->description) }}</textarea>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end gap-2">
                <a href="{{ route('keuskupans.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection