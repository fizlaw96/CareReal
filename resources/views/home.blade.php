@extends('layouts.app')

@section('content')
    @php
        $professionalLabels = [
            'gigi' => [
                'title' => 'Pergigian',
                'badges' => ['#DentalCare'],
            ],
            'mata' => [
                'title' => 'Oftalmologi & Refraktif',
                'badges' => ['#EyeCare'],
            ],
            'rambut' => [
                'title' => 'Trikologi & Restorasi Rambut',
                'badges' => ['#HairRestoration'],
            ],
            'kulit' => [
                'title' => 'Dermatologi & Estetik Kulit',
                'badges' => ['#SkinAesthetic'],
            ],
            'fitness' => [
                'title' => 'Kecergasan Klinikal & Rehabilitasi',
                'badges' => ['#FitnessRehab'],
            ],
            'general' => [
                'title' => 'Pemeriksaan Kesihatan Umum',
                'badges' => ['#HealthScreening'],
            ],
        ];

        $categoryImages = [
            'fitness' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1200&q=80',
            'general' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=1200&q=80',
            'gigi' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=1200&q=80',
            'kulit' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDSgTqED0VvcVFAImBn62C3cRxN6BXoR-zxXAyHHGfsk7yMi4BMRii4yQgoN9n41cIOnwixGbE9GAMIZDooQFih909KmcK5G_qIAtVRp0SPKDLiSeu33wQnHWoTo5pNBqfmjHCNTd-ZxeiPyKe4V08r0OQjhgSeWnxZr9ES91dzP3Qi-8_1TPfEMkaChqsP7okxTyKJU40_45jAqmp3nwYNyltFz8eO8H7THrzkqPUIisENlXKFjLBsI_Ldmqj6BSnW5kw_XEVGiDs',
            'mata' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBI7HG9FW9oWGSUPvtoapjHmJ0aTrJ8C6AzlbeUYssLLIXytn4r-Qjt91qDzTLM43tUZdZR85tUHBl5ggl2QjjCzVGND0qKmfV_5JUmSkd7wFcXibhDrdtZXUcmzNhDakhq8YaPJYZwxTUXC-ZYYUShjjv9UapNBbndx868y8WebNzmy7Yd0E5iuCedL0XXfhwkEvNMzhUujA6ERYV2KzuKvH9kSiO-a5AwfVDzNqI-qk3Xjc26f6KVSXwn9DuKSpqh5ypYECDnrAk',
            'rambut' => 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=1200&q=80',
        ];
    @endphp

    <style>
        .home-shell {
            margin-top: -1rem;
        }

        .home-fluid {
            position: relative;
            left: 50%;
            right: 50%;
            width: 100vw;
            margin-left: -50vw;
            margin-right: -50vw;
            padding-inline: clamp(1rem, 3.8vw, 3rem);
        }

        .home-fluid-container {
            margin-inline: auto;
            width: min(100%, 1520px);
        }

        .hero-aura {
            position: absolute;
            inset: auto;
            width: 760px;
            height: 760px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(20, 184, 166, 0.26) 0%, rgba(20, 184, 166, 0.05) 48%, transparent 70%);
            filter: blur(30px);
            animation: heroGlow 8s ease-in-out infinite;
            pointer-events: none;
            z-index: -1;
        }

        .hero-aura.aura-main {
            top: -160px;
            right: -120px;
        }

        .hero-aura.aura-alt {
            bottom: -220px;
            left: -160px;
            animation-delay: 1.4s;
            width: 600px;
            height: 600px;
        }

        .hero-glass {
            border: 1px solid rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.48);
            backdrop-filter: blur(24px);
            box-shadow: 0 30px 70px -24px rgba(2, 6, 23, 0.35);
        }

        .premium-btn {
            box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.22), 0 16px 30px -12px rgba(13, 148, 136, 0.55);
        }

        .pulse-price {
            animation: pulsePrice 2.2s ease-in-out infinite;
        }

        .ekg-line {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            animation: ekgFlow 3s linear infinite;
        }

        .float-soft {
            animation: floatSoft 6s ease-in-out infinite;
        }

        .float-delay {
            animation: floatSoft 7.5s ease-in-out infinite;
            animation-delay: 1.2s;
        }

        .home-reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 650ms ease, transform 650ms ease;
        }

        .home-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .home-delay-1 { transition-delay: 100ms; }
        .home-delay-2 { transition-delay: 200ms; }
        .home-delay-3 { transition-delay: 300ms; }

        .category-card {
            transition: transform 340ms cubic-bezier(0.4, 0, 0.2, 1), box-shadow 340ms ease, border-color 340ms ease;
        }

        .category-card:hover {
            transform: translateY(-9px);
            box-shadow: 0 18px 40px -16px rgba(15, 23, 42, 0.35);
            border-color: rgba(45, 212, 191, 0.35);
        }

        @keyframes heroGlow {
            0%, 100% { transform: scale(1); opacity: 0.75; }
            50% { transform: scale(1.14); opacity: 1; }
        }

        @keyframes floatSoft {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-16px); }
        }

        @keyframes ekgFlow {
            to { stroke-dashoffset: 0; }
        }

        @keyframes pulsePrice {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>

    <div class="home-shell home-fluid relative overflow-x-hidden">
        <section class="relative pb-14 pt-14 md:pt-20">
            <div class="hero-aura aura-main"></div>
            <div class="hero-aura aura-alt"></div>

            <div class="home-fluid-container">
                <div class="hero-glass relative overflow-hidden rounded-[28px] p-5 sm:p-8 md:rounded-[36px] md:p-12 lg:p-14 home-reveal is-visible">
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <div>
                            <div class="mb-7 inline-flex items-center rounded-full border border-white/70 bg-white/70 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.2em] text-teal-700">
                                Ketelusan Kos Kesihatan
                            </div>

                            <h1 class="font-display mb-5 max-w-2xl text-3xl font-extrabold leading-[1.05] text-slate-900 sm:text-4xl md:mb-6 md:text-6xl">
                                Anggar Kos Rawatan
                                <span class="bg-gradient-to-r from-teal-500 to-cyan-600 bg-clip-text text-transparent">lebih yakin</span>
                            </h1>

                            <p class="mb-10 max-w-2xl text-sm leading-relaxed text-slate-500 md:text-base">
                                Rancang bajet rawatan dengan cepat melalui aliran soalan ringkas dan anggaran kos yang lebih telus.
                            </p>

                            <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                                <a href="{{ route('categories') }}" class="premium-btn inline-flex w-full items-center justify-center rounded-2xl bg-teal-600 px-8 py-4 text-base font-bold text-white transition hover:-translate-y-0.5 hover:bg-teal-700 sm:w-auto">
                                    Mula Anggaran
                                </a>
                                <a href="{{ route('clinic.finder') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white/75 px-8 py-4 text-base font-bold text-slate-700 transition hover:border-teal-300 hover:text-teal-700 sm:w-auto">
                                    Cari Tempat Rawatan
                                </a>
                            </div>
                        </div>

                        <div class="relative flex min-h-[360px] items-center justify-center lg:min-h-[420px]">
                            <div class="absolute inset-0 rounded-full bg-teal-200/30 blur-3xl"></div>

                            <div class="relative w-full max-w-sm overflow-hidden rounded-[30px] border border-white/15 bg-slate-900/95 p-8 shadow-2xl">
                                <svg class="absolute inset-0 h-full w-full opacity-30" viewBox="0 0 420 220" preserveAspectRatio="none">
                                    <path class="ekg-line fill-none stroke-teal-300 stroke-[2.8]" d="M 0 110 L 70 110 L 90 90 L 110 130 L 132 24 L 154 194 L 178 110 L 250 110 L 270 82 L 290 136 L 312 12 L 336 205 L 360 110 L 420 110"></path>
                                </svg>

                                <div class="relative z-10 text-center">
                                    <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-teal-300/80">Estimate Pulse</p>
                                    <div class="mb-3 flex items-end justify-center gap-2">
                                        <span class="pulse-price text-2xl font-bold text-teal-300">RM</span>
                                        <span class="pulse-price text-6xl font-black tracking-tight text-white">1,402</span>
                                    </div>
                                    <p class="mb-5 text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">Live Price Intelligence</p>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-700">
                                        <div class="h-full w-[72%] bg-gradient-to-r from-teal-300 to-cyan-400"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="float-soft absolute -right-3 top-2 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/60 bg-white/70 text-2xl shadow-md">RM</div>
                            <div class="float-delay absolute -left-3 bottom-6 flex h-14 w-14 items-center justify-center rounded-full border border-white/60 bg-white/70 text-2xl">+</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pb-16 pt-8">
            <div class="home-fluid-container">
                <div class="grid gap-5 md:grid-cols-3">
                    <article class="home-reveal home-delay-1 rounded-[26px] border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-2xl">1</div>
                        <h3 class="mb-3 text-xl font-extrabold text-slate-900">Pilih Bidang Rawatan</h3>
                        <p class="text-sm leading-relaxed text-slate-600">Mulakan dengan bidang seperti Pergigian, Oftalmologi, Trikologi, Dermatologi atau Pemeriksaan Kesihatan Umum.</p>
                    </article>

                    <article class="home-reveal home-delay-2 rounded-[26px] border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-2xl">2</div>
                        <h3 class="mb-3 text-xl font-extrabold text-slate-900">Jawab Profil Rawatan</h3>
                        <p class="text-sm leading-relaxed text-slate-600">Lengkapkan maklumat klinikal ringkas seperti tahap kes, lokasi, sesi, atau pilihan tambahan untuk ketepatan anggaran.</p>
                    </article>

                    <article class="home-reveal home-delay-3 rounded-[26px] border border-slate-100 bg-white p-7 shadow-sm">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-2xl">3</div>
                        <h3 class="mb-3 text-xl font-extrabold text-slate-900">Terima Julat Anggaran</h3>
                        <p class="text-sm leading-relaxed text-slate-600">Semak julat kos dan teruskan ke carian pusat rawatan berdekatan untuk langkah seterusnya.</p>
                    </article>
                </div>
            </div>
        </section>

        @if($categories->isNotEmpty())
            <section class="pb-10 pt-4">
                <div class="home-fluid-container">
                    <div class="mb-9 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-end">
                        <h2 class="font-display text-2xl font-extrabold text-slate-900 sm:text-3xl">Bidang Rawatan Tersedia</h2>
                        <a href="{{ route('categories') }}" class="text-sm font-bold text-teal-700 hover:text-teal-600">Lihat semua →</a>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 md:grid-cols-3 lg:grid-cols-6">
                        @foreach($categories as $category)
                            @php
                                $label = $professionalLabels[$category->slug] ?? null;
                                $title = is_array($label) ? ($label['title'] ?? $category->name) : $category->name;
                                $badges = is_array($label) ? ($label['badges'] ?? []) : [];
                            @endphp
                            <a href="{{ route('estimator', $category->slug) }}" class="category-card overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                <div class="relative h-28 overflow-hidden bg-slate-100">
                                    <img src="{{ $categoryImages[$category->slug] ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 to-transparent"></div>
                                </div>
                                <div class="p-4 text-center">
                                    <p class="text-xs font-bold leading-snug text-slate-700">{{ $title }}</p>
                                    @if(!empty($badges))
                                        <div class="mt-2 flex flex-wrap items-center justify-center gap-1.5">
                                            @foreach($badges as $badge)
                                                <span class="rounded-full border border-teal-200 bg-teal-50 px-2 py-0.5 text-[10px] font-semibold text-teal-700">{{ $badge }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

    <script>
        (() => {
            const revealEls = Array.from(document.querySelectorAll('.home-reveal'));
            if (!revealEls.length) return;

            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.2 });

            revealEls.forEach((el) => io.observe(el));
        })();
    </script>
@endsection
