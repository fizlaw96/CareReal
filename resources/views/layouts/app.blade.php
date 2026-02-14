<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>{{ 'CareReal.my' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/carereal_logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/carereal_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/carereal_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-80 bg-[radial-gradient(45rem_20rem_at_20%_0%,rgba(20,184,166,0.16),transparent),radial-gradient(45rem_20rem_at_80%_0%,rgba(8,145,178,0.14),transparent)]"></div>

    <nav class="sticky top-0 z-40 border-b border-teal-100/70 bg-white/80 backdrop-blur-xl">
        <div class="mx-auto max-w-6xl px-4 py-3">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center">
                    <img src="{{ asset('assets/images/carereal_logo.png') }}" alt="CareReal.my" class="h-11 w-auto">
                    <span class="sr-only">CareReal.my</span>
                </a>

                <button
                    type="button"
                    id="mobileNavToggle"
                    aria-controls="mobileNavMenu"
                    aria-expanded="false"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 md:hidden"
                >
                    <svg id="mobileNavIconOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                    </svg>
                    <svg id="mobileNavIconClose" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="hidden h-5 w-5">
                        <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <span class="sr-only">Buka menu</span>
                </button>

                <div class="hidden items-center gap-6 text-sm font-semibold md:flex">
                    <a href="{{ route('categories') }}" class="{{ request()->routeIs('categories', 'estimator', 'estimator.questions', 'estimation.calculate') ? 'text-teal-700' : 'text-slate-600 hover:text-teal-600' }}">Anggar Kos</a>
                    <a href="{{ route('clinic.finder') }}" class="{{ request()->routeIs('clinic.finder', 'clinic.search') ? 'text-teal-700' : 'text-slate-600 hover:text-teal-600' }}">Cari Tempat</a>
                </div>
            </div>

            <div id="mobileNavMenu" class="mt-3 hidden flex-col gap-2 md:hidden">
                <a href="{{ route('categories') }}" class="rounded-lg border px-3 py-2 text-center text-xs font-semibold {{ request()->routeIs('categories', 'estimator', 'estimator.questions', 'estimation.calculate') ? 'border-teal-300 bg-teal-50 text-teal-700' : 'border-slate-200 text-slate-600' }}">Anggar Kos</a>
                <a href="{{ route('clinic.finder') }}" class="rounded-lg border px-3 py-2 text-center text-xs font-semibold {{ request()->routeIs('clinic.finder', 'clinic.search') ? 'border-teal-300 bg-teal-50 text-teal-700' : 'border-slate-200 text-slate-600' }}">Cari Tempat</a>
            </div>
        </div>
    </nav>

    <main class="mx-auto min-h-[calc(100vh-190px)] w-full max-w-6xl px-4 py-10">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-6 text-center text-xs text-slate-500">
            Anggaran untuk rujukan sahaja. Harga sebenar mungkin berbeza mengikut klinik dan keadaan individu.
        </div>
    </footer>

    <script>
        (() => {
            const toggle = document.getElementById('mobileNavToggle');
            const menu = document.getElementById('mobileNavMenu');
            const openIcon = document.getElementById('mobileNavIconOpen');
            const closeIcon = document.getElementById('mobileNavIconClose');
            if (!toggle || !menu || !openIcon || !closeIcon) return;

            const setState = (open) => {
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                menu.classList.toggle('hidden', !open);
                openIcon.classList.toggle('hidden', open);
                closeIcon.classList.toggle('hidden', !open);
            };

            toggle.addEventListener('click', () => {
                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                setState(!isOpen);
            });

            document.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof Node)) return;
                if (menu.classList.contains('hidden')) return;
                if (menu.contains(target) || toggle.contains(target)) return;
                setState(false);
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    setState(false);
                }
            });
        })();
    </script>
</body>
</html>
