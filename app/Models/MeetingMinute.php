<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    use HasFactory;

    protected $table = 'meeting_minutes';

    protected $fillable = [
        'meeting_id',
        'generated_from_note_id',
        'generated_by',
        'summary',
        'action_items',
        'decisions',
        'generated_at'
    ];

    protected $casts = [
        'action_items' => 'array',
        'decisions' => 'array',
        'generated_at' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function sourceNote()
    {
        return $this->belongsTo(MeetingNote::class, 'generated_from_note_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
