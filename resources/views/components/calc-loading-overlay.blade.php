@props([
    'id' => 'calcLoadingScreen',
    'badge' => 'CareReal Estimate Engine',
    'title' => 'Sedang kira anggaran rawatan anda',
    'subtitle' => 'Memproses faktor kos',
    'footer' => 'Membina keputusan akhir...',
    'duration' => 4200,
])

@php
    $durationInt = max((int) $duration, 600);
    $progressDuration = max($durationInt - 300, 300);
@endphp

@once
    <style>
        .crl-loading {
            backdrop-filter: blur(6px);
        }

        .crl-road {
            position: relative;
            overflow: hidden;
            border-radius: 9999px;
            border: 1px solid rgba(45, 212, 191, 0.35);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.95) 100%);
            height: 110px;
        }

        .crl-road-lane {
            position: absolute;
            left: -120%;
            top: 50%;
            width: 240%;
            border-top: 4px dashed rgba(255, 255, 255, 0.4);
            transform: translateY(-50%);
            animation: crlLaneSlide 1.1s linear infinite;
        }

        .crl-ambulance {
            position: absolute;
            top: 50%;
            font-size: 2.7rem;
            line-height: 1;
            transform: translate(-20%, -62%);
            filter: drop-shadow(0 8px 14px rgba(0, 0, 0, 0.35));
            animation: crlAmbulanceDrive 3.6s ease-in-out infinite;
        }

        .crl-progress-fill {
            width: 0;
        }

        .crl-loading.is-running .crl-progress-fill {
            animation: crlGrow var(--crl-progress-duration, 3900ms) ease-out forwards;
        }

        .crl-dots::after {
            content: '';
            display: inline-block;
            width: 1.2em;
            text-align: left;
            animation: crlDotTyping 1.2s steps(3, end) infinite;
        }

        @keyframes crlLaneSlide {
            from { transform: translate(-20%, -50%); }
            to { transform: translate(20%, -50%); }
        }

        @keyframes crlAmbulanceDrive {
            0% { left: 108%; }
            50% { left: 48%; }
            100% { left: -12%; }
        }

        @keyframes crlGrow {
            from { width: 0; }
            to { width: 100%; }
        }

        @keyframes crlDotTyping {
            0% { content: ''; }
            33% { content: '.'; }
            66% { content: '..'; }
            100% { content: '...'; }
        }
    </style>

    <script>
        window.CareRealLoadingOverlay = window.CareRealLoadingOverlay || {
            show(id) {
                const el = document.getElementById(id);
                if (!el) return;

                const progress = el.querySelector('[data-loading-progress]');
                if (progress) {
                    progress.style.width = '0';
                }

                el.classList.remove('hidden');
                el.classList.add('flex');
                el.classList.remove('is-running');
                void el.offsetWidth;
                el.classList.add('is-running');
                document.body.classList.add('overflow-hidden');
            },
            hide(id) {
                const el = document.getElementById(id);
                if (!el) return;

                el.classList.remove('flex', 'is-running');
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            },
            runFor(id, durationMs, callback) {
                this.show(id);
                window.setTimeout(() => {
                    if (typeof callback === 'function') {
                        callback();
                    }
                }, durationMs);
            },
        };
    </script>
@endonce

<div
    id="{{ $id }}"
    data-loading-overlay
    data-duration="{{ $durationInt }}"
    style="--crl-progress-duration: {{ $progressDuration }}ms"
    class="crl-loading fixed inset-0 z-[999] hidden items-center justify-center bg-slate-950/85 px-6"
>
    <div class="w-full max-w-2xl rounded-3xl border border-teal-500/30 bg-slate-900/95 p-6 text-center shadow-2xl shadow-teal-900/30 md:p-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-300">{{ $badge }}</p>
        <h2 class="mt-2 text-2xl font-extrabold text-white md:text-3xl">{{ $title }}</h2>
        <p class="crl-dots mt-2 text-sm text-slate-300">{{ $subtitle }}</p>

        <div class="crl-road mt-8">
            <div class="crl-road-lane"></div>
            <div class="crl-ambulance" aria-hidden="true">🚑</div>
        </div>

        <div class="mt-7 h-3 overflow-hidden rounded-full bg-slate-700/70">
            <div data-loading-progress class="crl-progress-fill h-full rounded-full bg-gradient-to-r from-teal-400 to-cyan-400"></div>
        </div>

        <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $footer }}</p>
    </div>
</div>
