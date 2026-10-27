<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['available' => 'boolean', 'price_minor' => 'integer', 'previous_price_minor' => 'integer', 'version' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->public_id ??= (string) Str::ulid());
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasOne(Media::class)->where('kind', 'product');
    }

    public function publicUrl(Shop $shop): string
    {
        return url("/{$shop->slug}/p/{$this->public_id}/{$this->slug}");
    }
}
