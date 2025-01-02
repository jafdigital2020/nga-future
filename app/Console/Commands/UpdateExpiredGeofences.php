<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\GeofencingSetting;

class UpdateExpiredGeofences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geofencing:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update geofence status to "Expired" when the expiration date is met';

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
        $today = Carbon::now()->startOfDay(); // Get today's date
        $expiredGeofences = GeofencingSetting::where('status', '!=', 'Expired')
            ->whereDate('expiration_date', '<', $today)
            ->update(['status' => 'Expired']);

        $this->info("Updated $expiredGeofences geofences to 'Expired'.");
    }
}
