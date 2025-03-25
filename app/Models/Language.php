<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }
}
