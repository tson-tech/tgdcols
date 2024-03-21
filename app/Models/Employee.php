<?php

namespace App\Models;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'tblemployees';

    protected $fillable = [
        'firstname', 
        'lastname', 
        'first_appointment_date', 
        'last_leave_date', 
        'leave_balance', 
        'is_new_employee'
    ];

    // Define a relationship to the Leave model (assuming you have one)
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

}
