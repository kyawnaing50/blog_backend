<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Helpers\ResponseHelper;

class CategoryController extends Controller
{
    public function index(){
        $categories=Category::orderBy('name')->get();
        return ResponseHelper::success(CategoryResource::collection($categories));
    }
}
