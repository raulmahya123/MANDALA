<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model {
  protected $fillable = ['name','slug','is_active'];
  public function docTypes(){ return $this->belongsToMany(DocType::class)->withPivot(['is_active','sort_order'])->withTimestamps(); }
  public function items(){ return $this->hasMany(DocItem::class); }
  public function documents(){ return $this->hasMany(Document::class); }
  public function admins(){ return $this->belongsToMany(User::class,'department_admins'); }
}
