<?php

namespace App\Console\Commands;

use App\Models\User;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\BiometricDevice;
use Illuminate\Console\Command;

class SyncBiometricUsers extends Command
{
    protected $signature = 'biometric:sync-users';
    protected $description = 'Sync biometric users with Laravel users';

    public function handle()
    {
        $devices = BiometricDevice::all();

        foreach ($devices as $device) {
            $this->info("Connecting to device: {$device->device_name}");

            $zk = new ZKTeco($device->ip_address, $device->port);

            if (!$zk->connect()) {
                $this->warn("Failed to connect to device: {$device->device_name}");
                continue;
            }

            // Fetch users from the device
            $deviceUsers = $zk->getUser(); // Correct method to fetch users
            $zk->disconnect();

            foreach ($deviceUsers as $deviceUser) {
                // Hinahanap ang user gamit ang empNumber
                $user = User::where('empNumber', $deviceUser['userid'])->first();
            
                if ($user) {
                    // Sync sa pivot table gamit ang empNumber
                    $user->devices()->syncWithoutDetaching([
                        $device->id => ['biometric_user_id' => $deviceUser['userid']],
                    ]);
                    $this->info("Synced biometric ID {$deviceUser['userid']} for user {$user->empNumber}");
                } else {
                    // Kapag walang match na user
                    $this->warn("No match found for biometric ID {$deviceUser['userid']}.");
                }
            }
        }

        $this->info('User synchronization completed!');
    }
    
}
