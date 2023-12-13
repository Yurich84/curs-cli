<?php

namespace App\Commands;

use App\Services\MonoService;
use App\Services\PrivatService;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Termwind\render;

class CheckCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'check {--n}';

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
        $pb_usd = (new PrivatService('USD'))->renderBlock();
        $pb_eur = (new PrivatService())->renderBlock();
        $mon_eur = (new MonoService())->renderBlock();

        render(<<<HTML
            <div class="py-1 ml-2">
                $pb_usd
                $pb_eur
                $mon_eur
            </div>
        HTML);
    }

    /**
     * Define the command's schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
