<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocItem extends Model
{
    protected $fillable = ['department_id','doc_type_id','name','slug','is_active'];

    protected static function booted()
    {
        static::saving(function (self $m) {
            $m->slug = Str::slug($m->name);
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
