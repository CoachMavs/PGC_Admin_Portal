<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExportToPDFController extends Controller
{

    public function fetchRepairs(Request $req)
    {
        $datefrom   = $req->query('datefrom');
        $dateto     = $req->query('dateto');
        $assignedto = $req->query('assignedFilter');

        if ($datefrom) {
            try {
                // Set datefrom to the start of the day
                $datefrom = \Carbon\Carbon::createFromFormat('M-d-Y', $datefrom)
                    ->startOfDay()
                    ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid datefrom format'], 400);
            }
        }

        if ($dateto) {
            try {
                // Set dateto to the end of the day
                $dateto = \Carbon\Carbon::createFromFormat('M-d-Y', $dateto)
                    ->endOfDay()
                    ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid dateto format'], 400);
            }
        }

        $query = DB::table('vWebRepairs')
            ->select([
                'ID as ID',
                DB::raw("DATE_FORMAT(DateReceived, '%b %d, %Y %h:%i %p') as Date Received"),
                'Employee as Portal by',
                'DeptDesc as Department',
                'DivDesc as Division',
                'Device as Device',
                'ProblemsEncountred as Problems Encountred',
                'Name_of_User as User',
                'Receivedby as Received By',
                'AssignedTo as Assigned To',
                'ActionsTaken as Actions Taken',
                'RepairStatus as Repair Status',
                'ReturnedTo as Returned To',
                DB::raw("DATE_FORMAT(DateReturned, '%b %d, %Y %h:%i %p') as Date Returned"),
                'ContactNo as Contact No',
                'OtherDevInfo as Other Info',
                'comment as Comment',
            ])
            ->when($assignedto === 'Only me', function ($q) {
                $q->where('AssignedTo', Auth::user()->empISU);
            });

        if ($datefrom) {
            $query->where('DateReceived', '>=', $datefrom);
        }
        if ($dateto) {
            $query->where('DateReceived', '<=', $dateto);
        }

        $data = $query
                  ->whereIn('RepairStatusID', [4, 5, 7, 8, 9])
            ->orderBy('DateReceived', 'asc')
            ->get();

        return response()->json($data);
    }


    //------------------------------------------------------------------------------------------------


    public function fetchZoom(Request $req)
    {
        $datefrom = $req->query('datefrom');
        $dateto   = $req->query('dateto');

        if ($datefrom) {
            try {
                $datefrom = \Carbon\Carbon::createFromFormat('M-d-Y', $datefrom)
                    ->startOfDay()
                    ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid datefrom format'], 400);
            }
        }

        if ($dateto) {
            try {
                $dateto = \Carbon\Carbon::createFromFormat('M-d-Y', $dateto)
                    ->endOfDay()
                    ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid dateto format'], 400);
            }
        }

        $query = DB::table('vWebZoomRequest')
            ->select([
                'id as ID',
                DB::raw("DATE_FORMAT(date_requested, '%b %d, %Y %h:%i %p') as date_requested"),
                'Employee as Requested by',
                'DeptDesc as Department',
                'DivDesc as Division',
                'topics as Topic',
                DB::raw("DATE_FORMAT(start_datetime, '%b %d, %Y %h:%i %p') as start_datetime"),
                DB::raw("DATE_FORMAT(end_datetime, '%b %d, %Y %h:%i %p') as end_datetime"),
                'noofparticipants as No. of Participants',
                'AssignedTo as Assigned To',
                DB::raw("DATE_FORMAT(approve_datetime, '%b %d, %Y %h:%i %p') as approve_datetime"),
                'zoomaccount as Zoom Account'
            ]);

         if ($datefrom) {
            $query->where('start_datetime', '>=', $datefrom);
        }
        if ($dateto) {
            $query->where('start_datetime', '<=', $dateto);
        }

        $data = $query
            ->whereNotNull('approve_datetime')
            ->where('start_datetime', '<=', now())
            ->orderBy('start_datetime', 'asc')
            ->get();

        return response()->json($data);
    }
}
