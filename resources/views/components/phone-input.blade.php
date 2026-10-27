@props(['name' => 'whatsapp', 'label' => 'WhatsApp', 'value' => null, 'countryName' => null, 'required' => true])
@php
    $countryName ??= $name.'_country';
    $localName = $name.'_local';
    $parts = \App\Support\Phone::split($value);
    $country = old($countryName, $parts['country']);
    $local = old($localName, $parts['local']);
    $error = $errors->first($localName) ?: $errors->first($countryName);
@endphp
<div class="field phone-field">
    <label for="{{ $localName }}">{{ $label }}</label>
    <div @class(['phone-control', 'has-error' => $error])>
        <select name="{{ $countryName }}" id="{{ $countryName }}" aria-label="País" @if ($required) required @endif>
            @foreach (\App\Support\Phone::COUNTRIES as $code => $countryLabel)
                <option value="{{ $code }}" @selected((string) $country === (string) $code)>{{ $countryLabel }} +{{ $code }}</option>
            @endforeach
        </select>
        <input id="{{ $localName }}" name="{{ $localName }}" type="tel" inputmode="tel" value="{{ $local }}"
            autocomplete="tel-national" placeholder="11 2345 6789" maxlength="25" @if ($required) required @endif
            @if ($error) aria-invalid="true" aria-describedby="{{ $localName }}-error" @endif>
    </div>
    @if ($error)
        <small class="field-error" id="{{ $localName }}-error">{{ $error }}</small>
    @else
        <small class="field-help">Argentina: código de área y número, sin 0 ni 15.</small>
    @endif
</div>
