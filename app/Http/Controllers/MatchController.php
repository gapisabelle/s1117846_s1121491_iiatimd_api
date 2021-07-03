<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Matches::where(function ($a) use ($request) {
        	$a->where('user1', $request->user()->id)->whereOr('user2', $request->user()->id);
        })->get()->all());
    }
}
