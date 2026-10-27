<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\ImageService;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
    public function onboarding(Request $r)
    {
        if ($r->user()->shop) {
            return redirect()->route('dashboard');
        }

        return view('onboarding');
    }

    public function create(Request $r)
    {
        if ($r->user()->shop) {
            return redirect()->route('dashboard');
        }
        $data = $r->validate(['name' => 'required|string|max:100', 'slug' => ['required', 'string', 'min:3', 'max:60', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', 'unique:shops,slug', Rule::notIn(['app', 'admin', 'api', 'login', 'register', 'regisster', 'registro', 'onboarding', 'precios', 'plantillas', 'catalogo-para-whatsapp', 'crear-catalogo-digital-gratis', 'ayuda', 'legal', 'media', 'dev', 'assets', 'storage', 'up', 'build', 'forgot-password', 'reset-password', 'verify-email', 'sitemap', 'robots', 'logout', 'email'])], 'whatsapp_country' => 'required|string|max:4', 'whatsapp_local' => 'required|string|max:25']);
        $data['whatsapp'] = Phone::normalizeParts($data['whatsapp_country'], $data['whatsapp_local']);
        unset($data['whatsapp_country'], $data['whatsapp_local']);
        $style = $r->session()->pull('selected_style');
        $selectedAccent = $r->session()->pull('selected_accent');
        $preset = config('cataloguito.styles.'.$style);
        $appearance = Shop::defaultAppearance();
        if ($preset) {
            $accent = array_key_exists((string) $selectedAccent, $preset['palettes']) ? $selectedAccent : $preset['accent'];
            $appearance = ['style' => $style, 'theme' => $preset['family'], 'accent' => $accent, 'tagline' => ''];
        }
        Shop::create($data + ['user_id' => $r->user()->id, 'appearance' => $appearance, 'appearance_draft' => $appearance]);

        return redirect()->route('dashboard')->with('success', 'Tu espacio está listo. Agregá tus primeros productos.');
    }

    public function dashboard(Request $r)
    {
        $shop = $r->attributes->get('shop');
        $recent = $shop->orders()->latest()->limit(5)->get();
        $counts = ['products' => $shop->products()->where('status', 'active')->count(), 'pending' => $shop->orders()->where('status', 'pending')->count(), 'requests' => $shop->orders()->count()];
        $metrics = DB::table('daily_metrics')->where('shop_id', $shop->id)->where('day', '>=', now()->subDays(6)->toDateString())->selectRaw('COALESCE(SUM(views),0) as views, COALESCE(SUM(whatsapp_clicks),0) as clicks')->first();

        $styles = config('cataloguito.styles');

        return view('dashboard', compact('shop', 'recent', 'counts', 'metrics', 'styles'));
    }

    public function settings(Request $r)
    {
        return view('shop.settings', ['shop' => $r->attributes->get('shop'), 'styles' => config('cataloguito.styles')]);
    }

    public function update(Request $r, ImageService $images)
    {
        $data = $r->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string|max:300', 'whatsapp_country' => 'required|string|max:4', 'whatsapp_local' => 'required|string|max:25', 'instagram' => 'nullable|regex:/^[A-Za-z0-9._]{1,50}$/', 'facebook' => 'nullable|url:https|max:200', 'image' => 'nullable|file|max:4883', 'remove_logo' => 'sometimes|boolean']);
        $data['whatsapp'] = Phone::normalizeParts($data['whatsapp_country'], $data['whatsapp_local']);
        unset($data['image'], $data['remove_logo'], $data['whatsapp_country'], $data['whatsapp_local']);
        DB::transaction(function () use ($r, $data, $images) {
            $shop = Shop::whereKey($r->attributes->get('shop')->id)->lockForUpdate()->firstOrFail();
            $shop->fill($data);
            $shop->catalog_version++;
            $shop->save();
            if ($r->hasFile('image')) {
                $images->replace($shop, $r->file('image'));
            } elseif ($r->boolean('remove_logo') && ($logo = $shop->media()->where('kind', 'logo')->first())) {
                $images->remove($shop, $logo);
            }
        });

        return back()->with('success', 'Datos del negocio guardados.');
    }

    public function appearance(Request $r)
    {
        $data = $r->validate(['style' => 'nullable|in:hilo,volumen,aura,casa,chispa,huellas,dulce', 'theme' => 'nullable|in:minimal,commercial,boutique', 'accent' => 'required|in:#b93828,#4338ca,#0f766e,#9f1239,#1d4ed8,#334155,#a53f35,#154b45,#8c4661,#b34b35,#c74f32,#36684c,#9c493d', 'tagline' => 'nullable|string|max:100']);
        $style = $data['style'] ?? $r->attributes->get('shop')->currentStyle();
        $preset = config('cataloguito.styles.'.$style);
        $data['style'] = $style;
        $data['theme'] = $preset['family'];
        $shop = $r->attributes->get('shop');
        if ($r->input('action') === 'publish') {
            $shop->update(['appearance' => $data, 'appearance_draft' => $data, 'catalog_version' => $shop->catalog_version + 1]);
        } else {
            $shop->update(['appearance_draft' => $data]);
        }

        return back()->with('success', $r->input('action') === 'publish' ? 'Diseño publicado.' : 'Vista previa guardada. Tu diseño público sigue igual.');
    }

    public function publish(Request $r)
    {
        $shop = $r->attributes->get('shop');
        if ($shop->status === 'published') {
            $shop->update(['status' => 'paused']);

            return back()->with('success', 'Catálogo pausado. Tus productos y pedidos se conservan.');
        }
        if (! $r->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        if (! $shop->whatsapp || ! $shop->products()->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['publish' => 'Agregá al menos un producto y configurá WhatsApp antes de publicar.']);
        }
        $shop->update(['status' => 'published']);

        return back()->with('success', 'Tu catálogo está publicado. Compartí el enlace.');
    }

    public function account(Request $r)
    {
        return view('shop.account', ['shop' => $r->attributes->get('shop')]);
    }

    public function accountUpdate(Request $r)
    {
        $data = $r->validate(['name' => 'required|string|max:80', 'email_orders' => 'sometimes|boolean', 'current_password' => 'nullable|required_with:password|current_password', 'password' => 'nullable|confirmed|min:10|regex:/[A-Za-z]/|regex:/[0-9]/']);
        $user = $r->user();
        $user->name = $data['name'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
            $user->remember_token = Str::random(60);
            DB::table('sessions')->where('user_id', $user->id)->where('id', '!=', $r->session()->getId())->delete();
            $r->session()->regenerate();
        }
        $user->save();
        $r->attributes->get('shop')->update(['email_orders' => $r->boolean('email_orders')]);

        return back()->with('success', 'Preferencias guardadas.');
    }

    public function delete(Request $r)
    {
        $r->validate(['password' => 'required|current_password', 'confirmation' => 'required|in:ELIMINAR']);
        $shop = $r->attributes->get('shop');
        $paths = $shop->media()->get()->flatMap(fn ($m) => array_values($m->variants))->all();
        DB::transaction(function () use ($r, $shop) {
            foreach (['outbox', 'order_events', 'order_items', 'orders', 'media', 'products', 'categories', 'daily_metrics'] as $table) {
                DB::table($table)->where('shop_id', $shop->id)->delete();
            }
            DB::table('local_messages')->where('recipient', $r->user()->email)->delete();
            $shop->delete();
            $r->user()->delete();
        });
        Storage::disk('local')->delete($paths);
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect('/')->with('success', 'Tu cuenta fue eliminada.');
    }
}
