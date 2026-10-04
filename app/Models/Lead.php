<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'source',
        'status',
        'assigned_to',
        'follow_up_date',
        'notes',
        'customer_id',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }
}
