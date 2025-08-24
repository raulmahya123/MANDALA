<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocItem extends Model
{
    protected $fillable = [
        'department_id',
        'doc_type_id',
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (self $m) {
            // handle slug
            if (blank($m->slug) && filled($m->name)) {
                $m->slug = Str::slug($m->name);
            } else {
                $m->slug = Str::slug($m->slug);
            }

            // handle is_active (default true kalau belum diset)
            if (is_null($m->is_active)) {
                $m->is_active = true;
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function docType()
    {
        return $this->belongsTo(DocType::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
