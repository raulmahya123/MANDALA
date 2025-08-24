<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model {
  protected $fillable=['user_id','action','auditable_type','auditable_id','meta'];
  protected $casts=['meta'=>'array'];
  public function auditable(){ return $this->morphTo(); }
  public function user(){ return $this->belongsTo(User::class); }
}
