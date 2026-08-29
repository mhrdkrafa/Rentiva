<?php

use App\Support\Money;

test('money helper formats integer amount into IDR format', function () {
    expect(Money::format(1500000))->toBe('Rp 1.500.000')
        ->and(Money::format(2350000, withPrefix: false))->toBe('2.350.000')
        ->and(Money::format(0))->toBe('Rp 0')
        ->and(Money::format(null))->toBe('-');
});

test('money helper parses formatted string into integer minor units', function () {
    expect(Money::parse('1.500.000'))->toBe(1500000)
        ->and(Money::parse('Rp 2.750.000'))->toBe(2750000)
        ->and(Money::parse(500000))->toBe(500000)
        ->and(Money::parse(null))->toBe(0);
});
