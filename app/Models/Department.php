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

    /* ---------------------------------
     | Slug otomatis dari name
     |----------------------------------*/
    protected static function booted(): void
    {
        static::saving(function (self $m) {
            if (blank($m->slug) && filled($m->name)) {
                $m->slug = Str::slug($m->name);
            }
        });
    }

    /* ---------------------------------
     | Route binding pakai slug
     |----------------------------------*/
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------------------------------
     | Scopes
     |----------------------------------*/
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /* ---------------------------------
     | Relasi
     |----------------------------------*/

    // DocType lewat pivot (tegasin nama tabel pivot supaya eksplisit)
    public function docTypes(): BelongsToMany
    {
        return $this->belongsToMany(DocType::class, 'department_doc_type', 'department_id', 'doc_type_id')
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

    // Semua user yang ter‐asosiasi (punya role di pivot department_user)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Hanya admin departemen — harden: LOWER/TRIM biar tahan typo casing/spasi
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withPivot('role')
            ->whereRaw("LOWER(TRIM(department_user.role)) = 'admin'")
            ->withTimestamps();
    }

    // Grant ACL per user (department_user_access)
    public function accessGrants(): HasMany
    {
        return $this->hasMany(DepartmentUserAccess::class, 'department_id');
    }
}
