<?php

namespace App\Models;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveTypes extends Model
{
    use HasFactory;

    protected $table = 'tblleavestpes';

    protected $fillable = [
        'leave_type',
        'duration',
        'description'
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'leave_type');
    }
}
