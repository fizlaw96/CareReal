@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-teal-100 bg-white p-10 shadow-sm md:p-16">
        <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-teal-100 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-12 h-52 w-52 rounded-full bg-cyan-100 blur-3xl"></div>

        <div class="relative text-center">
            <p class="mb-4 inline-flex items-center rounded-full border border-teal-200 bg-teal-50 px-4 py-1 text-xs font-bold uppercase tracking-wider text-teal-700">
                Ketelusan Kos Kesihatan
            </p>
            <h1 class="mx-auto mb-5 max-w-3xl text-4xl font-extrabold leading-tight text-slate-900 md:text-6xl">
                Anggar Kos Rawatan Anda
                <span class="bg-gradient-to-r from-teal-600 to-cyan-600 bg-clip-text text-transparent">dengan lebih yakin</span>
            </h1>
            <p class="mx-auto mb-10 max-w-2xl text-base text-slate-600 md:text-lg">
                Rancang bajet rawatan sebelum membuat keputusan. Pilih kategori, jawab soalan ringkas, dan terus dapat julat anggaran kos.
            </p>

            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('categories') }}" class="w-full rounded-xl bg-teal-600 px-8 py-3 text-center text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700 sm:w-auto">
                    Mula Anggaran
                </a>
                <a href="{{ route('clinic.finder') }}" class="w-full rounded-xl border border-slate-300 px-8 py-3 text-center text-sm font-bold text-slate-700 transition hover:border-teal-300 hover:text-teal-700 sm:w-auto">
                    Cari Klinik
                </a>
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-5 md:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="mb-2 font-bold">1. Pilih Kategori</h3>
            <p class="text-sm text-slate-600">Mulakan dengan kategori rawatan seperti Gigi, Mata, Rambut, Kulit, Fitness atau General.</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="mb-2 font-bold">2. Jawab Soalan</h3>
            <p class="text-sm text-slate-600">Isi faktor kos seperti tahap kes, lokasi, dan pilihan tambahan mengikut rawatan.</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="mb-2 font-bold">3. Dapat Keputusan</h3>
            <p class="text-sm text-slate-600">Lihat julat anggaran kos dan teruskan ke carian klinik berdekatan.</p>
        </article>
    </section>

    @if($categories->isNotEmpty())
        <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 text-lg font-bold">Kategori Tersedia</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($categories as $category)
                    <a href="{{ route('estimator', $category->slug) }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-700">
                        {{ $category->icon }} {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
