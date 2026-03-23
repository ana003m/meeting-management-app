<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'created_by',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class)->orderBy('order');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function notes()
    {
        return $this->hasMany(MeetingNote::class);
    }

    public function latestNotes()
    {
        return $this->hasOne(MeetingNote::class)->latest();
    }

    public function minutes()
    {
        return $this->hasMany(MeetingMinute::class);
    }

    public function latestMinutes()
    {
        return $this->hasOne(MeetingMinute::class)->latest();
    }
}
