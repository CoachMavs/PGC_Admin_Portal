<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Imagick;

class BuildingGalleryController extends Controller
{
    function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        }elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
          
        }
            
         // Save compressed image
        imagejpeg($image, $destination, $quality);
        
        // Free up memory
        imagedestroy($image);
    }
    public function SavePix(Request $req){
     
        $req->validate([
            'files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20000'
        ]);
      
        $id = $req->id;
        
        

        if ($req->hasFile('files')) {
            $files = $req->file('files');
       
            foreach ($files as $file) {
                $buildingId = $id;
                $fileName = $file->getClientOriginalName();
                $extName = $file->extension();
                $hashName = $file->hashName();
                $mimeType = $file->getMimeType();

                $this->compressImage($file->path(), 'output.jpg',70);  

                $file_id = DB::table('buildingspic')
                ->insertGetId([
                    'buildingId' => $buildingId,
                    'fileName' => $fileName,
                    'extName' => $extName,
                    'hashName' => $hashName,
                    'mimeType' => $mimeType,
                ]);
    
                // Upload the file to the storage
                $content = file_get_contents(public_path('output.jpg'));
                Storage::disk('ftp')->put($file_id. '.'. $extName, $content);
                
            }
       
        }     
    }
    
    
    

    public function deletePix(Request $req){
        
        $id = $req->id;

        if($id > 0){
            DB::table('buildingspic')
            ->where('id', $id)
            ->delete();
        }
    }

    public function fetchPixIds(Request $req)
    {
        $filename = $req->filename;
    
        // Attempt to delete the file from the FTP server
        try {
            Storage::disk('ftp')->delete($filename);
            $result[] = $filename; // Add filename to result if deletion is successful
        } catch (\Exception $e) {
            $errors[] = $filename . ': ' . $e->getMessage(); // Log error message
        }
    
        // Return appropriate response based on the result
    }
    
        
    public function fetchPhotos(Request $req)
    {
        $buildingId = $req->buildingId;
        Log::info('fetchPhotos called with buildingId: ' . $buildingId);
    
        if ($buildingId > 0) {
            $info = DB::table('buildingspic')
                ->where('buildingId', $buildingId)
                ->get();
    
            $result = [];
    
            foreach ($info as $item) {
                // $url = Storage::disk('ftp')->url("{$item->id}.{$item->extName}");
                $path = "{$item->id}.{$item->extName}";
                $imageContent = Storage::disk('ftp')->get($path);
                $base64ImageEncoded = base64_encode($imageContent);

                // Optionally, you can prepend the MIME type to the Base64 string
                $base64Image = 'data:' . $item->mimeType . ';base64,' . $base64ImageEncoded;
                $url = Storage::disk('ftp')->url($path);
                $exists = Storage::disk('ftp')->exists($path);
                Log::info('Generated URL: ' . $url);
                $result[] = [
                    'id' => $item->id,
                    'extName' => $item->extName,
                    'url' => $url,
                    'src' => $base64Image,
                    'exist' =>  $exists
                ];
            }
    
            return response()->json(['files' => $result]);
        }
    
        Log::error('Invalid building ID: ' . $buildingId);
        return response()->json(['message' => 'Invalid building ID'], 400);
    }
    
    
}