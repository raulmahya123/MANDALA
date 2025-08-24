<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocType extends Model
{
    protected $fillable = ['name','slug'];

    protected static function booted()
    {
        static::saving(function (self $m) {
            // jaga-jaga kalau slug belum diisi atau name berubah
            $m->slug = Str::slug($m->name);
        });
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class)
                    ->withPivot(['is_active','sort_order']);
    }

    public function items()
    {
        return $this->hasMany(DocItem::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
