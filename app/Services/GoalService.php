<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

class GoalService
{
    protected $goalDate = null;
    public function checkGoalFeasibility($goal, $user,$start_date = null){
        //get all schedulers
        if($start_date === null){
            $goalStartDate = Carbon::parse($goal->start_date);

        }else $goalStartDate = Carbon::parse($start_date);
        $goalYear = $goalStartDate->year;
        $goalStartDate = $goalStartDate->month;
        $this->goalDate = $goalStartDate;
//        echo($this->goalDate);
        $recurringDailySchedules = Schedule::whereYear('date',$goalYear)->where('user_id',$user)->where('status',1)->whereMonth('date',$goalStartDate-1)->where('recurring','=',1)->where('cycles_left','>',0)->orderBy('date','asc')->get();
        $recurringSchedules = Schedule::whereYear('date',$goalYear)->where('user_id',$user)->where('status',1)->whereMonth('date',$goalStartDate-1)->where('recurring','=',2)->where('cycles_left','>',0)->orderBy('date','asc')->get();
        $schedules = Schedule::whereYear('date',$goalYear)->where('user_id',$user)->where('status',1)->whereMonth('date',$goalStartDate)->where('cycles_left','>',0)->orderBy('date','asc')->get();

//        echo($schedules);
//        echo(PHP_EOL);
        $feasible = 0;
        $recurringCostArray = [];

        $flag = 1;
        foreach ($recurringDailySchedules as $schedule){
            $schedule->date = $this->previousMonthSchedulesConvert($schedule);
            $recurringCostArray = $this->recurringFunction($schedule,$recurringCostArray,$flag);
            $flag=0;

        }
        foreach($schedules as $schedule){
            $recurringCostArray = $this->recurringFunction($schedule,$recurringCostArray,$flag);
            $flag=0;
        }
        foreach($recurringSchedules as $schedule){
            $schedule->date = $this->previousMonthSchedulesConvert($schedule);
            $recurringCostArray = $this->recurringFunction($schedule,$recurringCostArray,$flag);
            $flag=0;
        }
        $balance = $this->calculateBalance($recurringCostArray);
        $user = User::where('id',$user)->first();
        if(($goal->price + $user->budget) < $balance){
            $feasible = 1;
        };


//        echo(PHP_EOL);
//        foreach ($recurringCostArray as $recurringObject) {
//            echo "Date: " . $recurringObject->date . ", Value: " . $recurringObject->value . "\n";
//        }

//        echo(PHP_EOL);

            return [
                'feasible' => $feasible,
                'balance' => $balance,
            ];

    }

    public function previousMonthSchedulesConvert($schedule){
        if($schedule->recurring == 2) {
            $schedule->date = Carbon::parse($schedule->date)->addMonths(1)->format('Y-m-d');
        }
//        else if($schedule->recurring == 1) {
////            $schedule->date = Carbon::parse($schedule->date)->addDays($schedule->cycle - $schedule->cycles_left)->format('Y-m-d');
//            echo("Testing the type 1 rec, ".$schedule->date."\n");
//        }
        return $schedule->date;
    }

    public function recurringFunction($schedule, $array, $flag,$dashboard = false){

        //flag is used only when we first fire the function

        $matchIndex = null;
        if($flag != 1){
            $date = Carbon::parse($schedule->date);
            for($i = 0;$i< count($array);$i++){
                $formattedDate = $date->toDateString();
                if(isset($array[$i]->date) && $formattedDate == $array[$i]->date){
//                    echo('Schedule date ' . $formattedDate .  'Array date ' . $array[$i]->date);
                    $matchIndex = $i;
                    break;
//                    echo('Index is set to '.$matchIndex);
                }
            }

        }
        $totalIncome = 0;
        $totalExpense = 0;
        $totalWants = 0;
        $totalNeeds = 0;
        $totalSavings = 0;
        $arrayCount = count($array);
        $today = Carbon::parse($schedule->date);
        $tmpCycles = $schedule->cycles_left;
        $index = ($flag == 1 || $matchIndex === null) ? $arrayCount : $matchIndex; // Use arrayCount if no match is found

//        echo('Index is set to '.$index);
        while($tmpCycles){
            $recurringObject = new \stdClass();
            $currentDate = $today->copy()->addDays($schedule->cycle - $tmpCycles);
            if($currentDate->month != $this->goalDate){
                $tmpCycles--;
                continue;
            }
            $formattedDate = $currentDate->toDateString();
            $recurringObject->date = $formattedDate;
            $schedule->type == 1 ? $totalExpense += $schedule->wage : $totalIncome += $schedule->wage;
            if($schedule->category_id == 7){
                $totalWants += $schedule->wage;
            }else if($schedule->category_id == 8){
                $totalNeeds += $schedule->wage;
            }else if($schedule->category_id === 9){
                $totalSavings += $schedule->wage;
            }
            if($flag == 1){
                $recurringObject->value = $schedule->type == 1 ? $schedule->wage : -$schedule->wage;
            }else if($flag == 0 && $index < $arrayCount && isset($array[$index])){
                $recurringObject->value = $schedule->type == 1 ? $array[$index]->value + $schedule->wage
                    : $array[$index]->value - $schedule->wage;

            }else{
                $recurringObject->value = $schedule->type == 1 ? $schedule->wage : -$schedule->wage;
            }
            $array[$index] = clone $recurringObject;
            if($schedule->recurring == 2){ break;}
            $tmpCycles--;
            $index++;
        }
        if ($dashboard) {
            return [
                'array' => $array,
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'totalWants' => $totalWants,
                'totalNeeds' => $totalNeeds,
                'totalSavings' => $totalSavings,
            ];
        }

        return $array;

    }

