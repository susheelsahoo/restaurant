<?php

namespace App\Http\Controllers\Mobile\Concerns;

trait FormatsMobileValues
{
    protected function formatMoney(float $amount): string
    {
        return config('app.price_sign') . ' ' . number_format($amount, 2);
    }

    protected function formatQuantity(float $quantity): string
    {
        return floor($quantity) === $quantity
            ? number_format($quantity, 0)
            : rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
    }
}
