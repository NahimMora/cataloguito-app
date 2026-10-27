<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme(config('app.env') === 'production' ? 'https' : parse_url(config('app.url'), PHP_URL_SCHEME));
        Event::listen(MessageSending::class, function ($event) {
            if (! app()->environment(['local', 'e2e']) || config('mail.default') !== 'array') {
                return;
            }
            foreach ($event->message->getTo() as $recipient) {
                DB::table('local_messages')->insert(['recipient' => $recipient->getAddress(), 'subject' => $event->message->getSubject() ?? 'Correo', 'html' => $event->message->getHtmlBody() ?? e($event->message->getTextBody() ?? ''), 'created_at' => now()]);
            }
        });
        RateLimiter::for('login', fn ($r) => [Limit::perMinute(6)->by(mb_strtolower((string) $r->input('email')).'|'.$r->ip()), Limit::perMinute(30)->by('login-ip|'.$r->ip())]);
        RateLimiter::for('orders', fn ($r) => [Limit::perMinutes(10, 5)->by($r->session()->getId().'|'.$r->route('slug')), Limit::perMinute(30)->by($r->ip())]);
        RateLimiter::for('quotes', fn ($r) => Limit::perMinute(40)->by($r->session()->getId()));
        VerifyEmail::toMailUsing(fn ($user, $url) => (new MailMessage)->subject('Verificá tu email · Cataloguito')->greeting('Hola, '.$user->name)->line('Tu catálogo está a un paso de salir al mundo. Confirmá tu dirección para publicarlo.')->action('Verificar mi email', $url)->line('Si no creaste esta cuenta, podés ignorar este correo.'));
        ResetPassword::toMailUsing(fn ($user, $token) => (new MailMessage)->subject('Recuperá tu acceso · Cataloguito')->greeting('Hola, '.$user->name)->line('Recibimos una solicitud para cambiar tu contraseña.')->action('Elegir nueva contraseña', route('password.reset', ['token' => $token, 'email' => $user->email]))->line('El enlace vence en 60 minutos. Si no lo pediste, ignorá este correo.'));
    }
}