    public function calculateBalance($array){
        $balance = 0;
        foreach($array as $arrayItem){
            $balance = $balance + $arrayItem->value;
        }

        return $balance;
    }

    public function getAllInformationMonthly($start_date, $user){
//        echo($start_date);
//        echo(PHP_EOL);

        $goalStartDate = Carbon::parse($start_date)->month;
        $goalYear = Carbon::parse($start_date)->year;
        $this->goalDate = $goalStartDate;
        $recurringDailySchedules = Schedule::whereYear('date',$goalYear)->where('status','=',1)->whereMonth('date',$goalStartDate-1)->where('user_id',$user->id)->where('recurring','=',1)->where('cycles_left','>',0)->orderBy('date','asc')->get();
        $monthlySchedules = Schedule::whereYear('date',$goalYear)->where('status','=',1)->whereMonth('date',$goalStartDate)->where('user_id',$user->id)->where('recurring','=',2)->where('cycles_left','>',0)->orderBy('date','asc')->get();
        $yearlySchedules = Schedule::whereYear('date',$goalYear)->where('status','=',1)->whereMonth('date',$goalStartDate)->where('user_id',$user->id)->where('recurring','=',3)->where('cycles_left','>',0)->orderBy('date','asc')->get();


        $recurringCostArray = [];
        $totalIncome = 0;
        $totalExpense = 0;
        $totalWants = 0;
        $totalNeeds = 0;
        $totalSavings = 0;

        $flag = 1;
        foreach ($recurringDailySchedules as $schedule){
            $schedule->date = $this->previousMonthSchedulesConvert($schedule);
            $recurringCostArray = $this->recurringFunction($schedule,$recurringCostArray,$flag,true);
            $totalIncome += $recurringCostArray['totalIncome'];
            $totalExpense += $recurringCostArray['totalExpense'];
            $totalWants += $recurringCostArray['totalWants'];
            $totalNeeds += $recurringCostArray['totalNeeds'];
            $totalSavings += $recurringCostArray['totalSavings'];
            $recurringCostArray = $recurringCostArray['array'];
            $flag=0;

        }
        foreach($yearlySchedules as $schedule){
            $recurringCostArray = $this->recurringFunction($schedule,$recurringCostArray,$flag,true);
            $totalIncome += $recurringCostArray['totalIncome'];
            $totalExpense += $recurringCostArray['totalExpense'];
            $totalWants += $recurringCostArray['totalWants'];
            $totalNeeds += $recurringCostArray['totalNeeds'];
            $totalSavings += $recurringCostArray['totalSavings'];
            $recurringCostArray = $recurringCostArray['array'];

        }
//        foreach($monthlySchedules as $schedule){
////            $schedule->date = $this->previousMonthSchedulesConvert($schedule);
//            $recurringCostArray = $this->recurringFunction($schedule,$recurringCostArray,$flag,true);
//            $totalIncome += $recurringCostArray['totalIncome'];
//            $totalExpense += $recurringCostArray['totalExpense'];
//            $totalWants += $recurringCostArray['totalWants'];
//            $totalNeeds += $recurringCostArray['totalNeeds'];
//            $totalSavings += $recurringCostArray['totalSavings'];
//            $recurringCostArray = $recurringCostArray['array'];
//
//        }


        return [
          'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalBalance' => $totalIncome - $totalExpense,
            'totalWants' => $totalWants,
            'totalNeeds' => $totalNeeds,
            'totalSavings' => $totalSavings,

        ];

    }

    public function getBalanceInformation($date,$user){
        $month = Carbon::parse($date)->month;
        $year = Carbon::parse($date)->year;

        $incomes = Balance::where('status',1)->where('user_id',$user->id)->whereYear('date',$year)->whereMonth('date',$month)->where('type',2)->get();
        $expenses = Balance::where('status',1)->where('user_id',$user->id)->whereYear('date',$year)->whereMonth('date',$month)->where('type',1)->get();



        $totalExp = 0;
        $totalInc = 0;

        foreach ($incomes as $income){
            $totalInc += $income->wage;
        }
        foreach ($expenses as $expense){
            $totalExp += $expense->wage;
        }

        return[
          'realBalance'=>$totalInc - $totalExp,
        ];
    }

}
