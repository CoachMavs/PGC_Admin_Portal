<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BuildingListController extends Controller
{

    function compressImage($source, $destination, $quality)
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);

        }

        // Save compressed image
        imagejpeg($image, $destination, $quality);

        // Free up memory
        imagedestroy($image);
    }

    public function fetchPictures(Request $req)
    {

        $id = $req->id;

        if ($id > 0) {
            $buildingspic = DB::table('buildingspic')
                ->where('id', $id)
                ->get();
            return $buildingspic;
        }
    }

    public function LoadCombobox(Request $req)
    {
        $buildingclass = DB::table('buildingclass')->select('id', 'description')->get();
        return $buildingclass;
    }

    public function fetchBuildingClass(Request $req)
    {
        try {
            $costs = $req->query('costs');
            info('The requested cost is:', ['costs' => $costs]);
            $buildingclass = DB::table('buildingclass')
                ->select('id', 'Description')
                ->where('Rangef', '<=', $costs)
                ->where('Ranget', '>=', $costs)
                ->get();
            return $buildingclass;
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
            // Return an error response
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


    public function fetchBuildings(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');

        if (strlen($searchkey) > 0) {
            $buildings = DB::table('vbuildings')
                ->where(function ($query) use ($searchkey) {
                    $query->where('name', 'like', '%' . $searchkey . '%')
                        ->orWhere('bid', 'like', '%' . $searchkey . '%')
                        ->orWhere('loc', 'like', '%' . $searchkey . '%');
                })
                ->orderBy('bid')
                ->take(50)
                ->get();
            return $buildings;
        } else {
            $buildingclass = DB::table('vbuildings')
                ->orderBy('bid')
                ->take(50)
                ->get();
            return $buildingclass;
        }
    }

    public function addBuilding(Request $req)
    {
        $req->validate([
            'Bid' => 'required',
            'Name' => 'required',
            'Loc' => 'required',
            'Cost' => 'required|numeric',
            'BClassID' => 'required|numeric',
            'Description' => 'nullable',
            'Details' => 'nullable',
            'files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20000'
        ]);
        $Bid = $req->Bid;
        $Name = $req->Name;
        $Loc = $req->Loc;
        $Cost = $req->Cost;
        $BClassID = $req->BClassID;
        $Description = $req->Description;
        $Details = $req->Details;

        $id = $req->id;

        if ($id > 0) {
            DB::table('buildings')
                ->where('id', $id)
                ->update([
                    'Bid' => $Bid,
                    'Name' => $Name,
                    'Loc' => $Loc,
                    'Cost' => $Cost,
                    'BClassID' => $BClassID,
                    'Description' => $Description,
                    'Details' => $Details,
                ]);

        } else {
            $id = DB::table('buildings')
                ->insertGetId([
                    'Bid' => $Bid,
                    'Name' => $Name,
                    'Loc' => $Loc,
                    'Cost' => $Cost,
                    'BClassID' => $BClassID,
                    'Description' => $Description,
                    'Details' => $Details,
                ]);

        }

        if ($req->hasFile('files')) {
            $files = $req->file('files');

            foreach ($files as $file) {
                $buildingId = $id;
                $fileName = $file->getClientOriginalName();
                $extName = $file->extension();
                $hashName = $file->hashName();
                $mimeType = $file->getMimeType();

                $this->compressImage($file->path(), 'output.jpg', 70);

                $file_id = DB::table('buildingspic')
                    ->insertGetId([
                        'buildingId' => $buildingId,
                        'fileName' => $fileName,
                        'extName' => $extName,
                        'hashName' => $hashName,
                        'mimeType' => $mimeType,
                    ]);

                // echo $fileName . ' - ' .  $extName . ' - ' .  $hashName . ' - ' . $mimeType . ' <br>';
                // Upload the file to the storage
                $content = file_get_contents(public_path('output.jpg'));
                Storage::disk('ftp')->put($file_id . '.' . $extName, $content);
                //$pics = Storage::disk('ftp')->get($file_id. '.'. $extName);
            }
        }


    }
    public function deleteBuilding(Request $req)
    {
        $id = $req->id;

        if ($id > 0) {
            DB::table('buildings')
                ->where('id', $id)
                ->delete();
        }
    }
}