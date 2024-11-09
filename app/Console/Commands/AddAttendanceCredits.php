<?php

namespace App\Console\Commands;

use DB;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\AttendanceCredit;

class AddAttendanceCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

     protected $signature = 'credits:add';
     protected $description = 'Add 3 attendance credits to all users';

    /**
     * The console command description.
     *
     * @var string
     */

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
        $users = User::all();
    
        foreach ($users as $user) {
            // Retrieve the existing AttendanceCredit record or create a new one
            $attendanceCredit = AttendanceCredit::firstOrCreate(
                ['user_id' => $user->id],
                ['attendanceCredits' => 3] // Set to 3 if no record exists
            );
    
            // Update the attendanceCredits to be exactly 3
            $attendanceCredit->attendanceCredits = 3;
            $attendanceCredit->save();
        }
    
        $this->info('Attendance credits have been set to 3 for all users.');
    }

}
