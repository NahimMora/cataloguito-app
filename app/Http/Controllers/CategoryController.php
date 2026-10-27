<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $r)
    {
        return view('categories.index', ['categories' => $r->attributes->get('shop')->categories()->withCount('products')->orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function save(Request $r, ?string $id = null)
    {
        $shop = $r->attributes->get('shop');
        $cat = $id ? $shop->categories()->where('public_id', $id)->firstOrFail() : null;
        $data = $r->validate(['name' => ['required', 'string', 'max:80', Rule::unique('categories', 'name')->where('shop_id', $shop->id)->ignore($cat?->id)], 'sort_order' => 'required|integer|min:0|max:9999']);
        if (! $cat && $shop->categories()->count() >= 100) {
            return back()->withErrors(['name' => 'Podés crear hasta 100 categorías.']);
        }
        $data['slug'] = Str::slug($data['name']) ?: 'categoria';
        if ($cat) {
            $cat->update($data);
        } else {
            $shop->categories()->create($data);
        }

        return back()->with('success', 'Categoría guardada.');
    }

    public function delete(Request $r, string $id)
    {
        $shop = $r->attributes->get('shop');
        DB::transaction(function () use ($shop, $id) {
            $cat = $shop->categories()->where('public_id', $id)->firstOrFail();
            $shop->products()->where('category_id', $cat->id)->update(['category_id' => null]);
            $cat->delete();
            $shop->increment('catalog_version');
        });

        return back()->with('success', 'Categoría eliminada. Sus productos siguen disponibles sin categoría.');
    }
}
