<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['variants' => 'array'];
    }

    public function url(int $size = 640): string
    {
        return route('media.show', [$this->id, $size]);
    }

    public function srcset(): string
    {
        return collect($this->variants)->map(fn ($path, $size) => $this->url((int) $size).' '.$size.'w')->implode(', ');
    }
}
