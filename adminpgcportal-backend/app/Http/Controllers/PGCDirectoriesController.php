<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PGCDirectoriesController extends Controller
{
    public function fetch(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');

        $employers = DB::table('tbldirectory')

            ->where(function ($query) use ($searchkey) {
                $query->where('Office', 'like', '%' . $searchkey . '%')
                    ->orWhere('Nname', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('Office', 'asc')
            ->orderBy('TelNo', 'asc')
            ->paginate(perPage: 10);
        return $employers;
    }


    public function updateDirectory(Request $req)
    {

        $rules = [
            'Nname' => 'required',
            'Office' => 'required',
            'TelNo' => 'required'
        ];

        $req->validate($rules);

        $id = $req->id;
        $Nname = $req->Nname;
        $Office = $req->Office;
        $TelNo = $req->TelNo;

        if ($id == null) {
            // Insert new record
            DB::table('tbldirectory')->insert([

                'Nname' => $Nname,
                'Office'   => $Office,
                'TelNo'    => $TelNo
            ]);
        } else {
            DB::table('tbldirectory')
                ->where('ID', $id)
                ->update([
                    'Nname' => $Nname,
                    'Office'   => $Office,
                    'TelNo'    => $TelNo
                ]);
        }
    }

     public function DeleteReq(Request $req)
    {
        $id = $req->id;

        DB::table('tbldirectory')
            ->where('ID', $id)
            ->delete();
    }
}
