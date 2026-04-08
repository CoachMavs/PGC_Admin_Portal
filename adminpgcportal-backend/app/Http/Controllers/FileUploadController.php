<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $uploadedFiles = [];

            foreach ($files as $file) {
                // Store each file in the storage/app/public directory
                $path = $file->store('public');
                
                // You may want to store additional information in the database, like the file name, path, etc.
                $uploadedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    // Add more fields as needed
                ];
            }

            // Now you can save the information about the uploaded files in your database
            // For example, you can use Eloquent to create records in your database
            // E.g., \App\Models\File::createMany($uploadedFiles);

            return response()->json(['message' => 'Files uploaded successfully', 'files' => $uploadedFiles], 200);
        }

        return response()->json(['error' => 'No files were uploaded'], 400);
    }
}
