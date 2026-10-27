<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalMailController extends Controller
{
    public function index(Request $r)
    {
        abort_unless(app()->environment(['local', 'e2e']) && in_array($r->ip(), ['127.0.0.1', '::1']), 404);
        $email = $r->user()?->email ?? $r->session()->get('mail_preview_email');
        $messages = $email ? DB::table('local_messages')->where('recipient', $email)->latest('id')->limit(20)->get() : collect();
        foreach ($messages as $message) {
            $message->action_url = null;
            preg_match_all('/href="([^"]+)"/i', $message->html, $links);
            foreach ($links[1] ?? [] as $link) {
                $url = html_entity_decode($link, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if (str_starts_with($url, rtrim(config('app.url'), '/').'/') && preg_match('~/(email/verify|reset-password|app/orders)/~', $url)) {
                    $message->action_url = $url;
                    break;
                }
            }
        }

        return view('auth.mailbox', compact('messages'));
    }
}
