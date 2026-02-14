@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Cari Klinik</h1>
        <p class="mt-2 text-slate-600">Pilih rawatan dan lokasi untuk papar klinik yang berkaitan di peta.</p>
    </div>

    <form method="GET" action="{{ route('clinic.search') }}" class="mb-8 space-y-4 rounded-2xl border border-slate-200 bg-white p-6">
        @if(!isset($selectedTreatment) || $selectedTreatment === null)
            <div>
                <label for="treatment" class="mb-2 block text-sm font-semibold text-slate-700">Rawatan</label>
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
            Cari
        </button>
    </form>

    @if(isset($mapUrl) && $mapUrl)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <iframe src="{{ $mapUrl }}" width="100%" height="450" loading="lazy"></iframe>
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
            Peta akan dipaparkan selepas anda jalankan carian lokasi.
        </div>
    @endif
@endsection
