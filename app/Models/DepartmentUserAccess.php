<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentUserAccess extends Model
{
    protected $table = 'department_user_access';
    protected $fillable = [
        'user_id','department_id','scope_type','scope_id','can_edit'
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function department(){ return $this->belongsTo(Department::class); }
}
