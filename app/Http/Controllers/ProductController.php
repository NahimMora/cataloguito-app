<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Services\ImageService;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $shop = $r->attributes->get('shop');
        $filters = $r->validate(['q' => 'nullable|string|max:100', 'category' => 'nullable|integer', 'status' => 'nullable|in:active,archived']);
        $products = $shop->products()->with(['category', 'media'])->where('status', $filters['status'] ?? 'active')->when($filters['q'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$s.'%')->orWhere('sku', 'like', '%'.$s.'%')))->when($filters['category'] ?? null, fn ($q, $id) => $q->where('category_id', $id))->orderBy('sort_order')->latest('id')->paginate(12)->withQueryString();

        return view('products.index', ['shop' => $shop, 'products' => $products, 'categories' => $shop->categories()->orderBy('sort_order')->get(), 'activeCount' => $shop->products()->where('status', 'active')->count()]);
    }

    public function form(Request $r, ?string $id = null)
    {
        $shop = $r->attributes->get('shop');
        $product = $id ? $shop->products()->with('media')->where('public_id', $id)->firstOrFail() : new Product(['available' => true, 'sort_order' => 0]);

        return view('products.form', ['product' => $product, 'categories' => $shop->categories()->orderBy('name')->get()]);
    }

    public function save(Request $r, ImageService $images, ?string $id = null)
    {
        $shop = $r->attributes->get('shop');
        $product = $id ? $shop->products()->where('public_id', $id)->firstOrFail() : null;
        $data = $r->validate(['name' => 'required|string|max:120', 'description' => 'nullable|string|max:5000', 'price' => 'required|string|max:14', 'previous_price' => 'nullable|string|max:14', 'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('shop_id', $shop->id)], 'sku' => ['nullable', 'string', 'max:60', Rule::unique('products', 'sku')->where('shop_id', $shop->id)->ignore($product?->id)], 'available' => 'sometimes|boolean', 'sort_order' => 'required|integer|min:0|max:99999', 'version' => $id ? 'required|integer' : 'nullable', 'image' => 'nullable|file|max:4883', 'remove_image' => 'sometimes|boolean']);
        $data['price_minor'] = Money::parse($data['price']);
        $data['previous_price_minor'] = Money::parse($data['previous_price'] ?? null, 'previous_price');
        if ($data['previous_price_minor'] !== null && $data['previous_price_minor'] <= $data['price_minor']) {
            throw ValidationException::withMessages(['previous_price' => 'El precio anterior debe ser mayor al actual.']);
        }
        $data['available'] = $r->boolean('available');
        $data['slug'] = Str::slug($data['name']) ?: 'producto';
        unset($data['price'],$data['previous_price'],$data['image'],$data['remove_image'],$data['version']);
        DB::transaction(function () use ($shop, $product, $data, $r, $images) {
            $locked = Shop::whereKey($shop->id)->lockForUpdate()->firstOrFail();
            if (! $product && $locked->products()->where('status', 'active')->count() >= $locked->productLimit()) {
                throw ValidationException::withMessages(['limit' => 'Llegaste al límite de productos activos de tu plan. Archivá uno para continuar.']);
            }
            $p = $product ? $locked->products()->whereKey($product->id)->lockForUpdate()->firstOrFail() : new Product(['shop_id' => $locked->id]);
            if ($product && $p->version !== (int) $r->input('version')) {
                throw ValidationException::withMessages(['version' => 'Este producto cambió en otra ventana. Recargá para revisar la última versión.']);
            }
            $p->fill($data);
            $p->version = ($product ? $p->version : 0) + 1;
            $p->save();
            if ($r->hasFile('image')) {
                $images->replace($locked, $r->file('image'), $p);
            } elseif ($r->boolean('remove_image') && ($media = $p->media)) {
                $images->remove($locked, $media);
            }
            $locked->increment('catalog_version');
        });

        return redirect()->route('products.index')->with('success', $id ? 'Producto actualizado.' : 'Producto agregado a tu catálogo.');
    }

    public function quick(Request $r, string $id)
    {
        $data = $r->validate(['price' => 'required|string|max:14', 'version' => 'required|integer', 'available' => 'sometimes|boolean']);
        $price = Money::parse($data['price']);
        $product = DB::transaction(function () use ($r, $id, $data, $price) {
            $shop = Shop::whereKey($r->attributes->get('shop')->id)->lockForUpdate()->firstOrFail();
            $p = $shop->products()->where('public_id', $id)->lockForUpdate()->firstOrFail();
            if ($p->version !== (int) $data['version']) {
                throw ValidationException::withMessages(['version' => 'El producto cambió. Recargá antes de guardar.']);
            }
            $p->price_minor = $price;
            if ($p->previous_price_minor && $p->previous_price_minor <= $price) {
                $p->previous_price_minor = null;
            }
            if (array_key_exists('available', $data)) {
                $p->available = $r->boolean('available');
            } $p->version++;
            $p->save();
            $shop->increment('catalog_version');

            return $p;
        });
        if ($r->expectsJson()) {
            return response()->json(['version' => $product->version, 'price' => Money::input($product->price_minor), 'formatted' => Money::format($product->price_minor)]);
        }

        return back()->with('success', 'Precio actualizado.');
    }

    public function archive(Request $r, string $id)
    {
        DB::transaction(function () use ($r, $id) {
            $shop = Shop::whereKey($r->attributes->get('shop')->id)->lockForUpdate()->firstOrFail();
            $p = $shop->products()->where('public_id', $id)->lockForUpdate()->firstOrFail();
            $target = $p->status === 'active' ? 'archived' : 'active';
            if ($target === 'active' && $shop->products()->where('status', 'active')->count() >= $shop->productLimit()) {
                throw ValidationException::withMessages(['limit' => 'Tu plan ya alcanzó el límite de productos activos.']);
            }
            $p->update(['status' => $target, 'version' => $p->version + 1]);
            $shop->increment('catalog_version');
        });

        return back()->with('success', 'Estado del producto actualizado.');
    }

    public function export(Request $r)
    {
        $shop = $r->attributes->get('shop');

        return response()->streamDownload(function () use ($shop) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['id', 'nombre', 'precio', 'sku', 'categoria', 'disponible', 'estado'], ';', '"', '');
            foreach ($shop->products()->with('category')->orderBy('id')->lazy(200) as $p) {
                $safe = fn ($v) => preg_match('/^[=+@\-\t\r]/u', (string) $v) ? "'".$v : $v;
                fputcsv($out, array_map($safe, [$p->public_id, $p->name, Money::input($p->price_minor), $p->sku, $p->category?->name, $p->available ? 'si' : 'no', $p->status]), ';', '"', '');
            } fclose($out);
        }, $shop->slug.'-productos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
