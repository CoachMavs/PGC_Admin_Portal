<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JobSeekersController extends Controller
{
    public function fetchJobSeekers(Request $req)
    {
        $details = DB::table('vjobseekers')
            ->orderBy('lastname', 'asc')
            ->orderBy('createdAt', 'asc')
            ->paginate(10);

        return $details;
    }


    public function fetchJobSeekers1(Request $req)
    {
        $details = DB::table('vjobseekers')
            ->get();
        return $details;
    }

    // public function fetchJobSeekersForApproval(Request $req)
    // {
    //     $jobpostinguuid = $req->jobpostinguuid;
    //     $jobapplicationsdataStatus = $req->jobapplicationsdataStatus;
    //     $details = DB::table('vjobseekersapplied')
    //         ->where('jobpostinguuid',  $jobpostinguuid)
    //         ->where('applicationStatus', $jobapplicationsdataStatus)
    //         ->orderBy('firstName', 'asc')
    //         ->paginate(9);

    //     return $details;
    // }
    public function fetchJobSeekersForApproval(Request $req)
    {
        $jobpostinguuid = $req->input('jobpostinguuid');
        $jobapplicationsdataStatus = $req->input('jobapplicationsdataStatus');
        Log::info('jobapplicationsdataStatus: ' . $jobapplicationsdataStatus);
        
        $query = DB::table('vjobseekersapplied')
            ->where('jobpostinguuid', $jobpostinguuid);
    
        if ($jobapplicationsdataStatus != 3) {
            $query->where('applicationStatus', $jobapplicationsdataStatus);
        }
    
        $details = $query->get();
    
        return $details;
    }

    public function fetchJobSeekersForMatching(Request $req)
    {
        $details = DB::table('vjobseekersapplied')    
            ->where('applicationStatus', 1)
            ->orderBy('firstName', 'asc')
            ->paginate(9);

        return $details;
    }

    public function updateJobApplicationStatus(Request $req)
    {
        $id = $req->input('id');
   
        try {
            DB::table('job_applications')
                ->where('id', $id)
                ->update(['applicationStatus' => 2]);

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function updateMatching(Request $req)
    {
        $id = $req->input('id');
        $statusvalue = $req->input('statusvalue');
   
        try {
            DB::table('job_applications')
                ->where('id', $id)
                ->update(['applicationStatus' => $statusvalue]);

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}