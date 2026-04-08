<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class NotificationController extends Controller
{
    public function addNotif(Request $req)
    {
        // $req->validate([
        //     'remarks' => 'required',
        //     'statusAction' => 'required|numeric',
        //     'isCurrentStatus' => 'required|numeric',
        //     'companyId' => 'required|numeric',
        //     'userAdminId' => 'required|numeric'
        // ]);

        $uuid = Uuid::uuid1()->toString();
        $notificationType = $req->notificationType;
        $recipientType = $req->recipientType;
        $senderId = $req->senderId;
        $recipientId = $req->recipientId;
        $value = $req->value;

        switch ($notificationType) {
            case 1:
                $title = 'Verified Document Attachment';
                break;
            case 2:
                $title = 'Invalid Document Attachment';
                break;
            case 3:
                $title = 'Expired Document Attachment';
                break;
            case 4:
                $title = 'Updated Employer Profile';
                break;
            case 5:
                $title = 'Approved Employer';
                break;
            case 6:
                $title = 'Suspended Employer';
                break;
            case 7:
                $title = 'Lifted Suspension of Employer';
                break;
            case 8:
                $title = 'Job Posting';
                break;
            case 9:
                $title = 'Approved Job Posting';
                break;
            case 10:
                $title = 'Disapproved Job Posting';
                break;
            case 11:
                $title = 'Application Entry';
                break;
            case 12:
                $title = 'Recommended for Interview';
                break;
            case 13:
                $title = 'Cancelled Application';
                break;
            case 14:
                $title = 'Hired Applicant';
                break;
            case 15:
                $title = 'Job Recommendation';
                break;
            case 16:
                $title = 'Disapproved Employer';
                break;
            default:
                $title = 'Unknown Notification Type';
                break;
        }


        DB::table('notifications')
            ->insert([
                'uuid' => $uuid,
                'notificationType' => $notificationType,
                'senderType' => 1,
                'recipientType' => $recipientType,
                'senderId' => $senderId,
                'recipientId' => $recipientId,
                'title' => $title,
                'value' => $value,
                'date' => now(),
                'createdAt' => now(),
                'updatedAt' => now()
            ]);

        return response()->json(['message' => 'Log added successfully']);
    }
}
