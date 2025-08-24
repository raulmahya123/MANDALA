<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormDefinition extends Model {
  protected $fillable=['department_id','doc_type_id','doc_item_id','title','slug','is_active'];
  public function department(){ return $this->belongsTo(Department::class); }
  public function docType(){ return $this->belongsTo(DocType::class); }
  public function item(){ return $this->belongsTo(DocItem::class,'doc_item_id'); }
  public function fields(){ return $this->hasMany(FormField::class)->orderBy('sort_order'); }
  public function entries(){ return $this->hasMany(FormEntry::class); }
  public function getRouteKeyName(){ return 'slug'; }
}
