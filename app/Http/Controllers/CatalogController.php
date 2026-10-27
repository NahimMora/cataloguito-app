<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Shop;
use App\Services\OrderService;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    private function shop(string $slug): Shop
    {
        return Shop::with(['media', 'user'])->where('slug', $slug)->where('status', 'published')->firstOrFail();
    }

    public function index(Request $r, string $slug, ?string $category = null, ?string $categorySlug = null)
    {
        $shop = $this->shop($slug);

        return $this->render($r, $shop, false, $category);
    }

    public function preview(Request $r)
    {
        $shop = $r->attributes->get('shop');
        $shop->load('media');

        return $this->render($r, $shop, true);
    }

    private function render(Request $r, Shop $shop, bool $preview, ?string $category = null)
    {
        $r->validate(['q' => 'nullable|string|max:100', 'sort' => 'nullable|in:original,price_asc,price_desc,name']);
        $categories = $shop->categories()->orderBy('sort_order')->orderBy('name')->get();
        $selected = $category ? $categories->firstWhere('public_id', $category) : null;
        if ($category && ! $selected) {
            abort(404);
        }
        $query = $shop->products()->with(['media', 'category'])->where('status', 'active')->when($selected, fn ($q) => $q->where('category_id', $selected->id))->when($r->q, fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$s.'%')->orWhere('sku', 'like', '%'.$s.'%')));
        match ($r->query('sort')) {
            'price_asc' => $query->orderBy('price_minor'),'price_desc' => $query->orderByDesc('price_minor'),'name' => $query->orderBy('name'),default => $query->orderBy('sort_order')->orderByDesc('id')
        };
        $products = $query->paginate(24)->withQueryString();
        $appearance = $preview ? $shop->appearance_draft : $shop->appearance;
        if (! $preview && ! $r->session()->has('viewed-'.$shop->id.'-'.now()->format('YmdH'))) {
            $this->metric($shop, 'views');
            $r->session()->put('viewed-'.$shop->id.'-'.now()->format('YmdH'), true);
        }
        $response = response()->view('catalog.index', compact('shop', 'categories', 'selected', 'products', 'appearance', 'preview'));
        $response->headers->set('Cache-Control', 'private, no-cache');
        if ($preview) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

return $response;
    }

    public function product(Request $r, string $slug, string $id, ?string $title = null)
    {
        $shop = $this->shop($slug);
        $product = $shop->products()->with(['media', 'category'])->where('status', 'active')->where('public_id', $id)->firstOrFail();
        if ($title !== $product->slug) {
            return redirect($product->publicUrl($shop), 301);
        }

        return response()->view('catalog.product', ['shop' => $shop, 'product' => $product, 'appearance' => $shop->appearance, 'preview' => false])->header('Cache-Control', 'private, no-cache');
    }

    public function quote(Request $r, string $slug, QuoteService $service)
    {
        $data = $this->items($r);

        return response()->json($service->issue($this->shop($slug), $data['items'], hash('sha256', $r->session()->getId())));
    }

    private function items(Request $r): array
    {
        return $r->validate(['items' => 'required|array|min:1|max:50', 'items.*' => 'required|array:id,quantity', 'items.*.id' => 'required|string|size:26', 'items.*.quantity' => 'required|integer|min:1|max:999']);
    }

    public function order(Request $r, string $slug, OrderService $service)
    {
        $data = $this->items($r) + $r->validate(['name' => 'required|string|max:100', 'phone' => 'required|string|max:25', 'note' => 'nullable|string|max:500', 'key' => 'required|uuid', 'quote' => 'required|string|max:60000', 'website' => 'nullable|prohibited']);
        $shop = $this->shop($slug);
        $order = $service->create($shop, $data, hash('sha256', $r->session()->getId()));

        return response()->json(['url' => route('catalog.receipt', [$slug, $order->public_id]), 'number' => $order->displayNumber()]);
    }

    public function receipt(Request $r, string $slug, string $id)
    {
        // Shop can be paused after confirmation; receipt remains accessible to its original session.
        $shop = Shop::where('slug', $slug)->firstOrFail();
        $order = $shop->orders()->with(['shop', 'items'])->where('public_id', $id)->where('session_hash', hash('sha256', $r->session()->getId()))->firstOrFail();

        return view('catalog.receipt', compact('shop', 'order'));
    }

    public function whatsapp(Request $r, string $slug)
    {
        $shop = $this->shop($slug);
        $this->metric($shop, 'whatsapp_clicks');

        return redirect()->away('https://wa.me/'.$shop->whatsapp);
    }

    private function metric(Shop $shop, string $column): void
    {
        DB::table('daily_metrics')->insertOrIgnore(['shop_id' => $shop->id, 'day' => now()->toDateString(), 'views' => 0, 'whatsapp_clicks' => 0]);
        DB::table('daily_metrics')->where('shop_id', $shop->id)->where('day', now()->toDateString())->increment($column);
    }

    public function media(Request $r, int $id, int $size)
    {
        $media = Media::findOrFail($id);
        $shop = Shop::findOrFail($media->shop_id);
        $owner = $r->user()?->id === $shop->user_id;
        $public = $shop->status === 'published' && (! $media->product_id || $shop->products()->whereKey($media->product_id)->where('status', 'active')->exists());
        abort_unless($owner || $public, 404);
        abort_unless(in_array($size, [320, 640, 1200], true), 404);
        $path = $media->variants[$size] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path),404);

        return response()->file(Storage::disk('local')->path($path),['Content-Type' => 'image/webp', 'Cache-Control' => $public ? 'public, max-age=604800' : 'private, no-store']);
    }
}
