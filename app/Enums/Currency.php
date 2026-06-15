<?php

namespace App\Enums;

/**
 * Currency enum
 * 
 * @package App\Enums
 * @method static self EUR()
 * @method static self GBP()
 * @method static self USD()
 */
enum Currency: string
{
    case EUR = 'EUR';
    case GBP = 'GBP';
    case USD = 'USD';

    /**
     * Get all currency values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the human readable label for the currency.
     */
    public function label(): string
    {
        return match ($this) {
            self::EUR => 'Euro',
            self::GBP => 'British Pound',
            self::USD => 'US Dollar',
        };
    }
}
