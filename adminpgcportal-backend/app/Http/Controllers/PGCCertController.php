<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PGCCertController extends Controller
{
    public function addPRE(Request $req)
    {
        $req->validate([
            'repairlogID' => 'required',
            'Diagnosis' => 'required',
            'Recommendation' => 'required',
            'DDate' => 'nullable|date',
        ]);

        $id = $req->id;
        $repairlogID = $req->repairlogID;
        $Diagnosis = $req->Diagnosis;
        $Recommendation = $req->Recommendation;
        $DDate = $req->filled('DDate')
            ? Carbon::parse($req->DDate)->endOfDay()
            : now();

        if (empty($id)) {
            $ReferenceNo = $this->generateReferenceCode();

            DB::table('tblprecertificate')->insert([
                'repairlogID' => $repairlogID,
                'Diagnosis' => $Diagnosis,
                'Recommendation' => $Recommendation,
                'ReferenceNo' => $ReferenceNo,
                'DDate' => $DDate,
            ]);
        } else {

            DB::table('tblprecertificate')
                ->where('ID', $id)
                ->update([
                    'repairlogID' => $repairlogID,
                    'Diagnosis' => $Diagnosis,
                    'Recommendation' => $Recommendation,
                    'DDate' => $DDate,
                ]);
        }

        DB::table('tblrepairlogHistory')
            ->insert([
                'repairlogID' => $repairlogID,
                'ActionTaken' => 'Issued Pre Certificate',
            ]);

        DB::table('tblrepairlog')
            ->where('ID', $repairlogID)
            ->update([
                'DateReturned' => now(),
                'Status_Remarks' => 7,
            ]);
    }

    public function fetchRequest(Request $req)
    {

        try {
            DB::statement('CALL sp_fix_tblrepairlog_no_dept_div()');

            $data = DB::table('vprecertreq')
                ->select(
                    'ReferenceNo',
                    'AssignedTo',
                    'DateReceived',
                    'Name_of_User',
                    'Type_of_Device',
                    'Brand_and_Model',
                    'ProblemsEncountred',
                    'ID',
                    'contactno',
                    'DeptDesc',
                    'DivDesc'
                )
                ->orderBy('DateReceived', 'asc')
                ->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchPre(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $assignedto = $req->query('assignedFilter');


        $query = DB::table('vprecertificate')
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

        DB::table('tblprecertificate')
            ->where('id', $id)
            ->delete();
    }

    private function generateReferenceCode()
    {
        $year = date('Y');
        $prefix = 'ISU-' . $year . '-PRE-';

        $latest = DB::table('tblprecertificate')
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
