<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_definition_id',
        'label',
        'name',
        'type',
        'options',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'options'  => 'array',
        'required' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(FormDefinition::class, 'form_definition_id');
    }

    // Mutator: pastikan selalu array
    public function setOptionsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['options'] = json_encode(array_values($value));
        } elseif (is_string($value) && strlen(trim($value)) > 0) {
            $decoded = json_decode($value, true);
            $this->attributes['options'] = json_encode(is_array($decoded) ? $decoded : []);
        } else {
            $this->attributes['options'] = json_encode([]);
        }
    }
}
