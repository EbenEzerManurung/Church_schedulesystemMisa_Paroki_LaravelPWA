@extends('layouts.app')

@section('title', 'Export Database')
@section('page-title', 'Export Database')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-database text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Backup Database</h2>
                        <p class="text-blue-100 text-sm">Super Admin Only</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white d-flex align-items-center">
      <i class="fas fa-database me-2"></i>
      <h4 class="mb-0">Export Database</h4>
    </div>
    <div class="card-body">
      @if (Auth::user()->level_akses == 'super_admin')
        <p>Tekan tombol di bawah ini untuk mengekspor seluruh database ke file MySQL.</p>

        <a href="{{ route('export.export') }}" class="btn btn-success">
          <i class="fas fa-download me-1"></i>
          Download Backup Database
        </a>
      @else
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Hanya admin yang dapat mengakses fitur ini.
        </div>
      @endif
    </div>
  </div>
</div>
        </div>
    </div>
</div>

 <!-- AKHIR TABLE -->

  </table>
  <!-- Add SweetAlert script -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Your custom scripts -->
  <script src="{{ asset('js/manage_account/account/script.js') }}"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

  <script type="text/javascript">
    @if ($message = Session::get('create_success'))
      swal(
        "Berhasil!",
        "{{ $message }}",
        "success"
      );
    @endif

    @if ($message = Session::get('update_success'))
      swal(
        "Berhasil!",
        "{{ $message }}",
        "success"
      );
    @endif
  </script>

  <script src="{{ asset('sw.js') }}"></script>
  <script>
    if (!navigator.serviceWorker.controller) {
      navigator.serviceWorker.register("/sw.js").then(function (reg) {
        console.log("Service worker has been registered for scope: " + reg.scope);
      });
    }
  </script>
  <script src="{{ asset('plugins/js/quagga.min.js') }}"></script>
  @if (session('create_success'))
  <script>
    Swal.fire({
      title: 'Success',
      text: '{{ session('create_success') }}',
      icon: 'success',
      confirmButtonText: 'OK'
    });
  </script>
  @endif
@endsection