<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuotationRequest;
use App\Models\Quotation;
use App\Services\QuotationCalculator;
use Illuminate\Http\JsonResponse;

class QuotationController extends Controller
{
    /**
     * Constructor
     * 
     * @param QuotationCalculator $quotationCalculator
     */
    public function __construct(
        private QuotationCalculator $quotationCalculator
    ) {}

    /**
     * Calculate the quotation
     * 
     * @param QuotationRequest $request
     * @return JsonResponse
     */
    public function calculate(QuotationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $total = $this->quotationCalculator->calculate($data);

        $quotation = $this->saveQuotation($data, $total);

        return response()->json([
            'total' => $total,
            'currency_id' => $data['currency_id'],
            'quotation_id' => $quotation->id,
        ], 200);
    }

    /**
     * Save the quotation
     * 
     * @param array $data
     * @param float $total
     * @return Quotation 
     */
    private function saveQuotation(array $data, float $total): Quotation
    {
        try {
            return Quotation::create([
                'age' => $data['age'],
                'currency_id' => $data['currency_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'trip_length' => $this->quotationCalculator->getTripLength($data['start_date'], $data['end_date']),
                'total_cost' => $total,
            ]); 
        } catch (\Exception $e) {
            throw new \Exception('Failed to save quotation: ' . $e->getMessage());
        }
    }
}