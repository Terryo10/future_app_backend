<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\DeviceToken;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'message' => 'required|min:1',
                'to' => 'required|numeric'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $user = Auth::user();

        //get chats
        $chats = Chat::where('user_1', '=', $user->id, 'and')->where('user_2', '=', $request->input('to'))
            ->orWhere('user_2', '=', $user->id, 'and')->where('user_1', '=', $request->input('to'))->get();


        if ($chats->first() == null) {
            //create chat
            $chat = new Chat();
            $chat->user_1 = $user->id;
            $chat->user_2 = $request->input('to');
            $chat->save();

            //send message

            $message = new Messages;
            $message->chat_id = $chat->id;
            $message->from = $user->id;
            $message->message = $request->input('message');
            $message->save();
            //notify user
            $name = $user->name;

            $this->sendNotification($message, "Message From $name", $this->tokens($request->input('to')), $chat);

            $chatt = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->get();
//            return ChatResource::collection($chatt);
            return ['data' => ChatResource::collection($chatt)];

        } else {
            $activeChat = $chats->first();
            $message = new Messages;
            $message->chat_id = $activeChat->id;
            $message->from = $user->id;
            $message->message = $request->input('message');
            $message->save();
            //notify user
            $name = $user->name;
            $this->sendNotification($message, "Message From $name", $this->tokens($request->input('to')), $activeChat);// bool to reflect if send or not
            $chatt = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->get();
            return ['data' => ChatResource::collection($chatt)];
        }
    }

    public function getChats()
    {
        $user = Auth::user();
        $chats = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->orderBy('created_at', 'desc')->get();

        return ['data' => ChatResource::collection($chats)];
    }

    public function chatInit(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'to' => 'required|numeric'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        $user = Auth::user();
        $chats = Chat::where('user_1', '=', $user->id, 'and')->where('user_2', '=', $request->input('to'))
            ->orWhere('user_2', '=', $user->id, 'and')->where('user_1', '=', $request->input('to'))->get();

        if ($chats->first() == null) {
            //create chat
            $chat = new Chat();
            $chat->user_1 = $user->id;
            $chat->user_2 = $request->input('to');
            $chat->save();
            return ['data' =>  ChatResource::make($chat)];
        } else {
            $activeChat = $chats->first();

            return ['data' =>  ChatResource::make($activeChat)];
        }

    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $searchResult = User::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('email', 'LIKE', '%' . $query . '%')->get();
        return response(['users' => $searchResult, 'success' => true]);
    }

    public function tokens($userId): array
    {
        $databaseStored = DeviceToken::where('user_id', '=', $userId)->get();
        $tokens = [];
        foreach ($databaseStored as $token) {
            array_push($tokens, $token->device_token);
        }
        return $tokens;
    }


    function sendNotification($body, $title, $firebaseToken, $type)
    {

        $SERVER_API_KEY = 'AAAApLSFgQo:APA91bF26q4G1pJMDsdJVmXagBL3UxrFCGAqc-TulqNfg3n34321Up5zetlyw-4SSBEcJlFFlpO-70XdzTAIDazts8uTKIyJQ8b6bP_HmErx1Bd7UINiEIvwAd5YDfhpT50WndS8Kv3Q';

        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body->message,
                "content_available" => true,
                "priority" => "high",
            ],
            "data"=>[
               "chat_id" =>  $type->id,
            ]
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
        return json_decode($response);
    }

}
