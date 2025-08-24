<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\DepartmentUserAccess;
// ...existing code...

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function acl()
    {
        return $this->hasMany(DepartmentUserAccess::class);
    }

    public function hasViewAccess(int $departmentId, ?int $docTypeId = null, ?int $itemId = null): bool
    {
        if ($this->role === 'super_admin') return true;

        // admin dept pemilik
        if ($this->adminDepartments()->whereKey($departmentId)->exists()) return true;

        // cek ACL bertahap: item > doc_type > department
        if ($itemId && $this->acl()->where([
            'department_id' => $departmentId,
            'scope_type' => 'item',
            'scope_id' => $itemId
        ])->exists()) return true;

        if ($docTypeId && $this->acl()->where([
            'department_id' => $departmentId,
            'scope_type' => 'doc_type',
            'scope_id' => $docTypeId
        ])->exists()) return true;

        return $this->acl()->where([
            'department_id' => $departmentId,
            'scope_type' => 'department'
        ])->exists();
    }

    public function hasEditAccess(int $departmentId, ?int $docTypeId = null, ?int $itemId = null): bool
    {
        if ($this->role === 'super_admin') return true;
        if ($this->adminDepartments()->whereKey($departmentId)->exists()) return true;

        $q = $this->acl()->where('department_id', $departmentId)->where('can_edit', true);

        return $q->where(function ($qq) use ($docTypeId, $itemId) {
            $qq->where('scope_type', 'department');

            if ($docTypeId) {
                $qq->orWhere(function ($w) use ($docTypeId) {
                    $w->where('scope_type', 'doc_type')->where('scope_id', $docTypeId);
                });
            }
            if ($itemId) {
                $qq->orWhere(function ($w) use ($itemId) {
                    $w->where('scope_type', 'item')->where('scope_id', $itemId);
                });
            }
        })->exists();
    }
    public function departments()
{
    return $this->belongsToMany(\App\Models\Department::class, 'department_user')
                ->withPivot('role')
                ->withTimestamps();
}

public function adminDepartments()
{
    return $this->belongsToMany(\App\Models\Department::class, 'department_user')
                ->withPivot('role')
                ->wherePivot('role', 'admin')
                ->withTimestamps();
}

}
