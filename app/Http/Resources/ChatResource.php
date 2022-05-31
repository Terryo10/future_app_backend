<?php

namespace App\Http\Resources;

use App\Models\Messages;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $messages = Messages::where('chat_id','=',$this->id)->get();
        return [
            'id'=>$this->id,
            'messages'=> $messages
        ];
    }
}
