<?php

namespace App\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CheckCommand extends Command
{
    const CACHE_LIFETIME = 5 * 60;

    const ISO_4217 = [
        'UAH' => self::UAH,
        'USD' => self::USD,
        'EUR' => self::EUR,
    ];

    const UAH = 980;
    const USD = 840;
    const EUR =978;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'check';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sel = $this->getPbFopSel();
        $buyPB = $this->getPbBuy();
        $buyMono = $this->getMonoBuy();

        $percentagePB = round(($buyPB - $sel)/$buyPB * 100, 2);
        $percentageMono = round(($buyMono - $sel)/$buyMono * 100 + 0.5, 2);

        $this->table(
            ['Sell FOP', 'Buy PB', 'PB %', 'Buy Mono', 'Mono +0.5%'],
            [
                [
                    format_uah($sel),
                    format_uah($buyPB),
                    $percentagePB.'%',
                    format_uah($buyMono),
                    $percentageMono.'%'
                ]
            ],
        );

        $this->notify("PB: " . $percentagePB . " %", "Mono: " . $percentageMono . " %");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private function getPbFopSel(string $currency = 'EUR'): float
    {
        $data = $this->getPbBusinessData();

        return (float) $data[$currency]['B']['rate'];
    }

    private function getPbBuy(string $currency = 'EUR'): float
    {
        $collection = collect($this->getPbUserData());

        return (float) $collection->firstWhere('ccy', $currency)['sale'];
    }

    private function getMonoBuy(string $currency = 'EUR'): float
    {
        $rate = collect($this->getMonoData())
            ->first(fn($item) =>
                $item['currencyCodeA'] === self::ISO_4217[$currency] && $item['currencyCodeB'] === self::UAH
            );

        return (float) $rate['rateSell'];
    }

    private function getPbBusinessData()
    {
        $url = 'https://acp.privatbank.ua/api/proxy/currency/';

        return Cache::remember('pbFop', self::CACHE_LIFETIME, fn() =>
            Http::withHeaders(['token' => env('PB_TOKEN')])->get($url)->json()
        );
    }

    private function getPbUserData()
    {
        $url = 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=11';

        return Cache::remember('pb', self::CACHE_LIFETIME, fn() => Http::get($url)->json());

    }

    private function getMonoData()
    {
        $url = 'https://api.monobank.ua/bank/currency';

        return Cache::remember('mono', self::CACHE_LIFETIME, fn() => Http::get($url)->json());
    }
}
