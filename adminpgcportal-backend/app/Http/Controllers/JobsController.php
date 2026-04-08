<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JobsController extends Controller
{

    public function fetchPending(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');
        $status = $req->query('status', 1); 

        $query = DB::table('vjoblist')
            ->where(function ($query) use ($searchkey) {
                $query->where('description', 'like', '%' . $searchkey . '%')
                    ->orWhere('businessName', 'like', '%' . $searchkey . '%')
                    ->orWhere('title', 'like', '%' . $searchkey . '%')
                    ->orWhere('jobtown', 'like', '%' . $searchkey . '%')
                    ->orWhere('jobprov', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('postingDate', 'asc');

            if ($status != 0) {
                $query->where('approvalStatus', $status);
            }
            else{
                $query->whereIn('approvalStatus', [1, 3]);
            }
            
            $list = $query->paginate(10);

        return $list;
    }

    public function fetch(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');
        $status = $req->query('status', 1);  // Default value is 1

        $query = DB::table('vjoblist')
            ->where('approvalStatus', 2) // Apply approvalStatus filter
            ->where(function ($query) use ($searchkey) {
                $query->where('description', 'like', '%' . $searchkey . '%')
                    ->orWhere('businessName', 'like', '%' . $searchkey . '%')
                    ->orWhere('title', 'like', '%' . $searchkey . '%')
                    ->orWhere('jobtown', 'like', '%' . $searchkey . '%')
                    ->orWhere('jobprov', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('postingDate', 'asc');

        // Apply the availabilityStatus filter only if the status is not 3
        if ($status != 2) {
            $query->where('availabilityStatus', $status);
        }

        // Fetch paginated results
        $list = $query->paginate(10);

        return $list;
    }

    public function fetchselected(Request $req)
    {
        $companyuuid = $req->companyuuid;
        $searchkey = $req->query('searchkey');
        $status = $req->query('status', 1);  // Default value is 1

        $query = DB::table('vjoblist')
            ->where('companyUuid', $companyuuid)
            ->where('approvalStatus', 2) // Apply approvalStatus filter
            ->where(function ($query) use ($searchkey) {
                $query->where('description', 'like', '%' . $searchkey . '%')
                    ->orWhere('title', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('postingDate', 'asc');

        // Apply the availabilityStatus filter only if the status is not 3
        if ($status != 2) {
            $query->where('availabilityStatus', $status);
        }

        // Fetch paginated results
        $list = $query->paginate(10);

        return $list;
    }

    public function fetchselected1(Request $req)
    {
        $id = $req->companyuuid;
        $searchkey = $req->query('searchkey');
        $status = $req->query('status', 1);  // Default value is 1

        $query = DB::table('vjoblist')
            ->where(function ($query) use ($id, $searchkey) {
                $query->where(function ($query) use ($id) {
                    $query->where('companyUuid', $id)
                        ->where('approvalStatus', 2);
                })
                    ->orWhere(function ($query) use ($searchkey) {
                        $query->where('description', 'like', '%' . $searchkey . '%')
                            ->orWhere('title', 'like', '%' . $searchkey . '%');
                    });
            })
            ->orderBy('postingDate', 'asc');

        // Apply the availabilityStatus filter only if the status is not 2
        if ($status != 2) {
            $query->where('availabilityStatus', $status);
        }

        // Execute the query and return the results
        $results = $query->get();

        return response()->json($results);
    }

    public function fetchDetails(Request $req)
    {
        $id = $req->query('id');
        $details = DB::table('vjobs')
            ->where('uuid', $id)
            ->get();
        return $details;
    }

    public function updateStatus(Request $req)
    {
        $id = $req->input('id');
        $statusValue = $req->input('statusvalue');
        $availStatus = 0;

        if ($statusValue == 2) {
            $availStatus = 1;
        }

        try {
            DB::table('job_postings')
                ->where('id', $id)
                ->update([
                    'approvalStatus' => $statusValue,
                    'availabilityStatus' => $availStatus,
                ]);

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // EmpProfile

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
}
