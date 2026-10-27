<?php

namespace Database\Seeders;

use App\Models\OrderEvent;
use App\Models\Shop;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing', 'e2e'])) {
            throw new \RuntimeException('La demo solo puede crearse en local/testing/e2e.');
        }
        if ($legacy = User::where('email', 'demo@vitrina.test')->first()) {
            $legacy->update(['email' => 'demo@cataloguito.test', 'password' => 'CataloguitoDemo2026']);
            DB::table('local_messages')->where('recipient', 'demo@vitrina.test')->update(['recipient' => 'demo@cataloguito.test']);
            $this->command?->info('La demo existente fue actualizada a Cataloguito.');

            return;
        }
        if (User::where('email', 'demo@cataloguito.test')->exists()) {
            $this->command?->info('La demo ya existe. No se modificaron sus datos.');

            return;
        }
        DB::transaction(function () {
            $user = User::create(['name' => 'Lucía', 'email' => 'demo@cataloguito.test', 'password' => 'CataloguitoDemo2026']);
            $user->markEmailAsVerified();
            // Intentionally invalid destination: demo never contacts a real merchant.
            $shop = Shop::create(['user_id' => $user->id, 'slug' => 'casa-luma', 'name' => 'Casa Luma', 'description' => 'Objetos que acompañan tus días. Una selección de diseño, texturas y pequeños detalles para disfrutar lo cotidiano.', 'whatsapp' => '5491100000000', 'status' => 'published', 'appearance' => ['theme' => 'minimal', 'accent' => '#b93828', 'tagline' => 'Objetos para los pequeños rituales.'], 'appearance_draft' => ['theme' => 'minimal', 'accent' => '#b93828', 'tagline' => 'Objetos para los pequeños rituales.']]);
            $cats = [];
            foreach (['Todo para casa', 'Para llevar', 'Accesorios'] as $i => $name) {
                $cats[] = $shop->categories()->create(['name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i]);
            }
            $specs = [['Taza de cerámica Arena', 1250000, 'mug', 0, 'Un café sin apuro. Taza de cerámica de 350 ml, con terminación artesanal.'], ['Botella térmica Bruma', 2490000, 'bottle', 1, 'Acero inoxidable, 500 ml. Para acompañarte todos los días.'], ['Mochila urbana Nómada', 3850000, 'bag', 1, 'Liviana y práctica, con compartimentos para lo esencial.'], ['Lámpara de mesa Alba', 4690000, 'lamp', 0, 'Luz cálida para tu rincón favorito. Diseño simple y atemporal.'], ['Anteojos de sol Órbita', 1980000, 'glasses', 2, 'Un diseño clásico para sumar a tus días. Consultá colores disponibles.'], ['Florero de cerámica Raíz', 1790000, 'vase', 0, 'Texturas naturales y líneas suaves. Cada detalle encuentra su lugar.']];
            $products = [];
            foreach ($specs as $i => [$name,$price,$photo,$cat,$description]) {
                $p = $shop->products()->create(['name' => $name, 'slug' => Str::slug($name), 'price_minor' => $price, 'previous_price_minor' => $i === 1 ? 2890000 : null, 'description' => $description, 'category_id' => $cats[$cat]->id, 'sku' => 'LUMA-'.($i + 1), 'available' => $i !== 4, 'sort_order' => $i]);
                $file = public_path('demo/'.$photo.'.webp');
                if (is_file($file)) {
                    app(ImageService::class)->replace($shop, new UploadedFile($file, $photo.'.webp', 'image/webp', null, true), $p);
                } $products[] = $p;
            }
            foreach ([['Sofía Martínez', 'pending'], ['Tomás Rodríguez', 'confirmed'], ['Valentina López', 'completed']] as $i => [$buyer,$status]) {
                $p = $products[$i];
                $order = $shop->orders()->create(['number' => $i + 1, 'status' => $status, 'buyer_name' => $buyer, 'buyer_phone' => '5491100000000', 'total_minor' => $p->price_minor, 'session_hash' => hash('sha256', 'seed-demo'), 'idempotency_key' => (string) Str::uuid(), 'payload_hash' => hash('sha256', 'seed-'.$i), 'created_at' => now()->subHours($i + 1)]);
                $order->items()->create(['shop_id' => $shop->id, 'product_id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'unit_price_minor' => $p->price_minor, 'quantity' => 1, 'total_minor' => $p->price_minor]);
                OrderEvent::create(['shop_id' => $shop->id, 'order_id' => $order->id, 'from_status' => null, 'to_status' => $status, 'created_at' => $order->created_at]);
            }
            $shop->update(['order_sequence' => 3]);
        });
        $this->command?->info('Demo creada: demo@cataloguito.test / CataloguitoDemo2026 · catálogo /casa-luma. Datos y teléfono ficticios.');
    }
}
