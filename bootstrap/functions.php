<?php

if (! function_exists('format_uah')) {
    /**
     * Format a value into UAH format.
     * Replaces deprecated money_format PHP helper.
     *
     * @param float $value
     * @return string
     */
    function format_uah(float $value)
    {
        return (new \NumberFormatter(config('app.locale'), \NumberFormatter::CURRENCY))
            ->formatCurrency($value, 'UAH');
    }
}
