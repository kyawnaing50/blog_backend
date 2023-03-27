<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index(Request $request){
        $query=Post::orderByDesc('created_at');

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

    public function create(Request $request){

        $request->validate([
            'title'=>'required|string',
            'description'=>'required|string',
            'category_id'=>'required',
        ],
        [
            'title.required'=>'title is required',
            'description.required'=>'description is required',
            'category_id.required'=>'category field is required'
        ]
    );
    //မှတ်ချက်
    // Post table ထဲကိုလည်း data ထည့်တယ်။ Media table ထဲလည်း data ထည့်တယ်။
    // Postထဲ ဒေတာရောက်ပြီး Media ထဲ ဒေတာမရောက်ဘူးဆိုတဲ့ အနေအထားမျိုးကြုံရနိုင်တာကြောင့် DB Transaction အုပ်လိုက်တာ ကောင်းတယ်။
    // DB Transaction အုပ်နည်းကတော့ try catch နဲ့ တွဲသုံးမယ်။
    // DB::beginTransacton() ခေါ်မယ်။ DB ကိုImportClass လုပ်ပေးရမယ်
    // DB ::commit လုပ်ပေးရမယ်။
    // ERROR တက်သွားပါက  ပြန်အလုပ်လုပ်နိုင်ဖို့အတွက် DB::rollBack() လုပ်ပေးရမယ်။

    DB::beginTransaction();
    try
    {
    $file_name=null;
    if($request->hasFile('image')){
        $file=$request->file('image');
        $file_name=uniqid().'-'. date('Y-m-d-H-i-s') .'.'. $file->getClientOriginalExtension();
        Storage::put('media/'.$file_name, file_get_contents($file));
    }
        $post=new Post();
        $post->user_id=auth()->user()->id;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->category_id = $request->category_id;
        $post->save();

        $media=new Media();
        $media->file_name=$file_name;
        $media->file_type='image';
        $media->model_id=$post->id;
        $media->model_type=Post::class;
        $media->save();
        DB::commit();
        return ResponseHelper::success([],'Successfully uploaded');
    }
    catch(Exception $ex){

        DB::rollback();
        return ResponseHelper::fail($ex->getMessage());
    }
    }
public function show($id){
    $post=Post::where('id',$id)->firstOrFail();
    return ResponseHelper::success(new PostDetailResource($post));
}

}
