<?php

if (! function_exists('format_uah')) {
    /**
     * Format a value into UAH format.
     * Replaces deprecated money_format PHP helper.
     *
     * @param float $value
     * @return string
     */
    function format_uah(float $value): string
    {
        return (new \NumberFormatter(config('app.locale'), \NumberFormatter::CURRENCY))
            ->formatCurrency($value, 'UAH');
    }
}


if (! function_exists('format_currency')) {
    function format_currency(float $value): string
    {
        return number_format($value, 2);
    }
}
