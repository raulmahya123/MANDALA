<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = ['name','slug','is_active'];

    public function docTypes(){ 
        return $this->belongsToMany(DocType::class)->withPivot(['is_active','sort_order'])->withTimestamps(); 
    }

    public function items(){ return $this->hasMany(DocItem::class); }
    public function documents(){ return $this->hasMany(Document::class); }

    // Semua user di departemen (punya role)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // Hanya admin departemen
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
                    ->withPivot('role')
                    ->wherePivot('role', 'admin')
                    ->withTimestamps();
    }
}
