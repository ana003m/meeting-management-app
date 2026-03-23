<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'topic',
        'description',
        'duration_minutes',
        'order'
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
