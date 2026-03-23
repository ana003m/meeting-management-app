<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingNote extends Model
{
    use HasFactory;

    protected $table = 'meeting_notes';

    protected $fillable = [
        'meeting_id',
        'user_id',
        'content',
        'is_final'
    ];

    protected $casts = [
        'is_final' => 'boolean',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generatedMinutes()
    {
        return $this->hasOne(MeetingMinute::class, 'generated_from_note_id');
    }
}













