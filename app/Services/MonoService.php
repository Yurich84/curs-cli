<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MonoService extends BankService
{
    const API_URL = 'https://api.monobank.ua/bank/currency';

    const ISO_4217 = [
        'UAH' => self::UAH,
        'USD' => self::USD,
        'EUR' => self::EUR,
    ];

    const UAH = 980;
    const USD = 840;
    const EUR =978;

    protected function getLabel(): string
    {
        return 'Mono ' . self::CURRENCY_SIGN[$this->currency];
    }

    protected function setSell()
    {
        $this->sell = (float) $this->getRate()['rateBuy'];
    }

    protected function setBuy()
    {
        $this->buy = (float) $this->getRate()['rateSell'];
    }

    private function getRate()
    {
        return collect($this->getData())
            ->first(fn($item) =>
                $item['currencyCodeA'] === self::ISO_4217[$this->currency] && $item['currencyCodeB'] === self::UAH
            );
    }

    private function getData()
    {
        return Cache::remember('mono', self::CACHE_LIFETIME, fn() => Http::get(self::API_URL)->json());
    }
}
