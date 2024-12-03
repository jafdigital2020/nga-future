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

            $deviceUsers = $zk->getUsers();
            $zk->disconnect();

            foreach ($deviceUsers as $deviceUser) {
                $user = User::where('name', $deviceUser['name'])->first();

                if ($user) {
                    $user->devices()->syncWithoutDetaching([
                        $device->id => ['biometric_user_id' => $deviceUser['userid']],
                    ]);
                    $this->info("Synced biometric ID {$deviceUser['userid']} for user {$user->name}");
                } else {
                    $this->warn("No match found for biometric ID {$deviceUser['userid']}.");
                }
            }
        }

        $this->info('User synchronization completed!');
    }
}
