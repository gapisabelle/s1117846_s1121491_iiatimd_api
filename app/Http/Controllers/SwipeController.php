<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Swipe;
use App\Models\Matches;
use App\Models\User;

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
            'image' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // if (\Cache::get('lastSwipe')) {
        // 	\Cache::put('lastSwipe', now(), now()->addMinutes(1));

        // 	// TODO: Search for other users and get a random one
        // }

        $validated = $validator->validated();

        if ($validated['liked'] == 1) {
	        $otherUserSwipe = Swipe::where('user_id', '!=', $request->user()->id)
	        		->where('filmid', $validated["filmId"])
	        		->where('liked', 1)
	        		->get();

	        if (!$otherUserSwipe->isEmpty()) {
	        	$otherUserSwipe = $otherUserSwipe->random();

	        	$result = Matches::where('filmid', $otherUserSwipe->filmid)->where(function($a) use ($otherUserSwipe) {
		       		$a->where('user1', $otherUserSwipe->user_id)->orWhere('user2', $otherUserSwipe->user_id);
		       	})->get()->first();
		       	if ($result == null) {
		       		$match = new Matches();
		       		$match->filmid = $otherUserSwipe->filmid;
		       		$match->user1 = $request->user()->id;
		       		$match->user2 = $otherUserSwipe->user_id;
		       		$match->image = $validated["image"];
        			$match->title = $validated["title"];
		       		$match->chat_id = min([$request->user()->id, $otherUserSwipe->user_id]) . "|" . max([$request->user()->id, $otherUserSwipe->user_id]);
		       		$match->save();

		       		MatchController::sendNotification("Movinder", ['Movinder' => "You've got a Match!"], [$request->user()->fcmtoken, User::where('id', $otherUserSwipe->user_id)->firstOrFail()->fcmtoken]);
		       	}
	        }
        }



        $swiped = new Swipe();
        $swiped->filmid = $validated["filmId"];
        $swiped->liked = $validated["liked"];
        $swiped->image = $validated["image"];
        $swiped->title = $validated["title"];
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
