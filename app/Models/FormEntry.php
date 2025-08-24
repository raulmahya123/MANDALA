<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormEntry extends Model {
  protected $fillable=['form_definition_id','user_id','status','submitted_at','approved_at','rejected_at'];
  protected $casts=['submitted_at'=>'datetime','approved_at'=>'datetime','rejected_at'=>'datetime'];
  public function form(){ return $this->belongsTo(FormDefinition::class,'form_definition_id'); }
  public function user(){ return $this->belongsTo(User::class); }
  public function values(){ return $this->hasMany(FormEntryValue::class); }
  public function approvals(){ return $this->hasMany(FormApproval::class); }
}
