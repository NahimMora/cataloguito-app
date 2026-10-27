<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageService
{
    /** Call inside the shop transaction/lock. Processed files remain private until authorized serving. */
    public function replace(Shop $shop, UploadedFile $file, ?Product $product = null): Media
    {
        $info = @getimagesize($file->getRealPath());
        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true) || $info[0] * $info[1] > 12_000_000 || $file->getSize() > 5_000_000) {
            throw ValidationException::withMessages(['image' => 'Usá JPG, PNG o WebP de hasta 5 MB y 12 megapíxeles.']);
        }
        $source = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (! $source) {
            throw ValidationException::withMessages(['image' => 'No pudimos leer esta imagen. Elegí otro archivo.']);
        }
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            if (in_array($orientation, [2, 4, 5, 7])) {
                imageflip($source, in_array($orientation, [2, 5, 7]) ? IMG_FLIP_HORIZONTAL : IMG_FLIP_VERTICAL);
            }
            $angle = match ($orientation) {
                3 => 180,5,6 => -90,7,8 => 90,default => 0
            };
            if ($angle) {
                $source = imagerotate($source, $angle, 0);
            }
        }
        $width = imagesx($source);
        $height = imagesy($source);
        $paths = [];
        $bytes = 0;
        $prefix = 'shops/'.$shop->id.'/'.Str::uuid();
        try {
            foreach ([320, 640, 1200] as $size) {
                $w = min($size, $width);
                $h = max(1, (int) round($height * $w / $width));
                $image = imagecreatetruecolor($w, $h);
                imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
                imagecopyresampled($image, $source, 0, 0, 0, 0, $w, $h, $width, $height);
                ob_start();
                imagewebp($image, null, 82);
                $contents = ob_get_clean();
                imagedestroy($image);
                $paths[$size] = $prefix.'-'.$size.'.webp';
                $bytes += strlen($contents);
                Storage::disk('local')->put($paths[$size], $contents);
            }
            $old = $shop->media()->where('kind', $product ? 'product' : 'logo')->when($product, fn ($q) => $q->where('product_id', $product->id))->first();
            if ($shop->storage_bytes - ($old?->bytes ?? 0) + $bytes > $shop->byteLimit()) {
                throw ValidationException::withMessages(['image' => 'Tu espacio de imágenes está completo. Quitá una foto o revisá tu plan.']);
            }
            $media = Media::create(['shop_id' => $shop->id, 'product_id' => $product?->id, 'kind' => $product ? 'product' : 'logo', 'path' => $paths[1200], 'variants' => $paths, 'bytes' => $bytes, 'width' => $width, 'height' => $height]);
            $shop->storage_bytes = $shop->storage_bytes - ($old?->bytes ?? 0) + $bytes;
            $shop->save();
            if ($old) {
                $oldPaths = $old->variants;
                $old->delete();
                DB::afterCommit(fn () => Storage::disk('local')->delete(array_values($oldPaths)));
            }

            return $media;
        } catch (\Throwable $e) {
            Storage::disk('local')->delete(array_values($paths));
            throw $e;
        } finally {
            imagedestroy($source);
        }
    }

    public function remove(Shop $shop, Media $media): void
    {
        abort_unless($media->shop_id === $shop->id, 404);
        $shop->storage_bytes = max(0, $shop->storage_bytes - $media->bytes);
        $shop->save();
        $paths = $media->variants;
        $media->delete();
        DB::afterCommit(fn () => Storage::disk('local')->delete(array_values($paths)));
    }
}
