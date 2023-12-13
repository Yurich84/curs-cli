<?php

namespace App\Services;

abstract class BankService
{
    const CACHE_LIFETIME = 5 * 60;

    const CURRENCY_SIGN = [
        'UAH' => '₴',
        'USD' => '$',
        'EUR' => '€',
    ];

    protected float $sell;

    protected float $buy;

    public function __construct(protected string $currency = 'EUR')
    {
        $this->setSell();
        $this->setBuy();
    }

    abstract protected function setSell();

    abstract protected function setBuy();

    protected function getLabel(): string
    {
        return basename(self::class) . ' ' . self::CURRENCY_SIGN[$this->currency];
    }

    protected function calcPercentage(): float
    {
        return round(($this->buy - $this->sell)/$this->buy * 100, 2);
    }

    protected function formatValue(): string
    {
        $percentage = $this->calcPercentage();

        return format_currency($this->sell) . ' / ' . format_currency($this->buy) . ' = ' . $percentage . '%';
    }

    public function renderBlock(): string
    {
        $value = $this->formatValue();

        return '
            <div class="px-1 bg-green-300 text-black w-10 font-bold">' . $this->getLabel() . '</div>
            <span class="ml-1 text-green-700">' . $value . '</span>';
    }
}
