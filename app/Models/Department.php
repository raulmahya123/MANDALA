<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Department extends Model
{
    protected $fillable = ['name','slug','is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Isi slug otomatis dari name kalau slug kosong.
     */
    protected static function booted(): void
    {
        static::saving(function (self $m) {
            if (blank($m->slug) && filled($m->name)) {
                $m->slug = Str::slug($m->name);
            }
        });
    }

    /**
     * (Opsional) Pakai slug untuk route model binding.
     * Hapus method ini jika kamu tetap ingin pakai id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** ================== RELASI ================== */

    public function docTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocType::class)
            ->withPivot(['is_active','sort_order'])
            ->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(DocItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /** Semua user di departemen (punya role di pivot department_user) */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /** Hanya admin departemen (pivot role = admin) */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('role')
            ->wherePivot('role','admin')
            ->withTimestamps();
    }
}
