@if (session('success'))
    <div class="alert success" role="status">
        <x-icon name="check" />{{ session('success') }}
    </div>
@endif
@if (session('mail_warning'))
    <div class="alert info" role="status"><x-icon name="mail" />{{ session('mail_warning') }}</div>
@endif
@if ($errors->any())
    <div class="alert error" role="alert">
        <div><strong>Revisá estos datos</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
