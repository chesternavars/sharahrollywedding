<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Service\Drive;
use Illuminate\Support\Facades\Http;

class UploadController extends Controller
{
    private $folderId = '1ImXddhW1JcdwCS2mlTTMvbkGOmeDR4Zf';

    /**
     * Initialize Google Drive Service
     */

    public function index()
{
   
    return view('home');
}
    

private function drive()
{
       


    $client->setAuthConfig(storage_path('app/google/navarrozawedding-79fc6ac12117.json'));

    $client->addScope(Drive::DRIVE);

    return new Drive($client);
}

    /**
     * Upload multiple files to Google Drive
     */
 

 public function uploadFile(Request $request)
{
    $request->validate([
        'category' => 'required|string',
        'files' => 'required',
       'files.*' => 'mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:20480'
    ]);

    $user = session('user');

    if (!$user) {
        return redirect('/auth/google');
    }

    $accessToken = $user['token'];

     $category = $request->category;

    // 🎯 CATEGORY → FOLDER MAP
    $folderMap = [
        'Bride laughing genuinely' => '11nTnqcvs9kYCpFCthB33Y2d3mRXouySF',
        'Groom fixing his suit/tie' => '1TMyKCYboqJxXnpBug1Br9460mpsNDbmx',
        'Parents getting emotional' => '1cZKV-dCL8-Zf9YBR8Z8vq9kbUBQg52r-',
        'First dance spin' => '1Vk2aKP9G_ntACKpvUnDpkdLfNF9ypqO_',
        'A stolen kiss' => '1jjU6CQ307NFG16m9oTp6rjHYGgMs3Hhw',
        'Group selfie with strangers' => '1dm5agUCMTng0RVV_5jy_erSZ-6bl2cC2',
    ];


      $folderId = $folderMap[$category] ?? env('GOOGLE_DRIVE_FOLDER_ID');


    //$folderId = env('GOOGLE_DRIVE_FOLDER_ID');

    $uploadedFiles = [];


    foreach ($request->file('files') as $file) {

    // $fileName = $category.'_'.time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());

    $fileName = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '', $file->getClientOriginalName());

    // 1. CREATE FILE
    $meta = Http::withToken($accessToken)->post(
    'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true',
    [
        'name' => $fileName,
        'parents' => [$folderId]
        
    ]
);

    $fileId = $meta->json('id');

    if (!$fileId) {
        dd($meta->body()); // IMPORTANT DEBUG
    }

    // 2. UPLOAD CONTENT (IMPORTANT FIX HERE)
    $upload = Http::withToken($accessToken)
    ->withHeaders([
        'Content-Type' => $file->getMimeType(),
    ])
    ->withBody(
        file_get_contents($file->getRealPath()),
        $file->getMimeType()
    )
    ->patch(
        "https://www.googleapis.com/upload/drive/v3/files/{$fileId}?uploadType=media&supportsAllDrives=true"
    );



    if ($upload->failed()) {
        dd($upload->body()); // show real Google error
    }

    if ($upload->status() == 401) {
    // TOKEN EXPIRED
    session()->forget('user');
    return response()->json([
        'error' => 'google_expired',
        'redirect' => url('/auth/google')
    ], 401);
}






  // SAVE TO JSON

$type = str_starts_with($file->getMimeType(), 'video/')
    ? 'video'
    : 'image';


    if ($type === 'video') {
    $url = "https://drive.google.com/uc?export=view&id={$fileId}";
} else {
    $url = "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000";
}

      $this->saveImages([
    'file_id' => $fileId,
    'category' => $category,
    'type' => $type,
    'url' => $url,
    'thumbnail' => "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000",
    'created_at' => now()->toDateTimeString() // 👈 added
]);



 


}

 Http::withToken($accessToken)->post(
    "https://www.googleapis.com/drive/v3/files/{$fileId}/permissions",
    [
        'role' => 'reader',
        'type' => 'anyone'
    ]
);


$type = str_starts_with($file->getMimeType(), 'video/')
    ? 'video'
    : 'image';

      if ($type === 'video') {
    $url = "https://drive.google.com/uc?export=view&id={$fileId}";
} else {
    $url = "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000";
}

$uploadedFiles[] = [
    'id' => $fileId,
    'name' => $fileName,
    'type' => $type,
    'thumbnail' => "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000",
    'url' => $url
];




 // $this->saveImages($images);





    return back()->with('success', count($uploadedFiles).' images uploaded 💍');
}



private function getAccessToken()
{
    $credentials = json_decode(
        file_get_contents(storage_path('app/google/navarrozawedding-79fc6ac12117.json')),
        true
    );

    $client = new \GuzzleHttp\Client();

    $jwt = $this->generateJwt($credentials);

    $response = $client->post('https://oauth2.googleapis.com/token', [
        'form_params' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]
    ]);

    return json_decode($response->getBody(), true)['access_token'];
}





private function getGoogleAccessToken()
{
    $credentials = json_decode(file_get_contents(storage_path('app/google/navarrozawedding-79fc6ac12117.json')), true);

    $now = time();

    $jwtHeader = base64_encode(json_encode([
        'alg' => 'RS256',
        'typ' => 'JWT'
    ]));

    $jwtClaim = base64_encode(json_encode([
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/drive',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600
    ]));

    $unsignedJwt = $jwtHeader.'.'.$jwtClaim;

    openssl_sign(
        $unsignedJwt,
        $signature,
        $credentials['private_key'],
        'sha256'
    );

    $jwt = $unsignedJwt.'.'.base64_encode($signature);

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);

     if (!$response->successful()) {
        throw new \Exception($response->body());
    }

    return $response->json()['access_token'];
}




private function generateJwt($credentials)
{
    $header = base64_encode(json_encode([
        'alg' => 'RS256',
        'typ' => 'JWT'
    ]));

    $now = time();

    $payload = base64_encode(json_encode([
        'iss' => $credentials['client_email'],
        'scope' => 'https://www.googleapis.com/auth/drive.file',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $now + 3600
    ]));

    $unsignedJwt = $header . "." . $payload;

    openssl_sign(
        $unsignedJwt,
        $signature,
        $credentials['private_key'],
        'SHA256'
    );

    return $unsignedJwt . "." . base64_encode($signature);
}


public function testToken()
{
    return response()->json([
        'token' => $this->getAccessToken()
    ]);
}



public function album(Request $request)
{
    $category = $request->category;

    $images = $this->getImages();

    if ($category && $category != 'All') {
        $images = array_filter($images, function ($img) use ($category) {
            return $img['category'] == $category;
        });
    }

    return view('album', compact('images', 'category'));
}



private function getImages()
{
    $path = storage_path('app/images.json');

    if (!file_exists($path)) {
        file_put_contents($path, json_encode([]));
    }

    return json_decode(file_get_contents($path), true);
}

private function saveImages($newImage)
{
    $path = storage_path('app/images.json');

    // create file if not exists
    if (!file_exists($path)) {
        file_put_contents($path, json_encode([]));
    }

    // read existing
    $images = json_decode(file_get_contents($path), true);

    if (!is_array($images)) {
        $images = [];
    }

    // prevent null data
    if (empty($newImage)) {
        return;
    }

    // add new image
    $images[] = $newImage;

    // save safely
    file_put_contents(
        $path,
        json_encode($images, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}


}
