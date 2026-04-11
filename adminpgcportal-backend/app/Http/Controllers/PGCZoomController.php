<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Events\PortalNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PGCZoomController extends Controller
{
    public function setZoomLink(Request $req)
    {
        $req->validate([
            'zoomLink' => 'required',
            'zoomID' => 'required'
        ]);
        $zoomLink = $req->zoomLink;
        $zoomID = $req->zoomID;

        $emp_no = Auth::user()->emp_no;
        $id = $req->id;

        DB::table('zoom_meetingrequest')
            ->where('id', $id)
            ->update([
                'zoomlink' => $zoomLink,
                'MeetingID' => $zoomID,
                'assignedto' => $emp_no,
                'approve_datetime' => date('Y-m-d H:i:s')
            ]);

        Log::info('setZoomLink reached');
        Log::info('Active Pusher config', [
            'key' => config('broadcasting.connections.pusher.key'),
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'host' => config('broadcasting.connections.pusher.options.host'),
            'port' => config('broadcasting.connections.pusher.options.port'),
            'scheme' => config('broadcasting.connections.pusher.options.scheme'),
        ]);
        broadcast(new MessageSent("triggerZoomPending"));
        broadcast(new MessageSent("triggerZoomUpcoming"));
        Log::info('Broadcasting PortalNotification triggerZoomPending');
        broadcast(new PortalNotification("triggerZoomPending"));
        Log::info('Broadcasting PortalNotification triggerZoomUpcoming');
        broadcast(new PortalNotification("triggerZoomUpcoming"));
    }

    public function fetchRequest(Request $req)
    {
        $data = DB::table('vWebZoomRequest')
            ->where('approve_datetime', null)
            ->orWhere('approve_datetime', '')
            ->get();
        return $data;
    }

    public function fetchUpcoming(Request $req)
    {
        $data = DB::table('vWebZoomRequest')
            ->where('approve_datetime', '!=', null)
            ->where('start_datetime', '!=', null)
            ->where('start_datetime', '>=', now())
            ->get();
        return $data;
    }

    public function fetchPrev(Request $req)
    {
        $searchkey = $req->query('searchkey');
        $datefrom    = $req->query('datefrom');
        $dateto      = $req->query('dateto');


        if ($datefrom) {
            try {
                // Set datefrom to the start of the day
                $datefrom = \Carbon\Carbon::createFromFormat('M-d-Y', $datefrom)
                    ->startOfDay() // Sets time to 00:00:00
                    ->format('Y-m-d H:i:s'); // Format to include time
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid datefrom format'], 400);
            }
        }

        if ($dateto) {
            try {
                // Set dateto to the end of the day
                $dateto = \Carbon\Carbon::createFromFormat('M-d-Y', $dateto)
                    ->endOfDay() // Sets time to 23:59:59
                    ->format('Y-m-d H:i:s'); // Format to include time
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid dateto format'], 400);
            }
        }

        $query = DB::table('vWebZoomRequest')
            ->when($searchkey, function ($query, $searchkey) {
                $query->where(function ($subQuery) use ($searchkey) {
                    $subQuery->where('Employee', 'like', '%' . $searchkey . '%')
                        ->orWhere('DeptDesc', 'like', '%' . $searchkey . '%')
                        ->orWhere('topics', 'like', '%' . $searchkey . '%');
                });
            });

        // Apply date filters regardless of searchkey
        if ($datefrom) {
            $query->where('start_datetime', '>=', $datefrom);
        }
        if ($dateto) {
            $query->where('start_datetime', '<=', $dateto);
        }

        // Continue with the rest of the query
        $data = $query
            ->whereNotNull('approve_datetime')
            ->where('start_datetime', '<=', now())
            ->orderBy('start_datetime', 'desc')
            ->paginate(perPage: 10);

        return $data;
    }
}
