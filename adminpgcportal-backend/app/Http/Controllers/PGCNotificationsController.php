<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;

class PGCNotificationsController extends Controller
{
    public function triggerPostInspection(Request $req)
    {
        broadcast(new MessageSent("triggerPostInspection"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }

    public function triggerWasteCertificate(Request $req)
    {
        broadcast(new MessageSent("triggerWasteCertificate"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }

    public function triggerZoomPending(Request $req)
    {
        broadcast(new MessageSent("triggerZoomPending"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }

    public function triggerZoomUpcoming(Request $req)
    {
        broadcast(new MessageSent("triggerZoomUpcoming"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }

    public function triggerZoomPrev(Request $req)
    {
        broadcast(new MessageSent("triggerZoomUpcoming"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }

    public function triggerPendingRepairs(Request $req)
    {
        broadcast(new MessageSent("triggerPendingRepairs"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }

    public function triggerForReceivingRepairs(Request $req)
    {
        broadcast(new MessageSent("triggerForReceivingRepairs"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }


    public function triggerCurrentRepairs(Request $req)
    {
        broadcast(new MessageSent("triggerCurrentRepairs"));

        return response()->json([
            'status' => 'Broadcast sent successfully!'
        ]);
    }
}
