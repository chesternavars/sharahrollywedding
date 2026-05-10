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
            return isset($img['category']) &&
       strtolower($img['category']) === strtolower($category);
        });
    }

    // FORCE URL ONLY
    $images = array_map(function ($img) {


       // extract fileId from url (safe fallback)
    preg_match('/id=([^&]+)/', $img['url'] ?? '', $matches);
    $fileId = $matches[1] ?? null;



        return [
            'category'    => $img['category'] ?? '',
            'url'         => $img['url'] ?? null,
            'type'        => $img['type'] ?? 'image',
             'thumbnail'   => $fileId
            ? "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000"
            : null,
            'created_at'  => $img['created_at'] ?? '1970-01-01 00:00:00'
        ];
    }, $images);


  

    $images = collect($images)
    ->sortByDesc('created_at')
    ->values()
    ->all();


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