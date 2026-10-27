<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthController extends Controller
{
    public function form(string $mode = 'login')
    {
        return view('auth.form', compact('mode'));
    }

    public function register(Request $r)
    {
        $r->merge(['email' => mb_strtolower(trim((string) $r->email))]);
        $data = $r->validate(['name' => 'required|string|max:80', 'email' => 'required|email|max:254|unique:users', 'password' => ['required', 'confirmed', PasswordRule::min(10)->letters()->numbers()], 'terms' => 'accepted']);
        $user = User::create(collect($data)->only('name', 'email', 'password')->all());
        Auth::login($user);
        $r->session()->regenerate();
        try {
            event(new Registered($user));
        } catch (TransportExceptionInterface) {
            $r->session()->flash('mail_warning', 'Tu cuenta está creada. El correo de verificación no pudo enviarse todavía; podés solicitar otro desde tu panel.');
        }

        return redirect()->route('onboarding');
    }

    public function login(Request $r)
    {
        $r->merge(['email' => mb_strtolower(trim((string) $r->email))]);
        $data = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (! Auth::attempt($data, $r->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'El email o la contraseña no coinciden.']);
        }
        $r->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect('/');
    }

    public function verifyNotice()
    {
        return view('auth.verify');
    }

    public function verify(EmailVerificationRequest $r)
    {
        $r->fulfill();

        return redirect()->route('dashboard')->with('success', 'Email verificado. Ya podés publicar tu catálogo.');
    }

    public function resend(Request $r)
    {
        if (! $r->user()->hasVerifiedEmail()) {
            try {
                $r->user()->sendEmailVerificationNotification();
            } catch (TransportExceptionInterface) {
                return back()->withErrors(['email' => 'No pudimos enviar el correo. Intentá nuevamente en unos minutos.']);
            }
        }

        return back()->with('success', 'Te enviamos un nuevo enlace de verificación.');
    }

    public function forgot(Request $r)
    {
        $r->validate(['email' => 'required|email|max:254']);
        $email = mb_strtolower(trim($r->email));
        try {
            Password::sendResetLink(['email' => $email]);
        } catch (TransportExceptionInterface) {
            // Preserve the same response for existing and unknown accounts.
        }
        // Preview only mail addressed to this request; never expose another user's inbox.
        $r->session()->put('mail_preview_email', $email);

        return back()->with('success', 'Si existe una cuenta con ese email, recibirá un enlace para recuperar el acceso.');
    }

    public function resetForm(Request $r, string $token)
    {
        return view('auth.form', ['mode' => 'reset', 'token' => $token, 'email' => $r->query('email')]);
    }

    public function reset(Request $r)
    {
        $data = $r->validate(['token' => 'required', 'email' => 'required|email', 'password' => ['required', 'confirmed', PasswordRule::min(10)->letters()->numbers()]]);
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            DB::table('sessions')->where('user_id', $user->id)->delete();
            event(new PasswordReset($user));
        });
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => 'El enlace venció o no es válido. Solicitá uno nuevo.']);
        }

        return redirect()->route('login')->with('success', 'Contraseña actualizada. Ya podés ingresar.');
    }
}
