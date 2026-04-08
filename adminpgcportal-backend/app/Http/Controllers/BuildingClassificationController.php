<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BuildingClassificationController extends Controller
{
    //
    // app/Http/Controllers/AuthController.php
    
    public function fetchOpeMain(Request $req){
        $buildingclass = DB::table('buildingclass')->get();
        // $buildingclass = DB::table('buildingclass')->orderBy('Rangef', 'asc')->get();
        return $buildingclass;
    }

    public function addOpeMain(Request $req){
        $req->validate([
            'Description' => 'required',
            // 'Rangef' => 'required|numeric',
            // 'Ranget' => 'required|numeric',
            'Ratiof' => 'required|numeric',
            'Ratiot' => 'required|numeric',
        ]);

        $Description = $req->Description;
        $Rangef = $req->Rangef;
        $Ranget = $req->Ranget;
        $Ratiof = $req->Ratiof;
        $Ratiot = $req->Ratiot;

        $id = $req->id;

        if($id > 0){
            DB::table('buildingclass')
            ->where('id', $id)
            ->update([
                'Description' => $Description,
                // 'Rangef' => $Rangef,
                // 'Ranget' => $Ranget,
                'Ratiof' => $Ratiof,
                'Ratiot' => $Ratiot,
            ]);

        }else{
            DB::table('buildingclass')
            ->insert([
                'Description' => $Description,
                // 'Rangef' => $Rangef,
                // 'Ranget' => $Ranget,
                'Ratiof' => $Ratiof,
                'Ratiot' => $Ratiot,
            ]);

        }
        
    }

    public function deleteOpeMain(Request $req){
        
        $id = $req->id;

        if($id > 0){
            DB::table('buildingclass')
            ->where('id', $id)
            ->delete();
        }
    }
}
