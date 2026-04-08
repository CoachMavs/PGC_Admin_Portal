<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashController extends Controller
{

    public function fetchPostInspection(Request $req)
    {
        $data = DB::table('vpostcertreq')->count();
        return $data;
    }

    public function fetchWasteCerticate(Request $req)
    {
        $data = DB::table('vpwastecertreq')->count();
        return $data;
    }

    public function fetchNewZoomRequest(Request $req)
    {
        $data = DB::table('vWebZoomRequest')
            ->where('approve_datetime', null)
            ->orWhere('approve_datetime', '')
            ->count();
        return $data;
    }

    public function fetchUpcomingZoom(Request $req)
    {
        $data = DB::table('vWebZoomRequest')
            ->where('approve_datetime', '!=', null)
            ->where('start_datetime', '!=', null)
            ->where('start_datetime', '>=', now())
            ->count();
        return $data;
    }

    public function fetchForApproval(Request $req)
    {
        $data = DB::table('vWebRepairs')
            ->where('RepairStatusID', 0)
            ->whereNull('DateReceived')
            ->whereNotIn('ID', function ($query) {
                $query->select('repairlogID')
                    ->from('vpwastecertreq');
            })
            ->count();
        return $data;
    }

    public function fetchForReceiving(Request $req)
    {
        $data = DB::table('tblrepairlog')
            ->where('Status_Remarks', 6)
            ->count();
        return $data;
    }

    public function fetchOngoing(Request $req)
    {
        $data = DB::table('tblrepairlog')
            ->where('Status_Remarks', 1)
            ->count();
        return $data;
    }

    public function fetchRepaired(Request $req)
    {
        $data = DB::table('tblrepairlog')
            ->where('Status_Remarks', 2)
            ->count();
        return $data;
    }

    public function fetchNotRepaired(Request $req)
    {
        $data = DB::table('tblrepairlog')
            ->where('Status_Remarks', 3)
            ->count();
        return $data;
    }
}
