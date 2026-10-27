<?php

namespace Tests\Feature;

use App\Mail\OrderRequested;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\OutboxProcessor;
use App\Support\Phone;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class CatalogPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withCredentials();
    }

    private function merchant(string $slug = 'prueba', bool $verified = true): Shop
    {
        $u = User::factory()->create(['email' => $slug.'@example.test', 'email_verified_at' => $verified ? now() : null]);

        return Shop::create(['user_id' => $u->id, 'name' => 'Tienda '.$slug, 'slug' => $slug, 'whatsapp' => '5491100000000', 'status' => 'published', 'appearance' => Shop::defaultAppearance(), 'appearance_draft' => Shop::defaultAppearance()]);
    }

    private function product(Shop $shop, array $data = []): Product
    {
        return $shop->products()->create(array_merge(['name' => 'Taza', 'slug' => 'taza', 'price_minor' => 1250050, 'available' => true, 'status' => 'active'], $data))->fresh();
    }

    private function payload(Product $p, array $override = []): array
    {
        return array_merge(['name' => 'Florencia', 'price' => '12500.50', 'previous_price' => '', 'available' => 1, 'sort_order' => 0, 'version' => $p->version], $override);
    }

    private function orderPayload(Shop $shop, Product $p): array
    {
        $items = [['id' => $p->public_id, 'quantity' => 2]];
        $quote = $this->postJson('/'.$shop->slug.'/quote', ['items' => $items])->assertOk()->json('token');
        $this->withCookie(config('session.cookie'), $this->app['session']->getId());

        return ['items' => $items, 'quote' => $quote, 'key' => (string) Str::uuid(), 'name' => 'Comprador prueba', 'phone_country' => '54', 'phone_local' => '11 0000 0000', 'note' => 'Retiro a coordinar', 'website' => ''];
    }

    public function test_landing_and_guest_routes_are_available(): void
    {
        $this->get('/')->assertOk()->assertSee('Tu catálogo online, listo para compartir por WhatsApp.')->assertSee('"@context":"https://schema.org"', false);
        $this->get('/app')->assertRedirect('/login');
        $this->get('/register')->assertOk();
        $this->get('/plantillas')->assertOk()->assertSee('Tienda de ropa')->assertSee('Mayoristas y distribuidores');
        $this->get('/plantillas/hilo')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, follow');
        $this->get('/regisster')->assertRedirect('/register');
    }

    public function test_argentine_phone_parts_are_normalized_without_duplicating_country(): void
    {
        $this->assertSame('5491112345678', Phone::normalizeParts('54', '011 15 1234-5678'));
        $this->assertSame('5491112345678', Phone::normalizeParts('54', '+54 9 11 1234-5678'));
        $this->assertSame(['country' => '54', 'local' => '1112345678'], Phone::split('5491112345678'));
    }

    public function test_selected_template_survives_registration_and_becomes_shop_appearance(): void
    {
        Notification::fake();
        $this->get('/register?style=hilo')->assertOk();
        $this->post('/register', ['name' => 'Hilda', 'email' => 'hilda@example.test', 'password' => 'Password12345', 'password_confirmation' => 'Password12345', 'terms' => 1])->assertRedirect('/onboarding');
        $this->post('/onboarding', ['name' => 'Ropa de Hilda', 'slug' => 'ropa-hilda', 'whatsapp_country' => '54', 'whatsapp_local' => '11 1234 5678'])->assertRedirect('/app');
        $shop = Shop::where('slug', 'ropa-hilda')->firstOrFail();
        $this->assertSame('boutique', $shop->appearance['theme']);
        $this->assertSame('#a53f35', $shop->appearance['accent']);
    }

    public function test_register_errors_are_linked_to_their_fields(): void
    {
        User::factory()->create(['email' => 'usado@example.test']);
        $this->followingRedirects()->from('/register')->post('/register', ['name' => '', 'email' => 'usado@example.test', 'password' => 'abc', 'password_confirmation' => 'xyz'])->assertOk()->assertSee('aria-invalid="true"', false)->assertSee('Este email ya tiene una cuenta. Podés ingresar o recuperar el acceso.');
    }

    public function test_robots_and_sitemap_expose_only_public_discovery_routes(): void
    {
        $shop = $this->merchant();
        $product = $this->product($shop);
        $this->get('/robots.txt')->assertOk()->assertSee(url('/sitemap.xml'))->assertDontSee('Disallow: /register');
        $this->get('/sitemap.xml')->assertOk()->assertSee(route('templates.index'))->assertSee(route('seo.whatsapp'))->assertSee(route('seo.free'))->assertSee($product->publicUrl($shop));
        $this->get('/prueba?q=taza')->assertHeader('X-Robots-Tag', 'noindex, follow');
    }

    public function test_registration_sends_verification_and_creates_shop(): void
    {
        Notification::fake();
        $this->post('/register', ['name' => 'Ana', 'email' => 'ANA@example.test', 'password' => 'Password12345', 'password_confirmation' => 'Password12345', 'terms' => 1])->assertRedirect('/onboarding');
        $user = User::where('email', 'ana@example.test')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->post('/onboarding', ['name' => 'Ana Shop', 'slug' => 'ana-shop', 'whatsapp_country' => '54', 'whatsapp_local' => '011 15 1234-5678'])->assertRedirect('/app');
        $this->assertDatabaseHas('shops', ['user_id' => $user->id, 'whatsapp' => '5491112345678']);
    }

    public function test_reserved_slug_and_cross_tenant_category_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/onboarding', ['name' => 'Foo', 'slug' => 'app', 'whatsapp_country' => '54', 'whatsapp_local' => '11 0000 0000'])->assertSessionHasErrors('slug');
        $a = $this->merchant('ana');
        $b = $this->merchant('beto');
        $cat = $b->categories()->create(['name' => 'Privada', 'slug' => 'privada']);
        $this->actingAs($a->user)->post('/app/products', ['name' => 'Prueba', 'price' => '10', 'sort_order' => 0, 'category_id' => $cat->id])->assertSessionHasErrors('category_id');
    }

    public function test_verification_requires_valid_signature_and_then_allows_publishing(): void
    {
        $shop = $this->merchant('ana', false);
        $shop->update(['status' => 'draft']);
        $this->product($shop);
        $this->actingAs($shop->user)->post('/app/publish')->assertRedirect('/verify-email');
        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $shop->user->id, 'hash' => sha1($shop->user->email)]);
        $this->get($url)->assertRedirect('/app');
        $this->post('/app/publish')->assertSessionHas('success');
        $this->assertSame('published', $shop->fresh()->status);
    }

    public function test_password_reset_changes_password_and_invalidates_sessions(): void
    {
        Notification::fake();
        $u = User::factory()->create(['email' => 'reset@example.test']);
        $token = null;
        $this->post('/forgot-password', ['email' => $u->email])->assertSessionHas('success');
        Notification::assertSentTo($u, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });
        $this->post('/reset-password', ['email' => $u->email, 'token' => $token, 'password' => 'NuevaClave12345', 'password_confirmation' => 'NuevaClave12345'])->assertRedirect('/login');
        $this->post('/login', ['email' => $u->email, 'password' => 'NuevaClave12345'])->assertRedirect('/app');
    }

    public function test_unverified_or_empty_shop_cannot_publish(): void
    {
        $shop = $this->merchant();
        $shop->update(['status' => 'draft']);
        $this->actingAs($shop->user)->post('/app/publish')->assertSessionHasErrors('publish');
    }

    public function test_product_creation_image_processing_and_public_media(): void
    {
        Storage::fake('local');
        $shop = $this->merchant();
        $this->actingAs($shop->user)->post('/app/products', ['name' => 'Foto propia', 'price' => '1234,56', 'previous_price' => '1500', 'sort_order' => 0, 'available' => 1, 'image' => UploadedFile::fake()->image('foto.png', 600, 600)])->assertRedirect('/app/products');
        $p = $shop->products()->firstOrFail();
        $this->assertSame(123456, $p->price_minor);
        $this->assertNotNull($p->media);
        $this->assertSame(3, count($p->media->variants));
        $this->assertGreaterThan(0, $shop->fresh()->storage_bytes);
        $this->get($p->media->url())->assertOk()->assertHeader('Content-Type', 'image/webp');
    }

    public function test_unsafe_upload_rolls_back_product(): void
    {
        Storage::fake('local');
        $shop = $this->merchant();
        $this->actingAs($shop->user)->post('/app/products', ['name' => 'Mala foto', 'price' => '20', 'sort_order' => 0, 'image' => UploadedFile::fake()->createWithContent('imagen.png', '<?php echo "bad";')])->assertSessionHasErrors('image');
        $this->assertSame(0, $shop->products()->count());
    }

    public function test_quick_edit_uses_cents_and_prevents_lost_update(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $this->actingAs($shop->user)->patchJson('/app/products/'.$p->public_id.'/quick', ['price' => '18000,75', 'version' => 1])->assertOk()->assertJsonPath('version', 2);
        $this->patchJson('/app/products/'.$p->public_id.'/quick', ['price' => '50', 'version' => 1])->assertUnprocessable()->assertJsonValidationErrors('version');
        $this->assertSame(1800075, $p->fresh()->price_minor);
    }

    public function test_product_and_order_tenant_isolation(): void
    {
        $a = $this->merchant('ana');
        $b = $this->merchant('beto');
        $p = $this->product($b);
        $data = $this->orderPayload($b, $p);
        $this->postJson('/beto/orders', $data)->assertOk();
        $o = $b->orders()->firstOrFail();
        $this->actingAs($a->user)->get('/app/products/'.$p->public_id.'/edit')->assertNotFound();
        $this->patchJson('/app/products/'.$p->public_id.'/quick', ['price' => '1', 'version' => 1])->assertNotFound();
        $this->post('/app/products/'.$p->public_id.'/archive')->assertNotFound();
        $this->get('/app/orders/'.$o->public_id)->assertNotFound();
        $this->get('/app/products/export')->assertOk()->assertDontSee($p->public_id);
    }

    public function test_database_rejects_cross_shop_relationship(): void
    {
        $a = $this->merchant('ana');
        $b = $this->merchant('beto');
        $cat = $b->categories()->create(['name' => 'B', 'slug' => 'b']);
        $this->expectException(QueryException::class);
        $this->product($a, ['category_id' => $cat->id]);
    }

    public function test_plan_quota_blocks_creation_but_archiving_frees_slot(): void
    {
        $shop = $this->merchant();
        for ($i = 0; $i < 50; $i++) {
            $this->product($shop, ['name' => 'P'.$i]);
        }
        $this->actingAs($shop->user)->post('/app/products', ['name' => 'Extra', 'price' => '1', 'sort_order' => 0])->assertSessionHasErrors('limit');
        $first = $shop->products()->first();
        $this->post('/app/products/'.$first->public_id.'/archive')->assertRedirect();
        $this->post('/app/products', ['name' => 'Extra', 'price' => '1', 'sort_order' => 0])->assertRedirect('/app/products');
        $this->assertSame(50, $shop->products()->where('status', 'active')->count());
    }

    public function test_storage_quota_rejects_image_without_losing_product(): void
    {
        Storage::fake('local');
        $shop = $this->merchant();
        $p = $this->product($shop);
        $shop->update(['storage_bytes' => 49_999_999]);
        $this->actingAs($shop->user)->put('/app/products/'.$p->public_id, $this->payload($p, ['image' => UploadedFile::fake()->image('a.jpg', 600, 600)]))->assertSessionHasErrors('image');
        $this->assertNull($p->fresh()->media);
        $this->assertSame(1250050, $p->fresh()->price_minor);
    }

    public function test_public_catalog_hides_archived_products_and_drafts(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop, ['name' => 'Visible']);
        $hidden = $this->product($shop, ['name' => 'Secreto', 'status' => 'archived']);
        $this->get('/prueba')->assertOk()->assertSee('Visible')->assertDontSee('Secreto');
        $this->get($hidden->publicUrl($shop))->assertNotFound();
        $shop->update(['status' => 'draft']);
        $this->get('/prueba')->assertNotFound();
        $this->actingAs($shop->user)->get('/app/preview')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_appearance_draft_does_not_change_public_theme(): void
    {
        $shop = $this->merchant();
        $this->product($shop);
        $data = ['theme' => 'commercial', 'accent' => '#0f766e', 'tagline' => 'Mi diseño nuevo', 'action' => 'draft'];
        $this->actingAs($shop->user)->put('/app/appearance', $data)->assertSessionHas('success');
        $this->get('/prueba')->assertDontSee('Mi diseño nuevo');
        $this->get('/app/preview')->assertSee('Mi diseño nuevo');
        $this->put('/app/appearance', array_merge($data, ['action' => 'publish']))->assertSessionHas('success');
        $this->get('/prueba')->assertSee('Mi diseño nuevo');
        $this->put('/app/appearance', array_merge($data, ['accent' => 'red; background:url(https://example.test)']))->assertSessionHasErrors('accent');
    }

    public function test_quote_and_order_revalidate_prices_and_snapshot_totals(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $data = $this->orderPayload($shop, $p);
        $p->update(['price_minor' => 2000000]);
        $this->postJson('/prueba/orders', $data)->assertUnprocessable()->assertJsonValidationErrors('quote');
        $data = $this->orderPayload($shop, $p);
        $this->postJson('/prueba/orders', $data)->assertOk();
        $order = $shop->orders()->firstOrFail();
        $this->assertSame(4000000, (int) $order->total_minor);
        $p->update(['name' => 'Otro nombre', 'price_minor' => 1]);
        $this->assertSame('Taza', $order->items->first()->name);
        $this->assertDatabaseHas('outbox', ['order_id' => $order->id, 'status' => 'pending']);
    }

    public function test_duplicate_request_returns_same_order_and_payload_change_is_rejected(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $data = $this->orderPayload($shop, $p);
        $first = $this->postJson('/prueba/orders', $data)->assertOk()->json('url');
        $this->postJson('/prueba/orders', $data)->assertOk()->assertJsonPath('url', $first);
        $this->assertSame(1, $shop->orders()->count());
        $this->assertSame(1, DB::table('outbox')->count());
        $this->postJson('/prueba/orders', array_merge($data, ['name' => 'Otra persona']))->assertUnprocessable()->assertJsonValidationErrors('key');
    }

    public function test_quote_rejects_foreign_product_unavailable_and_duplicate_lines(): void
    {
        $a = $this->merchant('ana');
        $b = $this->merchant('beto');
        $p = $this->product($b);
        $this->postJson('/ana/quote', ['items' => [['id' => $p->public_id, 'quantity' => 1]]])->assertUnprocessable();
        $p = $this->product($a, ['available' => false]);
        $this->postJson('/ana/quote', ['items' => [['id' => $p->public_id, 'quantity' => 1]]])->assertUnprocessable();
        $p->update(['available' => true]);
        $this->postJson('/ana/quote', ['items' => [['id' => $p->public_id, 'quantity' => 1], ['id' => $p->public_id, 'quantity' => 1]]])->assertUnprocessable();
    }

    public function test_tampered_quote_and_honeypot_cannot_create_order(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $data = $this->orderPayload($shop, $p);
        $this->postJson('/prueba/orders', array_merge($data, ['quote' => 'fake']))->assertUnprocessable();
        $this->postJson('/prueba/orders', array_merge($data, ['website' => 'https://spam.test']))->assertUnprocessable();
        $this->assertSame(0, Order::count());
    }

    public function test_receipt_requires_original_buyer_session(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $data = $this->orderPayload($shop, $p);
        $url = $this->postJson('/prueba/orders', $data)->assertOk()->json('url');
        $this->get($url)->assertOk()->assertSee('Tu solicitud quedó registrada.')->assertSee('El mensaje todavía requiere');
        $this->app['session']->invalidate();
        $this->withCookie(config('session.cookie'), $this->app['session']->getId());
        $this->get($url)->assertNotFound();
    }

    public function test_order_transitions_enforce_workflow(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $this->postJson('/prueba/orders', $this->orderPayload($shop, $p))->assertOk();
        $order = $shop->orders()->first();
        $this->actingAs($shop->user)->patch('/app/orders/'.$order->public_id.'/status', ['status' => 'completed'])->assertSessionHasErrors('status');
        $this->patch('/app/orders/'.$order->public_id.'/status', ['status' => 'confirmed'])->assertSessionHas('success');
        $this->patch('/app/orders/'.$order->public_id.'/status', ['status' => 'completed'])->assertSessionHas('success');
        $this->patch('/app/orders/'.$order->public_id.'/status', ['status' => 'cancelled'])->assertSessionHasErrors('status');
        $this->assertSame(3, $order->events()->count());
    }

    public function test_failed_email_keeps_order_and_outbox_retries(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $this->postJson('/prueba/orders', $this->orderPayload($shop, $p))->assertOk();
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP down'));
        $this->assertSame(0, app(OutboxProcessor::class)->process());
        $this->assertSame(1, Order::count());
        $this->assertDatabaseHas('outbox', ['status' => 'pending', 'attempts' => 1, 'error_code' => 'RuntimeException']);
    }

    public function test_outbox_sends_once_and_does_not_resend_sent_event(): void
    {
        Mail::fake();
        $shop = $this->merchant();
        $p = $this->product($shop);
        $this->postJson('/prueba/orders', $this->orderPayload($shop, $p))->assertOk();
        $this->assertSame(1, app(OutboxProcessor::class)->process());
        $this->assertSame(0, app(OutboxProcessor::class)->process());
        Mail::assertSent(OrderRequested::class, 1);
    }

    public function test_category_deletion_preserves_products_and_escapes_html(): void
    {
        $shop = $this->merchant();
        $cat = $shop->categories()->create(['name' => 'Hogar', 'slug' => 'hogar']);
        $p = $this->product($shop, ['category_id' => $cat->id, 'name' => '<script>alert(1)</script>']);
        $this->get('/prueba')->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $this->actingAs($shop->user)->delete('/app/categories/'.$cat->public_id)->assertSessionHas('success');
        $this->assertNull($p->fresh()->category_id);
    }

    public function test_account_deletion_requires_password_and_removes_only_own_data(): void
    {
        $a = $this->merchant('ana');
        $b = $this->merchant('beto');
        $a->user->update(['password' => 'Password12345']);
        $p = $this->product($a);
        $this->postJson('/ana/orders', $this->orderPayload($a, $p))->assertOk();
        $this->actingAs($a->user)->delete('/app/account', ['password' => 'wrong', 'confirmation' => 'ELIMINAR'])->assertSessionHasErrors('password');
        $this->delete('/app/account', ['password' => 'Password12345', 'confirmation' => 'ELIMINAR'])->assertRedirect('/');
        $this->assertDatabaseMissing('shops', ['id' => $a->id]);
        $this->assertDatabaseHas('shops', ['id' => $b->id]);
    }

    public function test_local_mailbox_is_disabled_outside_local(): void
    {
        $this->get('/dev/mail')->assertNotFound();
    }

    public function test_backup_refuses_relative_public_destination(): void
    {
        config(['vitrina.dump_binary' => PHP_BINARY]);
        $this->artisan('catalog:backup', ['--output' => 'public/unsafe-backup.zip'])
            ->expectsOutput('El respaldo debe guardarse fuera de public/.')
            ->assertFailed();
        $this->assertFileDoesNotExist(public_path('unsafe-backup.zip'));
    }

    public function test_all_merchant_screens_render(): void
    {
        $shop = $this->merchant();
        $product = $this->product($shop);
        $this->actingAs($shop->user);
        foreach (['/app', '/app/products', '/app/products/new', '/app/products/'.$product->public_id.'/edit', '/app/categories', '/app/orders', '/app/catalog', '/app/account', '/app/preview'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_quantity_limits_and_invalid_money_are_rejected(): void
    {
        $shop = $this->merchant();
        $p = $this->product($shop);
        $this->postJson('/prueba/quote', ['items' => [['id' => $p->public_id, 'quantity' => -1]]])->assertUnprocessable();
        $this->actingAs($shop->user)->patchJson('/app/products/'.$p->public_id.'/quick', ['price' => '1.000,50', 'version' => 1])->assertUnprocessable();
    }
}
