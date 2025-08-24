<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FormDefinition extends Model
{
    protected $fillable = [
        'department_id',
        'doc_type_id',
        'doc_item_id',
        'title',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== Relationships =====
    public function department(){ return $this->belongsTo(Department::class); }
    public function docType(){ return $this->belongsTo(DocType::class); }
    public function item(){ return $this->belongsTo(DocItem::class,'doc_item_id'); }
    public function fields(){ return $this->hasMany(FormField::class)->orderBy('sort_order'); }
    public function entries(){ return $this->hasMany(FormEntry::class); }

    // Route binding by slug
    public function getRouteKeyName(){ return 'slug'; }

    // ===== Scopes =====
    public function scopeActive($q){ return $q->where('is_active', true); }

    // ===== Slug handling =====
    protected static function booted()
    {
        // Normalisasi slug tiap kali set
        static::saving(function (self $m) {
            // Jika ada input slug, normalisasi dulu
            if (!empty($m->slug)) {
                $m->slug = static::normalizeSlug($m->slug);
            }

            // Jika slug kosong, generate dari title (boleh beda dari title, ada suffix)
            if (empty($m->slug)) {
                $base = Str::slug($m->title ?? Str::random(6));
                // kasih suffix acak kecil supaya tidak 1:1 dengan title
                $base = $base ?: Str::lower(Str::random(6));
                $m->slug = $base.'-'.Str::lower(Str::random(5));
            }

            // Pastikan unik (hindari tabrakan dengan record lain)
            $m->slug = static::generateUniqueSlug($m->slug, $m->getKey());
        });
    }

    protected static function normalizeSlug(string $slug): string
    {
        // hapus aksen, tolower, ganti non-alnum jadi dash, trim dash
        $s = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$slug);
        $s = strtolower($s ?? $slug);
        $s = preg_replace('/[^a-z0-9]+/','-',$s);
        $s = preg_replace('/(^-|-$)/','',$s);
        return $s ?: Str::lower(Str::random(6));
    }

    protected static function generateUniqueSlug(string $desired, $ignoreId = null): string
    {
        $slug = $desired;
        $i = 2;

        // Jika sudah ada yang pakai slug yang sama (selain diri sendiri), tambahkan -2, -3, dst
        while (static::query()
            ->when($ignoreId, fn($q) => $q->where('id','!=',$ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            // kalau sudah ada suffix numeric, ganti angkanya; kalau tidak, tambahkan
            if (preg_match('/-\d+$/', $slug)) {
                $slug = preg_replace('/-\d+$/', '-'.$i, $slug);
            } else {
                $slug = $desired.'-'.$i;
            }
            $i++;
            if ($i > 50) { // guard; kalau kebanyakan, pakai random
                $slug = $desired.'-'.Str::lower(Str::random(5));
                break;
            }
        }

        return $slug;
    }
}
