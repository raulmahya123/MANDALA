<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // gunakan jika kamu mengelola role di users table
    ];

    /**
     * Hidden attributes for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /* =======================================================
     |  RELATIONS
     |=======================================================*/

    /**
     * Grant/ACL per user (tabel: department_user_access)
     */
    public function acl(): HasMany
    {
        return $this->hasMany(DepartmentUserAccess::class, 'user_id');
    }

    /**
     * Semua departments via pivot department_user (memuat role di pivot).
     * Tabel: department_user (user_id, department_id, role, timestamps)
     */
    public function departments(): BelongsToMany
    {
        // qualify kolom untuk hindari ambiguous
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id')
            ->withPivot('role')
            ->withTimestamps()
            ->select('departments.id', 'departments.name', 'departments.slug');
    }

    /**
     * Hanya departments di mana user berperan sebagai admin (role = 'admin').
     * Sekalian qualify select + orderBy untuk cegah ambiguous & konsisten.
     */
    public function adminDepartments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id')
            ->withPivot('role')
            ->wherePivot('role', 'admin')
            ->withTimestamps()
            ->select('departments.id', 'departments.name', 'departments.slug')
            ->orderBy('departments.name');
    }

    /* =======================================================
     |  HELPERS
     |=======================================================*/

    /**
     * Kumpulan ID departemen yang bisa diedit user ini
     * (gabungan admin dept + ACL can_edit).
     */
    public function editableDepartmentIds()
    {
        // dept dari role admin
        $adminIds = $this->adminDepartments()->pluck('departments.id');

        // dept dari ACL yang mengizinkan edit
        $aclIds = $this->acl()
            ->where('can_edit', true)
            ->pluck('department_id');

        return $adminIds->merge($aclIds)->unique()->values();
    }

    /**
     * Apakah user boleh melihat resource pada scope tertentu.
     * View access: super_admin | admin dept | ACL (item > doc_type > department).
     */
    public function hasViewAccess(int $departmentId, ?int $docTypeId = null, ?int $itemId = null): bool
    {
        if (($this->role ?? null) === 'super_admin') {
            return true;
        }

        // admin dept pemilik
        if ($this->adminDepartments()->whereKey($departmentId)->exists()) {
            return true;
        }

        // ACL bertingkat: item → doc_type → department
        if ($itemId && $this->acl()->where([
            'department_id' => $departmentId,
            'scope_type'    => 'item',
            'scope_id'      => $itemId,
        ])->exists()) {
            return true;
        }

        if ($docTypeId && $this->acl()->where([
            'department_id' => $departmentId,
            'scope_type'    => 'doc_type',
            'scope_id'      => $docTypeId,
        ])->exists()) {
            return true;
        }

        return $this->acl()->where([
            'department_id' => $departmentId,
            'scope_type'    => 'department',
        ])->exists();
    }

    /**
     * Apakah user boleh mengedit pada scope (dept/docType/item) tertentu.
     * Edit access: super_admin | admin dept | ACL can_edit (department OR doc_type OR item).
     */
    public function hasEditAccess(int $departmentId, ?int $docTypeId = null, ?int $itemId = null): bool
    {
        if (($this->role ?? null) === 'super_admin') {
            return true;
        }

        // admin dept pemilik
        if ($this->adminDepartments()->whereKey($departmentId)->exists()) {
            return true;
        }

        // ACL can_edit dengan prioritas: department OR (doc_type match) OR (item match)
        $q = $this->acl()
            ->where('department_id', $departmentId)
            ->where('can_edit', true);

        return $q->where(function ($qq) use ($docTypeId, $itemId) {
            // level department
            $qq->where('scope_type', 'department');

            // level doc_type
            if ($docTypeId) {
                $qq->orWhere(function ($w) use ($docTypeId) {
                    $w->where('scope_type', 'doc_type')
                      ->where('scope_id', $docTypeId);
                });
            }

            // level item
            if ($itemId) {
                $qq->orWhere(function ($w) use ($itemId) {
                    $w->where('scope_type', 'item')
                      ->where('scope_id', $itemId);
                });
            }
        })->exists();
    }
}
