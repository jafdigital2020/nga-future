<?php

namespace App\Console\Commands;

use App\Models\OvertimeCredits;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetOvertimeCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overtime:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset overtime credits to 16:00:00 for all users every month';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        OvertimeCredits::query()->update(['otCredits' => '16:00:00']);

        $this->info('Overtime credits reset to 16:00:00 for all users.');
    }
}
