<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function executeQuery(Request $request) {
        $query = $request->get("query");
        $results = DB::select( DB::raw($query) );
        print_r(json_encode($results));
    }
}
