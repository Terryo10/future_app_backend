<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function sendMessage(Request $request){
        $validator = Validator::make(
            $request->all(),
            [
                'message' => 'required|min:1',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        return $request;


    }


}
