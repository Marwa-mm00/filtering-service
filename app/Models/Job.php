<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Events\JobAttempted;

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

    public function attributes()
    {
        return $this->hasMany(JobAttributeValue::class);
    }
}
