@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Cari Pusat Rawatan</h1>
        <p class="mt-2 text-slate-600">
            Pilih rawatan dan lokasi untuk cari tempat yang sesuai. Jenis tempat semasa:
            <span class="font-semibold text-teal-700">{{ $placeLabel ?? 'Pusat Rawatan' }}</span>
        </p>
    </div>

    <form method="GET" action="{{ route('clinic.search') }}" class="mb-8 space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
        @if(!isset($selectedTreatment) || $selectedTreatment === null)
            <div>
                <label for="treatment" class="mb-2 block text-sm font-semibold text-slate-700">Jenis Rawatan</label>
                <select id="treatment" name="treatment" class="w-full rounded-lg border border-slate-300 p-3 text-sm">
                    <option value="">Pilih rawatan</option>
                    @foreach($treatments as $treatment)
                        <option value="{{ $treatment->slug }}" @selected(request('treatment') === $treatment->slug)>
                            {{ $treatment->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm">
                Rawatan dipilih: <span class="font-bold text-teal-700">{{ $selectedTreatment->name }}</span>
                <span class="mx-2 text-slate-300">|</span>
                Tempat sasaran: <span class="font-bold text-teal-700">{{ $placeLabel ?? 'Pusat Rawatan' }}</span>
                <input type="hidden" name="treatment" value="{{ $selectedTreatment->slug }}">
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="negeri" class="mb-2 block text-sm font-semibold text-slate-700">Negeri</label>
                <select id="negeri" name="negeri" class="w-full rounded-lg border border-slate-300 p-3 text-sm">
                    <option value="">Pilih negeri</option>
                    @foreach(['Selangor', 'Johor', 'Pulau Pinang', 'Kuala Lumpur', 'Negeri Sembilan'] as $negeri)
                        <option value="{{ $negeri }}" @selected(($filters['negeri'] ?? '') === $negeri)>{{ $negeri }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="daerah" class="mb-2 block text-sm font-semibold text-slate-700">Daerah</label>
                <input id="daerah" type="text" name="daerah" value="{{ $filters['daerah'] ?? '' }}" placeholder="Contoh: Shah Alam" class="w-full rounded-lg border border-slate-300 p-3 text-sm">
            </div>
        </div>

        <button type="submit" class="rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700">
            Cari Tempat
        </button>
    </form>

    @if(!empty($searchQuery))
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4">
            <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Query + Filter Digunakan</p>
            <div class="flex flex-wrap gap-2">
                @foreach(($queryBadges ?? collect()) as $badge)
                    <span class="inline-flex items-center rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                        {{ $badge }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    @if(!empty($serpError))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
            Carian SerpApi: {{ $serpError }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="grid md:grid-cols-[360px_minmax(0,1fr)] lg:grid-cols-[390px_minmax(0,1fr)]">
            <div class="md:border-r md:border-slate-200">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Senarai Tempat</h2>
                    <p class="mt-1 text-xs text-slate-500">Klik satu tempat untuk fokuskan peta.</p>
                </div>

                <div class="max-h-[620px] overflow-y-auto p-4 space-y-3" id="placesList">
                    @forelse($places as $place)
                        @php
                            $rating = is_numeric($place['rating'] ?? null) ? (float) $place['rating'] : null;
                            $ratingBadgeClass = match (true) {
                                $rating !== null && $rating >= 4.5 => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                $rating !== null && $rating >= 4.0 => 'bg-teal-100 text-teal-700 border-teal-200',
                                $rating !== null && $rating >= 3.5 => 'bg-amber-100 text-amber-700 border-amber-200',
                                $rating !== null => 'bg-rose-100 text-rose-700 border-rose-200',
                                default => 'bg-slate-100 text-slate-500 border-slate-200',
                            };
                        @endphp
                        <article
                            data-place-item
                            data-map-url="{{ $place['embed_url'] ?? $mapUrl }}"
                            data-place-name="{{ $place['display_name'] ?? $place['name'] }}"
                            class="cursor-pointer rounded-xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-teal-300 hover:bg-white"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-bold text-slate-900" title="{{ $place['name'] }}">{{ $place['display_name'] ?? $place['name'] }}</p>
                                <span class="shrink-0 rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $ratingBadgeClass }}">
                                    @if($rating !== null)
                                        {{ number_format($rating, 1) }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            @if(!empty($place['type']))
                                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $place['type'] }}</p>
                            @endif
                            <div class="mt-2 space-y-1 text-xs text-slate-600">
                                @if(!empty($place['reviews']))
                                    <p>{{ $place['reviews'] }} ulasan</p>
                                @endif
                                @if(!empty($place['address']))
                                    <p>{{ $place['address'] }}</p>
                                @endif
                            </div>

                            <div class="mt-4 flex gap-2">
                                @if(!empty($place['maps_url']))
                                    <a href="{{ $place['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex flex-1 items-center justify-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700" onclick="event.stopPropagation()">
                                        Buka Google Maps
                                    </a>
                                @endif

                                @if(!empty($place['whatsapp_url']))
                                    <a href="{{ $place['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex flex-1 items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100" onclick="event.stopPropagation()">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="mr-1.5 h-3.5 w-3.5">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 8.016 0C3.66 0 .106 3.547.106 7.908c0 1.394.365 2.756 1.058 3.958L0 16l4.236-1.11a7.88 7.88 0 0 0 3.775.965h.004c4.355 0 7.909-3.547 7.91-7.908a7.86 7.86 0 0 0-2.324-5.621Zm-5.585 12.19h-.003a6.57 6.57 0 0 1-3.35-.92l-.24-.143-2.515.66.672-2.45-.156-.251a6.56 6.56 0 0 1-1.007-3.505c.002-3.63 2.96-6.585 6.595-6.585a6.55 6.55 0 0 1 4.666 1.932 6.56 6.56 0 0 1 1.93 4.67c-.002 3.63-2.96 6.59-6.592 6.59Zm3.615-4.93c-.197-.099-1.17-.578-1.352-.644-.181-.066-.313-.099-.446.1-.132.197-.511.644-.627.775-.115.132-.231.148-.428.05-.197-.1-.833-.307-1.588-.98-.587-.524-.983-1.17-1.098-1.368-.115-.198-.012-.304.087-.403.089-.088.197-.23.296-.346.099-.116.132-.198.198-.33.066-.132.033-.248-.017-.347-.05-.099-.446-1.074-.61-1.47-.16-.387-.323-.335-.446-.341a7.23 7.23 0 0 0-.38-.007.73.73 0 0 0-.528.248c-.181.198-.693.677-.693 1.652 0 .975.709 1.916.808 2.048.099.132 1.397 2.134 3.387 2.992.473.204.842.326 1.13.417.474.151.906.13 1.248.079.381-.057 1.17-.478 1.335-.94.165-.462.165-.858.115-.94-.05-.083-.181-.132-.38-.231Z"/>
                                        </svg>
                                        Contact
                                    </a>
                                @else
                                    <span class="inline-flex flex-1 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="mr-1.5 h-3.5 w-3.5">
                                            <path d="M13.601 2.326A7.854 7.854 0 0 0 8.016 0C3.66 0 .106 3.547.106 7.908c0 1.394.365 2.756 1.058 3.958L0 16l4.236-1.11a7.88 7.88 0 0 0 3.775.965h.004c4.355 0 7.909-3.547 7.91-7.908a7.86 7.86 0 0 0-2.324-5.621Zm-5.585 12.19h-.003a6.57 6.57 0 0 1-3.35-.92l-.24-.143-2.515.66.672-2.45-.156-.251a6.56 6.56 0 0 1-1.007-3.505c.002-3.63 2.96-6.585 6.595-6.585a6.55 6.55 0 0 1 4.666 1.932 6.56 6.56 0 0 1 1.93 4.67c-.002 3.63-2.96 6.59-6.592 6.59Z"/>
                                        </svg>
                                        Contact
                                    </span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                            Tiada tempat dipaparkan lagi. Jalankan carian untuk lihat senarai.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="border-t border-slate-200 md:border-t-0">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Peta Lokasi</h2>
                    <p class="mt-1 text-xs text-slate-500" id="activePlaceLabel">
                        @if($places->isNotEmpty())
                            Fokus: {{ $places->first()['name'] ?? 'Tempat dipilih' }}
                        @else
                            Peta akan dipaparkan selepas carian.
                        @endif
                    </p>
                </div>

                @if(isset($mapUrl) && $mapUrl)
                    <iframe
                        id="placesMap"
                        src="{{ $mapUrl }}"
                        width="100%"
                        height="640"
                        loading="lazy"
                    ></iframe>
                @else
                    <div class="flex h-[640px] items-center justify-center bg-slate-50 px-8 text-center text-sm text-slate-500">
                        Peta akan dipaparkan selepas anda jalankan carian lokasi.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        (() => {
            const placeItems = Array.from(document.querySelectorAll('[data-place-item]'));
            const map = document.getElementById('placesMap');
            const activePlaceLabel = document.getElementById('activePlaceLabel');

            if (!placeItems.length || !map) return;

            const setActiveCard = (activeItem) => {
                placeItems.forEach((item) => {
                    item.classList.remove('border-teal-500', 'bg-teal-50', 'shadow-sm');
                    item.classList.add('border-slate-200', 'bg-slate-50');
                });

                activeItem.classList.remove('border-slate-200', 'bg-slate-50');
                activeItem.classList.add('border-teal-500', 'bg-teal-50', 'shadow-sm');
            };

            const activate = (item) => {
                const mapUrl = item.dataset.mapUrl;
                const placeName = item.dataset.placeName;

                if (mapUrl) {
                    map.src = mapUrl;
                }

                if (activePlaceLabel && placeName) {
                    activePlaceLabel.textContent = `Fokus: ${placeName}`;
                }

                setActiveCard(item);
            };

            placeItems.forEach((item) => {
                item.addEventListener('click', () => activate(item));
            });

            activate(placeItems[0]);
        })();
    </script>
@endsection
