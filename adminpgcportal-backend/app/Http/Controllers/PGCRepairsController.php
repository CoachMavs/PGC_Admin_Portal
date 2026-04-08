<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PGCRepairsController extends Controller
{

    public function ReceiveReq(Request $req)
    {
        $id = $req->id;
        $rules = [
            'assignedto' => 'required',
            'ContactNo' => 'required',
            'Device' => 'required',
            'BrandModel' => 'required',
            'emp_no' => 'required',
            'dateRcvd' => 'required',
        ];

        // Only require InitialProblemsEncountered if $id is not null
        if ($id !== null) {
            $rules['InitialProblemsEncountered'] = 'required';
        } else {
            $rules['ProblemsEncountred'] = 'required';
        }

        $req->validate($rules);


        $dateRcvd = $req->dateRcvd;
        $assignedto = $req->assignedto;
        $ContactNo = $req->ContactNo;
        $Device = $req->Device;
        $BrandModel = $req->BrandModel;
        $InitialProblemsEncountered = $req->InitialProblemsEncountered;
        $ProblemsEncountred = $req->ProblemsEncountred;
        $OtherInfo = $req->OtherInfo;
        $emp_no = $req->emp_no;

        $Receivedby = Auth::user()->emp_no;

        if ($id == null) {
            // Insert new record
            DB::table('tblrepairlog')->insert([

                'DateReceived' => $dateRcvd,
                'AssignedTo'   => $assignedto,
                'contactno'    => $ContactNo,
                'Device'       => $Device,
                'Brand_and_Model'   => $BrandModel,
                'ProblemsEncountred' => $ProblemsEncountred,
                'InitialProblemsEncountered' => $ProblemsEncountred,
                'OtherDevInfo'    => $OtherInfo,
                'emp_no' => $emp_no,
                'Name_of_User' => $emp_no,
                'Status_Remarks' => 1,
                'Receivedby' => $Receivedby
            ]);
        } else {
            DB::table('tblrepairlog')
                ->where('id', $id)
                ->update([
                    'DateReceived' => $dateRcvd,
                    'AssignedTo'   => $assignedto,
                    'contactno'    => $ContactNo,
                    'Device'       => $Device,
                    'Brand_and_Model'   => $BrandModel,
                    'InitialProblemsEncountered' => $InitialProblemsEncountered,
                    'ProblemsEncountred' => $ProblemsEncountred,
                    'OtherDevInfo'    => $OtherInfo,
                    'emp_no' => $emp_no,
                    'Status_Remarks' => 1,
                    'Receivedby' => $Receivedby
                ]);
        }
    }
    public function AddActions(Request $req)
    {
        $req->validate([
            'repairlogID' => 'required',
            'actionsTaken' => 'required'
        ]);


        $repairlogID = $req->repairlogID;
        $actionsTaken = $req->actionsTaken;

        DB::table('tblrepairlogHistory')->insert([
            'repairlogID' => $repairlogID,
            'ActionTaken'   => $actionsTaken
        ]);
    }

    public function DeleteReq(Request $req)
    {
        $id = $req->id;

        DB::table('tblrepairlog')
            ->where('id', $id)
            ->delete();
    }

    public function UpdateStatusNotRepaired(Request $req)
    {

        $req->validate([
            'id' => 'required',
            'status' => 'required',
            'DateReturned' => 'required',
            'ReturnedToNo' => 'required',
            'Comments' => 'required'
        ]);

        $id = $req->id;
        $status = $req->status;
        $DateReturned = $req->DateReturned;
        $ReturnedToNo = $req->ReturnedToNo;
        $Comments = $req->Comments;

        DB::table('tblrepairlog')
            ->where('id', $id)
            ->update([
                'Status_Remarks' => $status,
                'DateReturned' => $DateReturned,
                'ReturnedTo' => $ReturnedToNo,
                'Comment' => $Comments
            ]);
    }

    public function UpdateStatusRepaired(Request $req)
    {

        $req->validate([
            'id' => 'required',
            'status' => 'required',
            'DateReturned' => 'required',
            'ReturnedToNo' => 'required'
        ]);

        $id = $req->id;
        $status = $req->status;
        $DateReturned = $req->DateReturned;
        $ReturnedToNo = $req->ReturnedToNo;


        DB::table('tblrepairlog')
            ->where('id', $id)
            ->update([
                'Status_Remarks' => $status,
                'DateReturned' => $DateReturned,
                'ReturnedTo' => $ReturnedToNo
            ]);
    }

    public function ApproveReq(Request $req)
    {
        $req->validate([
            'id' => 'required',
            'status' => 'required'
        ]);

        $id = $req->id;
        $status = $req->status;

        DB::table('tblrepairlog')
            ->where('id', $id)
            ->update([
                'Status_Remarks' => $status
            ]);
    }


    public function fetchUsers(Request $req)
    {
        $data = DB::table('vEmployee')
            ->select(
                'emp_no',
                DB::raw("CONCAT(Last_name, ', ', First_name) as full_name"),
                'DeptDesc',
                'DivDesc'
            )
            ->orderBy('Last_name', 'asc')
            ->orderBy('First_name', 'asc')
            ->get();

        return $data;
    }

    public function fetchActionsTaken(Request $req)
    {
        $id = $req->id;

        $data = DB::table('tblrepairlogHistory')
            ->where('repairlogID', $id)
            ->orderBy('DDate', 'desc')
            ->get();
        return $data;
    }

    public function fetchStatus(Request $req)
    {
        $data = DB::table('tblrepairstatus')
            ->where('ID', '!=', 0)
            ->where('ID', '!=', 6)
            ->get();
        return $data;
    }

    public function fetchTech(Request $req)
    {
        $data = DB::table('visu')
            ->select('emp_no', 'empISU')
            ->where('Active', 1)
            ->orderBy('empISU', 'asc')
            ->get();
        return $data;
    }

    public function updateRequest(Request $req)
    {
        broadcast(new MessageSent("pending_request_updated"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }


    public function fetchRequest(Request $req)
    {
        $data = DB::table('vWebRepairs')
            ->where('RepairStatusID', 0)
            ->whereNull('DateReceived')
            ->whereNotIn('ID', function ($query) {
                $query->select('repairlogID')
                    ->from('vpwastecertreq');
            })
            ->get();

        return $data;
    }


    public function fetchForReceiving(Request $req)
    {
        $searchkey = $req->query('searchkey');

        $data = DB::table('vWebRepairs')
            ->when($searchkey, function ($q) use ($searchkey) {
                $q->where(function ($sub) use ($searchkey) {
                    $sub->where('Employee',   'like', "%{$searchkey}%")
                        ->orWhere('ReferenceNo', 'like', "%{$searchkey}%")
                        ->orWhere('DeptDesc',   'like', "%{$searchkey}%")
                        ->orWhere('DivDesc',    'like', "%{$searchkey}%")
                        ->orWhere('Name_of_User', 'like', "%{$searchkey}%")
                        ->orWhere('InitialProblemsEncountered', 'like', "%{$searchkey}%");
                });
            })


            ->where('RepairStatusID', 6);
        return $data->get();
    }

    public function fetchCurrent(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $assignedto = $req->query('assignedFilter');


        $data = DB::table('vWebRepairs')
            ->when($searchkey, function ($q) use ($searchkey) {
                $q->where(function ($sub) use ($searchkey) {
                    $sub->where('Employee',   'like', "%{$searchkey}%")
                        ->orWhere('ReferenceNo', 'like', "%{$searchkey}%")
                        ->orWhere('DeptDesc',   'like', "%{$searchkey}%")
                        ->orWhere('DivDesc',    'like', "%{$searchkey}%")
                        ->orWhere('Name_of_User', 'like', "%{$searchkey}%")
                        ->orWhere('ProblemsEncountred', 'like', "%{$searchkey}%");
                });
            })

            ->when($assignedto === 'Only me', function ($q) {
                $q->where('AssignedTo', Auth::user()->empISU);
            })
            ->whereNotNull('DateReceived')
            ->whereIn('RepairStatusID', [0, 1, 2, 3])
            ->orderBy('DateReceived', 'desc')
            ->paginate(10);

        $empISU = Auth::user()->empISU;

        return [
            'data' => $data,
            'empISU' => $empISU
        ];
    }


    public function fetchPrev(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $datefrom  = $req->query('datefrom');
        $dateto    = $req->query('dateto');
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
            ->when($searchkey, function ($query, $searchkey) {
                $query->where(function ($subQuery) use ($searchkey) {
                    $subQuery->where('Employee',   'like', "%{$searchkey}%")
                        ->orWhere('ReferenceNo', 'like', "%{$searchkey}%")
                        ->orWhere('DeptDesc',   'like', "%{$searchkey}%")
                        ->orWhere('DivDesc',    'like', "%{$searchkey}%")
                        ->orWhere('Name_of_User', 'like', "%{$searchkey}%")
                        ->orWhere('ProblemsEncountred', 'like', "%{$searchkey}%");
                });
            })
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
            ->orderBy('DateReceived', 'desc')
            ->paginate(10);

        return $data;
    }
}
