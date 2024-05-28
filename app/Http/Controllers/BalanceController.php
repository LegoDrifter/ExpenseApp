<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BalanceController extends BaseController
{
    //
    public function index(){
        return $this->ResponseSuccess(Balance::all(), "","Successfully fetched balances.");
    }

    public function store(Request $request){
        $attributes = $this->validator($request);
        $balance = new Balance();
        $attempt = $this->attributeSetter($balance, $attributes, 'CREATE');

        if($attempt){
            return $this->ResponseSuccess(null,'Balance created');
        }else{
            return $this->ResponseError(null,null,'Balance creation failed');
        }

    }



    public function validator(Request $request){
        return $request->validate([
            'category' => ['required'],
            'description' => ['required', 'string', 'max:255'],
            'recurring' => ['required'],
            'date' => ['required', 'date'],
            'wage' => ['required', 'numeric'],
            'cycle' => ['required', 'numeric'],
            'status' => ['numeric'],
            'type' => ['required', 'numeric'],
        ]);
    }

    public function attributeSetter(Balance $balance,$attributes,$identifier){
        $balance->user_id = 1;
        $balance->description = $attributes['description'];
        $balance->wage = $attributes['wage'];
        $balance->cycle = $attributes['cycle'];
        $balance->status = 1;
        $balance->recurring = $attributes['recurring'];
        $balance->type = $attributes['type'];

        $dateString = $attributes['date'];
        $date =  Carbon::parse($dateString);
        $formattedDate = $date->format('Y-m-d');

        $balance->date = $formattedDate;

        $category = $attributes['category'];
        if(is_array($category)){
           $balance->category_id = $category['id'];
        }else{
            $newCategory = CategoriesController::store($category);
            $balance->category_id = $newCategory->id;
        }

        if($balance->save()) return true;
        else return false;

    }


}
