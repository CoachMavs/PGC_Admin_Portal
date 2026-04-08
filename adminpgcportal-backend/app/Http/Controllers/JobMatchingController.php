<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class JobMatchingController extends Controller
{
    public function fetchJobSeekers(Request $req)
    {
        $id = $req->id;

        $data = DB::table('vjobseekers')
            ->where('id', $id)
            ->where('datastatus', 1)
            ->get();

        return $data;
        
    }

    public function fetchEducBackground(Request $req)
    {
        $id = $req->id;

        $educ = DB::table('educational_backgrounds')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        $work = DB::table('work_experiences')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        $elig = DB::table('eligibilities')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        $skills = DB::table('skills')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        $trainings = DB::table('training_and_seminars')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        $references = DB::table('reference_persons')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();


        return response()->json([
            'educ' => $educ,
            'work' => $work,
            'elig' => $elig,
            'skills' => $skills,
            'trainings' => $trainings,
            'references' => $references

        ]);
    }

    public function fetchWorkExperiences(Request $req)
    {
        $id = $req->id;

        $data = DB::table('work_experiences')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        return $data;
    }

    public function fetchEligibilities(Request $req)
    {
        $id = $req->id;

        $data = DB::table('eligibilities')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        return $data;
    }

    public function fetchSkills(Request $req)
    {
        $id = $req->id;

        $data = DB::table('skills')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        return $data;
    }

    public function fetchTrainings(Request $req)
    {
        $id = $req->id;

        $data = DB::table('training_and_seminars')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        return $data;
    }

    public function fetchReferences(Request $req)
    {
        $id = $req->id;

        $data = DB::table('reference_persons')
            ->where('jobSeekerId', $id)
            ->where('datastatus', 1)
            ->get();

        return $data;
    }

    public function fetchApplicationID(Request $req)
    {
        $id = $req->id;
        $jobseekerid = $req->jobseekerid;

        $data = DB::table('job_applications')
            ->where('jobPostingId', $id)
            ->where('jobSeekerId', $jobseekerid)
            ->where('dataStatus', 1)
            ->first(['id']);

        return $data ? $data->id : null;
    }

     //Job Matching

     public function fetchDetailsMatching(Request $req)
     {
         $searchBusinessName = $req->query('jobFilterCompany');
         $searchJobTitle = $req->query('jobFilterTitle');
 
        $employers = DB::table('vjobs')
             ->where(function ($query) use ($searchBusinessName, $searchJobTitle) {
                 if ($searchBusinessName) {
                     $query->where('businessName', 'like', '%' . $searchBusinessName . '%');
                 }
                 if ($searchJobTitle) {
                     $query->where('title', 'like', '%' . $searchJobTitle . '%');
                 }
             })
             ->where('approvalStatus', 2)
             ->where('availabilityStatus', 1)
 
             ->orderBy('businessName', 'asc')
             ->paginate(1);
 
         return $employers;
     }

     public function fetchJobSeekersMatching(Request $req)
     {
        $educ = $req->query('jobFilterEduc');
        $work = $req->query('jobFilterWork');
        $elig = $req->query('jobFilterElig');
        $skills = $req->query('jobFilterSkills');

        $query = DB::table('vjobseekersmatching')
            ->select(DB::raw('DISTINCT id, displayName, dateOfBirth, age, citizenship, sex, civilStatus, height, mobileNo, email, websiteURL, profilePictureURL, googleAvatarURL, lookingForWork'))
            ->where(function ($query) use ($educ, $work, $elig, $skills) {
                if ($educ) {
                    $query->where(function($q) use ($educ) {
                        $q->where('nameOfSchool', 'like', '%' . $educ . '%')
                          ->orWhere('courseOrSHSStrand', 'like', '%' . $educ . '%')
                          ->orWhere('academicHonorsReceived', 'like', '%' . $educ . '%');
                    });
                }
                if ($work) {
                    $query->where(function($q) use ($work) {
                        $q->where('positionTitle', 'like', '%' . $work . '%')
                        ->orWhere('employer', 'like', '%' . $work . '%')
                        ->orWhere('jobDescription', 'like', '%' . $work . '%');
                    });
                }
                if ($elig) {
                    $query->where(function($q) use ($elig) {
                        $q->where('elibtitle', 'like', '%' . $elig . '%');
                    });
                }
                if ($skills) {
                    $query->where(function($q) use ($skills) {
                        $q->where('skills', 'like', '%' . $skills . '%');
                    });
                }

            })
            ->where('lookingForWork', 1);

        $data = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query)
            ->orderBy('displayName', 'asc')
            ->paginate(1);

        return $data;
     }

     public function fetchCheckApplication(Request $req)
     {
         $id = $req->id;
         $jobseekerid = $req->jobseekerid;
     
         $data = DB::table('job_applications')
             ->where('jobPostingId', $id)
             ->where('jobSeekerId', $jobseekerid)
             ->where('dataStatus', 1)
             ->where('applicationStatus', '<>', 0)
             ->get();
     
         return $data;
     }

     public function Refer(Request $req)
     {
        $validator = Validator::make($req->all(), [
            'jobPostingId' => [
                'required',
                Rule::unique('job_suggestions')->where(function ($query) use ($req) {
                    return $query->where('jobPostingId', $req->jobPostingId)
                                 ->where('jobSeekerId', $req->jobSeekerId);
                }),
            ],
            'jobSeekerId' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'This job posting has already been suggested to the jobseeker.'], 422);
        }
        

        $userAdminId = $req->userAdminId;
        $jobPostingId = $req->jobPostingId;
        $jobSeekerId = $req->jobSeekerId;
        $uuid = Uuid::uuid1()->toString();
    
        DB::table('job_suggestions')
            ->insert([
                'uuid' => $uuid,
                'userAdminId' => $userAdminId,
                'jobPostingId' => $jobPostingId,
                'jobSeekerId' => $jobSeekerId,
                'createdAt' => now(),
                'updatedAt' => now()
            ]);
    
        return response()->json(['message' => 'Log added successfully']);
     }
}
