<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class CategoriesController extends BaseController
{
    //

    public function index(){
//        $categories = Category::whereIn('title',['Wants','Needs','Investment and Savings','Income'])->get();
        $categories = Categories::where('user_id','=',0)->where('parent_id','=',0)->get();
        return $categories;
    }

    public function subCategories(){
        $user = Auth::user();
        $categories = Category::where('parent_id','!=',0)->where('user_id',$user->id)->where('parent_id','!=',null)->get();
        return $categories;
    }

    public function expCategories(){
        $user = Auth::user();
//        $category = Category::where('title','=','Income')->first();
        $categories = Category::where('parent_id','!=',0)->where('user_id',$user->id)->where('parent_id','!=',null)->get();
        return $categories;
    }

    public function incCategories(){
        $user = Auth::user();
        $category = Category::where('title','=','Income')->first();
        $categories = Category::where('parent_id','!=',0)->where('parent_id','!=',null)->where('user_id',$user->id)->where('parent_id','=',$category->id)->get();
        return $categories;
    }


    static public function store($title,$parent_id = null,$userID){

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
        $category->user_id = $userID;
        $category->id = $newId;
        if($parent_id){$category->parent_id = $parent_id;}
        $category->save();

        return $category;
    }

    public function savePresets(Request $request)
    {
        $data = $request->all();
        $categories = $this->index();
        $categories[0]->percentage = $data['wants'];
        $categories[1]->percentage = $data['needs'];
        $categories[2]->percentage = $data['saveInvest'];

        $categories[0]->save();
        $categories[1]->save();
        $categories[2]->save();

        return $this->ResponseSuccess(null,null,'Category presets updated!');

    }
}
