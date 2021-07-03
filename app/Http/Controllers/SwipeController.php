<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Swipe;

class SwipeController extends Controller {
    /**
        * Display a listing of the resource.
        *
        * @return Response
        */
    public function index(Request $request)
    {
        return response()->json(Swipe::where('user_id', $request->user()->id)->get());
    }

    /**
        * Store a newly created resource in storage.
        *
        * @return Response
        */
    public function store(Request $request) {
        $validator = \Validator::make($request->all(), [
            'filmId' => 'required|integer',
            'liked' => 'required|integer|between:-1,1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validated = $validator->validated();

        $swiped = new Swipe();
        $swiped->filmid = $validated["filmId"];
        $swiped->liked = $validated["liked"];
        $swiped->user_id = $request->user()->id;

        return response()->json(["result" => $swiped->save()]);
    }

    /**
        * Update the specified resource in storage.
        *
        * @param  int  $id
        * @return Response
        */
    public function update($id)
    {
        //
    }

    /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return Response
        */
    public function destroy($id)
    {
        //
    }
}
