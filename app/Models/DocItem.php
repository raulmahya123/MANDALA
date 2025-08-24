<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocItem extends Model {
  protected $fillable = ['department_id','doc_type_id','name','slug','is_active'];
  public function department(){ return $this->belongsTo(Department::class); }
  public function docType(){ return $this->belongsTo(DocType::class); }
  public function documents(){ return $this->hasMany(Document::class); }
}

