<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocType extends Model {
  protected $fillable = ['name','slug'];
  public function departments(){ return $this->belongsToMany(Department::class)->withPivot(['is_active','sort_order']); }
  public function items(){ return $this->hasMany(DocItem::class); }
  public function documents(){ return $this->hasMany(Document::class); }
}
