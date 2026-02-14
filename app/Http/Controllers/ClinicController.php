<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Services\SerpApiMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $stateDistricts = $this->stateDistricts();
        $states = array_keys($stateDistricts);

        $treatments = Treatment::with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category_id', 'base_min', 'base_max']);

        $selectedTreatment = null;
        if ($request->filled('treatment')) {
            $selectedTreatment = $treatments->firstWhere('slug', $request->string('treatment')->toString());
        }

        $selectedState = $request->string('negeri')->toString();
        $selectedDistrict = $request->string('daerah')->toString();
        $districtsForState = $selectedState && isset($stateDistricts[$selectedState])
            ? $stateDistricts[$selectedState]
            : [];

        return view('clinic.index', [
            'title' => 'Cari Pusat Rawatan',
            'treatments' => $treatments,
            'selectedTreatment' => $selectedTreatment,
            'states' => $states,
            'districtsForState' => $districtsForState,
            'placeLabel' => $this->resolvePlaceLabel($selectedTreatment),
            'mapUrl' => null,
            'places' => collect(),
            'serpError' => null,
            'searchQuery' => null,
            'queryBadges' => collect(),
            'filters' => [
                'negeri' => $selectedState,
                'daerah' => $selectedDistrict,
                'location_hint' => $request->string('location_hint')->toString(),
            ],
        ]);
    }

    public function search(Request $request, SerpApiMapsService $serpApiMapsService)
    {
        $stateDistricts = $this->stateDistricts();
        $states = array_keys($stateDistricts);

        $validated = $request->validate([
            'treatment' => ['required', 'string', 'exists:treatments,slug'],
            'negeri' => ['required', 'string', 'max:100', Rule::in($states)],
            'daerah' => ['nullable', 'string', 'max:100'],
            'location_hint' => ['nullable', 'string', 'max:120'],
        ], [
            'treatment.required' => 'Sila pilih jenis rawatan.',
            'treatment.exists' => 'Jenis rawatan tidak sah.',
            'negeri.required' => 'Sila pilih negeri.',
            'negeri.in' => 'Negeri dipilih tidak sah.',
        ]);

        $treatments = Treatment::with('category')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'category_id', 'base_min', 'base_max']);

        $selectedTreatment = null;
        if (! empty($validated['treatment'])) {
            $selectedTreatment = $treatments->firstWhere('slug', $validated['treatment']);
        }

        $placeLabel = $this->resolvePlaceLabel($selectedTreatment);
        $placeKeyword = $this->resolvePlaceKeyword($selectedTreatment);
        $whatsappMessage = $selectedTreatment
            ? 'Hi, saya nak tahu lebih lanjut mengenai '.$selectedTreatment->name
            : 'Hi, saya nak tahu lebih lanjut mengenai rawatan ini';

        $districtsForState = [];
        if (! empty($validated['negeri']) && isset($stateDistricts[$validated['negeri']])) {
            $districtsForState = $stateDistricts[$validated['negeri']];
        }

        $searchQuery = trim(implode(', ', array_filter([
            $selectedTreatment ? $selectedTreatment->name.' '.$placeKeyword : $placeKeyword,
            $validated['location_hint'] ?? null,
            $validated['daerah'] ?? null,
            $validated['negeri'] ?? null,
            'Malaysia',
        ])));

        $queryBadges = collect([
            $selectedTreatment?->name,
            $placeKeyword,
            $validated['location_hint'] ?? null,
            $validated['daerah'] ?? null,
            $validated['negeri'] ?? null,
            'Malaysia',
        ])
            ->filter(fn ($badge) => filled($badge))
            ->map(fn ($badge) => trim((string) $badge))
            ->unique()
            ->values();

        $serpResult = $serpApiMapsService->searchNearby($searchQuery, 8, $whatsappMessage);
        $places = collect($serpResult['results']);

        $mapUrl = data_get($places->first(), 'embed_url');
        if (! filled($mapUrl)) {
            $mapUrl = 'https://www.google.com/maps?q='.urlencode($searchQuery).'&output=embed';
        }

        return view('clinic.index', [
            'title' => 'Cari Pusat Rawatan',
            'treatments' => $treatments,
            'selectedTreatment' => $selectedTreatment,
            'states' => $states,
            'districtsForState' => $districtsForState,
            'placeLabel' => $placeLabel,
            'mapUrl' => $mapUrl,
            'places' => $places,
            'serpError' => $serpResult['error'],
            'searchQuery' => $searchQuery,
            'queryBadges' => $queryBadges,
            'filters' => [
                'negeri' => $validated['negeri'] ?? '',
                'daerah' => $validated['daerah'] ?? '',
                'location_hint' => $validated['location_hint'] ?? '',
            ],
        ]);
    }

    public function districts(Request $request)
    {
        $stateDistricts = $this->stateDistricts();
        $states = array_keys($stateDistricts);
        $selectedState = $request->string('negeri')->toString();

        $districts = [];
        if ($selectedState !== '' && isset($stateDistricts[$selectedState])) {
            $districts = $stateDistricts[$selectedState];
        }

        return response()->json([
            'states' => $states,
            'negeri' => $selectedState,
            'districts' => $districts,
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

    private function stateDistricts(): array
    {
        $fallback = config('malaysia_locations.states', []);
        $sourceEnabled = (bool) config('malaysia_locations.source.enabled', true);
        $sourceUrl = (string) config('malaysia_locations.source.url', '');
        $cacheTtlMinutes = max((int) config('malaysia_locations.source.cache_ttl_minutes', 10080), 60);

        if (! $sourceEnabled || $sourceUrl === '') {
            return $this->mergePopularAreas($fallback);
        }

        return Cache::remember('malaysia_locations.states_districts', now()->addMinutes($cacheTtlMinutes), function () use ($sourceUrl, $fallback) {
            try {
                $response = Http::timeout(25)->retry(2, 250)->get($sourceUrl);
                if (! $response->successful()) {
                    return $this->mergePopularAreas($fallback);
                }

                $mapped = $this->extractDistrictMapFromCsv($response->body());
                if (empty($mapped)) {
                    return $this->mergePopularAreas($fallback);
                }

                return $this->mergePopularAreas($mapped);
            } catch (\Throwable) {
                return $this->mergePopularAreas($fallback);
            }
        });
    }

    private function extractDistrictMapFromCsv(string $csv): array
    {
        $map = [];
        $handle = fopen('php://temp', 'r+');

        if (! $handle) {
            return [];
        }

        fwrite($handle, $csv);
        rewind($handle);

        $header = fgetcsv($handle);
        if (! is_array($header)) {
            fclose($handle);
            return [];
        }

        $headerIndexes = array_flip(array_map(static fn ($col) => strtolower(trim((string) $col)), $header));
        $stateIndex = $headerIndexes['state'] ?? null;
        $districtIndex = $headerIndexes['district'] ?? null;

        if ($stateIndex === null || $districtIndex === null) {
            fclose($handle);
            return [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            $state = trim((string) ($row[$stateIndex] ?? ''));
            $district = trim((string) ($row[$districtIndex] ?? ''));

            if ($state === '' || $district === '' || strtolower($district) === 'overall') {
                continue;
            }

            $map[$state] ??= [];
            $map[$state][$district] = true;
        }

        fclose($handle);

        if (empty($map)) {
            return [];
        }

        ksort($map, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($map as $state => $districts) {
            $keys = array_keys($districts);
            sort($keys, SORT_NATURAL | SORT_FLAG_CASE);
            $map[$state] = $keys;
        }

        return $map;
    }

    private function mergePopularAreas(array $stateDistricts): array
    {
        $popularAreas = config('malaysia_locations.popular_areas', []);
        if (empty($popularAreas)) {
            return $stateDistricts;
        }

        foreach ($popularAreas as $state => $areas) {
            $stateName = trim((string) $state);
            if ($stateName === '') {
                continue;
            }

            $stateDistricts[$stateName] ??= [];

            foreach ((array) $areas as $area) {
                $name = trim((string) $area);
                if ($name === '') {
                    continue;
                }

                if (! in_array($name, $stateDistricts[$stateName], true)) {
                    $stateDistricts[$stateName][] = $name;
                }
            }

            sort($stateDistricts[$stateName], SORT_NATURAL | SORT_FLAG_CASE);
        }

        ksort($stateDistricts, SORT_NATURAL | SORT_FLAG_CASE);

        return $stateDistricts;
    }
}
