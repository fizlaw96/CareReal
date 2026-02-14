<?php

namespace App\Services;

use App\Models\Treatment;
use Illuminate\Support\Collection;

class EstimationCalculator
{
    /**
     * @param  Collection<int, \App\Models\Option>  $answers
     * @return array{min:int,max:int}
     */
    public function calculate(Treatment $treatment, Collection $answers): array
    {
        $min = (float) $treatment->base_min;
        $max = (float) $treatment->base_max;

        foreach ($answers as $option) {
            if ($option->multiplier !== null) {
                $min *= $option->multiplier;
                $max *= $option->multiplier;
            }

            if ($option->add_min !== null) {
                $min += $option->add_min;
                $max += (float) ($option->add_max ?? 0);
            }
        }

        return [
            'min' => (int) round(max($min, 0), -2),
            'max' => (int) round(max($max, 0), -2),
        ];
    }
}
