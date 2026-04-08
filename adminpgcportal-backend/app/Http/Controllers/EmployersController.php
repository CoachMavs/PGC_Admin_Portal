<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmployersController extends Controller
{
    public function fetchAEmployers(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');

        $employers = DB::table('vcompanies')
            ->where('accountStatus', 2)
            ->where(function ($query) use ($searchkey) {
                $query->where('businessName', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('businessName', 'asc')
            ->paginate(perPage: 10);
        return $employers;
    }

    public function fetchDEmployers(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');

        $employers = DB::table('vcompanies')
            ->whereIn('accountStatus', [1, 3])
            ->where(function ($query) use ($searchkey) {
                $query->where('businessName', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('businessName', 'asc')
            ->paginate(perPage: 10);
        return $employers;
    }
    public function fetchSEmployers(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');

        $employers = DB::table('vcompanies')
            ->where('accountStatus', 4)
            ->where(function ($query) use ($searchkey) {
                $query->where('businessName', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('businessName', 'asc')
            ->paginate(perPage: 10);
        return $employers;
    }

    public function fetchEmpProfile(Request $req)
    {
        $id = $req->query('id');
        $details = DB::table('vcompanies')
            ->where('uuid1', $id)
            ->get();
        return $details;
    }

    public function updateStatus(Request $req)
    {
        $id = $req->input('id');
        $statusvalue = $req->input('statusvalue');

        // $id = $req->input('companyId');
        // $statusValue = $req->input('statusAction');


        try {
            DB::table('user_companies')
                ->where('companyId', $id)
                ->update(['accountStatus' => $statusvalue]);

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function updateAttachmentStatus(Request $req)
    {
        $id = $req->input('id');
        $statusValue = $req->input('statusvalue');

        try {
            DB::table('company_complied_requirements')
                ->where('uploadedAttachmentId', $id)
                ->update(['attachmentStatus' => $statusValue]);

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function countApproval(Request $req)
    {
        $id = $req->input('id');
        $jobappstatus = $req->input('jobappstatus');
        $count = DB::table('vjobseekersapplied')
            ->where('jobpostingid', $id)
            ->where('applicationStatus', $jobappstatus)
            ->count();
        return $count;
    }

    public function addLog(Request $req)
    {
        $req->validate([
            'remarks' => 'required',
            'statusAction' => 'required|numeric',
            'isCurrentStatus' => 'required|numeric',
            'companyId' => 'required|numeric',
            'userAdminId' => 'required|numeric'
        ]);

        $remarks = $req->remarks;
        $statusAction = $req->statusAction;
        $isCurrentStatus = $req->isCurrentStatus;
        $companyId = $req->companyId;
        $userAdminId = $req->userAdminId;

        DB::table('company_account_status_action')
            ->insert([
                'remarks' => $remarks,
                'statusAction' => $statusAction,
                'isCurrentStatus' => $isCurrentStatus,
                'companyId' => $companyId,
                'userAdminId' => $userAdminId,
                'createdAt' => now(),
                'updatedAt' => now()
            ]);
            
            return response()->json(['message' => 'Log added successfully']);
    }
}
