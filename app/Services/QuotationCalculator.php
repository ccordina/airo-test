<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class QuotationCalculator
{
    /**
     * The fixed rate for the quotation
     * 
     * @var float
     */
    private const FIXED_RATE = 3;

    /**
     * Calculate the total cost of the quotation
     * 
     * @param array $data
     * @return float
     */
    public function calculate(array $data): float
    {
        $tripLength = $this->getTripLength(data_get($data, 'start_date'), data_get($data, 'end_date'));

        $fixedRate = $this->getFixedRate();

        $ages = array_map('intval', explode(',', data_get($data, 'age')));

        $total = 0;

        collect($ages)->each(function ($age) use ($fixedRate, $tripLength, &$total) {
            $ageLoad = $this->getAgeLoad($age);
            
            $total += $fixedRate * $ageLoad * $tripLength;
        });

        return round($total, 2);
    }

    /**
     * Get the trip length in days
     * 
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function getTripLength(string $startDate, string $endDate): int
    {
        return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
    }

    /**
     * Get the fixed rate
     * 
     * @return float
     */
    private function getFixedRate(): float
    {
        return config('quotation.fixed_rate') ?? self::FIXED_RATE;
    }

    /**
     * Get the age load
     * 
     * @param int $age
     * @return float
     */
    private function getAgeLoad(int $age): float
    {
        $ageLoads = collect(config('quotation.age_loads') ?? []);

        $ageLoad = $ageLoads->first(
            fn ($ageLoad) => $age >= $ageLoad[0] && $age <= $ageLoad[1]
        );

        return $ageLoad[2] ?? 0;
    }
}