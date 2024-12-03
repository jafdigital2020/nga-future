<?php

namespace App\Console\Commands;

use App\Models\User;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\BiometricDevice;
use Illuminate\Console\Command;
use App\Models\EmployeeAttendance;
use Illuminate\Support\Facades\Log;

class FetchAttendance extends Command
{
    protected $signature = 'attendance:fetch';
    protected $description = 'Fetch attendance logs from biometric devices';

    public function handle()
    {
        $devices = BiometricDevice::all();

        foreach ($devices as $device) {
            $this->info("Connecting to device: {$device->device_name}");
            Log::info("Attempting to connect to device: {$device->device_name} (IP: {$device->ip_address}, Port: {$device->port})");

            try {
                $zk = new ZKTeco($device->ip_address, $device->port);

                if (!$zk->connect()) {
                    $this->warn("Failed to connect to device: {$device->device_name}");
                    Log::error("Failed to connect to device: {$device->device_name} (IP: {$device->ip_address})");
                    continue;
                }

                Log::info("Successfully connected to device: {$device->device_name}");
                $logs = $zk->getAttendance();
                $zk->disconnect();

                Log::info("Fetched " . count($logs) . " attendance logs from device: {$device->device_name}");

                foreach ($logs as $log) {
                    Log::debug("Processing log: " . json_encode($log));

                    $user = User::whereHas('devices', function ($query) use ($log, $device) {
                        $query->where('biometric_user_id', $log['userid'])
                              ->where('biometric_device_id', $device->id);
                    })->first();

                    if ($user) {
                        EmployeeAttendance::updateOrCreate(
                            [
                                'users_id' => $user->id,
                                'date' => $log['timestamp']->format('Y-m-d'),
                                'biometric_device_id' => $device->id,
                            ],
                            [
                                'timeIn' => $log['timestamp']->format('H:i:s'),
                            ]
                        );
                        $this->info("Attendance saved for {$user->name}");
                        Log::info("Attendance saved for user: {$user->name} (Biometric ID: {$log['userid']})");
                    } else {
                        $this->warn("No matching user for biometric ID {$log['userid']}.");
                        Log::warning("No matching user found for biometric ID: {$log['userid']} on device: {$device->device_name}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error occurred while processing device: {$device->device_name}");
                Log::error("Exception on device: {$device->device_name} - " . $e->getMessage());
            }
        }

        $this->info('Attendance fetching completed!');
        Log::info('Attendance fetching process completed.');
    }
}
