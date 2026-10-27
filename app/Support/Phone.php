<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class Phone
{
    public static function normalize(string $input, string $field = 'whatsapp'): string
    {
        $number = preg_replace('/[\s()+.\-]/', '', trim($input));
        if (! preg_match('/^[1-9][0-9]{8,14}$/D', $number)) {
            throw ValidationException::withMessages([$field => 'Usá el número internacional, por ejemplo 5491112345678.']);
        }

        return $number;
    }
}
