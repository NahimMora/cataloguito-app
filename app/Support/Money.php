<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class Money
{
    public static function parse(string|int|null $input, string $field = 'price'): ?int
    {
        if ($input === null || trim((string) $input) === '') {
            return null;
        }
        $value = trim((string) $input);
        if (! preg_match('/^\d{1,9}(?:[.,]\d{1,2})?$/D', $value)) {
            throw ValidationException::withMessages([$field => 'Ingresá el importe sin separadores de miles (ej. 12500,50).']);
        }
        $parts = explode('.', str_replace(',', '.', $value));
        $minor = ((int) $parts[0]) * 100 + (int) str_pad($parts[1] ?? '', 2, '0');
        if ($minor < 1 || $minor > 99_999_999_999) {
            throw ValidationException::withMessages([$field => 'El precio debe ser mayor a cero y menor a mil millones.']);
        }

        return $minor;
    }

    public static function format(int $minor): string
    {
        return '$ '.number_format($minor / 100, $minor % 100 ? 2 : 0, ',', '.');
    }

    public static function input(?int $minor): string
    {
        return $minor === null ? '' : sprintf('%d.%02d', intdiv($minor, 100), $minor % 100);
    }
}
