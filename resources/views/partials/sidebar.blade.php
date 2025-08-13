<div id="sidebar" class="sidebar">
    <!-- Desktop sidebar header (hidden on mobile) -->
    <div class="sidebar-header">
        <button id="toggle-btn" class="hamburger">☰</button>
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">
    </div>

    <nav>
        {{-- Menu Dashboard --}}
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            📁 <span class="nav-text">Dashboard</span>
        </a>

        {{-- Tentukan menu Tambah yang tepat --}}
        @php
            // Cek apakah sedang di halaman dokumen (upload file / list file)
            $isDocumentPage = request()->is('documents*');

            // Cek apakah sedang di halaman dalam folder (misal: lihat folder detail)
            $isInsideFolder = request()->routeIs('folders.show') || request()->routeIs('documents.index');

            // Ambil folder_id dari URL jika ada (supaya bisa tambah file ke folder tsb)
            $currentRoute = request()->route();
            $folderId = $currentRoute?->parameter('id') ?? $currentRoute?->parameter('folder_id') ?? null;
        @endphp

        @if ($isDocumentPage || $isInsideFolder)
            <a href="{{ isset($folderId) ? route('documents.create', ['folder_id' => $folderId]) : '#' }}"
               class="nav-item {{ request()->routeIs('documents.create') ? 'active' : '' }}">
                ➕ <span class="nav-text">Tambah File</span>
            </a>
        @else
            <a href="{{ route('folders.create') }}"
               class="nav-item {{ request()->routeIs('folders.create') ? 'active' : '' }}">
                ➕ <span class="nav-text">Tambah Folder</span>
            </a>
        @endif

        <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            👤 <span class="nav-text">Profil</span>
        </a>
    </nav>
</div>
