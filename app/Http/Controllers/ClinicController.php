<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Services\SerpApiMapsService;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $treatments = Treatment::with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category_id']);

        $selectedTreatment = null;
        if ($request->filled('treatment')) {
            $selectedTreatment = $treatments->firstWhere('slug', $request->string('treatment')->toString());
        }

        return view('clinic.index', [
            'title' => 'Cari Pusat Rawatan',
            'treatments' => $treatments,
            'selectedTreatment' => $selectedTreatment,
            'placeLabel' => $this->resolvePlaceLabel($selectedTreatment),
            'mapUrl' => null,
            'places' => collect(),
            'serpError' => null,
            'searchQuery' => null,
            'queryBadges' => collect(),
            'filters' => [
                'negeri' => '',
                'daerah' => '',
            ],
        ]);
    }

    public function search(Request $request, SerpApiMapsService $serpApiMapsService)
    {
        $validated = $request->validate([
            'treatment' => ['nullable', 'string', 'exists:treatments,slug'],
            'negeri' => ['nullable', 'string', 'max:100'],
            'daerah' => ['nullable', 'string', 'max:100'],
        ]);

        $treatments = Treatment::with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category_id']);

        $selectedTreatment = null;
        if (! empty($validated['treatment'])) {
            $selectedTreatment = $treatments->firstWhere('slug', $validated['treatment']);
        }

        $placeLabel = $this->resolvePlaceLabel($selectedTreatment);
        $placeKeyword = $this->resolvePlaceKeyword($selectedTreatment);

        $searchQuery = trim(implode(', ', array_filter([
            $selectedTreatment ? $selectedTreatment->name.' '.$placeKeyword : $placeKeyword,
            $validated['daerah'] ?? null,
            $validated['negeri'] ?? null,
            'Malaysia',
        ])));
        $queryBadges = collect(preg_split('/[\s,]+/', $searchQuery))
            ->filter(fn ($token) => filled($token))
            ->map(fn ($token) => trim((string) $token))
            ->filter(fn ($token) => $token !== '')
            ->unique()
            ->values();

        $serpResult = $serpApiMapsService->searchNearby($searchQuery, 8);
        $places = collect($serpResult['results']);

        $mapUrl = data_get($places->first(), 'embed_url');
        if (! filled($mapUrl)) {
            $mapUrl = 'https://www.google.com/maps?q='.urlencode($searchQuery).'&output=embed';
        }

        return view('clinic.index', [
            'title' => 'Cari Pusat Rawatan',
            'treatments' => $treatments,
            'selectedTreatment' => $selectedTreatment,
            'placeLabel' => $placeLabel,
            'mapUrl' => $mapUrl,
            'places' => $places,
            'serpError' => $serpResult['error'],
            'searchQuery' => $searchQuery,
            'queryBadges' => $queryBadges,
            'filters' => [
                'negeri' => $validated['negeri'] ?? '',
                'daerah' => $validated['daerah'] ?? '',
            ],
        ]);
    }

    private function resolvePlaceLabel(?Treatment $treatment): string
    {
        $slug = $treatment?->category?->slug;

        return match ($slug) {
            'gigi' => 'Klinik Pergigian / Dentist',
            'fitness' => 'Gym / Studio Kecergasan',
            'rambut' => 'Pusat Rawatan Rambut',
            'mata' => 'Klinik Mata',
            'kulit' => 'Klinik Kulit / Estetik',
            'general' => 'Klinik / Hospital',
            default => 'Pusat Rawatan',
        };
    }

    private function resolvePlaceKeyword(?Treatment $treatment): string
    {
        $slug = $treatment?->category?->slug;

        return match ($slug) {
            'gigi' => 'dental clinic',
            'fitness' => 'gym',
            'rambut' => 'hair treatment center',
            'mata' => 'eye clinic',
            'kulit' => 'skin clinic aesthetic',
            'general' => 'medical clinic hospital',
            default => 'healthcare center',
        };
    }
}
