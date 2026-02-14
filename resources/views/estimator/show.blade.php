@extends('layouts.app')

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Kategori</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Anggaran: {{ $category->name }}</h1>
        </div>

        @if($treatments->count() > 1)
            <form method="GET" action="{{ route('estimator', $category->slug) }}" class="w-full rounded-xl border border-slate-200 bg-white p-3 md:w-[320px]">
                <label for="treatment" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Rawatan</label>
                <select id="treatment" name="treatment" onchange="this.form.submit()" class="w-full rounded-lg border border-slate-300 p-2 text-sm">
                    @foreach($treatments as $item)
                        <option value="{{ $item->slug }}" @selected($item->id === $treatment->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <form method="POST" action="{{ route('estimation.calculate') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="category" value="{{ $category->slug }}">
        <input type="hidden" name="treatment" value="{{ $treatment->slug }}">

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-sm text-slate-500">Rawatan dipilih</p>
            <h2 class="text-xl font-bold text-slate-900">{{ $treatment->name }}</h2>
            <p class="mt-2 text-sm text-slate-600">Base cost: RM {{ number_format($treatment->base_min) }} - RM {{ number_format($treatment->base_max) }}</p>
        </div>

        @forelse($questions as $question)
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <label class="mb-3 block font-semibold text-slate-800" for="question-{{ $question->id }}">
                    {{ $question->label }}
                </label>

                @if($question->type === 'radio')
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($question->options as $option)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 text-sm">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="h-4 w-4 text-teal-600" @checked(old('answers.'.$question->id, $question->options->first()?->id) == $option->id)>
                                <span>{{ $option->label }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <select id="question-{{ $question->id }}" name="answers[{{ $question->id }}]" class="w-full rounded-lg border border-slate-300 p-3 text-sm">
                        @foreach($question->options as $option)
                            <option value="{{ $option->id }}" @selected(old('answers.'.$question->id, $question->options->first()?->id) == $option->id)>
                                {{ $option->label }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-700">
                Tiada soalan tersedia untuk rawatan ini.
            </div>
        @endforelse

        <button type="submit" class="rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700">
            Kira Anggaran
        </button>
    </form>
@endsection
