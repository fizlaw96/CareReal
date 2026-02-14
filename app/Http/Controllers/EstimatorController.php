<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Option;
use App\Models\Treatment;
use App\Services\EstimationCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EstimatorController extends Controller
{
    public function show(Category $category, Request $request)
    {
        $treatments = $category->treatments()->get();

        abort_if($treatments->isEmpty(), 404, 'Tiada rawatan ditemui untuk kategori ini.');

        $requestedTreatmentSlug = $request->query('treatment');
        if (filled($requestedTreatmentSlug)) {
            $selectedTreatment = $treatments->firstWhere('slug', $requestedTreatmentSlug);
            if ($selectedTreatment) {
                return redirect()->route('estimator.questions', [
                    'category' => $category->slug,
                    'treatment' => $selectedTreatment->slug,
                ]);
            }
        }

        $treatmentGuides = $this->buildTreatmentGuides($treatments);
        $treatmentImages = $this->buildTreatmentImages($treatments);

        return view('estimator.treatment', [
            'title' => 'Pilih Rawatan: '.$category->name,
            'category' => $category,
            'treatments' => $treatments,
            'treatmentGuides' => $treatmentGuides,
            'treatmentImages' => $treatmentImages,
        ]);
    }

    public function questions(Category $category, Treatment $treatment)
    {
        abort_if(
            $treatment->category_id !== $category->id,
            404,
            'Rawatan ini bukan dalam kategori yang dipilih.'
        );

        $treatment->load(['questions.options']);

        return view('estimator.show', [
            'title' => 'Anggaran: '.$category->name.' - '.$treatment->name,
            'category' => $category,
            'treatment' => $treatment,
            'questions' => $treatment->questions,
        ]);
    }

    /**
     * @param  Collection<int, Treatment>  $treatments
     * @return array<string, string>
     */
    private function buildTreatmentGuides(Collection $treatments): array
    {
        $defaults = [
            'metal-braces' => 'Braces asas yang kukuh dan pilihan paling biasa untuk pembetulan susunan gigi.',
            'ceramic-braces' => 'Braces warna seakan gigi, lebih subtle untuk penampilan harian.',
            'self-ligating-braces' => 'Braces dengan mekanisme klip khas, biasanya lebih selesa ketika adjustment.',
            'clear-aligner' => 'Tray lutsinar boleh tanggal, sesuai untuk gaya rawatan lebih fleksibel.',
            'whitening' => 'Rawatan pencerahan warna gigi bagi kesan estetik yang cepat.',
            'prk' => 'Rawatan pembetulan rabun tanpa flap kornea, sesuai untuk profil tertentu.',
            'lasik' => 'Prosedur laser popular dengan pemulihan yang biasanya lebih cepat.',
            'smile' => 'Teknik laser minima invasif dengan bukaan kecil pada kornea.',
            'ortho-k' => 'Kanta khas dipakai waktu malam untuk bantu kawal rabun pada waktu siang.',
            'prp-rambut' => 'Suntikan plasma untuk rangsang folikel rambut dan kurangkan keguguran.',
            'hair-transplant' => 'Pemindahan folikel rambut ke kawasan menipis untuk hasil lebih kekal.',
            'scalp-treatment' => 'Rawatan kulit kepala untuk kesihatan folikel dan persekitaran pertumbuhan.',
            'facial-klinikal' => 'Rawatan asas klinikal untuk pembersihan dan pemulihan kulit.',
            'laser-jerawat' => 'Rawatan laser untuk keradangan jerawat aktif dan kawalan minyak.',
            'laser-parut' => 'Rawatan fokus pada parut jerawat atau tekstur kulit tidak sekata.',
            'chemical-peel' => 'Exfoliation kimia terkawal untuk tona kulit dan pori lebih sekata.',
            'online-coaching' => 'Bimbingan kecergasan jarak jauh dengan pelan latihan tersusun.',
            'personal-trainer' => 'Sesi fizikal bersama jurulatih untuk pemantauan teknik dan progres.',
            'pelan-pemakanan' => 'Perancangan pemakanan ikut matlamat berat, tenaga, dan komposisi badan.',
            'basic-checkup' => 'Pemeriksaan asas rutin untuk saringan kesihatan umum.',
            'executive-checkup' => 'Pakej lebih menyeluruh dengan panel ujian tambahan.',
            'full-screening' => 'Saringan komprehensif untuk penilaian kesihatan yang lebih mendalam.',
        ];

        return $treatments
            ->mapWithKeys(fn (Treatment $treatment) => [
                $treatment->slug => $defaults[$treatment->slug]
                    ?? 'Rawatan ini sesuai untuk keperluan umum dalam kategori ini.',
            ])
            ->all();
    }

    /**
     * @param  Collection<int, Treatment>  $treatments
     * @return array<string, string|null>
     */
    private function buildTreatmentImages(Collection $treatments): array
    {
        return $treatments
            ->mapWithKeys(function (Treatment $treatment) {
                $baseName = str_replace('-', '_', $treatment->slug);
                $directory = 'assets/images/jenis_rawatan';
                $extensions = ['jpg', 'jpeg', 'png', 'webp'];

                foreach ($extensions as $extension) {
                    $relativePath = $directory.'/'.$baseName.'.'.$extension;
                    if (file_exists(public_path($relativePath))) {
                        return [$treatment->slug => asset($relativePath)];
                    }
                }

                return [$treatment->slug => null];
            })
            ->all();
    }

    public function calculate(Request $request, EstimationCalculator $calculator)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'exists:categories,slug'],
            'treatment' => ['required', 'string', 'exists:treatments,slug'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer', 'exists:options,id'],
        ]);

        $category = Category::where('slug', $validated['category'])->firstOrFail();

        $treatment = Treatment::where('slug', $validated['treatment'])
            ->where('category_id', $category->id)
            ->firstOrFail();

        $questionIds = $treatment->questions()->pluck('id');

        $answerIds = collect($validated['answers'] ?? [])
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $answers = Option::whereIn('id', $answerIds)
            ->whereIn('question_id', $questionIds)
            ->get();

        $result = $calculator->calculate($treatment, $answers);

        $questions = $treatment->questions()
            ->orderBy('sort_order')
            ->get();

        $locationHints = collect();
        $selectedState = null;
        $selectedDistrict = null;

        $summary = $questions
            ->map(function ($question) use ($answers, &$locationHints, &$selectedState, &$selectedDistrict) {
                $selected = $answers->firstWhere('question_id', $question->id);
                if (! $selected) {
                    return null;
                }

                $detailParts = [];

                if ($selected->multiplier !== null) {
                    $detailParts[] = 'Kesan kos: x'.number_format($selected->multiplier, 2);
                }

                $addMin = $selected->add_min ?? 0;
                $addMax = $selected->add_max ?? 0;
                if ($addMin !== 0 || $addMax !== 0) {
                    if ($addMin === $addMax) {
                        $detailParts[] = 'Tambah kos: RM '.number_format($addMin);
                    } else {
                        $detailParts[] = 'Tambah kos: RM '.number_format($addMin).' - RM '.number_format($addMax);
                    }
                }

                if (empty($detailParts)) {
                    $detailParts[] = 'Digunakan sebagai faktor pengiraan anggaran.';
                }

                if (
                    $this->isPlaceRelatedKey((string) $question->key)
                    || $this->isPlaceRelatedLabel((string) $question->label)
                ) {
                    $locationHints->push($selected->label);
                }

                $normalizedKey = strtolower((string) $question->key);
                if (str_contains($normalizedKey, 'state') || str_contains($normalizedKey, 'negeri')) {
                    $selectedState = $selected->label;
                }

                if (str_contains($normalizedKey, 'district') || str_contains($normalizedKey, 'daerah')) {
                    $selectedDistrict = $selected->label;
                }

                return [
                    'question' => $question->label,
                    'answer' => $selected->label,
                    'detail' => implode(' • ', $detailParts),
                ];
            })
            ->filter()
            ->values();

        $clinicSearchParams = ['treatment' => $treatment->slug];

        $locationHint = (string) $locationHints->filter()->first();
        if (filled($locationHint)) {
            $clinicSearchParams['location_hint'] = $locationHint;
        }

        if (filled($selectedState)) {
            $clinicSearchParams['negeri'] = $selectedState;
        }

        if (
            filled($selectedDistrict)
            && strcasecmp($selectedDistrict, 'Tak pasti / pilih kemudian') !== 0
        ) {
            $clinicSearchParams['daerah'] = $selectedDistrict;
        }

        return view('estimator.result', [
            'title' => 'Keputusan Anggaran',
            'category' => $category,
            'treatment' => $treatment,
            'result' => $result,
            'summary' => $summary,
            'clinicSearchParams' => $clinicSearchParams,
        ]);
    }

    private function isPlaceRelatedKey(string $key): bool
    {
        $normalized = strtolower($key);

        foreach (['location', 'negeri', 'daerah', 'bandar', 'city', 'state', 'region'] as $token) {
            if (str_contains($normalized, $token)) {
                return true;
            }
        }

        return false;
    }

    private function isPlaceRelatedLabel(string $label): bool
    {
        $normalized = strtolower($label);

        foreach (['lokasi', 'negeri', 'daerah', 'bandar', 'city', 'state', 'region', 'tempat', 'kawasan'] as $token) {
            if (str_contains($normalized, $token)) {
                return true;
            }
        }

        return false;
    }
}
