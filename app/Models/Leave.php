<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\LeaveTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'tblleaves';

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date'
    ];

    // Define a relationship to the Employee model
    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveTypes::class, 'leave_type');
    }

     // Method to update leave balance for employee
     public function updateEmployeeLeaveBalance()
     {
         $employee = $this->employee;
         $leaveType = $this->leaveType;
 
         if ($employee && $leaveType) {
             $employee->leave_balance += $leaveType->duration;
             $employee->save();
         }
     }

}
