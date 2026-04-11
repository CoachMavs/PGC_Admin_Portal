<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PGCCertPostController extends Controller
{
    public function addPRE(Request $req)
    {
        $req->validate([
            'Recommendation' => 'required',
        ]);

        $id = $req->id;
        $Recommendation = $req->Recommendation;
        $repairlogID = $req->repairlogID;

        $ReferenceNo = $this->generateReferenceCode();

        DB::table('tblpostcertificate')
            ->where('ID', $id)
            ->update([
                'Recommendation' => $Recommendation,
                'DDate' => now(),
            ]);

        DB::table('tblpostcertificate')
            ->where('ID', $id)
            ->whereNull('ReferenceNo')
            ->update([
                'ReferenceNo' => $ReferenceNo,
            ]);

        DB::table('tblrepairlog')
            ->where('ID', $repairlogID)
            ->update([
                'Status_Remarks' => 8,
                'DateReturned' => now(),
            ]);

        DB::table('tblrepairlogHistory')
            ->insert([
                'repairlogID' => $repairlogID,
                'ActionTaken' => 'Issued Post Certificate',

            ]);

        broadcast(new MessageSent("triggerPostInspection"));
    }

    public function fetchRequest(Request $req)
    {
        try {
            // Execute the stored procedure first
            DB::statement('CALL sp_fix_tblrepairlog_no_dept_div()');

            $data = DB::table('vpostcertreq')
                ->select(
                    'ReferenceNo',
                    'Name_of_User',
                    'Type_of_Device',
                    'Brand_and_Model',
                    'ID',
                    'DeptDesc',
                    'AssignedTo',
                    'repairlogID'
                )
                ->orderBy('ID', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function fetchPost(Request $req)
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

        $query = DB::table('vpostcertificate')
            ->when($searchkey, function ($query, $searchkey) {
                $query->where(function ($subQuery) use ($searchkey) {
                    $subQuery->where('Name_of_User',   'like', "%{$searchkey}%")
                        ->orWhere('ReferenceNo', 'like', "%{$searchkey}%")
                        ->orWhere('DeptDesc',   'like', "%{$searchkey}%")
                        ->orWhere('Device',   'like', "%{$searchkey}%")
                        ->orWhere('Brand_and_Model',   'like', "%{$searchkey}%")
                        ->orWhere('DivDesc',    'like', "%{$searchkey}%");
                });
            })
            ->when($assignedto === 'Only me', function ($q) {
                $q->where('AssignedTo', Auth::user()->empISU);
            });

        if ($datefrom) {
            $query->where('DDate', '>=', $datefrom);
        }
        if ($dateto) {
            $query->where('DDate', '<=', $dateto);
        }

        $data = $query
            ->orderBy('ReferenceNo', 'desc')
            ->paginate(5);

        return $data;
    }

    public function DeleteReq(Request $req)
    {
        $id = $req->id;

        DB::table('tblpostcertificate')
            ->where('ID', $id)
            ->update([
                'Recommendation' => null,
                'ReferenceNo' => null,
                'DDate' => null,
            ]);

        broadcast(new MessageSent("triggerPostInspection"));
    }

    private function generateReferenceCode()
    {
        $year = date('Y');
        $prefix = 'ISU-' . $year . '-POST-';

        $latest = DB::table('tblpostcertificate')
            ->where('ReferenceNo', 'like', $prefix . '%')
            ->orderBy('ReferenceNo', 'desc')
            ->value('ReferenceNo');

        if ($latest) {
            // Extract the last numeric part, remove leading zeros, default to 0 if not numeric
            $numberPart = trim(Str::afterLast($latest, '-'));
            $number = is_numeric($numberPart) ? (int)$numberPart : 0;
            $nextNumber = str_pad($number + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '00001';
        }

        return $prefix . $nextNumber;
    }
}
