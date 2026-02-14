@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Pilih Kategori Rawatan</h1>
        <p class="mt-2 text-slate-600">Langkah pertama untuk anggaran: pilih kategori yang paling hampir dengan rawatan anda.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($categories as $category)
            <a href="{{ route('estimator', $category->slug) }}" class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md">
                <div class="mb-4 text-3xl">{{ $category->icon }}</div>
                <h2 class="mb-2 text-xl font-bold text-slate-900">{{ $category->name }}</h2>
                <p class="mb-4 text-sm text-slate-600">{{ $category->description ?? 'Anggaran kos rawatan dalam kategori ini.' }}</p>
                <div class="text-sm font-semibold text-teal-700">
                    {{ $category->treatments_count }} jenis rawatan
                    <span aria-hidden="true" class="ml-1 transition group-hover:ml-2">→</span>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-700">
                Tiada kategori tersedia lagi. Jalankan seeder untuk data permulaan.
            </div>
        @endforelse
    </div>
@endsection
