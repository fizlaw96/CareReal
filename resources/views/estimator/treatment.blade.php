@extends('layouts.app')

@section('content')
    @php
        $legacyTreatmentImages = [
            'metal-braces' => 'https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=1200&q=80',
            'ceramic-braces' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=1200&q=80',
            'self-ligating-braces' => 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=1200&q=80',
            'clear-aligner' => 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?auto=format&fit=crop&w=1200&q=80',
            'whitening' => 'https://images.unsplash.com/photo-1600170311833-c2cf5280ce49?auto=format&fit=crop&w=1200&q=80',
            'prk' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1200&q=80',
            'lasik' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
            'smile' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80',
            'ortho-k' => 'https://images.unsplash.com/photo-1551884170-09fb70a3a2ed?auto=format&fit=crop&w=1200&q=80',
            'prp-rambut' => 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=1200&q=80',
            'hair-transplant' => 'https://images.unsplash.com/photo-1620331311520-246422fd82f9?auto=format&fit=crop&w=1200&q=80',
            'scalp-treatment' => 'https://images.unsplash.com/photo-1562322140-8baeececf3df?auto=format&fit=crop&w=1200&q=80',
            'facial-klinikal' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=1200&q=80',
            'laser-jerawat' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1200&q=80',
            'laser-parut' => 'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?auto=format&fit=crop&w=1200&q=80',
            'chemical-peel' => 'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?auto=format&fit=crop&w=1200&q=80',
            'online-coaching' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80',
            'personal-trainer' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1200&q=80',
            'pelan-pemakanan' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80',
            'basic-checkup' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=1200&q=80',
            'executive-checkup' => 'https://images.unsplash.com/photo-1666214280557-f1b5022eb634?auto=format&fit=crop&w=1200&q=80',
            'full-screening' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80',
        ];
    @endphp

    <style>
        .treatment-card {
            transition: transform 420ms cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 420ms ease;
        }

        .treatment-card:hover {
            transform: translateY(-8px) scale(1.012);
            box-shadow: 0 30px 60px -12px rgba(2, 6, 23, 0.28);
        }

        .treatment-card:hover .treatment-image {
            transform: scale(1.08);
        }
    </style>

    <section class="mx-auto max-w-7xl">
        <div class="mb-8 text-center md:mb-10">
            <p class="text-sm font-semibold uppercase tracking-widest text-teal-700">Kategori {{ $category->name }}</p>
            <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl md:text-4xl">Pilih Jenis Rawatan Dahulu</h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-600">
                Setiap jenis rawatan ada fungsi berbeza. Pilih satu rawatan untuk terus ke soalan anggaran.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-7 xl:grid-cols-3">
            @foreach($treatments as $item)
                @php
                    $imageSrc = $treatmentImages[$item->slug]
                        ?? $legacyTreatmentImages[$item->slug]
                        ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80';
                @endphp
                <a
                    href="{{ route('estimator.questions', ['category' => $category->slug, 'treatment' => $item->slug]) }}"
                    class="treatment-card group relative flex h-[340px] flex-col overflow-hidden rounded-3xl border border-white/60 bg-white shadow-sm sm:h-[390px]"
                >
                    <div class="absolute inset-0">
                        <img
                            src="{{ $imageSrc }}"
                            alt="{{ $item->name }}"
                            class="treatment-image h-full w-full object-cover transition duration-700"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                    </div>

                    <div class="relative z-10 mt-auto p-5 sm:p-7">
                        <h2 class="text-xl font-bold text-white sm:text-2xl">{{ $item->name }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-200">
                            {{ $treatmentGuides[$item->slug] ?? 'Penerangan rawatan belum ditetapkan.' }}
                        </p>

                        <span class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-teal-400 px-4 py-3 text-sm font-bold text-slate-900 transition group-hover:brightness-110">
                            Pilih Rawatan Ini
                            <span aria-hidden="true">→</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
