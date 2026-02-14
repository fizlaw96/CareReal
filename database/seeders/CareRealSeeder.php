<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Option;
use App\Models\Question;
use App\Models\Treatment;
use Illuminate\Database\Seeder;

class CareRealSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategory(
            [
                'name' => 'Gigi',
                'slug' => 'gigi',
                'icon' => '🦷',
                'description' => 'Anggaran rawatan pergigian umum, braces, aligner dan whitening.',
            ],
            [
                ['name' => 'Metal Braces', 'slug' => 'metal-braces', 'base_min' => 4000, 'base_max' => 7000],
                ['name' => 'Ceramic Braces', 'slug' => 'ceramic-braces', 'base_min' => 6000, 'base_max' => 9000],
                ['name' => 'Self-Ligating Braces', 'slug' => 'self-ligating-braces', 'base_min' => 7000, 'base_max' => 10000],
                ['name' => 'Clear Aligner', 'slug' => 'clear-aligner', 'base_min' => 8000, 'base_max' => 20000],
                ['name' => 'Whitening', 'slug' => 'whitening', 'base_min' => 800, 'base_max' => 2500],
            ],
            [
                [
                    'label' => 'Tahap Kes',
                    'key' => 'severity',
                    'type' => 'radio',
                    'options' => [
                        ['label' => 'Ringan', 'multiplier' => 1.0],
                        ['label' => 'Sederhana', 'multiplier' => 1.2],
                        ['label' => 'Teruk', 'multiplier' => 1.4],
                    ],
                ],
                [
                    'label' => 'Lokasi Klinik',
                    'key' => 'location',
                    'options' => [
                        ['label' => 'Luar bandar', 'multiplier' => 0.9],
                        ['label' => 'Bandar biasa', 'multiplier' => 1.0],
                        ['label' => 'Bandar besar', 'multiplier' => 1.2],
                    ],
                ],
                [
                    'label' => 'Rahang',
                    'key' => 'jaw_count',
                    'options' => [
                        ['label' => '1 Rahang', 'multiplier' => 1.0],
                        ['label' => '2 Rahang', 'multiplier' => 1.25],
                    ],
                ],
                [
                    'label' => 'Add-on Tambahan',
                    'key' => 'addon',
                    'options' => [
                        ['label' => 'Tiada add-on', 'add_min' => 0, 'add_max' => 0],
                        ['label' => 'X-ray + Konsultasi lanjut', 'add_min' => 200, 'add_max' => 500],
                    ],
                ],
            ]
        );

        $this->seedCategory(
            [
                'name' => 'Mata',
                'slug' => 'mata',
                'icon' => '👁️',
                'description' => 'Bandingkan kos PRK, LASIK, SMILE dan Ortho-K.',
            ],
            [
                ['name' => 'PRK', 'slug' => 'prk', 'base_min' => 4500, 'base_max' => 7000],
                ['name' => 'LASIK', 'slug' => 'lasik', 'base_min' => 7000, 'base_max' => 12000],
                ['name' => 'SMILE', 'slug' => 'smile', 'base_min' => 9000, 'base_max' => 15000],
                ['name' => 'Ortho-K', 'slug' => 'ortho-k', 'base_min' => 3000, 'base_max' => 6000],
            ],
            [
                [
                    'label' => 'Tahap Rabun',
                    'key' => 'myopia_level',
                    'type' => 'radio',
                    'options' => [
                        ['label' => 'Rendah', 'multiplier' => 1.0],
                        ['label' => 'Sederhana', 'multiplier' => 1.1],
                        ['label' => 'Tinggi', 'multiplier' => 1.25],
                    ],
                ],
                [
                    'label' => 'Teknologi Prosedur',
                    'key' => 'technology',
                    'options' => [
                        ['label' => 'Standard', 'multiplier' => 1.0],
                        ['label' => 'Custom / Advanced', 'multiplier' => 1.15],
                    ],
                ],
                [
                    'label' => 'Lokasi',
                    'key' => 'location',
                    'options' => [
                        ['label' => 'Negeri biasa', 'multiplier' => 1.0],
                        ['label' => 'Lembah Klang', 'multiplier' => 1.2],
                    ],
                ],
                [
                    'label' => 'Pemeriksaan Tambahan',
                    'key' => 'addon',
                    'options' => [
                        ['label' => 'Tiada', 'add_min' => 0, 'add_max' => 0],
                        ['label' => 'Topografi kornea lengkap', 'add_min' => 200, 'add_max' => 800],
                    ],
                ],
            ]
        );

        $this->seedCategory(
            [
                'name' => 'Rambut',
                'slug' => 'rambut',
                'icon' => '💇',
                'description' => 'Rawatan keguguran rambut termasuk PRP dan transplant.',
            ],
            [
                ['name' => 'PRP Rambut', 'slug' => 'prp-rambut', 'base_min' => 800, 'base_max' => 1500],
                ['name' => 'Hair Transplant', 'slug' => 'hair-transplant', 'base_min' => 8000, 'base_max' => 20000],
                ['name' => 'Scalp Treatment', 'slug' => 'scalp-treatment', 'base_min' => 300, 'base_max' => 1000],
            ],
            [
                [
                    'label' => 'Tahap Keguguran',
                    'key' => 'severity',
                    'options' => [
                        ['label' => 'Awal', 'multiplier' => 1.0],
                        ['label' => 'Sederhana', 'multiplier' => 1.2],
                        ['label' => 'Teruk', 'multiplier' => 1.4],
                    ],
                ],
                [
                    'label' => 'Kawasan Rawatan',
                    'key' => 'area',
                    'options' => [
                        ['label' => 'Kecil', 'multiplier' => 1.0],
                        ['label' => 'Sederhana', 'multiplier' => 1.2],
                        ['label' => 'Besar', 'multiplier' => 1.5],
                    ],
                ],
                [
                    'label' => 'Lokasi Klinik',
                    'key' => 'clinic_tier',
                    'options' => [
                        ['label' => 'Standard', 'multiplier' => 1.0],
                        ['label' => 'Premium', 'multiplier' => 1.25],
                    ],
                ],
                [
                    'label' => 'Add-on Ubat',
                    'key' => 'addon',
                    'options' => [
                        ['label' => 'Tiada', 'add_min' => 0, 'add_max' => 0],
                        ['label' => 'Pakej ubat 3 bulan', 'add_min' => 300, 'add_max' => 1200],
                    ],
                ],
            ]
        );

        $this->seedCategory(
            [
                'name' => 'Kulit',
                'slug' => 'kulit',
                'icon' => '✨',
                'description' => 'Anggaran dermatologi dan rawatan estetik seperti laser/peel.',
            ],
            [
                ['name' => 'Facial Klinikal', 'slug' => 'facial-klinikal', 'base_min' => 150, 'base_max' => 400],
                ['name' => 'Laser Jerawat', 'slug' => 'laser-jerawat', 'base_min' => 500, 'base_max' => 1500],
                ['name' => 'Laser Parut', 'slug' => 'laser-parut', 'base_min' => 800, 'base_max' => 2500],
                ['name' => 'Chemical Peel', 'slug' => 'chemical-peel', 'base_min' => 300, 'base_max' => 900],
            ],
            [
                [
                    'label' => 'Tahap Masalah Kulit',
                    'key' => 'severity',
                    'options' => [
                        ['label' => 'Ringan', 'multiplier' => 1.0],
                        ['label' => 'Sederhana', 'multiplier' => 1.2],
                        ['label' => 'Teruk', 'multiplier' => 1.4],
                    ],
                ],
                [
                    'label' => 'Jenis Klinik',
                    'key' => 'clinic_tier',
                    'options' => [
                        ['label' => 'Klinik biasa', 'multiplier' => 1.0],
                        ['label' => 'Klinik estetik premium', 'multiplier' => 1.3],
                    ],
                ],
                [
                    'label' => 'Bilangan Sesi',
                    'key' => 'session_count',
                    'options' => [
                        ['label' => '1 sesi', 'multiplier' => 1.0],
                        ['label' => '3 sesi', 'multiplier' => 3.0],
                        ['label' => '6 sesi', 'multiplier' => 6.0],
                    ],
                ],
                [
                    'label' => 'Add-on Produk',
                    'key' => 'addon',
                    'options' => [
                        ['label' => 'Tiada', 'add_min' => 0, 'add_max' => 0],
                        ['label' => 'Serum klinikal khas', 'add_min' => 150, 'add_max' => 600],
                    ],
                ],
            ]
        );

        $this->seedCategory(
            [
                'name' => 'Fitness',
                'slug' => 'fitness',
                'icon' => '🏋️',
                'description' => 'Program kecergasan dan pemulihan fizikal dengan anggaran bulanan.',
            ],
            [
                ['name' => 'Online Coaching', 'slug' => 'online-coaching', 'base_min' => 150, 'base_max' => 400],
                ['name' => 'Personal Trainer', 'slug' => 'personal-trainer', 'base_min' => 800, 'base_max' => 2000],
                ['name' => 'Pelan Pemakanan', 'slug' => 'pelan-pemakanan', 'base_min' => 200, 'base_max' => 600],
            ],
            [
                [
                    'label' => 'Tempoh Program',
                    'key' => 'duration',
                    'options' => [
                        ['label' => '1 bulan', 'multiplier' => 1.0],
                        ['label' => '3 bulan', 'multiplier' => 3.0],
                        ['label' => '6 bulan', 'multiplier' => 6.0],
                    ],
                ],
                [
                    'label' => 'Sesi Seminggu (PT)',
                    'key' => 'weekly_sessions',
                    'options' => [
                        ['label' => '1x seminggu', 'multiplier' => 1.0],
                        ['label' => '2-3x seminggu', 'multiplier' => 1.3],
                        ['label' => '4-5x seminggu', 'multiplier' => 1.6],
                    ],
                ],
                [
                    'label' => 'Jenis Gym',
                    'key' => 'gym_tier',
                    'options' => [
                        ['label' => 'Standard', 'multiplier' => 1.0],
                        ['label' => 'Premium', 'multiplier' => 1.3],
                    ],
                ],
                [
                    'label' => 'Add-on Supplement',
                    'key' => 'addon',
                    'options' => [
                        ['label' => 'Tiada', 'add_min' => 0, 'add_max' => 0],
                        ['label' => 'Supplement bulanan', 'add_min' => 100, 'add_max' => 400],
                    ],
                ],
            ]
        );

        $this->seedCategory(
            [
                'name' => 'General',
                'slug' => 'general',
                'icon' => '🩺',
                'description' => 'Check-up dan screening kesihatan umum.',
            ],
            [
                ['name' => 'Basic Checkup', 'slug' => 'basic-checkup', 'base_min' => 100, 'base_max' => 300],
                ['name' => 'Executive Checkup', 'slug' => 'executive-checkup', 'base_min' => 500, 'base_max' => 1500],
                ['name' => 'Full Screening', 'slug' => 'full-screening', 'base_min' => 1500, 'base_max' => 4000],
            ],
            [
                [
                    'label' => 'Julat Umur',
                    'key' => 'age_range',
                    'options' => [
                        ['label' => '< 30 tahun', 'multiplier' => 1.0],
                        ['label' => '30-45 tahun', 'multiplier' => 1.1],
                        ['label' => '45+ tahun', 'multiplier' => 1.25],
                    ],
                ],
                [
                    'label' => 'Jenis Hospital/Klinik',
                    'key' => 'hospital_type',
                    'options' => [
                        ['label' => 'Kerajaan', 'multiplier' => 0.5],
                        ['label' => 'Swasta', 'multiplier' => 1.0],
                        ['label' => 'Premium hospital', 'multiplier' => 1.3],
                    ],
                ],
                [
                    'label' => 'Lokasi',
                    'key' => 'location',
                    'options' => [
                        ['label' => 'Negeri biasa', 'multiplier' => 1.0],
                        ['label' => 'Bandar besar', 'multiplier' => 1.2],
                    ],
                ],
                [
                    'label' => 'Ujian Tambahan',
                    'key' => 'addon',
                    'options' => [
                        ['label' => 'Tiada', 'add_min' => 0, 'add_max' => 0],
                        ['label' => 'Panel darah lengkap', 'add_min' => 120, 'add_max' => 700],
                    ],
                ],
            ]
        );
    }

    /**
     * @param  array{name:string,slug:string,icon:string,description:string}  $categoryData
     * @param  array<int, array{name:string,slug:string,base_min:int,base_max:int}>  $treatmentsData
     * @param  array<int, array{label:string,key:string,type?:string,options:array<int, array{label:string,multiplier?:float,add_min?:int,add_max?:int}>}>  $questionsData
     */
    private function seedCategory(array $categoryData, array $treatmentsData, array $questionsData): void
    {
        $questionsData = $this->appendLocationPreferenceQuestions($questionsData);

        $category = Category::updateOrCreate(
            ['slug' => $categoryData['slug']],
            $categoryData
        );

        foreach ($treatmentsData as $treatmentData) {
            $treatment = Treatment::updateOrCreate(
                ['slug' => $treatmentData['slug']],
                [
                    ...$treatmentData,
                    'category_id' => $category->id,
                ]
            );

            $desiredQuestionKeys = collect($questionsData)
                ->pluck('key')
                ->values()
                ->all();

            if (! empty($desiredQuestionKeys)) {
                $treatment->questions()
                    ->whereNotIn('key', $desiredQuestionKeys)
                    ->delete();
            }

            foreach ($questionsData as $questionIndex => $questionData) {
                $question = Question::updateOrCreate(
                    [
                        'treatment_id' => $treatment->id,
                        'key' => $questionData['key'],
                    ],
                    [
                        'label' => $questionData['label'],
                        'type' => $questionData['type'] ?? 'select',
                        'sort_order' => $questionIndex + 1,
                    ]
                );

                $desiredOptionLabels = collect($questionData['options'] ?? [])
                    ->pluck('label')
                    ->values()
                    ->all();

                if (empty($desiredOptionLabels)) {
                    $question->options()->delete();
                } else {
                    $question->options()
                        ->whereNotIn('label', $desiredOptionLabels)
                        ->delete();
                }

                foreach (($questionData['options'] ?? []) as $optionIndex => $optionData) {
                    Option::updateOrCreate(
                        [
                            'question_id' => $question->id,
                            'label' => $optionData['label'],
                        ],
                        [
                            'multiplier' => $optionData['multiplier'] ?? null,
                            'add_min' => $optionData['add_min'] ?? null,
                            'add_max' => $optionData['add_max'] ?? null,
                            'sort_order' => $optionIndex + 1,
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param  array<int, array{label:string,key:string,type?:string,options:array<int, array{label:string,multiplier?:float,add_min?:int,add_max?:int}>}>  $questionsData
     * @return array<int, array{label:string,key:string,type?:string,options:array<int, array{label:string,multiplier?:float,add_min?:int,add_max?:int}>}>
     */
    private function appendLocationPreferenceQuestions(array $questionsData): array
    {
        $existingKeys = collect($questionsData)->pluck('key')->all();

        if (! in_array('preferred_state', $existingKeys, true)) {
            $questionsData[] = [
                'label' => 'Negeri (tempat tinggal / kawasan carian)',
                'key' => 'preferred_state',
                'type' => 'select',
                'options' => $this->buildStateOptions(),
            ];
        }

        if (! in_array('preferred_district', $existingKeys, true)) {
            $questionsData[] = [
                'label' => 'Daerah (jika tahu)',
                'key' => 'preferred_district',
                'type' => 'select',
                'options' => $this->buildDistrictOptions(),
            ];
        }

        return $questionsData;
    }

    /**
     * @return array<int, array{label:string,multiplier?:float,add_min?:int,add_max?:int}>
     */
    private function buildStateOptions(): array
    {
        return collect(array_keys(config('malaysia_locations.states', [])))
            ->sort()
            ->values()
            ->map(fn (string $state) => ['label' => $state])
            ->all();
    }

    /**
     * @return array<int, array{label:string,multiplier?:float,add_min?:int,add_max?:int}>
     */
    private function buildDistrictOptions(): array
    {
        $districts = collect(config('malaysia_locations.states', []))
            ->flatten()
            ->merge(collect(config('malaysia_locations.popular_areas', []))->flatten())
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->sort()
            ->values();

        return $districts
            ->prepend('Tak pasti / pilih kemudian')
            ->map(fn (string $district) => ['label' => $district])
            ->all();
    }
}
