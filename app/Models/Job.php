<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'employment_jobs';

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }
}
