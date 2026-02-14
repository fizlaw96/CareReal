@extends('layouts.app')

@section('content')
    <style>
        .wizard-panel {
            opacity: 0;
            pointer-events: none;
            transform: translateX(56px) scale(0.98);
            transition: transform 340ms cubic-bezier(0.22, 1, 0.36, 1), opacity 260ms ease;
        }

        .wizard-panel.is-active {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0) scale(1);
        }

        .wizard-panel.is-prev {
            opacity: 0;
            pointer-events: none;
            transform: translateX(-56px) scale(0.98);
        }

        .wizard-progress-fill {
            position: relative;
            overflow: hidden;
        }

        .wizard-progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 140px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.55), transparent);
            animation: lineFlow 1.5s linear infinite;
        }

        @keyframes lineFlow {
            from { transform: translateX(-140px); }
            to { transform: translateX(500px); }
        }
    </style>

    <section class="mx-auto max-w-3xl text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-teal-700">Kategori {{ $category->name }}</p>
        <h1 class="mt-2 text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $treatment->name }}</h1>
        <a href="{{ route('estimator', $category->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-slate-600 hover:text-teal-700">
            ← Tukar jenis rawatan
        </a>
    </section>

    @if($questions->isEmpty())
        <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-700">
            Tiada soalan tersedia untuk rawatan ini.
        </div>
    @else
        <form method="POST" action="{{ route('estimation.calculate') }}" id="estimatorWizard" class="mx-auto mt-10 max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            @csrf
            <input type="hidden" name="category" value="{{ $category->slug }}">
            <input type="hidden" name="treatment" value="{{ $treatment->slug }}">

            <div class="mb-8">
                <div class="mb-3 flex items-center justify-between text-xs font-semibold uppercase tracking-widest text-slate-500">
                    <span id="wizardCurrent">Langkah 1</span>
                    <span>{{ $questions->count() }} Soalan</span>
                </div>
                <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                    <div id="wizardBar" class="wizard-progress-fill h-full w-0 rounded-full bg-gradient-to-r from-teal-500 to-cyan-500 transition-all duration-500"></div>
                </div>
                <p id="wizardQuestion" class="mt-3 text-left text-sm font-semibold text-slate-600"></p>
            </div>

            @foreach($questions as $question)
                <input type="hidden" name="answers[{{ $question->id }}]" id="answer-{{ $question->id }}" value="{{ old('answers.'.$question->id) }}">
            @endforeach

            <div class="relative min-h-[320px] overflow-hidden">
                @foreach($questions as $question)
                    <section data-panel data-step="{{ $loop->index }}" data-question-id="{{ $question->id }}" data-question-label="{{ $question->label }}" class="wizard-panel absolute inset-0 rounded-2xl border border-slate-200 bg-slate-50 p-5 md:p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-teal-700">Soalan {{ $loop->iteration }} / {{ $questions->count() }}</p>
                        <h2 class="mt-2 text-2xl font-extrabold leading-tight text-slate-900">{{ $question->label }}</h2>
                        <p class="mt-2 text-sm text-slate-500">Pilih satu jawapan untuk teruskan ke soalan seterusnya.</p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach($question->options as $option)
                                <button
                                    type="button"
                                    data-option-button
                                    data-question-id="{{ $question->id }}"
                                    data-option-id="{{ $option->id }}"
                                    data-step="{{ $loop->parent->index }}"
                                    class="rounded-xl border border-slate-300 bg-white px-4 py-4 text-left text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-700"
                                >
                                    {{ $option->label }}
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between">
                <button type="button" id="wizardBack" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-700 disabled:cursor-not-allowed disabled:opacity-40">
                    Kembali
                </button>

                <button type="submit" id="wizardSubmit" class="hidden rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700">
                    Kira Anggaran
                </button>
            </div>
        </form>

        <x-calc-loading-overlay
            id="calcLoadingScreen"
            :duration="4200"
            badge="CareReal Estimate Engine"
            title="Sedang kira anggaran rawatan anda"
            subtitle="Memproses faktor kos"
            footer="Membina keputusan akhir..."
        />

        <script>
            (() => {
                const wizard = document.getElementById('estimatorWizard');
                if (!wizard) return;

                const panels = Array.from(wizard.querySelectorAll('[data-panel]'));
                const answerInputs = Array.from(wizard.querySelectorAll('input[id^="answer-"]'));
                const optionButtons = Array.from(wizard.querySelectorAll('[data-option-button]'));
                const backBtn = document.getElementById('wizardBack');
                const submitBtn = document.getElementById('wizardSubmit');
                const bar = document.getElementById('wizardBar');
                const currentLabel = document.getElementById('wizardCurrent');
                const questionLabel = document.getElementById('wizardQuestion');
                const loadingScreen = document.getElementById('calcLoadingScreen');
                const loadingDuration = Number(loadingScreen?.dataset.duration || 4200);
                let isSubmitting = false;

                const total = panels.length;
                const isAnswered = (questionId) => {
                    const input = wizard.querySelector(`#answer-${questionId}`);
                    return input && input.value !== '';
                };

                const firstUnansweredStep = () => {
                    for (let i = 0; i < panels.length; i++) {
                        if (!isAnswered(panels[i].dataset.questionId)) return i;
                    }
                    return panels.length - 1;
                };

                let currentStep = Math.max(firstUnansweredStep(), 0);

                const setActiveOptionStyles = () => {
                    optionButtons.forEach((button) => {
                        const qid = button.dataset.questionId;
                        const oid = button.dataset.optionId;
                        const input = wizard.querySelector(`#answer-${qid}`);
                        const active = input && input.value === oid;

                        button.classList.toggle('border-teal-600', active);
                        button.classList.toggle('bg-teal-50', active);
                        button.classList.toggle('text-teal-700', active);
                        button.classList.toggle('shadow-sm', active);
                    });
                };

                const allAnswered = () => answerInputs.every((input) => input.value !== '');

                const render = () => {
                    panels.forEach((panel, index) => {
                        panel.classList.remove('is-active', 'is-prev');
                        if (index < currentStep) panel.classList.add('is-prev');
                        if (index === currentStep) panel.classList.add('is-active');
                    });

                    const progress = ((currentStep + 1) / total) * 100;
                    bar.style.width = `${progress}%`;
                    currentLabel.textContent = `Langkah ${currentStep + 1} dari ${total}`;
                    questionLabel.textContent = panels[currentStep]?.dataset.questionLabel || '';
                    backBtn.disabled = currentStep === 0;
                    submitBtn.classList.toggle('hidden', !allAnswered());

                    setActiveOptionStyles();
                };

                optionButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const qid = button.dataset.questionId;
                        const oid = button.dataset.optionId;
                        const step = Number(button.dataset.step);
                        const input = wizard.querySelector(`#answer-${qid}`);

                        if (!input) return;
                        input.value = oid;
                        setActiveOptionStyles();

                        if (step === currentStep && currentStep < total - 1) {
                            setTimeout(() => {
                                currentStep += 1;
                                render();
                            }, 180);
                        } else {
                            render();
                        }
                    });
                });

                backBtn.addEventListener('click', () => {
                    if (currentStep > 0) {
                        currentStep -= 1;
                        render();
                    }
                });

                wizard.addEventListener('submit', (event) => {
                    if (isSubmitting) return;

                    event.preventDefault();
                    isSubmitting = true;
                    submitBtn.disabled = true;
                    backBtn.disabled = true;

                    optionButtons.forEach((button) => {
                        button.disabled = true;
                    });

                    if (window.CareRealLoadingOverlay && loadingScreen) {
                        window.CareRealLoadingOverlay.runFor('calcLoadingScreen', loadingDuration, () => {
                            wizard.submit();
                        });
                        return;
                    }

                    window.setTimeout(() => {
                        wizard.submit();
                    }, loadingDuration);
                });

                render();
            })();
        </script>
    @endif
@endsection
