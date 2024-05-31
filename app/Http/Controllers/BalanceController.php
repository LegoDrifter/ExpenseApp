<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Categories;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BalanceController extends BaseController
{
    //
    public function index(){
        return $this->ResponseSuccess(Balance::where('status',1)->with('category')->get(), "","Successfully fetched balances.");
    }

    public function income(){
        return $this->ResponseSuccess(Balance::where('type',1)->with('category')->get(), "","Successfully fetched income.");
    }

    public function expense(){
        return $this->ResponseSuccess(Balance::where('type',2)->with('category')->get(), "","Successfully fetched income.");
    }

    public function store(Request $request){
        $attributes = $this->validator($request);
        $balance = new Balance();
        $attempt = $this->attributeSetter($balance, $attributes, 'CREATE');
        if($attempt){
            return $this->ResponseSuccess($attempt,'Balance created');
        }else{
            return $this->ResponseError($this->errors,'Balance creation failed',400);
        }

    }

    public function update(Request $request, $id){
        $attributes = $this->validator($request);
        $balance = Balance::find($id);
        $attempt = null;
        if($balance) $attempt = $this->attributeSetter($balance, $attributes, 'UPDATE');
        if($attempt){
            return $this->ResponseSuccess($attempt,null,'Balance updated');
        }else{
            return $this->ResponseError(null,null,'Balance update failed');
        }
//        dd($request->all());
    }

    public function delete($id){
        $balance = Balance::find($id);
        if($balance) {
            $balance->status = 0;
            $balance->save();
        }
        return $this->ResponseSuccess($balance,'Balance deleted');
    }



    public function validator(Request $request){
        return $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'recurring' => ['required'],
            'date' => ['required', 'date'],
            'category' => [''],
            'wage' => ['required', 'numeric'],
            'cycle' => ['required', 'numeric'],
            'status' => ['numeric'],
            'type' => ['required', 'numeric'],
        ]);


    }

    public function attributeSetter(Balance $balance,$attributes,$identifier){
        if($identifier = 'CREATE'){
            $balance->user_id = 1;
            //TODO CHANGE TO USER WHO IS LOGGED IN ATM
        }
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
            echo $category;
            $newCategory = CategoriesController::store($category);
            if($newCategory){
                $balance->category_id = $newCategory->id;

            }else{
                $this->errors = ['Duplicate key entry'];
                return false;
            }
        }

        if($balance->save()){
            $category = Categories::find($balance->category_id);
            $balance->category = $category;
            return $balance;
        }
        else return false;

    }


}
