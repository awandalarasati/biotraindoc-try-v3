<div id="sidebar" class="sidebar">
    <!-- Header Sidebar (Hamburger + Logo) -->
    <div class="sidebar-header">
        <button id="toggle-btn" class="hamburger">☰</button>
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <!-- Menu Sidebar -->
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

<style>
    /* ==== SIDEBAR STYLING ==== */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background: white;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        border-radius: 0 20px 20px 0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        transition: width 0.3s ease;
        z-index: 1000;
    }

    /* COLLAPSE MODE */
    .sidebar.collapsed {
        width: 70px;
    }

    /* SIDEBAR HEADER */
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    /* LOGO */
    .logo {
        width: 120px;
        transition: width 0.3s ease;
    }
    .sidebar.collapsed .logo {
        width: 40px;
    }

    /* HAMBURGER */
    .hamburger {
        font-size: 24px;
        background: none;
        border: none;
        cursor: pointer;
        color: #029dbb;
    }

    /* MENU ITEM */
    nav {
        display: flex;
        flex-direction: column;
        gap: 10px;
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

    /* HIDE TEXT WHEN COLLAPSED */
    .sidebar.collapsed .nav-text {
        opacity: 0;
        visibility: hidden;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -250px;
        }
        .sidebar.open {
            left: 0;
        }
    }
</style>

<script>
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open'); // Mode slide untuk mobile
        } else {
            sidebar.classList.toggle('collapsed'); // Mode collapse untuk desktop
        }
    });
</script>
