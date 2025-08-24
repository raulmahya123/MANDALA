<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormApproval extends Model {
  protected $fillable=['form_entry_id','reviewer_id','action','notes'];
  public function entry(){ return $this->belongsTo(FormEntry::class,'form_entry_id'); }
  public function reviewer(){ return $this->belongsTo(User::class,'reviewer_id'); }
}
