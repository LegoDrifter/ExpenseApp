<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Categories;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends BaseController
{
    //
    public function index(){

        $user = auth()->user();
        return $this->ResponseSuccess(Balance::where('user_id',$user->id)->where('status',1)->with('category')->get(), "","Successfully fetched balances.");
    }

    public function income(){
        $user = Auth::user();
        return $this->ResponseSuccess(Balance::where('user_id',$user->id)->where('type',1)->with('category')->get(), "",$user);
    }

    public function expense(){
        $user = Auth::user();
        return $this->ResponseSuccess(Balance::where('user_id',$user->id)->where('type',2)->with('category')->get(), "","Successfully fetched income.");
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

    public function getBalancesByYear($year){
        $fullStats = [];
        for($month = 1;$month <= 12; $month++){
            $expenseObject = new \stdClass();
            $expenseObject->expenses = Balance::whereYear('date', $year)->where('status',1)->where('type',2)->whereMonth('date', $month)->get();
            $expenseObject->incomes = Balance::whereYear('date', $year)->where('status',1)->where('type',1)->whereMonth('date', $month)->get();
            $expenseObject->goals = Goal::whereYear('start_date',$year)->where('status',1)->whereMonth('start_date',$month)->get();
            $fullStats[$month] = $expenseObject;
        }
        return $this->ResponseSuccess($fullStats,"","Successfully fetched balances for given year.");
    }

//    public function expenseTrack($month){
//        $expenseObject = new \stdClass();
//
//        $expenses = Balance::whereMonth('date',$month)->where('type',2)->where('status',1)->get();
//        $incomes = Balance::whereMonth('date',$month)->where('type',1)->where('status',1)->get();
//        $goals = Goal::whereMonth('start_date',$month)->where('status',1)->get();
//
//        $expenseObject->expenses = $expenses;
//        $expenseObject->incomes = $incomes;
//        $expenseObject->goals = $goals;
//
////        dd($expenseObject);
//
//        return $this->ResponseSuccess($expenseObject, '','Successfully fetched expenses.');
//    }



    public function validator(Request $request){
        return $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'category' => [''],
            'wage' => ['required', 'numeric'],
            'status' => ['numeric'],
            'type' => ['required', 'numeric'],
        ]);

    }

    public function attributeSetter(Balance $balance,$attributes,$identifier){
        $user = Auth::user();
        if($identifier = 'CREATE'){
            $balance->user_id = $user->id;
            //TODO CHANGE TO USER WHO IS LOGGED IN ATM
        }
        $balance->description = $attributes['description'];
        $balance->wage = $attributes['wage'];
        $balance->status = 1;
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
