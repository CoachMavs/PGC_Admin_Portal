<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BuildingOpeMainController extends Controller
{
    public function fetchOpeMain(Request $req){
        $onmpercentage = DB::table('onmpercentage')
            ->get();
        return $onmpercentage;
    }

    public function saveOpeMain(Request $req) {
        $data = $req->all();
       

        try {
            foreach ($data as $item) {
                DB::table('onmpercentage')
                    ->where('id', $item['id'])
                    ->update([
                        'onmpercentage' => $item['onmpercentage'],
                    ]);
            }
        
            return response()->json(['message' => 'Data updated successfully']);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()]);
        }
        
    }

}