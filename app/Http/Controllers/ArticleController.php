<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function getArticles(){
        $categories = Category::all();
        return ['categories' => ArticleResource::collection($categories)];
    }

    public function commentArticle(Request $request){

        $validator = Validator::make(
            $request->all(),
            [
                'comment' => 'string|required',
                'article_id'=> 'numeric|required'
            ]
        );


        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        //find article first
        $article = Article::find($request->input('article_id'));
        if($article){
            $user = Auth::user();
            $comment = new ArticleComment();
            $comment->user_id = $user->id;
            $comment->comment = $request->input('comment');
            $comment->article_id = $request->input('article_id');
            $comment->save();

            return response(['success' => true, 'message' => 'comment posted successfully']);
        }else{

            return $this->jsonError(401, "article does not exist");
        }




    }



}
