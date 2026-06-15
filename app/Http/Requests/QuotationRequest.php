<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationRequest extends FormRequest
{
    
    /**
     * Prepare the data for validation
     * 
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('age')) {
            $this->merge([
                'age' => preg_replace('/\s+/', '', (string) trim($this->input('age'), ',')),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'age' => [
                'required',
                'string',
                'regex:/^[1-9][0-9]?(,[1-9][0-9]?)*$/',
                function ($attribute, $value, $fail) {
                    $ages = explode(',', $value);
                    foreach ($ages as $age) {
                        if (!ctype_digit(trim($age))) {
                            $fail('Age must be a number');
                            return;
                        }

                        $age = (int) trim($age);
                        if ($age < 18 || $age > 70) {
                            $fail('Age must be between 18 and 70');
                            return;
                        }
                    }
                },
            ],
            'currency_id' => ['required', 'string', Rule::enum(Currency::class)],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'age.required' => 'Age is required',
            'currency_id.required' => 'Currency is required',
            'currency_id.in' => 'Currency must be EUR, GBP or USD',
            'start_date.required' => 'Start date is required',
            'start_date.date' => 'Start date must be a date',
            'end_date.required' => 'End date is required',
            'end_date.date' => 'End date must be a date',
            'end_date.after_or_equal' => 'End date must be after start date',
        ];
    }
}