@extends('layouts.app')

@section('page-title', 'Export')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Ekspor Data</h4>
                </div>
                <div class="card-body">
                    
                  <form action="{{ route('assignments.export.filtered') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label class="form-label">Pilih Status</label>
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Periode</label>
        <div class="row">
            <div class="col">
                <input type="date" name="start_date" class="form-control" 
                       value="{{ request('start_date') }}" placeholder="Tanggal Mulai">
            </div>
            <div class="col">
                <input type="date" name="end_date" class="form-control" 
                       value="{{ request('end_date') }}" placeholder="Tanggal Akhir">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Format Ekspor</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="format" value="xlsx" checked>
            <label class="form-check-label">Excel (.xlsx)</label>
        </div>
        {{-- <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="format" value="csv">
            <label class="form-check-label">CSV (.csv)</label>
        </div> --}}
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('assignments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Ekspor Excel
        </button>
    </div>
</form>

           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection