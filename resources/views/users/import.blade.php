@extends('layouts.app')

@section('title', 'Import Data User')
@section('page-title', 'Import Data User')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-upload mr-2"></i> Import Data User
        </h1>
        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(session('import_failures'))
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow">
            <p class="font-bold">Detail kegagalan import:</p>
            <ul class="list-disc list-inside mt-2">
                @foreach(session('import_failures') as $failure)
                    <li>
                        Baris {{ $failure->row() }}: 
                        @foreach($failure->errors() as $error)
                            {{ $error }}
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    <div>
                        <p class="font-bold">Petunjuk Import Data:</p>
                        <ul class="list-disc list-inside mt-2 text-sm">
                            <li>File harus berformat .xlsx, .xls, atau .csv</li>
                            <li>Ukuran file maksimal 5MB</li>
                            <li>Kolom wajib: <strong>nama</strong>, <strong>email</strong>, <strong>level_akses</strong></li>
                            <li>Level akses: user, admin_gereja, admin_keuskupan, super_admin</li>
                            <li>Untuk admin_gereja dan user, wajib mengisi kolom <strong>gereja</strong></li>
                            <li>Untuk admin_keuskupan, wajib mengisi kolom <strong>keuskupan</strong></li>
                            <li>Download template untuk melihat format yang benar</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <a href="{{ route('users.template') }}" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-download mr-2"></i> Download Template Import
                </a>
            </div>

            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel/CSV <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full border rounded-lg p-2">
                    @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-upload mr-2"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection