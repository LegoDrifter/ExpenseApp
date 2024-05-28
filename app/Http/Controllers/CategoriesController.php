<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //
    public function index(){
        return Categories::all();
    }

    static public function store($title){
        $lastCategory = Categories::latest('id')->first();
        $newId = $lastCategory ? $lastCategory->id + 1 : 1;

        $status = 1;
        $category = new Categories();
        $category->title = ucwords($title);
        $category->status = $status;
        $category->id = $newId;
        $category->save();

        return $category;
    }
}
