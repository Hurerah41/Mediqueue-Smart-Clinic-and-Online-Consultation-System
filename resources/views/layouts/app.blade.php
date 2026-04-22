<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MediQueue' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-grid-pattern text-slate-500 font-sans antialiased overflow-x-hidden selection:bg-primary/20 selection:text-primary relative flex flex-col">
    <div id="page-loader" class="page-loader" role="status" aria-live="polite" aria-label="Loading MediQueue">
        <div class="loader-card">
            <div class="loader-logo-wrap">
                <span class="loader-ring loader-ring-blue"></span>
                <span class="loader-ring loader-ring-purple"></span>
                <div class="loader-logo">
                    <i class="ph-bold ph-heartbeat"></i>
                </div>
            </div>
            <p class="loader-brand">MediQueue</p>
        </div>
    </div>

    <div class="hero-gradient"></div>

    <nav class="fixed top-0 w-full z-50 transition-all duration-300 pt-3" id="navbar">
        <div class="nav-shell w-[96%] max-w-[1700px] mx-auto px-3 sm:px-4 lg:px-5 h-16 sm:h-[4.5rem] rounded-[1.6rem] flex items-center justify-between transition-all duration-300">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white shadow-glow-primary group-hover:scale-105 transition-transform duration-300">
                    <i class="ph-bold ph-heartbeat text-lg"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-dark">MediQueue</span>
            </a>

            <div class="hidden md:flex items-center gap-8 font-semibold text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-dark transition-colors relative group">Home<span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span></a>
                <a href="{{ route('clinics.index') }}" class="hover:text-dark transition-colors relative group">Clinics<span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span></a>
                <a href="{{ route('home') }}#features" class="hover:text-dark transition-colors relative group">Features<span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span></a>
                <a href="{{ route('home') }}#how-it-works" class="hover:text-dark transition-colors relative group">How it works<span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"></span></a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-dark transition-colors">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-dark transition-colors">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-500 hover:text-dark transition-colors">Sign In</a>
                    <a href="{{ route('register') }}" class="relative group bg-dark text-white px-5 py-2.5 rounded-full text-sm font-bold overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-dark/20">
                        <span class="relative z-10">Get Started</span>
                        <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-primary via-secondary to-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                @endauth
            </div>

            <button class="md:hidden text-2xl text-dark p-2" id="mobile-menu-btn" type="button" aria-label="Toggle navigation">
                <i class="ph ph-list"></i>
            </button>
        </div>
    </nav>

    <div class="fixed inset-0 bg-white/95 backdrop-blur-xl z-40 transform translate-x-full transition-transform duration-300 flex flex-col pt-24 px-6 border-l border-slate-100" id="mobile-menu">
        <div class="flex flex-col gap-6 text-xl font-bold text-dark">
            <a href="{{ route('home') }}" class="mobile-link hover:text-primary">Home</a>
            <a href="{{ route('clinics.index') }}" class="mobile-link hover:text-primary">Clinics</a>
            <a href="{{ route('home') }}#features" class="mobile-link hover:text-primary">Features</a>
            <a href="{{ route('home') }}#how-it-works" class="mobile-link hover:text-primary">How it works</a>
            <hr class="border-slate-100 my-2">
            @auth
                <a href="{{ route('dashboard') }}" class="mobile-link text-slate-500">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-left text-slate-500">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mobile-link text-slate-500">Sign In</a>
                <a href="{{ route('register') }}" class="bg-dark text-white text-center py-4 rounded-full shadow-md hover:bg-slate-800">Get Started</a>
            @endauth
        </div>
    </div>

    <main class="w-[96%] max-w-[1700px] mx-auto px-2 sm:px-3 lg:px-4 pt-28 pb-12 flex-1">
        @if (session('success'))
            <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50/80 px-5 py-4 text-emerald-700 shadow-soft">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50/80 px-5 py-4 text-rose-700 shadow-soft">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer-shell pt-20 pb-8 mt-auto">
        <div class="w-[96%] max-w-[1700px] mx-auto px-2 sm:px-3 lg:px-4 grid grid-cols-2 md:grid-cols-5 gap-10 mb-16">
            <div class="col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white shadow-sm">
                        <i class="ph-bold ph-heartbeat text-base"></i>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-dark">MediQueue</span>
                </a>
                <p class="text-sm text-slate-500 font-medium leading-relaxed max-w-xs mb-8">
                    The ultra-fast, modern healthcare platform designed to eliminate waiting rooms and optimize clinic efficiency.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-dark mb-5 text-sm uppercase tracking-wider">Product</h4>
                <ul class="space-y-4 text-sm font-medium text-slate-500">
                    <li><a href="{{ route('clinics.index') }}" class="hover:text-primary transition-colors">Clinics</a></li>
                    <li><a href="{{ route('home') }}#features" class="hover:text-primary transition-colors">Features</a></li>
                    <li><a href="{{ route('home') }}#clinics" class="hover:text-primary transition-colors">About us</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-primary transition-colors">Get Started</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-dark mb-5 text-sm uppercase tracking-wider">Company</h4>
                <ul class="space-y-4 text-sm font-medium text-slate-500">
                    <li><a href="#" class="hover:text-primary transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Blog</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Contact</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Support</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-dark mb-5 text-sm uppercase tracking-wider">Legal</h4>
                <ul class="space-y-4 text-sm font-medium text-slate-500">
                    <li><a href="#" class="hover:text-primary transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">HIPAA Compliance</a></li>
                </ul>
            </div>
        </div>

        <div class="w-[96%] max-w-[1700px] mx-auto px-2 sm:px-3 lg:px-4 pt-8 border-t border-slate-200/60 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[11px] font-semibold text-slate-400">&copy; 2026 MediQueue Inc. All rights reserved.</p>
            <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-green-500"></span> All systems operational
            </div>
        </div>
    </footer>

    <div id="toast-stack" class="fixed bottom-6 right-6 z-[9999] space-y-3 w-[calc(100vw-3rem)] max-w-sm"></div>

    <script>
        const navbar = document.getElementById('navbar');
        const mobileMenuButton = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const toastStack = document.getElementById('toast-stack');
        const csrfTokenGlobal = document.querySelector('meta[name="csrf-token"]').content;
        const pageLoader = document.getElementById('page-loader');

        function hidePageLoader() {
            pageLoader?.classList.add('is-hidden');
        }

        function showPageLoader() {
            pageLoader?.classList.remove('is-hidden');
        }

        window.addEventListener('load', () => {
            setTimeout(hidePageLoader, 650);
        });

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                hidePageLoader();
            }
        });

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');

            if (!link) return;
            if (link.target && link.target !== '_self') return;
            if (link.hasAttribute('download')) return;
            if (link.dataset.noLoader === 'true') return;

            const href = link.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

            const destination = new URL(href, window.location.href);
            if (destination.origin !== window.location.origin) return;
            if (destination.pathname === window.location.pathname && destination.hash) return;

            showPageLoader();
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) return;
            if (form.dataset.noLoader === 'true') return;

            showPageLoader();
        });

        function escapeGlobalHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        window.showToast = function (message, type = 'success') {
            if (!toastStack || !message) return;

            const toast = document.createElement('div');
            const styles = type === 'error'
                ? 'border-rose-200 bg-rose-50 text-rose-700'
                : 'border-emerald-200 bg-emerald-50 text-emerald-700';

            toast.className = `rounded-3xl border px-5 py-4 shadow-soft ${styles}`;
            toast.textContent = message;
            toastStack.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(8px)';
                toast.style.transition = '0.3s ease';
            }, 2600);

            setTimeout(() => toast.remove(), 3000);
        };

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                const isClosed = mobileMenu.classList.contains('translate-x-full');

                if (isClosed) {
                    mobileMenu.classList.remove('translate-x-full');
                    mobileMenuButton.innerHTML = '<i class="ph ph-x"></i>';
                    document.body.style.overflow = 'hidden';
                } else {
                    mobileMenu.classList.add('translate-x-full');
                    mobileMenuButton.innerHTML = '<i class="ph ph-list"></i>';
                    document.body.style.overflow = 'auto';
                }
            });
        }

        if (navbar) {
            const navbarShell = navbar.firstElementChild;
            const syncNavbarState = () => {
                if (!navbarShell) return;

                if (window.scrollY > 10) {
                    navbarShell.classList.add('shadow-lg');
                    navbarShell.style.background = 'rgba(255,255,255,0.98)';
                } else {
                    navbarShell.classList.remove('shadow-lg');
                    navbarShell.style.background = '';
                }
            };

            syncNavbarState();
            window.addEventListener('scroll', syncNavbarState, { passive: true });
        }

        @if (session('success'))
            window.showToast(@json(session('success')), 'success');
        @endif

        @if ($errors->any())
            window.showToast(@json($errors->first()), 'error');
        @endif

        @auth
            const notificationBell = document.getElementById('notification-bell');
            const notificationPanel = document.getElementById('notification-panel');
            const notificationList = document.getElementById('notification-list');
            const notificationCount = document.getElementById('notification-count');
            const notificationLiveUrl = "{{ route('notifications.live') }}";
            let latestNotificationId = 0;
            let notificationFeedBooted = false;

            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission().catch(() => {});
            }

            if (notificationBell && notificationPanel) {
                notificationBell.addEventListener('click', () => {
                    notificationPanel.classList.toggle('hidden');
                });
            }

            async function markNotificationRead(notificationId) {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenGlobal,
                        'Accept': 'application/json'
                    }
                });
            }

            async function refreshNotifications() {
                const response = await fetch(notificationLiveUrl, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) return;

                const data = await response.json();
                const notifications = data.notifications || [];
                const unreadCount = Number(data.unread_count || 0);

                if (notificationCount) {
                    notificationCount.innerText = unreadCount;
                    notificationCount.classList.toggle('hidden', unreadCount === 0);
                }

                if (notifications.length && notificationFeedBooted && notifications[0].id > latestNotificationId) {
                    const newest = notifications[0];
                    window.showToast(`${newest.title}: ${newest.message}`, 'success');

                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification(newest.title, { body: newest.message });
                    }
                }

                if (notifications.length) {
                    latestNotificationId = Math.max(latestNotificationId, notifications[0].id);
                }

                notificationFeedBooted = true;

                if (!notificationList) return;

                if (!notifications.length) {
                    notificationList.innerHTML = '<p class="text-sm text-slate-500 p-3">No notifications yet.</p>';
                    return;
                }

                notificationList.innerHTML = notifications.map((notification) => `
                    <a href="${escapeGlobalHtml(notification.action_url || '#')}" data-notification-id="${notification.id}" class="block rounded-2xl px-4 py-3 mb-2 ${notification.is_read ? 'bg-slate-50' : 'bg-blue-50'} hover:bg-primary/10 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-bold text-dark">${escapeGlobalHtml(notification.title)}</div>
                                <div class="text-xs text-slate-600 mt-1">${escapeGlobalHtml(notification.message)}</div>
                            </div>
                            <span class="text-[10px] text-slate-400 shrink-0">${escapeGlobalHtml(notification.created_at)}</span>
                        </div>
                    </a>
                `).join('');

                notificationList.querySelectorAll('[data-notification-id]').forEach((item) => {
                    item.addEventListener('click', () => {
                        markNotificationRead(item.dataset.notificationId).catch(() => {});
                    });
                });
            }

            refreshNotifications();
            setInterval(refreshNotifications, 4000);
        @endauth
    </script>
</body>
</html>
