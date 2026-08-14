<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2bb8ab">
    <title>@yield('title', 'Church Schedule System')</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <!-- Icons -->
    @php
        $iconSizes = [72, 96, 128, 144, 152, 192, 384, 512];
    @endphp
    @foreach($iconSizes as $size)
    <link rel="icon" type="image/png" sizes="{{ $size }}x{{ $size }}" href="{{ asset("icons/icon-{$size}x{$size}.png") }}">
    @endforeach
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet">
    
    <style>
        /* ============================================ */
        /* RESET & BASE */
        /* ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f0f2f5;
            color: #1a1a2e;
            min-height: 100vh;
        }
        
        /* ============================================ */
        /* SIDEBAR */
        /* ============================================ */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 280px;
            background: linear-gradient(180deg, #40e0d0 0%, #2bb8ab 50%, #1f9d92 100%);
            color: #f0fdfa;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 4px 0 20px rgba(0,0,0,0.2);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }
        
        .sidebar-collapsed {
            transform: translateX(-100%);
        }
        
        /* Sidebar Header */
        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            text-align: center;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .sidebar-logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0d9488, #0f766e);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            box-shadow: 0 4px 12px rgba(13,148,136,0.4);
        }
        
        .sidebar-logo-text {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #ffffff, #d1fffa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .sidebar-subtitle {
            font-size: 0.6rem;
            color: #e6fffb;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 0.25rem;
            -webkit-text-fill-color: #e6fffb;
            opacity: 0.85;
        }
        
        /* Sidebar User Info */
        .sidebar-user-info {
            padding: 1rem 1.25rem;
            margin: 0.75rem 1rem;
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.15);
        }
        
        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d9488, #0f766e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            flex-shrink: 0;
        }
        
        .sidebar-user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #ffffff;
        }
        
        .sidebar-user-role {
            font-size: 0.65rem;
            color: #e6fffb;
            opacity: 0.85;
        }
        
        .sidebar-user-church {
            font-size: 0.6rem;
            color: #ffffff;
            margin-top: 0.15rem;
            opacity: 0.9;
        }
        
        /* Sidebar Navigation */
        .sidebar-nav {
            padding: 0.5rem 0.75rem;
        }
        
        .sidebar-nav-label {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #e6fffb;
            padding: 0.75rem 0.75rem 0.5rem;
            font-weight: 600;
            opacity: 0.7;
        }
        
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.75rem;
            margin: 0.15rem 0;
            color: #f0fdfa;
            transition: all 0.25s;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            position: relative;
            gap: 0.75rem;
            opacity: 0.85;
        }
        
        .sidebar-nav-item:hover {
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            transform: translateX(4px);
            opacity: 1;
        }
        
        .sidebar-nav-item.active {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.25);
            opacity: 1;
        }
        
        .sidebar-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: #ffffff;
            border-radius: 0 4px 4px 0;
        }
        
        .sidebar-nav-item i {
            width: 20px;
            font-size: 1rem;
            text-align: center;
            flex-shrink: 0;
        }
        
        .sidebar-nav-item .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: 0.6rem;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
        }
        
        /* ============================================ */
        /* MAIN CONTENT */
        /* ============================================ */
        .main-content {
            margin-left: 280px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            background: #f0f2f5;
        }
        
        .main-content-expanded {
            margin-left: 0;
        }
        
        /* ============================================ */
        /* NAVBAR */
        /* ============================================ */
        .navbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
            transition: all 0.3s;
        }
        
        .navbar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: -0.3px;
        }
        
        .navbar-title i {
            color: #0d9488;
            margin-right: 0.5rem;
        }
        
        /* Navbar Notification Bell */
        .navbar-bell-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f1f5f9;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
        }
        
        .navbar-bell-btn:hover {
            background: #e2e8f0;
            color: #1a1a2e;
        }
        
        .navbar-bell-btn .bell-icon {
            font-size: 1.15rem;
        }
        
        .navbar-bell-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            border: 2px solid white;
            animation: pulse-badge 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 380px;
            max-height: 420px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
            z-index: 100;
        }
        
        .notification-header {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }
        
        .notification-header-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .notification-header-title i {
            color: #0d9488;
            margin-right: 0.5rem;
        }
        
        .notification-mark-all {
            font-size: 0.7rem;
            color: #0d9488;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .notification-mark-all:hover {
            color: #0f766e;
        }
        
        .notification-list {
            overflow-y: auto;
            max-height: 320px;
        }
        
        .notification-list::-webkit-scrollbar {
            width: 4px;
        }
        .notification-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .notification-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }
        
        .notification-item:hover {
            background: #f8fafc;
        }
        
        .notification-item.unread {
            background: #f0fdfa;
        }
        
        .notification-item.unread:hover {
            background: #ccfbf1;
        }
        
        .notification-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.8rem;
        }
        
        .notification-icon.blue { background: #dbeafe; color: #2563eb; }
        .notification-icon.green { background: #d1fae5; color: #059669; }
        .notification-icon.yellow { background: #fef3c7; color: #d97706; }
        .notification-icon.purple { background: #ede9fe; color: #7c3aed; }
        .notification-icon.red { background: #fee2e2; color: #dc2626; }
        
        .notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .notification-message {
            font-size: 0.8rem;
            color: #1a1a2e;
            line-height: 1.4;
        }
        
        .notification-message strong {
            font-weight: 600;
        }
        
        .notification-time {
            font-size: 0.65rem;
            color: #94a3b8;
            margin-top: 0.2rem;
        }
        
        .notification-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #0d9488;
            flex-shrink: 0;
            margin-top: 8px;
        }
        
        .notification-empty {
            padding: 2rem 1.25rem;
            text-align: center;
            color: #94a3b8;
        }
        
        .notification-empty i {
            font-size: 2.5rem;
            color: #e2e8f0;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .notification-empty p {
            font-size: 0.85rem;
        }
        
        .notification-footer {
            padding: 0.5rem 1.25rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            background: #f8fafc;
        }
        
        .notification-footer a {
            font-size: 0.75rem;
            color: #0d9488;
            text-decoration: none;
            font-weight: 500;
        }
        
        .notification-footer a:hover {
            color: #0f766e;
        }
        
        /* ============================================ */
        /* USER DROPDOWN */
        /* ============================================ */
        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.3rem 0.75rem 0.3rem 0.3rem;
            border-radius: 9999px;
            background: #f1f5f9;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .user-dropdown-btn:hover {
            background: #e2e8f0;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d9488, #0f766e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            color: white;
            flex-shrink: 0;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .user-name {
            font-size: 0.8rem;
            font-weight: 500;
            color: #1a1a2e;
        }
        
        .user-dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 220px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
            z-index: 100;
        }
        
        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.25rem;
            color: #475569;
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .user-dropdown-item:hover {
            background: #f1f5f9;
            color: #1a1a2e;
        }
        
        .user-dropdown-item i {
            width: 18px;
            color: #94a3b8;
        }
        
        .user-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 0.25rem 0;
        }
        
        .user-dropdown-item.danger {
            color: #ef4444;
        }
        
        .user-dropdown-item.danger i {
            color: #ef4444;
        }
        
        .user-dropdown-item.danger:hover {
            background: #fef2f2;
        }
        
        /* ============================================ */
        /* ORGANIZATION BADGE */
        /* ============================================ */
        .org-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #f1f5f9;
            color: #475569;
            padding: 0.2rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
        }
        
        .org-badge i {
            font-size: 0.6rem;
            color: #94a3b8;
        }
        
        .org-badge.keuskupan {
            background: #ccfbf1;
            color: #0f766e;
            border-color: #99f6e4;
        }
        
        .org-badge.keuskupan i {
            color: #0d9488;
        }
        
        .org-badge.gereja {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        
        .org-badge.gereja i {
            color: #10b981;
        }
        
        .org-badge.role {
            background: #ede9fe;
            color: #5b21b6;
            border-color: #ddd6fe;
        }
        
        .org-badge.role i {
            color: #8b5cf6;
        }
        
        /* ============================================ */
        /* ALERT MESSAGES */
        /* ============================================ */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border-left: 4px solid;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }
        
        .alert-success {
            background: #ecfdf5;
            border-color: #10b981;
            color: #065f46;
        }
        
        .alert-error {
            background: #fef2f2;
            border-color: #ef4444;
            color: #991b1b;
        }
        
        .alert-warning {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #92400e;
        }
        
        .alert-info {
            background: #f0fdfa;
            border-color: #0d9488;
            color: #0f766e;
        }
        
        .alert-close {
            background: none;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            padding: 0 0.25rem;
        }
        
        .alert-close:hover {
            opacity: 1;
        }
        
        /* ============================================ */
        /* CARDS */
        /* ============================================ */
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: all 0.3s;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
        }
        
        .card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbfc;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* ============================================ */
        /* BUTTONS */
        /* ============================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            font-size: 0.85rem;
            border-radius: 10px;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            color: white;
            box-shadow: 0 4px 12px rgba(13,148,136,0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13,148,136,0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 12px rgba(239,68,68,0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239,68,68,0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 4px 12px rgba(245,158,11,0.3);
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245,158,11,0.4);
        }
        
        .btn-outline {
            background: transparent;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-outline:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 8px;
        }
        
        /* ============================================ */
        /* TABLE */
        /* ============================================ */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table thead th {
            padding: 0.6rem 1rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .table tbody td {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .table tbody tr:hover {
            background: #f8fafc;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* ============================================ */
        /* BADGES */
        /* ============================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.6rem;
            font-size: 0.65rem;
            font-weight: 600;
            border-radius: 9999px;
            gap: 0.3rem;
        }
        
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-accepted { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-completed { background: #ccfbf1; color: #0f766e; }
        .badge-cancelled { background: #f1f5f9; color: #475569; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        
        /* ============================================ */
        /* FORM */
        /* ============================================ */
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.4rem;
        }
        
        .form-label .required {
            color: #ef4444;
            margin-left: 0.2rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            transition: all 0.2s;
            font-size: 0.85rem;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
        }
        
        .form-control.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }
        
        .form-error {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        /* ============================================ */
        /* LOADING */
        /* ============================================ */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top: 3px solid #2dd4bf;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        .loading-text {
            color: white;
            margin-top: 1rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* ============================================ */
        /* PAGINATION */
        /* ============================================ */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.3rem;
            margin-top: 1.5rem;
        }
        
        .pagination .page-item {
            list-style: none;
        }
        
        .pagination .page-link {
            padding: 0.4rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #475569;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .pagination .page-link:hover {
            background: #f1f5f9;
        }
        
        .pagination .active .page-link {
            background: #0d9488;
            border-color: #0d9488;
            color: white;
        }
        
        .pagination .disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* ============================================ */
        /* RESPONSIVE */
        /* ============================================ */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .notification-dropdown {
                width: 320px;
                right: -80px;
            }
            
            .navbar-title {
                font-size: 0.9rem;
            }
            
            .user-name {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .notification-dropdown {
                width: 280px;
                right: -100px;
            }
        }
        
        /* ============================================ */
        /* SCROLLBAR */
        /* ============================================ */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* ============================================ */
        /* X-CLOAK */
        /* ============================================ */
        [x-cloak] {
            display: none !important;
        }
    </style>
    
    @stack('styles')
</head>
<body x-data="{ 
    sidebarOpen: window.innerWidth > 768, 
    mobileOpen: false
}" 
@resize.window="sidebarOpen = window.innerWidth > 768">
    
    <!-- ============================================ -->
    <!-- SIDEBAR -->
    <!-- ============================================ -->
    <aside class="sidebar" :class="{ 'mobile-open': mobileOpen, 'sidebar-collapsed': !sidebarOpen }">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">
                    <i class="fas fa-cross"></i>
                </div>
                <div>
                    <div class="sidebar-logo-text">Church Schedule System</div>
                    <div class="sidebar-subtitle">Multi Keuskupan</div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar User Info -->
        @auth
        <div class="sidebar-user-info">
            <div class="flex items-center gap-3">
                <div class="sidebar-user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="sidebar-user-name truncate">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">
                        @if(auth()->user()->isSuperAdmin()) Super Admin
                        @elseif(auth()->user()->isAdminKeuskupan()) Admin Keuskupan
                        @elseif(auth()->user()->isAdminGereja()) Admin Gereja
                        @else User
                        @endif
                    </div>
                    @if(auth()->user()->church_name)
                    <div class="sidebar-user-church">
                        <i class="fas fa-church mr-1"></i> {{ auth()->user()->church_name }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endauth
        
        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav">
            @include('layouts.partials.sidebar')
        </nav>
    </aside>
    
    <!-- ============================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================ -->
    <div class="main-content" :class="{ 'main-content-expanded': !sidebarOpen }">
        
        <!-- ============================================ -->
        <!-- NAVBAR -->
        <!-- ============================================ -->
        @include('layouts.partials.navbar')
        
        <!-- ============================================ -->
        <!-- PAGE CONTENT -->
        <!-- ============================================ -->
        <main class="p-4 md:p-6">
            
            <!-- Organization Info Bar -->
            @auth
            <div class="flex flex-wrap gap-2 items-center mb-4">
                <span class="org-badge keuskupan">
                    <i class="fas fa-diocese"></i>
                    {{ auth()->user()->keuskupan_name ?? 'Semua Keuskupan' }}
                </span>
                @if(auth()->user()->church_name)
                <span class="org-badge gereja">
                    <i class="fas fa-church"></i>
                    {{ auth()->user()->church_name }}
                </span>
                @endif
                <span class="org-badge role">
                    <i class="fas fa-user-tag"></i>
                    @if(auth()->user()->isSuperAdmin()) Super Admin
                    @elseif(auth()->user()->isAdminKeuskupan()) Admin Keuskupan
                    @elseif(auth()->user()->isAdminGereja()) Admin Gereja
                    @else User
                    @endif
                </span>
            </div>
            @endauth
            
            <!-- Alert Messages -->
            @if(session('success'))
            <div class="alert alert-success" x-data="{ show: true }" x-show="show">
                <div>
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
                <button class="alert-close" @click="show = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-error" x-data="{ show: true }" x-show="show">
                <div>
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
                <button class="alert-close" @click="show = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @if(session('warning'))
            <div class="alert alert-warning" x-data="{ show: true }" x-show="show">
                <div>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('warning') }}
                </div>
                <button class="alert-close" @click="show = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    <!-- ============================================ -->
    <!-- SCRIPTS -->
    <!-- ============================================ -->
    <script>
        // ============================================
        // GLOBAL VARIABLES
        // ============================================
        @php
            $userData = null;
            if (auth()->check()) {
                $userData = [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'role' => auth()->user()->getRoleNames()->first() ?? 'user',
                    'isSuperAdmin' => auth()->user()->isSuperAdmin(),
                    'isAdminKeuskupan' => auth()->user()->isAdminKeuskupan(),
                    'isAdminGereja' => auth()->user()->isAdminGereja(),
                    'keuskupanCode' => auth()->user()->keuskupan_code ?? null,
                    'keuskupanName' => auth()->user()->keuskupan_name ?? null,
                    'churchCode' => auth()->user()->church_code ?? null,
                    'churchName' => auth()->user()->church_name ?? null
                ];
            }
        @endphp
        
        window.app = {
            user: @json($userData),
            csrfToken: '{{ csrf_token() }}'
        };
        
        // ============================================
        // SERVICE WORKER
        // ============================================
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('ServiceWorker registered successfully:', registration.scope);
                        })
                        .catch(function(err) {
                            console.log('ServiceWorker registration failed: ', err);
                        });
                }, 3000);
            });
        }
        
        // ============================================
        // GLOBAL FUNCTIONS
        // ============================================
        
        window.confirmDelete = function(message) {
            return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
        };
        
        window.showLoading = function() {
            let overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.id = 'loadingOverlay';
            overlay.innerHTML = `
                <div class="spinner"></div>
                <p class="loading-text">Memuat...</p>
            `;
            document.body.appendChild(overlay);
            return overlay;
        };
        
        window.hideLoading = function(overlay) {
            if (overlay) overlay.remove();
            const existingOverlay = document.getElementById('loadingOverlay');
            if (existingOverlay) existingOverlay.remove();
        };
        
        window.showToast = function(message, type = 'success') {
            const colors = {
                success: 'bg-emerald-500',
                error: 'bg-red-500',
                warning: 'bg-amber-500',
                info: 'bg-blue-500'
            };
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 z-50 p-4 rounded-xl shadow-2xl text-white ${colors[type] || colors.info} max-w-sm`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas ${icons[type] || icons.info} text-lg"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };
        
        window.formatRupiah = function(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        };
        
        window.formatDate = function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        };
        
        window.formatDateTime = function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        };
        
        window.getStatusBadgeClass = function(status) {
            const map = {
                'pending': 'badge-pending',
                'accepted': 'badge-accepted',
                'rejected': 'badge-rejected',
                'completed': 'badge-completed',
                'cancelled': 'badge-cancelled',
                'active': 'badge-active',
                'inactive': 'badge-inactive'
            };
            return map[status] || 'badge-pending';
        };
        
        window.getStatusLabel = function(status) {
            const map = {
                'pending': 'Menunggu',
                'accepted': 'Diterima',
                'rejected': 'Ditolak',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan',
                'active': 'Aktif',
                'inactive': 'Tidak Aktif'
            };
            return map[status] || status;
        };
        
        window.getRoleBadgeClass = function(role) {
            const map = {
                'super_admin': 'bg-purple-100 text-purple-800',
                'admin_keuskupan': 'bg-blue-100 text-blue-800',
                'admin_gereja': 'bg-green-100 text-green-800',
                'user': 'bg-gray-100 text-gray-800'
            };
            return map[role] || 'bg-gray-100 text-gray-800';
        };
    </script>
    
    @stack('scripts')
</body>
</html>
