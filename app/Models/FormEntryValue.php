<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormEntryValue extends Model {
  protected $fillable=['form_entry_id','form_field_id','value'];
  public function entry(){ return $this->belongsTo(FormEntry::class); }
  public function field(){ return $this->belongsTo(FormField::class); }
}