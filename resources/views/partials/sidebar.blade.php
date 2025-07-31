<div id="sidebar" class="sidebar">
    <!-- Desktop sidebar header (hidden on mobile) -->
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