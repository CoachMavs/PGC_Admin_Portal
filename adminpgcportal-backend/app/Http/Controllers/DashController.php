<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashController extends Controller
{
    public function fetchSummary(Request $req)
    {
        $postInspectionRequests = DB::table('vpostcertreq')->count();
        $wasteCertificationRequests = DB::table('vpwastecertreq')->count();
        $newZoomRequests = DB::table('vWebZoomRequest')
            ->whereNull('approve_datetime')
            ->orWhere('approve_datetime', '')
            ->count();
        $upcomingZoomMeetings = DB::table('vWebZoomRequest')
            ->whereNotNull('approve_datetime')
            ->whereNotNull('start_datetime')
            ->where('start_datetime', '>=', now())
            ->count();
        $repairRequestsForApproval = DB::table('vWebRepairs')
            ->where('RepairStatusID', 0)
            ->whereNull('DateReceived')
            ->whereNotIn('ID', function ($query) {
                $query->select('repairlogID')
                    ->from('vpwastecertreq');
            })
            ->count();
        $repairRequestsForReceiving = DB::table('tblrepairlog')
            ->where('Status_Remarks', 6)
            ->count();
        $ongoingRepair = DB::table('tblrepairlog')
            ->where('Status_Remarks', 1)
            ->count();
        $forPickUpRepaired = DB::table('tblrepairlog')
            ->where('Status_Remarks', 2)
            ->count();
        $forPickupNotRepaired = DB::table('tblrepairlog')
            ->where('Status_Remarks', 3)
            ->count();

        return response()->json([
            'postInspectionRequests' => $postInspectionRequests,
            'wasteCertificationRequests' => $wasteCertificationRequests,
            'newZoomRequests' => $newZoomRequests,
            'upcomingZoomMeetings' => $upcomingZoomMeetings,
            'repairRequestsForApproval' => $repairRequestsForApproval,
            'repairRequestsForReceiving' => $repairRequestsForReceiving,
            'ongoingRepair' => $ongoingRepair,
            'forPickUpRepaired' => $forPickUpRepaired,
            'forPickupNotRepaired' => $forPickupNotRepaired,
        ]);
    }

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
