<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model {
  protected $fillable = ['department_id','doc_type_id','doc_item_id','title','slug','summary','file_path','file_ext','status','published_at','uploaded_by'];
  protected $casts = ['published_at'=>'datetime'];
  public function department(){ return $this->belongsTo(Department::class); }
  public function docType(){ return $this->belongsTo(DocType::class); }
  public function item(){ return $this->belongsTo(DocItem::class,'doc_item_id'); }
  public function uploader(){ return $this->belongsTo(User::class,'uploaded_by'); }
  public function scopePublished($q){ return $q->where('status','open')->whereNotNull('published_at'); }
}

