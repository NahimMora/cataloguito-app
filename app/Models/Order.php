<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['session_hash', 'payload_hash', 'idempotency_key'];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->public_id ??= (string) Str::ulid());
    }

    public const LABELS = ['pending' => 'Pendiente', 'confirmed' => 'Confirmado', 'completed' => 'Completado', 'cancelled' => 'Cancelado'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events()
    {
        return $this->hasMany(OrderEvent::class)->orderBy('id');
    }

    public function displayNumber(): string
    {
        return '#'.str_pad((string) $this->number, 6, '0', STR_PAD_LEFT);
    }
}
