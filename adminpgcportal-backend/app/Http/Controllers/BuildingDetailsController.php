<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BuildingDetailsController extends Controller
{
    public function fetchDetails(Request $req){
        $id = $req->query('id');
    
        $buildingdetails = DB::table('vbuildingdetails')
        ->where('id', $id)
        ->get();
        return $buildingdetails;
    } 

    public function fetchPixDetails(Request $req){
        $id = $req->query('id');
    
        $buildingpix = DB::table('buildingspic')
        ->where('buildingId', $id)
        ->get();
        return $buildingpix;
    } 
}