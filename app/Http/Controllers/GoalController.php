<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends BaseController
{
    public function index(){
        $user = Auth::user();
        return $this->ResponseSuccess(Goal::where('user_id',$user->id)->where('status',1)->get());
    }

    public function getGoal($id){
        $goal = Goal::find($id);
        if($goal){
            return $this->ResponseSuccess($goal,null,'Goal exists');
        }else{
            return $this->ResponseError(Goal::all(),null,'Goal not found');
        }
    }


    public function store(Request $request){
       $attributes = $this->validator($request);
       $goal = new Goal();
       $attempt = $this->attributeSetter($goal, $attributes,'CREATE');

       if($attempt){
           return $this->ResponseSuccess(null,null,'Goal created');
       }else{
           return $this->ResponseError(null,null,'Goal creation failed');
       }

    }

    public function update(Request $request,$id)
    {
//        dd($request->all());
        $attributes = $this->validator($request);
        $goal = Goal::find($id);
        $attempt = null;

        if($goal) $attempt = $this->attributeSetter($goal, $attributes,'UPDATE');
        if($attempt){
            return $this->ResponseSuccess(null,null,'Goal updated');
        }else{
            return $this->ResponseError(null,null,'Goal update failed');
        }
    }

    public function delete($id){
        $goal = Goal::find($id);
        if($goal){
            $goal->delete();
            return $this->ResponseSuccess("Goal deleted");
        }else{
            return $this->ResponseError(Goal::all(),null,'Goal not found');
        }
    }

    public function validator(Request $request){
    return $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string', 'max:255'],
        'start_date' => ['required', 'date'],
        'price' => ['required', 'numeric'],
        'initial_budget' => ['required', 'numeric'],
        'status' => ['required', 'numeric'],
    ]);
    }

    public function attributeSetter(Goal $goal,$attributes,$identifier){
        $user = Auth::user();
        $goal->title = $attributes['title'];
        $goal->description = $attributes['description'];
        $goal->start_date = $attributes['start_date'];
        $goal->price = $attributes['price'];
        $goal->initial_budget = $attributes['initial_budget'];
        $goal->status = $attributes['status'];
        if($identifier == 'CREATE'){
            $goal->user_id= $user->id;
        }
        if($goal->save()) return true;
        else return false;

    }

}
