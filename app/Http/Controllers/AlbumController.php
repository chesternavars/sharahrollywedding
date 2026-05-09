<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW ALBUM PAGE
    |--------------------------------------------------------------------------
    */

   public function index(Request $request)
{
    $images = $this->getImages();

    if (!is_array($images)) {
        $images = [];
    }

    $category = $request->category ?? 'All';

    // FILTER
    if ($category !== 'All') {
        $images = array_filter($images, function ($img) use ($category) {
            return isset($img['category']) && $img['category'] === $category;
        });
    }

    // FORCE URL ONLY
    $images = array_map(function ($img) {

        return [
            'category' => $img['category'] ?? '',

            // 🔥 ALWAYS USE URL ONLY
            'url' => $img['url'] ?? null
        ];
    }, $images);

    return view('album', compact('images', 'category'));
}

    /*
    |--------------------------------------------------------------------------
    | READ JSON FILE (SAFE)
    |--------------------------------------------------------------------------
    */

 private function getImages()
{
    $path = storage_path('app/images.json');

    if (!file_exists($path)) {
        return [];
    }

    $images = json_decode(file_get_contents($path), true);

    return is_array($images) ? $images : [];
}

    /*
    |--------------------------------------------------------------------------
    | SAVE IMAGE (OPTIONAL - if same controller gamit nimo)
    |--------------------------------------------------------------------------
    */

    private function saveImage($newImage)
    {
        $path = storage_path('app/images.json');

        if (!file_exists($path)) {
            file_put_contents($path, json_encode([], JSON_PRETTY_PRINT));
        }

        $images = json_decode(file_get_contents($path), true);

        if (!is_array($images)) {
            $images = [];
        }

        // prevent duplicate
        foreach ($images as $img) {
            if (($img['file_id'] ?? null) === ($newImage['file_id'] ?? null)) {
                return;
            }
        }

        $images[] = $newImage;

        file_put_contents(
            $path,
            json_encode($images, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }
}