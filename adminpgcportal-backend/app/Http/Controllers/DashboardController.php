<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{

public function fetchPhotos(Request $req)
{
    $user = Auth::user();
    $result = [];

    if ($user && $user->DP) {
        $path = "{$user->DP}";

        $exists = Storage::disk('ftp')->exists($path);

        if ($exists) {
            $imageContent = Storage::disk('ftp')->get($path);
            $base64ImageEncoded = base64_encode($imageContent);

            $extension = pathinfo($path, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'gif':
                    $mimeType = 'image/gif';
                    break;
                default:
                    $mimeType = 'application/octet-stream';
                    break;
            }

            $base64Image = 'data:' . $mimeType . ';base64,' . $base64ImageEncoded;

            Log::info('Generated path: ' . $path);

            $result[] = [
                'src' => $base64Image
            ];

            return response()->json(['files' => $result]);
        } else {
            return response()->json(['message' => 'Profile picture not found.'], 404);
        }
    }
}

   
}
