<?php

namespace App\Support;

class Money
{
    /**
     * Format whole integer minor units into formatted IDR currency.
     * Example: 1500000 -> "Rp 1.500.000"
     */
    public static function format(?int $amount, bool $withPrefix = true): string
    {
        if ($amount === null) {
            return '-';
        }

        $formatted = number_format($amount, 0, ',', '.');

        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }

    /**
     * Parse formatted string back to whole integer minor unit.
     */
    public static function parse(string|int|float|null $input): int
    {
        if ($input === null) {
            return 0;
        }

        if (is_int($input)) {
            return $input;
        }

        $cleaned = preg_replace('/[^0-9]/', '', (string) $input);

        return (int) $cleaned;
    }
}
