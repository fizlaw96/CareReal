<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        $treatments = Treatment::orderBy('name')->get(['id', 'name', 'slug']);

        $selectedTreatment = null;
        if ($request->filled('treatment')) {
            $selectedTreatment = $treatments->firstWhere('slug', $request->string('treatment')->toString());
        }

        return view('clinic.index', [
            'title' => 'Cari Klinik',
            'treatments' => $treatments,
            'selectedTreatment' => $selectedTreatment,
            'mapUrl' => null,
            'filters' => [
                'negeri' => '',
                'daerah' => '',
            ],
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'treatment' => ['nullable', 'string', 'exists:treatments,slug'],
            'negeri' => ['nullable', 'string', 'max:100'],
            'daerah' => ['nullable', 'string', 'max:100'],
        ]);

        $treatments = Treatment::orderBy('name')->get(['id', 'name', 'slug']);

        $selectedTreatment = null;
        if (! empty($validated['treatment'])) {
            $selectedTreatment = $treatments->firstWhere('slug', $validated['treatment']);
        }

        $mapQuery = implode(', ', array_filter([
            $selectedTreatment?->name,
            $validated['daerah'] ?? null,
            $validated['negeri'] ?? null,
            'Malaysia',
        ]));

        return view('clinic.index', [
            'title' => 'Cari Klinik',
            'treatments' => $treatments,
            'selectedTreatment' => $selectedTreatment,
            'mapUrl' => 'https://www.google.com/maps?q='.urlencode($mapQuery).'&output=embed',
            'filters' => [
                'negeri' => $validated['negeri'] ?? '',
                'daerah' => $validated['daerah'] ?? '',
            ],
        ]);
    }
}
