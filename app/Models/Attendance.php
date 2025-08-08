<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'longitude',
        'latitude',
        'date',
        'check_in_time',
        'check_out_time',
        'keterangan',
        'lokasi'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
