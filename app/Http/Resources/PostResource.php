<?php

namespace App\Http\Resources;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'user_name'=>optional($this->user)->name?? 'Unknown User',
            'created_at'=>Carbon::parse($this->created_at)->format('Y-m-d h:i:s A') ,
            'created_at_readable'=>Carbon::parse($this->created_at)->diffForHumans() ,
            'category_name'=>optional($this->category) ->name?? 'Unknown category',
            'title'=>$this->title,
            'description'=>Str::limit($this->description,100),
            'image_path'=>$this->image? asset('storage/media/' .$this->image->file_name):null,
        ];
    }
}
