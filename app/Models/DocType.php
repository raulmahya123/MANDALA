<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocType extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Pakai slug untuk route model binding: {docType:slug}
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Normalisasi slug saat diset (baik create maupun update).
     * - Jika kosong, fallback dari name.
     * - Tidak menimpa slug manual kalau sudah ada.
     */
    public function setSlugAttribute($value): void
    {
        $value = $value ?: ($this->attributes['name'] ?? null);
        $this->attributes['slug'] = $value ? Str::slug($value) : null;
    }

    /**
     * Saat creating: pastikan slug TERISI & UNIK.
     * (Kalau double di DB, tambahkan suffix -2, -3, dst.)
     */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            // pastikan ada slug (kalau user tak isi, turunkan dari name)
            if (blank($m->slug)) {
                $m->slug = Str::slug($m->name);
            }
            $m->slug = static::uniqueSlug($m->slug);
        });

        static::updating(function (self $m) {
            // Jika user mengubah slug atau name, normalisasi & jaga keunikan
            // Hanya cek unik kalau slug berubah
            if ($m->isDirty('slug')) {
                $m->slug = Str::slug($m->slug ?: $m->name);
                $m->slug = static::uniqueSlug($m->slug, $m->id);
            }
        });
    }

    /**
     * Generate slug unik dengan menambahkan suffix numerik jika perlu.
     */
    protected static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $i = 2;

        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    // ================= Relations =================

    public function departments()
    {
        return $this->belongsToMany(Department::class)
                    ->withPivot(['is_active','sort_order'])
                    ->withTimestamps();
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
