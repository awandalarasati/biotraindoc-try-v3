<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biofarma Dokumentasi</title>
    <link rel="icon" href="{{ asset('assets/images/logo1.png') }}" type="image/png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
            background: linear-gradient(#b5f1ff, white);
            font-family: sans-serif;
            overflow-x: hidden;
        }

        body {
            display: flex;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: white;
            border-radius: 0 20px 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 1000;
            padding: 20px 15px;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
            margin-bottom: 30px;
            height: 60px;
        }

        .hamburger {
            font-size: 22px;
            background: none;
            border: none;
            color: #029dbb;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .logo {
            height: 36px;
            width: auto;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar.collapsed .logo {
            width: 0;
            opacity: 0;
            visibility: hidden;
        }

        nav {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 10px;
            color: #029dbb;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s ease;
            height: 48px;
        }

        .nav-item:hover,
        .nav-item.active {
            background-color: #029dbb;
            color: white;
        }

        .nav-text {
            margin-left: 10px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
        }

        .content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            max-width: calc(100% - 250px);
        }

        .sidebar.collapsed ~ .content {
            margin-left: 70px;
            max-width: calc(100% - 70px);
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.4);
            z-index: 900;
            display: none;
        }

        .overlay.show {
            display: block;
        }

        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: white;
            border-bottom: 1px solid #e6e6e6;
            z-index: 1100;
            padding: 0 20px;
            align-items: center;
            gap: 15px;
        }

        .mobile-header .hamburger {
            font-size: 22px;
            background: none;
            border: none;
            color: #029dbb;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-header .logo {
            height: 36px;
            width: auto;
        }

        @media (max-width: 768px) {
            .mobile-header {
                display: flex;
            }

            .sidebar {
                left: -250px;
                transition: left 0.3s ease;
                border-radius: 0 20px 20px 0;
                width: 250px !important;
                padding-top: 80px;
            }

            .sidebar .sidebar-header {
                display: none;
            }

            .sidebar.open {
                left: 0;
            }

            .content {
                margin-left: 0 !important;
                max-width: 100% !important;
                padding-top: 80px;
            }

            .sidebar.collapsed .nav-text,
            .sidebar.collapsed .logo {
                opacity: 1 !important;
                visibility: visible !important;
                width: auto !important;
            }

            .sidebar.collapsed {
                width: 250px !important;
            }
        }

        @media (max-width: 480px) {
            .mobile-header {
                padding: 0 15px;
            }
            
            .content {
                padding: 15px;
                padding-top: 75px;
            }
            
            .mobile-header .logo {
                height: 32px;
            }
            
            .sidebar {
                padding-top: 75px;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-header" id="mobile-header">
        <button id="mobile-toggle-btn" class="hamburger">☰</button>
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="logo">
    </div>

    @include('partials.sidebar')

    <div id="overlay" class="overlay"></div>

    <div class="content" id="main-content">
        @yield('content')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggle-btn');
            const mobileToggleBtn = document.getElementById('mobile-toggle-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const content = document.getElementById('main-content');

            function handleSidebarToggle() {
                if (window.innerWidth <= 768) {
                    // Mobile behavior
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('show');
                } else {
                    // Desktop behavior
                    sidebar.classList.toggle('collapsed');
                }
            }

            function closeMobileSidebar() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            }

            // Desktop hamburger button
            if (toggleBtn) {
                toggleBtn.addEventListener('click', handleSidebarToggle);
            }

            // Mobile hamburger button
            if (mobileToggleBtn) {
                mobileToggleBtn.addEventListener('click', handleSidebarToggle);
            }

            // Overlay click to close sidebar on mobile
            if (overlay) {
                overlay.addEventListener('click', closeMobileSidebar);
            }

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    // Desktop mode
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                } else {
                    // Mobile mode - remove collapsed state
                    sidebar.classList.remove('collapsed');
                }
            });

            // Close mobile sidebar when clicking nav items
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        setTimeout(closeMobileSidebar, 100);
                    }
                });
            });
        });
    </script>
</body>
</html>