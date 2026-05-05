<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
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
        return response()->json(['message' => 'Upload API working']);
    }
    private function drive()
{


    $client = new \Google\Client();

    $client->setAuthConfig(
        storage_path('app/google/navarrozawedding-04594488a7be.json')
    );

    
   $client->addScope(\Google\Service\Drive::DRIVE);

    return new \Google\Service\Drive($client);
}

    /**
     * Upload multiple files to Google Drive
     */
 

public function upload(Request $request)
{
    $request->validate([
        'files' => 'required|array',
        'files.*' => 'image|mimes:jpg,jpeg,png,jfif,webp|max:5120'
    ]);

    $accessToken = $this->getAccessToken();
    $folderId = '1ImXddhW1JcdwCS2mlTTMvbkGOmeDR4Zf';

    $results = [];

    foreach ($request->file('files') as $file) {

        if (!$file->isValid()) {
            continue;
        }

        $fileName = time().'_'.$file->getClientOriginalName();

        /*
        |----------------------------------------------------
        | STEP 1: CREATE EMPTY FILE IN DRIVE
        |----------------------------------------------------
        */
        $meta = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/drive/v3/files', [
                'name' => $fileName,
                'parents' => [$folderId]
            ])->json();

        if (!isset($meta['id'])) {
            $results[] = [
                'error' => 'metadata failed',
                'file' => $fileName,
                'google' => $meta
            ];
            continue;
        }

        $fileId = $meta['id'];

        /*
        |----------------------------------------------------
        | STEP 2: UPLOAD FILE CONTENT (FIXED STREAM)
        |----------------------------------------------------
        */

       $mime = $file->getClientMimeType();

      $upload = Http::withToken($accessToken)
    ->withHeaders([
        'Content-Type' => $file->getClientMimeType()
    ])
    ->withBody(
        fopen($file->getRealPath(), 'r'),
        $file->getClientMimeType()
    )
    ->put("https://www.googleapis.com/upload/drive/v3/files?uploadType=media");
    
        if (!$upload->successful()) {
            $results[] = [
                'error' => 'upload failed',
                'file' => $fileName,
                'google' => $upload->body()
            ];
            continue;
        }

        /*
        |----------------------------------------------------
        | STEP 3: MAKE FILE PUBLIC
        |----------------------------------------------------
        */
        Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                'role' => 'reader',
                'type' => 'anyone'
            ]);

        /*
        |----------------------------------------------------
        | RESULT
        |----------------------------------------------------
        */
        $results[] = [
            'id' => $fileId,
            'link' => "https://drive.google.com/uc?id={$fileId}"
        ];
    }

    return response()->json([
        'message' => 'Upload successful 💍',
        'files' => $results
    ]);
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


}
