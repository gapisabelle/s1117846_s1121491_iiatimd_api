<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matches;

class MatchController extends Controller
{
    public function index(Request $request)
    {
    	$userId = $request->user()->id;

    	$r1 = Matches::where('user1', $userId)->get()->all();
    	$r2 = Matches::where('user2', $userId)->get()->all();

    	return response()->json(array_merge($r1, $r2));

        // return response()->json(Matches::where('user1', $userId)->whereOr('user2', $userId)->get()->all());
    }

    public static function sendNotification($title, $message, $device_tokens) {
        $SERVER_API_KEY = 'AAAA3NAP7pc:APA91bHpbkGwNe8J6K1VxbB8pyjpamNsNKfGXEr_PuDuFy1EyeexxLp17fuxYxQ40IuNDHHhMXm5VVjDacJTrxodETIJj36W1EsJK5Rc2F4xy8TjB3J_5YfpSf9RHm1KTs5zI2pDurMJ';
  
        // payload data, it will vary according to requirement
        $data = [
        	"title" => $title,
            "registration_ids" => $device_tokens, // for single device id
            "data" => $message
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
      
        curl_close($ch);

        (new \Symfony\Component\Console\Output\ConsoleOutput())->writeln("FCMResponse");
        (new \Symfony\Component\Console\Output\ConsoleOutput())->writeln($response);
      
        return $response;
    }

    public static function notifyChatMsg(Request $request) {
    	$validator = \Validator::make($request->all(), [
            'chatid' => 'required|string|min:3',
            'msg' => 'required|string'
        ]);

        $validated = $validator->validated();

        $match = Matches::where('chat_id', $validated["chatid"])->firstOrFail();

        $fcmTokens = [
        	// $request->user()->fcmtoken,
        	$match->otherUser->fcmtoken
        ];

        $msg = "(".$request->user()->name."): " . $validated["msg"];

    	return response()->json(['result' => MatchController::sendNotification("Movinder", ['Movinder' => $msg, 'type' => 'msg', 'chatid' => $validated["chatid"]], $fcmTokens)]);
    }
}
