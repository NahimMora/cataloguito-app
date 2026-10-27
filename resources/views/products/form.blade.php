<x-app-layout :title="$product->exists ? 'Editar producto' : 'Nuevo producto'" subtitle="Dale a tus clientes todo lo que necesitan para elegir."><x-slot:actions><a
            class="button secondary" href="{{ route('products.index') }}">Volver a productos</a></x-slot:actions>
    <form method="post" enctype="multipart/form-data"
        action="{{ $product->exists ? route('products.update', $product->public_id) : route('products.store') }}"
        class="form-layout">@csrf @if ($product->exists)
            @method('PUT')<input type="hidden" name="version" value="{{ old('version', $product->version) }}">
        @endif
        <div class="card">
            <section class="form-section">
                <h2>Lo esencial</h2>
                <p>Un buen nombre hace que tu producto sea fácil de encontrar.</p>
                <div class="field"><label for="name">Nombre del producto</label><input name="name"
                        id="name" value="{{ old('name', $product->name) }}" required maxlength="120"
                        placeholder="Por ejemplo, Taza de cerámica Arena"></div>
                <div class="field"><label for="description">Descripción <small>opcional</small></label>
                    <textarea name="description" id="description" rows="4" maxlength="5000"
                        placeholder="Materiales, medidas, detalles que lo hacen especial…">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="form-grid">
                    <div class="field"><label for="price">Precio en pesos</label><span class="money-field"><b>$</b><input name="price"
                            id="price" value="{{ old('price', \App\Support\Money::input($product->price_minor)) }}"
                            inputmode="decimal" required placeholder="12.500"></span><small class="field-help">Podés escribir 12500 o 12.500. Los centavos se separan con coma.</small></div>
                    <div class="field"><label for="previous_price">Precio anterior
                            <small>opcional</small></label><span class="money-field"><b>$</b><input name="previous_price" id="previous_price"
                            value="{{ old('previous_price', \App\Support\Money::input($product->previous_price_minor)) }}"
                            inputmode="decimal" placeholder="15.000"></span></div>
                </div>
            </section>
            <section class="form-section">
                <h2>Orden y disponibilidad</h2>
                <p>Todo en su lugar, sin complicaciones.</p>
                <div class="form-grid">
                    <div class="field"><label for="category_id">Categoría</label><select name="category_id"
                            id="category_id">
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('categories.index') }}" target="_blank"
                            class="field-help text-link">Administrar categorías ↗</a>
                    </div>
                    <div class="field"><label for="sku">SKU o código <small>opcional</small></label><input
                            name="sku" id="sku" value="{{ old('sku', $product->sku) }}" maxlength="60"
                            placeholder="No necesitás uno para vender"></div>
                </div>
                <div class="field"><label for="sort_order">Orden en el catálogo</label><input type="number"
                        name="sort_order" id="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                        min="0" max="99999" required><small class="field-help">Los números menores aparecen
                        primero.</small></div><input type="hidden" name="available" value="0"><label
                    class="checkbox-label"><input type="checkbox" name="available" value="1"
                        @checked(old('available', $product->available))>Disponible para recibir pedidos</label>
            </section>
        </div>
        <aside class="stack">
            <div class="card card-padding" x-data="imagePreview(@js($product->media?->url()))">
                <h2 style="margin-bottom:18px">La primera impresión</h2><template x-if="url"><img
                        :src="url" alt="Vista previa de la foto" class="photo-preview" width="640" height="640"></template>
                <div class="photo-preview" x-show="!url"><x-icon name="image" :size="45" /></div>
                <div class="field"><label for="image">Foto principal</label><input type="file" name="image"
                        id="image" accept="image/jpeg,image/png,image/webp" @change="select"><small
                        class="field-help">JPG, PNG o WebP. Hasta 5 MB y 12 megapíxeles. Una buena foto hace la
                        diferencia.</small></div>
                @if ($product->media)
                    <label class="checkbox-label"><input type="checkbox" name="remove_image" value="1">Quitar foto
                        actual</label>
                @endif
            </div><button class="button dark full" type="submit"><x-icon name="check"
                    :size="17" />{{ $product->exists ? 'Guardar cambios' : 'Agregar producto' }}</button><a
                href="{{ route('products.index') }}" class="button secondary full">Cancelar</a>
        </aside>
    </form>
</x-app-layout>
