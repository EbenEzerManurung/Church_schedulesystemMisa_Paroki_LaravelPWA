<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style>
        .bg-paleturquoise-grad { background: linear-gradient(to bottom right, #afeeee, #7ec8c9); }
        .text-paleturquoise-dark { color: #2f7d7e; }
        .text-paleturquoise-soft { color: #d3f5f5; }
        .bg-paleturquoise-btn { background-color: #5fb8b9; }
        .bg-paleturquoise-btn:hover { background-color: #4ea3a4; }
        .border-paleturquoise:focus { border-color: #7ec8c9; }
    </style>

</head>
<body class="bg-paleturquoise-grad min-h-screen">
    <div class="container mx-auto px-4 py-20">
        <div class="max-w-md mx-auto">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-church text-paleturquoise-dark text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white">Welcome</h1>
              <p class="text-paleturquoise-soft mt-2 text-xl font-semibold">Church Schedule System</p>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Login</h2>
                    
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none border-paleturquoise"
                                       placeholder="admin@church.com" required>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                                <input type="password" name="password" 
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none border-paleturquoise"
                                       placeholder="password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-paleturquoise-btn text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login
                        </button>
                    </form>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500 text-center">
                            Demo Credentials:<br>
                            Admin: admin@church.com / password123<br>
                            Admin keusukupan: admin.bogor@keuskupan.com / password123<br>
                            Admin Gereja: admin.bogor@gereja.com / password123<br>
                            User PIC Koor: samuel.koor@group.com / password123<br>
                            User biasa: ebenmanurung@gmail.com / password123
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
