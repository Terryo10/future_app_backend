<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'heading'=>$this->heading,
            'topic'=>$this->topic,
            'image'=>$this->image_path,
            'user'=> $this->user,
            'body'=>$this->body,
            'category_id'=> $this->category_id,
            'created_at'=>$this->created_at,
            'comments'=> ArticleCommentResource::collection($this->comments)
        ];
    }
}
