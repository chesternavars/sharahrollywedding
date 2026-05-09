<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleDriveController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120'
        ]);

        $user = session('user');

        if (!$user) {
            return redirect('/auth/google');
        }

        $accessToken = $user['token'];
        $file = $request->file('file');

        $fileName = time().'_'.$file->getClientOriginalName();
        $folderId = env('GOOGLE_DRIVE_FOLDER_ID');

        // 🟢 STEP 1: CREATE FILE METADATA
        $metadataResponse = Http::withToken($accessToken)->post(
            'https://www.googleapis.com/drive/v3/files',
            [
                'name' => $fileName,
                'parents' => [$folderId]
            ]
        );

        $fileId = $metadataResponse->json('id');

        if (!$fileId) {
            return back()->with('error', 'Failed to create file metadata');
        }

        // 🟢 STEP 2: UPLOAD FILE CONTENT
        $uploadResponse = Http::withToken($accessToken)
            ->attach(
                'data',
                file_get_contents($file),
                $fileName
            )
            ->post(
                "https://www.googleapis.com/upload/drive/v3/files/{$fileId}?uploadType=media"
            );

        return back()->with('success', 'Uploaded to Google Drive 💍');
    }



    public function uploadMultiple(Request $request)
{
    $request->validate([
        'files' => 'required',
        'files.*' => 'image|max:5120'
    ]);

    $user = session('user');

    if (!$user) {
        return redirect('/auth/google');
    }

    $accessToken = $user['token'];
    $folderId = env('GOOGLE_DRIVE_FOLDER_ID');

    $uploadedFiles = [];


    foreach ($request->file('files') as $file) {

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
}

Http::withToken($accessToken)->post(
    "https://www.googleapis.com/drive/v3/files/{$fileId}/permissions",
    [
        'role' => 'reader',
        'type' => 'anyone'
    ]
);

$uploadedFiles[] = [
    'id' => $fileId,
    'name' => $fileName,
    'url' => "https://drive.google.com/file/d/{$fileId}/view"
];



    return back()->with('success', count($uploadedFiles).' images uploaded 💍');
}
}