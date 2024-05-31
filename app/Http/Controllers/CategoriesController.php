<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class CategoriesController extends BaseController
{
    //

    public function index(){
        return Categories::all();
    }


    static public function store($title){

        $data = ['title' => $title];

        $rules = [
            'title' => 'required|unique:categories|max:255',
        ];

        $validator = Validator::make($data, $rules);
        if($validator->fails()){
            return false;
        }

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
