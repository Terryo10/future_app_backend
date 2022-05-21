<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|alpha_num|min:5',
                'confirm_password' => 'required|same:password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        $input = array(
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        );
        // check if email already registered
        $user = User::where('email', $request->email)->first();
        if (!is_null($user)) {
            return response()->json(['success' => false, 'message' => 'Sorry! this email is already registered']);
        }
        // create and return data
        $user = User::create($input);
        $accessToken = $user->createToken('authToken')->plainTextToken;
        $presentedUser = User::find($user)->first();
        return response(['user' => $presentedUser, 'token' => $accessToken, 'success' => true, 'message' => 'Registered successfully']);
    }
}
