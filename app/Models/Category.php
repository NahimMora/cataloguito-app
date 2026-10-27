<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->public_id ??= (string) Str::ulid());
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function publicUrl(Shop $shop): string
    {
        return url("/{$shop->slug}/c/{$this->public_id}/{$this->slug}");
    }
}
