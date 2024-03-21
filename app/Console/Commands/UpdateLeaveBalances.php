<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Leave;

class UpdateLeaveBalances extends Command
{
    protected $signature = 'leave:balance';

    protected $description =  'Update leave balances for employees on annual leave';

    public function handle()
    {
        // Get all annual leaves that have not yet expired
        $annualLeaves = Leave::where('leave_type', 1) // Assuming annual leave type ID is 2
            ->where('end_date', '>=', Carbon::today())
            ->get();

        // Update leave balances for each annual leave
        foreach ($annualLeaves as $leave) {
            // Calculate remaining days of leave
            $remainingDays = $leave->end_date->diffInDays(Carbon::today());

            // Update leave balance for the employee
            $employee = $leave->employee;
            $newLeaveBalance = max(0, $employee->leave_balance - $remainingDays);
            $employee->update(['leave_balance' => $newLeaveBalance]);
        }

        $this->info('Leave balances updated successfully.');
    }
}
