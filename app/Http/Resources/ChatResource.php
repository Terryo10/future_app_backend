<?php

namespace App\Http\Resources;

use App\Models\Messages;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = 'data';
    public function toArray($request)
    {
        $messages = Messages::where('chat_id','=',$this->id)->get();
            if($this->user_1 == Auth::user()->id){
                $user_id = $this->user_2;
            }else{
                $user_id = $this->user_1;
            }
        $opponent = User::find($user_id);
            $current = User::find(Auth::user()->id);
        return [
            'id'=>$this->id,
            'opponent'=> $opponent,
            'current_user'=>$current,
            'messages'=> $messages,
            'created_at'=> $this->created_at
        ];
    }
}
