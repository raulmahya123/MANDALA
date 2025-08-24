<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model {
  protected $fillable=['form_definition_id','label','name','type','options','required','sort_order'];
  protected $casts=['options'=>'array','required'=>'boolean'];
  public function form(){ return $this->belongsTo(FormDefinition::class,'form_definition_id'); }
}
