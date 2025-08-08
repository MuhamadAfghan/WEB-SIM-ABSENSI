<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date-start',
        'date-end',
        'type',
        'is_approved',
        'description',
        'upload_attachment'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
