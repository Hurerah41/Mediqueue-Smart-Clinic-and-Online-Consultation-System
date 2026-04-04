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
<body class="min-h-screen bg-light text-dark font-sans antialiased overflow-x-hidden selection:bg-primary/20 selection:text-primary flex flex-col">
    <div class="fixed inset-0 w-full h-full z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/20 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-secondary/20 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob"></div>
        <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-accent/10 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob"></div>
    </div>

    <nav class="fixed top-0 w-full z-50 glass-panel border-b border-white/40 transition-all duration-300" id="navbar">
        <div class="w-full px-4 sm:px-6 lg:px-10 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-premium flex items-center justify-center text-white shadow-glow group-hover:scale-105 transition-transform duration-300">
                    <i class="ph-bold ph-heartbeat text-2xl"></i>
                </div>
                <span class="text-xl font-bold tracking-tight text-dark">Medi<span class="text-primary">Queue</span></span>
            </a>

            <div class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <a href="{{ route('clinics.index') }}" class="hover:text-primary transition-colors">Clinics</a>
                <a href="{{ route('clinic-applications.create') }}" class="hover:text-primary transition-colors">Register Clinic</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                @endauth
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    <div class="relative">
                        <button type="button" id="notification-bell" class="relative w-11 h-11 rounded-full bg-white border border-slate-200 flex items-center justify-center text-xl text-dark hover:text-primary">
                            <i class="ph-fill ph-bell"></i>
                            <span id="notification-count" class="hidden absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center">0</span>
                        </button>
                        <div id="notification-panel" class="hidden absolute right-0 mt-3 w-96 max-w-[90vw] bg-white rounded-[1.5rem] border border-slate-100 shadow-soft overflow-hidden">
                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                <span class="font-extrabold text-dark">Notifications</span>
                                <span class="text-xs text-slate-400">Live</span>
                            </div>
                            <div id="notification-list" class="max-h-96 overflow-y-auto hide-scrollbar p-3">
                                <p class="text-sm text-slate-500 p-3">No notifications yet.</p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-600 hover:text-dark transition-colors">Log out</button>
                    </form>
                    <a href="{{ route('dashboard') }}" class="bg-dark text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-slate-800 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-dark transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="bg-dark text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-slate-800 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">Book Token</a>
                @endauth
            </div>

            <button class="md:hidden w-11 h-11 rounded-xl bg-white/80 border border-slate-200 flex items-center justify-center text-2xl text-dark" id="mobile-menu-btn" type="button" aria-label="Toggle navigation">
                <i class="ph ph-list"></i>
            </button>
        </div>

        <div class="md:hidden hidden border-t border-slate-200/70 bg-white/90 backdrop-blur-xl px-6 py-4" id="mobile-menu">
            <div class="flex flex-col gap-4 text-sm font-semibold text-slate-700">
                <a href="{{ route('home') }}" class="hover:text-primary">Home</a>
                <a href="{{ route('clinics.index') }}" class="hover:text-primary">Clinics</a>
                <a href="{{ route('clinic-applications.create') }}" class="hover:text-primary">Register Clinic</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-primary">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-left hover:text-rose-600">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-primary">Log in</a>
                    <a href="{{ route('register') }}" class="bg-gradient-premium text-white px-5 py-3 rounded-2xl text-center shadow-glow">Book Token</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="w-full px-4 sm:px-6 lg:px-10 pt-28 pb-12 flex-1">
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

    <footer class="mt-auto bg-white/70 border-t border-slate-200/70 py-8">
        <div class="w-full px-4 sm:px-6 lg:px-10 flex flex-col md:flex-row justify-between gap-3 text-sm text-slate-500">
            <p>&copy; {{ date('Y') }} MediQueue. Smart Clinic SaaS MVP.</p>
            <p>Built with Laravel, MySQL, Tailwind CSS, and JavaScript.</p>
        </div>
    </footer>

    <div id="toast-stack" class="fixed bottom-6 right-6 z-[9999] space-y-3 w-[calc(100vw-3rem)] max-w-sm"></div>

    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const toastStack = document.getElementById('toast-stack');
        const csrfTokenGlobal = document.querySelector('meta[name="csrf-token"]').content;

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
                mobileMenu.classList.toggle('hidden');
                mobileMenuButton.innerHTML = mobileMenu.classList.contains('hidden')
                    ? '<i class="ph ph-list"></i>'
                    : '<i class="ph ph-x"></i>';
            });
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
