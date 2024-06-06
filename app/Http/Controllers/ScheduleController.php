<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends BaseController
{
    //
    public function index(){
        $user = Auth::user();
        $data = Schedule::where('user_id',$user->id)->where('status',1)->with('category')->get();
        return $this->ResponseSuccess($data,"","Schedules fetched");
    }

    public function store(Request $request){
        $attributes = $this->validator($request);
        $schedule = new Schedule();
        $attempt = $this->attributeSetter($schedule, $attributes, 'CREATE');
        if($attempt){
            return $this->ResponseSuccess($attempt,"","Schedule created");
        }else return $this->ResponseError("","Failed to create schedule");

    }

    public function update(Request $request, $id){
        $attributes = $this->validator($request);
        $schedule = Schedule::find($id);
        $attempt = null;
        if($schedule) $attempt = $this->attributeSetter($schedule,$attributes,'UPDATE');
        if($attempt){
            return $this->ResponseSuccess($attempt,null,"Schedule was updated");
        }else{
            return $this->ResponseError("","There are errors");
        }
    }

    public function delete($id){
        $schedule = Schedule::find($id);
        if($schedule){
            $schedule->status = 0;
            $schedule->save();
        }
        if($schedule) return $this->ResponseSuccess($schedule,"","Succesfully deleted");
        else return $this->ResponseError("","Could not delete.");
    }

    public function validator(Request $request){
        return $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'recurring' => ['required'],
            'date' => ['required', 'date'],
            'wage' => ['required', 'numeric'],
            'cycle' => ['required', 'numeric'],
            'category' => ['required'],
            'type' => ['required', 'numeric'],
        ]);

    }

    public function attributeSetter(Schedule $schedule, $attributes, $identifier){
        $user = Auth::user();
        if($identifier == 'CREATE'){
            $schedule->user_id=$user->id;
        }
        $schedule->description = $attributes['description'];
        $schedule->recurring = $attributes['recurring'];
        $schedule->wage = $attributes['wage'];
        $schedule->cycle = $attributes['cycle'];
        $schedule->cycles_left = $attributes['cycle'];
        $schedule->type = $attributes['type'];
        $schedule->status = 1;

        $dateString = $attributes['date'];
        $date =  Carbon::parse($dateString);
        $formattedDate = $date->format('Y-m-d');

        $schedule->date = $formattedDate;
        $category = $attributes['category'];
        if(is_array($category)){
            $schedule->category_id = $category['id'];
        }else{
            $newCategory = CategoriesController::store($category);
            if($newCategory){
                $schedule->category_id = $newCategory->id;

            }else{
                $this->errors = ['Duplicate key entry'];
                return false;
            }
        }

        if($schedule->save()){
            $category = Categories::find($schedule->category_id);
            $schedule->category = $category;
            return $schedule;
        }else return false;

    }
}
