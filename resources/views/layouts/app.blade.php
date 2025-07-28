<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biofarma Dokumentasi</title>
    <link rel="icon" href="{{ asset('assets/images/logo1.png') }}" type="image/png">

    <style>
        /* RESET & BASE STYLE */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            background: linear-gradient(#b5f1ff, white);
            font-family: sans-serif;
            overflow-x: hidden !important;
            position: relative;
            max-width: 100%;
        }
        body {
            display: flex;
            touch-action: pan-y;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            border-radius: 0 20px 20px 0;
            padding: 20px 15px;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
            z-index: 1000;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        @media (max-width: 650px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.open {
                left: 0;
            }
        }

        /* HEADER SIDEBAR */
        .sidebar-header {
            display: flex;
            align-items: center;   /* ✅ hamburger dan logo sejajar vertikal */
            margin-bottom: 30px;
            padding: 4px 8px;
        }

        /* HAMBURGER ICON */
        .hamburger {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #029dbb;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }

        /* LOGO BIOFARMA */
        .logo {
            width: 120px;       /* ✅ logo besar kembali */
            height: auto;
            margin-left: 10px;  /* ✅ sedikit jarak dari hamburger */
            transition: opacity 0.3s ease, width 0.3s ease;
        }

        .sidebar.collapsed .logo {
            opacity: 0;
            width: 0;
            visibility: hidden;
        }

        /* MENU SIDEBAR */
        nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
            overflow-y: auto;
        }
        nav::-webkit-scrollbar {
            display: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 10px;
            color: #029dbb;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .nav-item:hover,
        .nav-item.active {
            background-color: #029dbb;
            color: white;
        }

        .nav-text {
            margin-left: 10px;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
        }

        /* KONTEN */
        .content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            overflow-x: auto;
            transition: margin-left 0.3s ease;
            max-width: calc(100% - 250px);
        }

        .sidebar.collapsed ~ .content {
            margin-left: 80px;
            max-width: calc(100% - 80px);
        }

        @media (max-width: 650px) {
            .content {
                margin-left: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    {{-- ==== SIDEBAR ==== --}}
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <button id="toggle-btn" class="hamburger">☰</button>
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">
        </div>

        <nav>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                📁 <span class="nav-text">Dashboard</span>
            </a>

            @php
                $currentRoute = request()->route();
                $folderId = $currentRoute?->parameter('id') ?? $currentRoute?->parameter('folder_id') ?? null;
            @endphp

            @if ($folderId)
                <a href="{{ route('documents.create', ['folder_id' => $folderId]) }}" class="nav-item {{ request()->routeIs('documents.create') ? 'active' : '' }}">
                    ➕ <span class="nav-text">Tambah File</span>
                </a>
            @else
                <a href="{{ route('folders.create') }}" class="nav-item {{ request()->routeIs('folders.create') ? 'active' : '' }}">
                    ➕ <span class="nav-text">Tambah Folder</span>
                </a>
            @endif

            <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                👤 <span class="nav-text">Profil</span>
            </a>
        </nav>
    </div>

    {{-- ==== KONTEN ==== --}}
    <div class="content">
        @yield('content')
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 650) {
                sidebar.classList.toggle('open');   // Mobile: slide sidebar
            } else {
                sidebar.classList.toggle('collapsed'); // Desktop: collapse sidebar
            }
        });
    </script>

</body>
</html>
