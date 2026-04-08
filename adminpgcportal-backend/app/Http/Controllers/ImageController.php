<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SFTP;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        // Connect to FTP server
        $sftp = new SFTP('172.16.50.37');
        if (!$sftp->login('lballad', '!@#kulangot321')) {
            return response()->json(['error' => 'FTP login failed'], 500);
        }

        // Handle each uploaded file
        foreach ($request->file('images') as $image) {
            // Get the original filename
            $filename = $image->getClientOriginalName();

            // Upload the file to the FTP server
            $sftp->put('//172.16.50.37/bcomcs/' . $filename, $image->get());

            // Optionally, you may want to store the filename in your database
        }

        return response()->json(['message' => 'Images uploaded successfully']);
    }
}
