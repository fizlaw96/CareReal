@extends('layouts.app')

@section('content')
    @php
        $professionalLabels = [
            'gigi' => [
                'title' => 'Pergigian',
                'badge' => '#DentalCare',
                'description' => 'Anggaran rawatan umum, braces, aligner dan pemutihan gigi profesional.',
            ],
            'mata' => [
                'title' => 'Oftalmologi',
                'badge' => '#EyeCare',
                'description' => 'Bandingkan kos PRK, LASIK, SMILE dan prosedur pembedahan mata terkini.',
            ],
            'rambut' => [
                'title' => 'Trikologi & Restorasi Rambut',
                'badge' => '#HairRestoration',
                'description' => 'Ketelusan harga rawatan keguguran rambut dan teknologi transplant moden.',
            ],
            'kulit' => [
                'title' => 'Estetik & Kulit',
                'badge' => '#SkinAesthetic',
                'description' => 'Anggaran dermatologi klinikal, rawatan jerawat dan estetik wajah premium.',
            ],
            'fitness' => [
                'title' => 'Kecergasan Klinikal',
                'badge' => '#FitnessRehab',
                'description' => 'Program pengurusan berat badan dan sesi pemulihan fizikal berstruktur.',
            ],
            'general' => [
                'title' => 'Pemeriksaan Kesihatan',
                'badge' => '#HealthScreening',
                'description' => 'Konsultasi pakar, saringan menyeluruh dan pemeriksaan kesihatan umum.',
            ],
        ];

        $categoryImages = [
            'gigi' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=1200&q=80',
            'general' => 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=1200&q=80',
            'mata' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBI7HG9FW9oWGSUPvtoapjHmJ0aTrJ8C6AzlbeUYssLLIXytn4r-Qjt91qDzTLM43tUZdZR85tUHBl5ggl2QjjCzVGND0qKmfV_5JUmSkd7wFcXibhDrdtZXUcmzNhDakhq8YaPJYZwxTUXC-ZYYUShjjv9UapNBbndx868y8WebNzmy7Yd0E5iuCedL0XXfhwkEvNMzhUujA6ERYV2KzuKvH9kSiO-a5AwfVDzNqI-qk3Xjc26f6KVSXwn9DuKSpqh5ypYECDnrAk',
            'kulit' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDSgTqED0VvcVFAImBn62C3cRxN6BXoR-zxXAyHHGfsk7yMi4BMRii4yQgoN9n41cIOnwixGbE9GAMIZDooQFih909KmcK5G_qIAtVRp0SPKDLiSeu33wQnHWoTo5pNBqfmjHCNTd-ZxeiPyKe4V08r0OQjhgSeWnxZr9ES91dzP3Qi-8_1TPfEMkaChqsP7okxTyKJU40_45jAqmp3nwYNyltFz8eO8H7THrzkqPUIisENlXKFjLBsI_Ldmqj6BSnW5kw_XEVGiDs',
            'fitness' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1200&q=80',
            'rambut' => 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?auto=format&fit=crop&w=1200&q=80',
        ];
    @endphp

    <style>
        .category-card-pro {
            transition: transform 420ms cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 420ms ease;
        }

        .category-card-pro:hover {
            transform: translateY(-8px) scale(1.015);
            box-shadow: 0 30px 60px -12px rgba(2, 6, 23, 0.3);
        }

        .category-card-pro:hover .category-image-pro {
            transform: scale(1.08);
        }
    </style>

    <div class="mb-8 md:mb-10">
        <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Pilih Bidang Rawatan</h1>
        <p class="mt-2 text-sm text-slate-600 sm:text-base">Langkah pertama untuk anggaran: pilih bidang rawatan yang paling sesuai dengan keperluan anda.</p>
    </div>

    <div class="grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
        @forelse($categories as $category)
            @php
                $label = $professionalLabels[$category->slug] ?? null;
                $title = is_array($label) ? ($label['title'] ?? $category->name) : $category->name;
                $badge = is_array($label) ? ($label['badge'] ?? null) : null;
                $description = is_array($label) ? ($label['description'] ?? ($category->description ?? 'Anggaran kos rawatan dalam kategori ini.')) : ($category->description ?? 'Anggaran kos rawatan dalam kategori ini.');
            @endphp

            <a href="{{ route('estimator', $category->slug) }}" class="category-card-pro group relative flex h-[340px] flex-col overflow-hidden rounded-3xl border border-white/60 bg-white shadow-sm sm:h-[400px]">
                <div class="absolute inset-0">
                    <img src="{{ $categoryImages[$category->slug] ?? 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $title }}" class="category-image-pro h-full w-full object-cover transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                </div>

                <div class="relative z-10 mt-auto p-5 sm:p-7">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="text-xl">{{ $category->icon }}</span>
                        @if($badge)
                            <span class="rounded-full border border-teal-300/50 bg-teal-300/20 px-2.5 py-1 text-[11px] font-semibold text-teal-100">{{ $badge }}</span>
                        @endif
                    </div>

                    <h2 class="mb-2 text-xl font-bold text-white sm:text-2xl">{{ $title }}</h2>
                    <p class="mb-5 line-clamp-3 text-sm leading-relaxed text-slate-200 sm:mb-6">{{ $description }}</p>

                    <span class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-teal-400 px-4 py-3 text-sm font-bold text-slate-900 transition group-hover:brightness-110">
                        Terus ke jenis rawatan
                        <span aria-hidden="true">→</span>
                    </span>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-700">
                Tiada kategori tersedia lagi. Jalankan seeder untuk data permulaan.
            </div>
        @endforelse
    </div>
@endsection
