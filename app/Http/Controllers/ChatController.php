<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

            $message = new Messages();
            $message->chat_id = $chat->id;
            $message->from = $user->id;
            $message->message = $request->input('message');
            $message->save();

            $chatt = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->get();
            return ChatResource::collection($chatt);

        } else {
            $activeChat = $chats->first();
            $message = new Messages();
            $message->chat_id = $activeChat->id;
            $message->from = $user->id;
            $message->message = $request->input('message');
            $message->save();

            $chatt = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->get();
            return ChatResource::collection($chatt);
        }
    }

    public function getChats()
    {
        $user = Auth::user();
        $chats = Chat::where('user_1', '=', $user->id)->orWhere('user_2', '=', $user->id)->get();
        return ChatResource::collection($chats);
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('query');
        $searchResult = User::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('email', 'LIKE', '%' . $query . '%')->get();
        return response(['users' => $searchResult, 'success' => true]);
  }

}
