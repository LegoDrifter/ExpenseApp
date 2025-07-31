<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Categories;
use App\Models\Goal;
use App\Models\Schedule;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use function PHPUnit\Framework\isEmpty;

class GoalController extends BaseController
{
    protected $goalService;

    public function __construct(GoalService  $goalService) {
        $this->goalService = $goalService;
    }

    public function index(){
        $user = Auth::user();
        $goals = Goal::where('user_id',$user->id)->get();


        foreach($goals as $goal){
            $helpArray = [];
            $startDate = $goal->start_date;
            $index = 0;

            while(true){
                $results = $this->goalService->checkGoalFeasibility($goal,$user->id ,$startDate);
//                echo("The goal price is ". $goal->price);
//                echo('The goal is ' . $results['feasible']);
//                echo(PHP_EOL);
//                echo('The monthly balance is '. $results['balance']);
//                echo(PHP_EOL);
                $totalBalance = array_sum($helpArray);
//                echo("The total balance ". $totalBalance);
//                echo(PHP_EOL);
                $monthlyCalculation = $totalBalance >= $goal->price;
//                echo("The test value is " . ($monthlyCalculation? 'true' : 'false'));
                if ($results['feasible'] == 1 || $monthlyCalculation || $index == 3) {
//                    echo("Estimate date of goal completion ". $startDate);
                    $startDate = Carbon::parse($goal->start_date);
                    $startDate->addMonths($index);
//                    echo(" The date the goal will end will be". $startDate);
                    $goal->end_date = $startDate;
                    $goal->save();
                    break;
                } else {
                    $helpArray[$index] = $results['balance'];
                    $startDate = Carbon::parse($startDate)->addMonths(1);
                    $index++;
                }
                if ($index > 12) { // Assuming 12 months is a reasonable limit
                    echo(" Exiting loop to prevent infinite loop.");
                    break;
                }
            }

        }
        $goals = Goal::where('user_id',$user->id)->get();
        return $this->ResponseSuccess($goals,null,'Goals fetched successfully');
    }

    public function getGoal($id){
        $goal = Goal::find($id);
        if($goal){
            return $this->ResponseSuccess($goal,null,'Goal exists');
        }else{
            return $this->ResponseError(Goal::all(),null,'Goal not found');
        }
    }

    public function finishGoal(Request $request){

        $user = Auth::user();
        $id = $request->input('id');
        $wants = Categories::where('title','Wants')->first();
        $category = Categories::where('title','Goals')->first();
        if(is_null($category)){
            $category = new Categories();
            $category->title = 'Goals';
            $category->percentage = 0;
            $category->status = 1;
            $category->parent_id = $wants->id;
            $category->save();
        }
        else {
            $wantId = $wants->id;
        }
        $goal = Goal::find($id);
        if($goal){
            $goal->status = 2;
            $goal->save();
            $balance = new Balance();
            $balance->category_id = $wantId;
            $balance->user_id = $user->id;
            $balance->wage = $goal->price;
            $balance->date = $goal->end_date;
            $balance->description = $goal->description;
            $balance->type = 1;
            $balance->status = 1;
            $balance->save();

            return $this->ResponseSuccess($goal,null,'Goal finished successfully');
        }else{
            return $this->ResponseError('Goal not found',null,null,);
        }
    }

    public function store(Request $request){
       $attributes = $this->validator($request);
       $goal = new Goal();
       $attempt = $this->attributeSetter($goal, $attributes,'CREATE');

       if($attempt){
           return $this->ResponseSuccess($attempt,null,'Goal created');
       }else{
           return $this->ResponseError(null,null,'Goal creation failed');
       }

    }

    public function demo2(Request $request){
        $endDate = Carbon::parse($request['goalEndDate']);
        $endMonth = $endDate->month;
    }

    public function demo(Request $request)
    {
        try{
            $endDate = Carbon::parse($request['goalEndDate']);
            $startDate = Carbon::now();
            $array = [];
            $feasible = false;

            $endMonth = $endDate->month;
            $startMonth = $startDate->month;
            $currentDate = $startDate->copy();
            for($i = 0 ; ; $i++){
                $object = new \stdClass();
                $balance = $request['wage'] - ($request['wants'] + $request['needs'] + $request['savings']);
                $budget = $request['budget'];
                $object->month = $currentDate->format('m');
                $object->budget = $budget;
                if($i > 0){
                    $object->balance = $balance + $array[$i-1]->balance;
                } else $object->balance = $balance + $budget;
                if($feasible = $object->balance > $request['goalPrice']){
                    $object->feasible = true;
                    $object->balanceNow = $object->balance - $request['goalPrice'];
                    $feasible = true;

                }
                $array[] = $object;
                if($feasible){
                    $object->endDate = $currentDate->format('Y-m-d');
                    break;
                }
                $currentDate->addMonths(1);
                if($i == 20){
                    break;
                }
            }
            return response()->json([
                'success' => true,
                'data' => $array,
                'message'=> 'Demo initiated'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error processing the request: ' . $e->getMessage()
            ], 500);
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
            return $this->ResponseSuccess($attempt,null,'Goal updated');
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
        if($goal->save()){
            return $goal;
        }
        else return false;

    }

    public function checkGoal($id)
    {

        $today = Carbon::now()->toDateString();
        $goal = Goal::find($id);
        echo("Today is ". $today);
        echo("The goal date is ". $goal->end_date);
        if($goal->end_date == $today) return true;
        else return false;

    }



}
