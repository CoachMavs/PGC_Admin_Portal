<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PGCEmployeesController extends Controller
{
    public function fetchEmployees(Request $req)
    {
        $id = $req->id;
        $searchkey = $req->query('searchkey');

        $employers = DB::table('vEmployee')
       
            ->where(function ($query) use ($searchkey) {
                $query->where('fullname', 'like', '%' . $searchkey . '%');
            })
            ->orderBy('DeptDesc', 'asc')
            ->orderBy('DivDesc', 'asc')
            ->orderBy('Last_name', 'asc')
            ->paginate(perPage: 10);
        return $employers;
    }  
}
