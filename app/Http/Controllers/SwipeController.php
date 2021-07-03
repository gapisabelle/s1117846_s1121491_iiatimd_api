<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Swipe;
use App\Models\Matches;

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

        // if (\Cache::get('lastSwipe')) {
        // 	\Cache::put('lastSwipe', now(), now()->addMinutes(1));

        // 	// TODO: Search for other users and get a random one
        // }

        $validated = $validator->validated();

        $otherUserSwipe = Swipe::where('id', '!=', $request->user()->id)
        		->where('filmid', $validated["filmId"])
        		->where('liked', 1)
        		->get();

        if ($otherUserSwipe != null) $otherUserSwipe = $otherUserSwipe->random();

       	$result = Matches::where('filmid', $otherUserSwipe->filmid)->where(function($a) {
       		$a->where('user1', $otherUserSwipe->user_id)->orWhere('user2', $otherUserSwipe->user_id);
       	})->get()->first();

       	if ($result == null) {
       		$match = new Matches();
       		$match->filmid = $otherUserSwipe->filmid;
       		$match->user1 = $request->user()->id;
       		$match->user2 = $otherUserSwipe->user_id;
       		$match->chat_id = min([$request->user()->id, $otherUserSwipe->user_id]) . "|" . max([$request->user()->id, $otherUserSwipe->user_id]);
       		$match->save();
       		// TODO: Send notification to both users.
       	}

        $swiped = new Swipe();
        $swiped->filmid = $validated["filmId"];
        $swiped->liked = $validated["liked"];
        $swiped->user_id = $request->user()->id;

        return response()->json(["result" => $swiped->save()]);
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
