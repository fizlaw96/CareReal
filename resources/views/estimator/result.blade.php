@extends('layouts.app')

@section('content')
    <div class="rounded-3xl border border-slate-200 bg-white p-5 text-center shadow-sm sm:p-8 md:p-12">
        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-700">Keputusan Anggaran</p>
        <h1 class="mb-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $treatment->name }}</h1>
        <p class="mb-8 text-slate-600">Berdasarkan pilihan yang anda berikan.</p>

        <div class="mb-8 text-3xl font-black text-teal-600 sm:text-4xl md:text-5xl">
            RM {{ number_format($result['min']) }} - RM {{ number_format($result['max']) }}
        </div>

        @if($summary->isNotEmpty())
            <div class="mx-auto mb-8 max-w-2xl rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left">
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-700">Rumusan Pilihan</h2>
                <ul class="space-y-3 text-sm text-slate-700">
                    @foreach($summary as $item)
                        <li class="rounded-xl border border-slate-200 bg-white p-3">
                            <div class="flex flex-col items-start gap-1 text-left sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                <span class="font-medium">{{ $item['question'] }}</span>
                                <span class="text-slate-500">{{ $item['answer'] }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $item['detail'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $searchParams = $clinicSearchParams ?? ['treatment' => $treatment->slug];
            $hasState = !empty($searchParams['negeri'] ?? null);
            $finderUrl = $hasState ? route('clinic.search', $searchParams) : route('clinic.finder', $searchParams);
        @endphp

        <div class="flex flex-col items-stretch justify-center gap-3 sm:flex-row sm:items-center">
            <a href="{{ $finderUrl }}" class="rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700">
                Cari Tempat Rawatan Berdekatan
            </a>
            <a href="{{ route('estimator.questions', ['category' => $category->slug, 'treatment' => $treatment->slug]) }}" class="rounded-xl border border-slate-300 px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-teal-300 hover:text-teal-700">
                Ubah Jawapan
            </a>
        </div>
    </div>
@endsection
