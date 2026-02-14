<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Option;
use App\Models\Treatment;
use App\Services\EstimationCalculator;
use Illuminate\Http\Request;

class EstimatorController extends Controller
{
    public function show(Category $category, Request $request)
    {
        $treatments = $category
            ->treatments()
            ->with(['questions.options'])
            ->get();

        abort_if($treatments->isEmpty(), 404, 'Tiada rawatan ditemui untuk kategori ini.');

        $selectedTreatment = $treatments->firstWhere('slug', $request->string('treatment')->toString())
            ?? $treatments->first();

        return view('estimator.show', [
            'title' => 'Anggaran: '.$category->name,
            'category' => $category,
            'treatments' => $treatments,
            'treatment' => $selectedTreatment,
            'questions' => $selectedTreatment->questions,
        ]);
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

        $summary = $treatment->questions()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($question) use ($answers) {
                $selected = $answers->firstWhere('question_id', $question->id);

                return [
                    'question' => $question->label,
                    'answer' => $selected?->label,
                ];
            })
            ->filter(fn ($item) => filled($item['answer']))
            ->values();

        return view('estimator.result', [
            'title' => 'Keputusan Anggaran',
            'category' => $category,
            'treatment' => $treatment,
            'result' => $result,
            'summary' => $summary,
        ]);
    }
}
