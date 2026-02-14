<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'CareReal.my' }}</title>
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
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <a href="{{ route('home') }}" class="inline-flex items-center">
                <img src="{{ asset('assets/images/carereal_logo.png') }}" alt="CareReal.my" class="h-11 w-auto">
                <span class="sr-only">CareReal.my</span>
            </a>

            <div class="hidden items-center gap-6 text-sm font-semibold md:flex">
                <a href="{{ route('categories') }}" class="{{ request()->routeIs('categories', 'estimator', 'estimator.questions', 'estimation.calculate') ? 'text-teal-700' : 'text-slate-600 hover:text-teal-600' }}">Anggar Kos</a>
                <a href="{{ route('clinic.finder') }}" class="{{ request()->routeIs('clinic.finder', 'clinic.search') ? 'text-teal-700' : 'text-slate-600 hover:text-teal-600' }}">Cari Tempat</a>
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
</body>
</html>
