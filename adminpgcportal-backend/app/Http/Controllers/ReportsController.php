<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{

    public function fetchOpCostIndi(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $mecost = DB::table('vopcostnew')
            ->where(function ($query) use ($searchkey) {
                $query->where('id', '=', $searchkey);
            })
            ->get();
        return $mecost;
    }
    public function fetchMeCostIndi(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $mecost = DB::table('vmecostnew')
            ->where(function ($query) use ($searchkey) {
                $query->where('id', '=', $searchkey);
            })
            ->get();
        return $mecost;
    }

    public function fetchMeCost(Request $req)
    {
        $searchkey = $req->query('searchkey');
        if ($searchkey === 'All') {
            $mecost = DB::table('vmecostnew')->get();
        } else {
            $mecost = DB::table('vmecostnew')
                ->where(function ($query) use ($searchkey) {
                    $query->where('Class', 'like', '%' . $searchkey . '%');
                })
                ->get();
        }

        return $mecost;
    }
    public function fetchOpCost(Request $req)
    {
        $searchkey = $req->query('searchkey');
        if ($searchkey === 'All') {
            $opcost = DB::table('vopcostnew')->get();
        } else {
            $opcost = DB::table('vopcostnew')
                ->where(function ($query) use ($searchkey) {
                    $query->where('Class', 'like', '%' . $searchkey . '%');
                })
                ->get();
        }
        return $opcost;
    }

    public function fetchOpCost2(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $opcost = [];
        if ($searchkey === 'All') {
            $opcost = DB::table('vopcostnew')->get();
        } else {
            $opcost = DB::table('vopcostnew')
                ->where(function ($query) use ($searchkey) {
                    $query->where('Class', 'like', '%' . $searchkey . '%');
                })
                ->get();
        }
        return view('opcost2')
            ->with('opcost',$opcost);
    }
    public function fetchClass(Request $req)
    {
        $classifix = DB::table('buildingclass')->get();
        return $classifix;
    }
}
