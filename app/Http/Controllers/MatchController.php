<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Matches::whereOr('user1', 2)->whereOr('user2', 2)->get()->all());
    }
}
