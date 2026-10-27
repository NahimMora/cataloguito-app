<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class Phone
{
    public const COUNTRIES = [
        '54' => 'Argentina',
        '598' => 'Uruguay',
        '56' => 'Chile',
        '595' => 'Paraguay',
        '591' => 'Bolivia',
        '51' => 'Perú',
        '55' => 'Brasil',
        '57' => 'Colombia',
        '52' => 'México',
        '1' => 'Estados Unidos / Canadá',
        '34' => 'España',
    ];

    public static function normalize(string $input, string $field = 'whatsapp'): string
    {
        $number = preg_replace('/[\s()+.\-]/', '', trim($input));
        if (! preg_match('/^[1-9][0-9]{8,14}$/D', $number)) {
            throw ValidationException::withMessages([$field => 'Usá el número internacional, por ejemplo 5491112345678.']);
        }

        return $number;
    }

    public static function normalizeParts(string $country, string $local, string $field = 'whatsapp_local'): string
    {
        $country = preg_replace('/\D/', '', $country);
        $local = preg_replace('/\D/', '', trim($local));
        if (! isset(self::COUNTRIES[$country])) {
            throw ValidationException::withMessages([$field => 'Elegí un código de país válido.']);
        }
        if (str_starts_with($local, $country) && strlen($local) > strlen($country) + 7) {
            $local = substr($local, strlen($country));
        }
        if ($country === '54') {
            $local = preg_replace('/^0/', '', $local);
            if (strlen($local) === 12) {
                foreach ([2, 3, 4] as $position) {
                    if (substr($local, $position, 2) === '15') {
                        $local = substr($local, 0, $position).substr($local, $position + 2);
                        break;
                    }
                }
            }
            if (! str_starts_with($local, '9')) {
                $local = '9'.$local;
            }
        }

        return self::normalize($country.$local, $field);
    }

    public static function split(?string $number): array
    {
        $digits = preg_replace('/\D/', '', (string) $number);
        foreach (array_keys(self::COUNTRIES) as $country) {
            $country = (string) $country;
            if (str_starts_with($digits, $country)) {
                $local = substr($digits, strlen($country));
                if ($country === '54' && str_starts_with($local, '9')) {
                    $local = substr($local, 1);
                }

                return ['country' => $country, 'local' => $local];
            }
        }

        return ['country' => '54', 'local' => $digits];
    }
}
