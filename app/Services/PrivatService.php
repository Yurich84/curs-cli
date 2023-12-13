<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PrivatService extends BankService
{
    const API_URL = 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=11';

    const BUSINESS_API_URL = 'https://acp.privatbank.ua/api/proxy/currency/';

    protected function getLabel(): string
    {
        return 'PB ' . self::CURRENCY_SIGN[$this->currency];
    }

    protected function setSell()
    {
        $data = $this->getBusinessData();

        $this->sell = (float) $data[$this->currency]['B']['rate'];
    }

    protected function setBuy()
    {
        $collection = collect($this->getUserData());

        $this->buy = (float) $collection->firstWhere('ccy', $this->currency)['sale'];
    }


    private function getBusinessData()
    {
        return Cache::remember('pbFop', self::CACHE_LIFETIME, fn() =>
            Http::withHeaders(['token' => env('PB_TOKEN')])->get(self::BUSINESS_API_URL)->json()
        );
    }

    private function getUserData()
    {
        return Cache::remember('pb', self::CACHE_LIFETIME, fn() => Http::get(self::API_URL)->json());

    }
}
