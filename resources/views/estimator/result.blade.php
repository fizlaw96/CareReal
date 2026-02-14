@extends('layouts.app')

@section('content')
    <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm md:p-12">
        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-700">Keputusan Anggaran</p>
        <h1 class="mb-2 text-3xl font-extrabold text-slate-900">{{ $treatment->name }}</h1>
        <p class="mb-8 text-slate-600">Berdasarkan pilihan yang anda berikan.</p>

        <div class="mb-8 text-4xl font-black text-teal-600 md:text-5xl">
            RM {{ number_format($result['min']) }} - RM {{ number_format($result['max']) }}
        </div>

        @if($summary->isNotEmpty())
            <div class="mx-auto mb-8 max-w-2xl rounded-2xl border border-slate-200 bg-slate-50 p-5 text-left">
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-700">Rumusan Pilihan</h2>
                <ul class="space-y-2 text-sm text-slate-700">
                    @foreach($summary as $item)
                        <li class="flex items-start justify-between gap-3 border-b border-slate-200 pb-2 last:border-none last:pb-0">
                            <span class="font-medium">{{ $item['question'] }}</span>
                            <span class="text-slate-500">{{ $item['answer'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('clinic.finder', ['treatment' => $treatment->slug]) }}" class="rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700">
                Cari Klinik Berdekatan
            </a>
            <a href="{{ route('estimator.questions', ['category' => $category->slug, 'treatment' => $treatment->slug]) }}" class="rounded-xl border border-slate-300 px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-teal-300 hover:text-teal-700">
                Ubah Jawapan
            </a>
        </div>
    </div>
@endsection
