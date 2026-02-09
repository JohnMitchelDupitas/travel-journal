<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Journal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --tl-cream: #FAF7F4;
            --tl-parchment: #F5EFE8;
            --tl-stone: #E8E2DB;
            --tl-warm-gray: #6B6360;
            --tl-charcoal: #2D2A28;
            --tl-terracotta: #B85C38;
            --tl-terracotta-light: #D4856A;
            --tl-sage: #4A7C59;
            --tl-sage-light: #6B9B6F;
            --tl-amber: #C9A227;
            --tl-amber-light: #E5C457;
            --tl-shadow-sm: 0 1px 2px rgba(45, 42, 40, 0.04);
            --tl-shadow-md: 0 4px 12px rgba(45, 42, 40, 0.06), 0 2px 4px rgba(45, 42, 40, 0.04);
            --tl-shadow-lg: 0 12px 40px rgba(45, 42, 40, 0.08), 0 4px 12px rgba(45, 42, 40, 0.04);
            --tl-shadow-hover: 0 20px 50px rgba(45, 42, 40, 0.1), 0 8px 20px rgba(45, 42, 40, 0.06);
        }

        /* Dark mode variables */
        .dark {
            --tl-cream: #1a1a1a;
            --tl-parchment: #2a2a2a;
            --tl-stone: #3a3a3a;
            --tl-warm-gray: #b0b0b0;
            --tl-charcoal: #e0e0e0;
            --tl-terracotta: #D4856A;
            --tl-terracotta-light: #B85C38;
            --tl-sage: #6B9B6F;
            --tl-sage-light: #4A7C59;
            --tl-amber: #E5C457;
            --tl-amber-light: #C9A227;
            --tl-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.2);
            --tl-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.3), 0 2px 4px rgba(0, 0, 0, 0.2);
            --tl-shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.4), 0 4px 12px rgba(0, 0, 0, 0.2);
            --tl-shadow-hover: 0 20px 50px rgba(0, 0, 0, 0.5), 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        * { font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
        body {
            background: var(--tl-cream);
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(184, 92, 56, 0.06), transparent),
                linear-gradient(180deg, var(--tl-cream) 0%, var(--tl-parchment) 100%);
        }

        h1, h2, h3, h4, .font-display { font-family: 'Cormorant Garamond', Georgia, serif; }

        /* Smooth scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--tl-stone); border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: var(--tl-warm-gray); border-radius: 3px; opacity: 0.5; }
        ::-webkit-scrollbar-thumb:hover { background: var(--tl-charcoal); }

        /* Animations */
        @keyframes slideInRight { from { transform: translateX(16px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes subtleFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-2px); } }

        .animate-slide { animation: slideInRight 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
        .animate-fade { animation: fadeIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards; }

        /* Nav item styling */
        .nav-item {
            position: relative;
            transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
            color: var(--tl-warm-gray);
        }
        .nav-item:hover {
            color: var(--tl-terracotta);
            background-color: rgba(184, 92, 56, 0.06);
        }
        .nav-item.active {
            color: var(--tl-terracotta);
            background-color: rgba(184, 92, 56, 0.08);
        }
        .nav-item.active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: linear-gradient(180deg, var(--tl-terracotta), var(--tl-sage));
            border-radius: 3px 0 0 3px;
        }

        /* Card system */
        .card-shadow { box-shadow: var(--tl-shadow-sm); }
        .card-shadow-hover {
            transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .card-shadow-hover:hover {
            box-shadow: var(--tl-shadow-hover);
            transform: translateY(-2px);
        }

        /* Gradient buttons & accents */
        .gradient-primary { background: linear-gradient(135deg, var(--tl-terracotta) 0%, #9A4A2E 100%); }
        .gradient-success { background: linear-gradient(135deg, var(--tl-sage) 0%, #3D6848 100%); }
        .gradient-warning { background: linear-gradient(135deg, var(--tl-amber) 0%, #A8871F 100%); }

        /* Input & form polish */
        main input:focus, main select:focus, main textarea:focus {
            border-color: var(--tl-terracotta) !important;
            box-shadow: 0 0 0 3px rgba(184, 92, 56, 0.12) !important;
            outline: none !important;
        }

        /* Leaflet popup styling */
        .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: var(--tl-shadow-lg); }

        /* Content area overrides - professional travel log aesthetic */
        main .bg-white { background: rgba(255,255,255,0.9) !important; }
        main .bg-gray-50 { background: var(--tl-parchment) !important; }
        main .border-gray-100,
        main .border-gray-200 { border-color: rgba(232,226,219,0.9) !important; }
        main h1, main .text-4xl { font-family: 'Cormorant Garamond', Georgia, serif !important; color: var(--tl-charcoal) !important; letter-spacing: -0.02em; }
        main .text-gray-600 { color: var(--tl-warm-gray) !important; }
        main .text-gray-900 { color: var(--tl-charcoal) !important; }
        main a[href].text-blue-600 { color: var(--tl-terracotta) !important; }
        main a[href].text-blue-600:hover { color: #9A4A2E !important; }
        main .text-blue-600 { color: var(--tl-terracotta) !important; }
        main .text-blue-100 { color: rgba(184,92,56,0.8) !important; }
        main .bg-blue-50,
        main .bg-gradient-to-br.from-blue-50,
        main .bg-gradient-to-r.from-blue-50 { background: rgba(184,92,56,0.04) !important; }
        main .bg-gradient-to-r.from-gray-50 { background: linear-gradient(90deg, var(--tl-parchment), var(--tl-stone)) !important; }
        main .from-blue-600, main .to-cyan-600,
        main .bg-gradient-to-r.from-blue-600 { background: linear-gradient(135deg, var(--tl-terracotta), var(--tl-sage)) !important; }
        main .rounded-2xl { border-radius: 16px; }
        main table thead { background: var(--tl-parchment) !important; }
        main .hover\:bg-gray-50:hover { background: rgba(245,239,232,0.8) !important; }
        main .hover\:bg-gray-100:hover { background: var(--tl-stone) !important; }
        main .bg-gradient-to-br.from-blue-100 { background: rgba(184,92,56,0.08) !important; }
        main .text-yellow-500 { color: var(--tl-amber) !important; }
        main .text-yellow-300 { color: var(--tl-amber-light) !important; }
        main .bg-purple-500, main .from-purple-500,
        main .from-purple-500.to-pink-500 { background: linear-gradient(135deg, #7B5B8A, #9A7AA8) !important; }
        main .file\:bg-blue-500 { background: var(--tl-terracotta) !important; }
        main .file\:hover\:bg-blue-600:hover { background: #9A4A2E !important; }
        main .focus\:border-blue-500:focus { border-color: var(--tl-terracotta) !important; }
        main .focus\:ring-blue-100:focus { --tw-ring-color: rgba(184,92,56,0.15) !important; }
        main button[type="submit"].gradient-primary:hover { box-shadow: 0 4px 20px rgba(184,92,56,0.35); }
    </style>
</head>
<body class="h-screen flex overflow-hidden text-[#2D2A28]" style="background: var(--tl-cream); background-image: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(184,92,56,0.06), transparent), linear-gradient(180deg, var(--tl-cream) 0%, var(--tl-parchment) 100%);">

    <aside class="w-20 md:w-64 flex flex-col justify-between flex-shrink-0 transition-all duration-300" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); border-right: 1px solid rgba(232,226,219,0.9); box-shadow: 0 1px 0 rgba(255,255,255,0.8) inset;">
        <div>
            <div class="h-20 flex items-center justify-center md:justify-start md:px-6" style="border-bottom: 1px solid rgba(232,226,219,0.8);">
                <span class="text-3xl transform transition-transform hover:scale-110 duration-300" style="filter: grayscale(0.2);">✈️</span>
                <span class="hidden md:block ml-3 font-semibold text-xl tracking-tight" style="font-family: 'Cormorant Garamond', serif; background: linear-gradient(135deg, #B85C38, #4A7C59); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">TravelLog</span>
            </div>

            <nav class="mt-8 flex flex-col gap-1 px-2 md:px-3">
                
                <a href="{{ url('/') }}" class="nav-item group flex items-center px-4 md:px-4 py-3 rounded-lg {{ request()->is('/') ? 'active' : '' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"/></svg>
                    <span class="hidden md:block ml-3 font-medium text-sm">Overview</span>
                </a>

                <a href="{{ url('/map') }}" class="nav-item group flex items-center px-4 md:px-4 py-3 rounded-lg {{ request()->is('map') ? 'active' : '' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0121 18.382V7.618a1 1 0 01-.447-.894L15 7m0 13V7"/></svg>
                    <span class="hidden md:block ml-3 font-medium text-sm">World Map</span>
                </a>

                <a href="{{ url('/gallery') }}" class="nav-item group flex items-center px-4 md:px-4 py-3 rounded-lg {{ request()->is('gallery') ? 'active' : '' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="hidden md:block ml-3 font-medium text-sm">Photo Gallery</span>
                </a>

                <a href="{{ url('/bucket-list') }}" class="nav-item group flex items-center px-4 md:px-4 py-3 rounded-lg {{ request()->is('bucket-list') ? 'active' : '' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l1 1m0 0l1 1m-1-1l-1 1m1-1l1-1"/></svg>
                    <span class="hidden md:block ml-3 font-medium text-sm">Bucket List</span>
                </a>

            </nav>
        </div>

        <div class="p-4 md:p-6 flex flex-col gap-3" style="border-top: 1px solid rgba(232,226,219,0.8);">
            <!-- Dark Mode Toggle -->
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium" style="color: var(--tl-warm-gray);">Dark Mode</span>
                <button id="darkModeToggle" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-terracotta" style="background: var(--tl-stone);">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" style="transform: translateX(1px);"></span>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm flex-shrink-0" style="background: linear-gradient(135deg, #B85C38, #9A4A2E); box-shadow: 0 2px 8px rgba(184,92,56,0.3);">U</div>
                <div class="hidden md:block min-w-0">
                    <p class="text-sm font-semibold truncate" style="color: var(--tl-charcoal);">Profile</p>
                    <p class="text-xs truncate" style="color: var(--tl-warm-gray);">My Account</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto relative">
        @yield('content')
    </main>

    @yield('scripts')

    <script>
        // Dark Mode Toggle Functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            html.classList.add('dark');
            if (darkModeToggle) {
                darkModeToggle.querySelector('span').style.transform = 'translateX(21px)';
                darkModeToggle.style.background = 'var(--tl-terracotta)';
            }
        }

        // Toggle dark mode
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', () => {
                const isDark = html.classList.toggle('dark');
                const toggleSpan = darkModeToggle.querySelector('span');

                if (isDark) {
                    localStorage.setItem('theme', 'dark');
                    toggleSpan.style.transform = 'translateX(21px)';
                    darkModeToggle.style.background = 'var(--tl-terracotta)';
                } else {
                    localStorage.setItem('theme', 'light');
                    toggleSpan.style.transform = 'translateX(1px)';
                    darkModeToggle.style.background = 'var(--tl-stone)';
                }
            });
        }
    </script>
</body>
</html>
