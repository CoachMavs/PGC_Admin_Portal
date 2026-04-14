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
use Carbon\Carbon;

class PGCCertPostController extends Controller
{
    public function addPRE(Request $req)
    {
        $req->validate([
            'Recommendation' => 'required',
            'DDate' => 'nullable|date',
        ]);

        $id = $req->id;
        $Recommendation = $req->Recommendation;
        $repairlogID = $req->repairlogID;
        $DDate = $req->filled('DDate')
            ? Carbon::parse($req->DDate)->endOfDay()
            : now();

        $ReferenceNo = $this->generateReferenceCode();

        DB::table('tblpostcertificate')
            ->where('ID', $id)
            ->update([
                'Recommendation' => $Recommendation,
                'DDate' => $DDate,
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
        $assignedto = $req->query('assignedFilter');

        $query = DB::table('vpostcertificate')
            ->when($searchkey, function ($query, $searchkey) {
                $query->where(function ($subQuery) use ($searchkey) {
                    $subQuery->where('Name_of_User',   'like', "%{$searchkey}%")
                        ->orWhere('ReferenceNo', 'like', "%{$searchkey}%")
                        ->orWhere('ReferenceNo1', 'like', "%{$searchkey}%")
                        ->orWhere('Name_of_User',   'like', "%{$searchkey}%")
                        ->orWhere('Device',   'like', "%{$searchkey}%")
                        ->orWhere('Brand_and_Model',   'like', "%{$searchkey}%");
                });
            })
            ->when($assignedto === 'Only me', function ($q) {
                         $q->where('AssignedTo_emp_no', Auth::user()->emp_no);
            });

        $data = $query
            ->orderBy('ReferenceNo', 'desc')
            ->paginate(10);

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
