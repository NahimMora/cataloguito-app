<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['appearance' => 'array', 'appearance_draft' => 'array', 'email_orders' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->public_id ??= (string) Str::ulid());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function productLimit(): int
    {
        return $this->plan === 'pro' ? 500 : 50;
    }

    public function byteLimit(): int
    {
        return $this->plan === 'pro' ? 1_000_000_000 : 50_000_000;
    }

    public function publicUrl(): string
    {
        return url('/'.$this->slug);
    }

    public static function defaultAppearance(): array
    {
        return ['style' => 'casa', 'theme' => 'minimal', 'accent' => '#b34b35', 'tagline' => 'Una selección hecha para vos'];
    }

    public function currentStyle(): string
    {
        $style = $this->appearance_draft['style'] ?? $this->appearance['style'] ?? null;

        return array_key_exists((string) $style, config('cataloguito.styles')) ? $style : 'casa';
    }

    public function logoUrl(): ?string
    {
        return $this->media->firstWhere('kind', 'logo')?->url();
    }
}
