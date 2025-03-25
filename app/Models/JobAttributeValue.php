<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAttributeValue extends Model
{
    protected $fillable = ['job_id', 'attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function getTypedValueAttribute()
    {
        return match ($this->attribute->type) {
            'number' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'date' => \Carbon\Carbon::parse($this->value),
            'select' => $this->value,
            'text' => $this->value,
            default => $this->value,
        };
    }
}
