<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_start',
        'work_finish',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rest()
    {
        return $this->hasMany(Rest::class);
    }

    public function scopeDateGroup($query)
    {
        $query->selectRaw('DATE(work_start) as date')
            ->groupBy('date')
            ->oldest('date');
    }

    public function scopeTimeFormat($query, $users)
    {
        $query->selectRaw('TIME(work_start) as start_time');
    }

    public function scopeWorkSearch($query, $index, $dates)
    {
        if (array_key_exists($index, $dates)) {
            $query->where('work_start', 'LIKE', $dates[$index] . '%');
        }
    }
}
