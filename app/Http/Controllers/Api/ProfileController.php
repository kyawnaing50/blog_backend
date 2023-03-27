<?php

namespace App\Http\Controllers\Api;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    public function profile(){
        $user=auth()->guard()->user();
        return ResponseHelper::success(new ProfileResource($user));
    }

    public function posts(Request $request){
        $query=Post::orderByDesc('created_at')->where('user_id',auth()->user()->id);

        //request ထဲမှာ category_id ဆိုတဲ့ key ပါလာရင် category table မှာ category_id ဆိုတဲ့ column name နဲ့ ဒဲ့တိုက်စစ်မည်။
        if($request->category_id){
            $query->where('category_id',$request->category_id);
        }
        //requestထဲမှာ search ဆိုတဲ့ key  ပါလာရင် title / description အထဲမှာ အဲ့ဒီစာသားပါလာလားတိုင်စစ်မယ်။
        if($request->search){
            $query->where(function($q1)use($request){
                $q1->where('title','like','%'.$request->search.'%')
                    ->orWhere('description','like','%'.$request->search.'%');
            });
        }
        $posts=$query-> paginate(10);
        return PostResource::collection($posts)->additional(['message'=>'success']);
    }
}
