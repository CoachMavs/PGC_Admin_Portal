<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\select;

class AuthController extends Controller
{
    //
    // app/Http/Controllers/AuthController.php

    public function login(Request $req)
    {

        $username = $req->username;
        $password = $req->password;
        $verifyUser = DB::table('visu')
            ->where('username', $username)
            ->Where('password', $password)
            ->count();

        if ($verifyUser == 0) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $user = User::where('username', $req['username'])->firstOrFail();

        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addMinutes(540)
        )->plainTextToken;


        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'emp_no' => $user,
        ]);
    }

    public function me(Request $req)
    {
        $user = $req->user();
        $userInfo = DB::table('visu')
            ->select('id','empISU')
            ->where('emp_no', $user->emp_no)
            ->first();

        return $userInfo;
    }

    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
    }

    
}
