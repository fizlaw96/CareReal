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
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl md:text-4xl">{{ $treatment->name }}</h1>
        <a href="{{ route('estimator', $category->slug) }}" class="mt-4 inline-flex text-sm font-semibold text-slate-600 hover:text-teal-700">
            ← Tukar jenis rawatan
        </a>
    </section>

    @if($questions->isEmpty())
        <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-700">
            Tiada soalan tersedia untuk rawatan ini.
        </div>
    @else
        <form method="POST" action="{{ route('estimation.calculate') }}" id="estimatorWizard" class="mx-auto mt-8 max-w-3xl rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 md:mt-10 md:p-8">
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

            <div id="wizardViewport" class="relative min-h-[300px] overflow-hidden transition-[height] duration-300">
                @foreach($questions as $question)
                    <section data-panel data-step="{{ $loop->index }}" data-question-id="{{ $question->id }}" data-question-label="{{ $question->label }}" class="wizard-panel absolute inset-0 rounded-2xl border border-slate-200 bg-slate-50 p-5 md:p-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-teal-700">Soalan {{ $loop->iteration }} / {{ $questions->count() }}</p>
                        <h2 class="mt-2 text-xl font-extrabold leading-tight text-slate-900 sm:text-2xl">{{ $question->label }}</h2>
                        @if(in_array($question->key, ['preferred_state', 'preferred_district'], true))
                            <p class="mt-2 text-sm text-slate-500">Pilih daripada senarai untuk teruskan ke soalan seterusnya.</p>

                            @if($question->key === 'preferred_state')
                                <div class="mt-8 md:mt-9">
                                    <select
                                        data-select-answer
                                        data-state-select
                                        data-question-id="{{ $question->id }}"
                                        data-step="{{ $loop->index }}"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-100"
                                    >
                                        <option value="">Pilih negeri</option>
                                        @foreach($question->options as $option)
                                            <option value="{{ $option->id }}" data-state-label="{{ $option->label }}" @selected(old('answers.'.$question->id) == $option->id)>
                                                {{ $option->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if($question->key === 'preferred_district')
                                @php
                                    $districtOptionMap = $question->options
                                        ->mapWithKeys(fn ($option) => [$option->label => $option->id])
                                        ->all();
                                    $defaultDistrictOptionId = $districtOptionMap['Tak pasti / pilih kemudian'] ?? null;
                                @endphp

                                <div class="mt-8 md:mt-9">
                                    <select
                                        data-select-answer
                                        data-district-select
                                        data-question-id="{{ $question->id }}"
                                        data-step="{{ $loop->index }}"
                                        data-option-map='@json($districtOptionMap)'
                                        data-default-option-id="{{ $defaultDistrictOptionId }}"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 focus:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-100"
                                    >
                                        <option value="{{ $defaultDistrictOptionId }}" @selected(old('answers.'.$question->id) == $defaultDistrictOptionId)>Tak pasti / pilih kemudian</option>
                                    </select>
                                </div>
                            @endif
                        @else
                            <p class="mt-2 text-sm text-slate-500">Pilih satu jawapan untuk teruskan ke soalan seterusnya.</p>

                            <div class="mt-8 grid gap-3 sm:mt-9 sm:grid-cols-2">
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
                        @endif
                    </section>
                @endforeach
            </div>

            <div class="mt-8 flex flex-col-reverse items-stretch gap-3 sm:flex-row sm:items-center sm:justify-between">
                <button type="button" id="wizardBack" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-700 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto">
                    Kembali
                </button>

                <button type="submit" id="wizardSubmit" class="hidden w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-200 transition hover:bg-teal-700 sm:w-auto">
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
                const viewport = document.getElementById('wizardViewport');
                const answerInputs = Array.from(wizard.querySelectorAll('input[id^="answer-"]'));
                const optionButtons = Array.from(wizard.querySelectorAll('[data-option-button]'));
                const selectAnswers = Array.from(wizard.querySelectorAll('[data-select-answer]'));
                const stateSelect = wizard.querySelector('[data-state-select]');
                const districtSelect = wizard.querySelector('[data-district-select]');
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

                const setAnswerValue = (questionId, value) => {
                    const input = wizard.querySelector(`#answer-${questionId}`);
                    if (!input) return;
                    input.value = value || '';
                };

                const getAnswerValue = (questionId) => {
                    const input = wizard.querySelector(`#answer-${questionId}`);
                    return input ? input.value : '';
                };

                const moveToNextStep = (step) => {
                    if (step === currentStep && currentStep < total - 1) {
                        setTimeout(() => {
                            currentStep += 1;
                            render();
                        }, 180);
                    } else {
                        render();
                    }
                };

                const buildDistrictOptions = (districtNames, selectedId = '') => {
                    if (!districtSelect) return;

                    const optionMap = JSON.parse(districtSelect.dataset.optionMap || '{}');
                    const defaultOptionId = districtSelect.dataset.defaultOptionId || String(Object.values(optionMap)[0] || '');
                    districtSelect.innerHTML = '';

                    const appendedIds = new Set();

                    const appendOption = (value, label) => {
                        if (!value || appendedIds.has(String(value))) return;
                        appendedIds.add(String(value));

                        const opt = document.createElement('option');
                        opt.value = String(value);
                        opt.textContent = label;
                        districtSelect.appendChild(opt);
                    };

                    appendOption(defaultOptionId, 'Tak pasti / pilih kemudian');

                    districtNames.forEach((districtName) => {
                        const optionId = optionMap[districtName];
                        if (optionId) {
                            appendOption(optionId, districtName);
                        }
                    });

                    const finalSelected = appendedIds.has(String(selectedId))
                        ? String(selectedId)
                        : String(defaultOptionId);

                    districtSelect.value = finalSelected;
                    setAnswerValue(districtSelect.dataset.questionId, finalSelected);
                };

                const fetchDistrictsByState = async (stateLabel, selectedId = '') => {
                    if (!districtSelect) return;

                    if (!stateLabel) {
                        buildDistrictOptions([], selectedId);
                        return;
                    }

                    try {
                        const endpoint = `{{ route('clinic.districts') }}?negeri=${encodeURIComponent(stateLabel)}`;
                        const response = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });

                        if (!response.ok) {
                            throw new Error('Gagal ambil daftar daerah');
                        }

                        const payload = await response.json();
                        const districts = Array.isArray(payload.districts) ? payload.districts : [];
                        buildDistrictOptions(districts, selectedId);
                    } catch (error) {
                        buildDistrictOptions([], selectedId);
                    }
                };

                selectAnswers.forEach((select) => {
                    const qid = select.dataset.questionId;
                    setAnswerValue(qid, select.value || getAnswerValue(qid));

                    select.addEventListener('change', async () => {
                        const qidInner = select.dataset.questionId;
                        const step = Number(select.dataset.step);
                        setAnswerValue(qidInner, select.value);

                        if (select.hasAttribute('data-state-select')) {
                            const selectedOption = select.options[select.selectedIndex];
                            const stateLabel = select.value
                                ? (selectedOption?.dataset?.stateLabel || selectedOption?.textContent || '')
                                : '';
                            await fetchDistrictsByState(stateLabel, getAnswerValue(districtSelect?.dataset.questionId));
                        }

                        moveToNextStep(step);
                    });
                });

                if (districtSelect) {
                    const qid = districtSelect.dataset.questionId;
                    if (!getAnswerValue(qid) && districtSelect.value) {
                        setAnswerValue(qid, districtSelect.value);
                    }
                }

                if (stateSelect) {
                    const selectedOption = stateSelect.options[stateSelect.selectedIndex];
                    const stateLabel = stateSelect.value
                        ? (selectedOption?.dataset?.stateLabel || selectedOption?.textContent || '')
                        : '';
                    fetchDistrictsByState(stateLabel, getAnswerValue(districtSelect?.dataset.questionId));
                }

                const render = () => {
                    panels.forEach((panel, index) => {
                        panel.classList.remove('is-active', 'is-prev');
                        if (index < currentStep) panel.classList.add('is-prev');
                        if (index === currentStep) panel.classList.add('is-active');
                    });

                    if (viewport) {
                        const activePanel = panels[currentStep];
                        if (activePanel) {
                            viewport.style.height = `${activePanel.scrollHeight}px`;
                        }
                    }

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
                        setAnswerValue(qid, oid);
                        setActiveOptionStyles();
                        moveToNextStep(step);
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
                    selectAnswers.forEach((select) => {
                        select.disabled = true;
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

                window.addEventListener('resize', () => {
                    render();
                });

                render();
            })();
        </script>
    @endif
@endsection
