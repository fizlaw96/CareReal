@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900 sm:text-3xl">Cari Pusat Rawatan</h1>
        <p class="mt-2 text-slate-600">
            Pilih rawatan dan lokasi untuk cari tempat yang sesuai. Jenis tempat semasa:
            <span class="font-semibold text-teal-700">{{ $placeLabel ?? 'Pusat Rawatan' }}</span>
        </p>

        @if(isset($selectedTreatment) && $selectedTreatment && $selectedTreatment->base_min && $selectedTreatment->base_max)
            <div class="mt-4 inline-flex flex-wrap items-center gap-2 rounded-xl border border-teal-200 bg-teal-50 px-4 py-2 text-sm text-teal-800">
                <span class="font-semibold">Julat harga {{ $selectedTreatment->name }}:</span>
                <span class="font-extrabold">RM {{ number_format($selectedTreatment->base_min) }} - RM {{ number_format($selectedTreatment->base_max) }}</span>
            </div>
        @endif
    </div>

    <form id="clinicSearchForm" method="GET" action="{{ route('clinic.search') }}" class="mb-8 space-y-4 rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-semibold">Sila semak maklumat carian.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!isset($selectedTreatment) || $selectedTreatment === null)
            <div>
                <label for="treatment" class="mb-2 block text-sm font-semibold text-slate-700">Jenis Rawatan</label>
                <select id="treatment" name="treatment" required class="w-full rounded-lg border p-3 text-sm {{ $errors->has('treatment') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }}">
                    <option value="">Pilih rawatan</option>
                    @foreach($treatments as $treatment)
                        <option value="{{ $treatment->slug }}" @selected(request('treatment') === $treatment->slug)>
                            {{ $treatment->name }}
                        </option>
                    @endforeach
                </select>
                @error('treatment')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        @else
            <div class="rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm">
                Rawatan dipilih: <span class="font-bold text-teal-700">{{ $selectedTreatment->name }}</span>
                <span class="mx-2 text-slate-300">|</span>
                Tempat sasaran: <span class="font-bold text-teal-700">{{ $placeLabel ?? 'Pusat Rawatan' }}</span>
                <input type="hidden" name="treatment" value="{{ $selectedTreatment->slug }}">
            </div>
        @endif

        @if(!empty($filters['location_hint'] ?? null))
            <input type="hidden" name="location_hint" value="{{ $filters['location_hint'] }}">
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="negeri" class="mb-2 block text-sm font-semibold text-slate-700">Negeri</label>
                <select id="negeri" name="negeri" required class="w-full rounded-lg border p-3 text-sm {{ $errors->has('negeri') ? 'border-rose-400 bg-rose-50' : 'border-slate-300' }}">
                    <option value="">Pilih negeri</option>
                    @foreach(($states ?? []) as $negeri)
                        <option value="{{ $negeri }}" @selected(($filters['negeri'] ?? '') === $negeri)>{{ $negeri }}</option>
                    @endforeach
                </select>
                @error('negeri')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="daerah" class="mb-2 block text-sm font-semibold text-slate-700">Daerah</label>
                <select id="daerah" name="daerah" class="w-full rounded-lg border border-slate-300 p-3 text-sm" data-selected-daerah="{{ $filters['daerah'] ?? '' }}">
                    <option value="">Pilih daerah</option>
                    @foreach(($districtsForState ?? []) as $daerah)
                        <option value="{{ $daerah }}" @selected(($filters['daerah'] ?? '') === $daerah)>{{ $daerah }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-end">
            <button id="clinicSearchSubmit" type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.63 3.631a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 9 3.5Zm-4 5.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                </svg>
                Cari Tempat
            </button>
        </div>

        @if(!empty($searchQuery))
            <div class="border-t border-slate-200 pt-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Filter</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(($queryBadges ?? collect()) as $badge)
                        <span class="inline-flex items-center rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                            {{ $badge }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </form>

    <x-calc-loading-overlay
        id="clinicFinderLoadingScreen"
        :duration="4200"
        badge="CareReal Place Finder"
        title="Sedang cari tempat rawatan"
        subtitle="Menyusun hasil mengikut carian anda"
        footer="Memuatkan peta dan senarai lokasi..."
    />

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

                            <div class="mt-4 flex flex-col gap-2 sm:flex-row">
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
                        class="h-[420px] md:h-[640px]"
                        loading="lazy"
                    ></iframe>
                @else
                    <div class="flex h-[420px] items-center justify-center bg-slate-50 px-8 text-center text-sm text-slate-500 md:h-[640px]">
                        Peta akan dipaparkan selepas anda jalankan carian lokasi.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        (() => {
            const form = document.getElementById('clinicSearchForm');
            if (!form) return;

            const submitButton = document.getElementById('clinicSearchSubmit');
            const loadingScreen = document.getElementById('clinicFinderLoadingScreen');
            const loadingDuration = Number(loadingScreen?.dataset.duration || 4200);
            let isSubmitting = false;

            form.addEventListener('submit', (event) => {
                if (isSubmitting) return;

                event.preventDefault();
                isSubmitting = true;

                if (submitButton) {
                    submitButton.disabled = true;
                }

                if (window.CareRealLoadingOverlay && loadingScreen) {
                    window.CareRealLoadingOverlay.runFor('clinicFinderLoadingScreen', loadingDuration, () => {
                        form.submit();
                    });
                    return;
                }

                window.setTimeout(() => {
                    form.submit();
                }, loadingDuration);
            });
        })();

        (() => {
            const negeriSelect = document.getElementById('negeri');
            const daerahSelect = document.getElementById('daerah');

            if (!negeriSelect || !daerahSelect) return;

            const baseOptionLabel = 'Pilih daerah';

            const renderDistrictOptions = (districts, selectedValue = '') => {
                daerahSelect.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = baseOptionLabel;
                daerahSelect.appendChild(placeholderOption);

                districts.forEach((district) => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district;
                    option.selected = district === selectedValue;
                    daerahSelect.appendChild(option);
                });
            };

            const loadDistricts = async (state, selectedValue = '') => {
                if (!state) {
                    renderDistrictOptions([], '');
                    return;
                }

                try {
                    const endpoint = `{{ route('clinic.districts') }}?negeri=${encodeURIComponent(state)}`;
                    const response = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });

                    if (!response.ok) {
                        throw new Error('Gagal dapatkan senarai daerah.');
                    }

                    const payload = await response.json();
                    const districts = Array.isArray(payload.districts) ? payload.districts : [];
                    renderDistrictOptions(districts, selectedValue);
                } catch (error) {
                    renderDistrictOptions([], '');
                }
            };

            negeriSelect.addEventListener('change', async (event) => {
                await loadDistricts(event.target.value, '');
            });

            const initialState = negeriSelect.value || '';
            const initialDistrict = daerahSelect.dataset.selectedDaerah || '';

            if (initialState) {
                loadDistricts(initialState, initialDistrict);
            }
        })();

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
