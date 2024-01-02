<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

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
        try {
            $this->setSell();
            $this->setBuy();
        } catch (Exception $exception) {
            Log::error($exception);
        }
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

    protected function formattedValues(): string
    {
        $percentage = $this->calcPercentage();

        return format_currency($this->sell) . ' / ' . format_currency($this->buy) . ' = ' . $percentage . '%';
    }

    public function renderBlock(): string
    {
        if (!$this->sell || !$this->buy) {
            return '
            <div class="px-1 bg-red-300 text-black w-10 font-bold">' . $this->getLabel() . '</div>
            <span class="ml-1 text-red-700">Error</span>';
        }

        return '
            <div class="px-1 bg-green-300 text-black w-10 font-bold">' . $this->getLabel() . '</div>
            <span class="ml-1 text-green-700">' . $this->formattedValues() . '</span>';
    }
}
