<?php

namespace App\Console\Commands;

use App\Http\Controllers\BalanceController;
use App\Models\Balance;
use App\Models\Schedule;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CustomTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected  $signature = 'custom-task';
    protected  $schedules;


    public function loadSchedules(){
        $this->schedules = Schedule::where('status','1')->where('cycles_left',">",'0')->where('date','=',Carbon::today()->toDateString())->get();
    }


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function balanceSetter(Balance $balance, Schedule $schedule, $currentDateTime){
        $balance->user_id = $schedule->user_id;
        $balance->description = $schedule->description;
        $balance->category_id = $schedule->category_id;
        $balance->date = Carbon::today()->toDateString();
        $balance->status = 1;
        $balance->type = $schedule->type;
        $balance->wage = $schedule->wage;
        $balance->save();
        $this->setUserBalance($schedule->user_id,$schedule->wage,$schedule->type);
        $this->info($balance);
    }
    public function setUserBalance($userid,$wage,$type){
        $user =  User::where('id', $userid)->first();
        $type == 1 ?  $user->balance -= $wage : $user->balance += $wage;
        $user->save();
    }

    public function getArgumentByRecurring($schedule){
        $offSet = $schedule->cycle - $schedule->cycles_left;
        $recurringObject = new \stdClass();
        if($schedule->recurring == 1) {
//             $dateString = '2024-06-20';
//             $currentDateTime = Carbon::parse($dateString)->toDateString();
            $currentDateTime = Carbon::createFromDate(now()->year, now()->month, now()->day)->toDateString();
            $comparator = Carbon::parse($schedule->date)->addDays($offSet)->toDateString();
        }
        else if($schedule->recurring == 2) {
            $currentDateTime = Carbon::createFromDate(now()->year, now()->month)->startOfMonth()->format('Y-m');
            $comparator = Carbon::parse($schedule->date)->addMonths($offSet)->format('Y-m');
        }
        else if($schedule->recurring == 3) {
            $currentDateTime = Carbon::createFromDate(now()->year)->startOfYear()->format('Y');
            $comparator = Carbon::parse($schedule->date)->addYears($offSet)->format('Y');
        }
        $recurringObject->currentDateTime = $currentDateTime;
        $recurringObject->comparator = $comparator;
        $recurringObject->offSet = $offSet;
        return $recurringObject;
    }

    public function checkIfDatesMatch($schedule){
        $schedulerObject = $this->getArgumentByRecurring($schedule);
        $this->info($schedulerObject->currentDateTime);
            if ($schedulerObject->currentDateTime == $schedulerObject->comparator) {
                $this->info("Inside");
                $schedule->cycles_left = $schedule->cycles_left - 1;
                $schedule->save();
                $balance = new Balance();
                $this->balanceSetter($balance, $schedule, $schedulerObject->currentDateTime);
                return true;
            } else return false;

    }



    public function handle()
    {
//        $this->loadSchedules();
//        $this->info($this->schedules);
        if(is_null($this->schedules)){

            $this->loadSchedules();
        }
        foreach ($this->schedules as $schedule){
            $checkDate = $this->checkIfDatesMatch($schedule);
            if($checkDate){
                $this->info($schedule->id);
                $this->info("True");
            }
        }

    }
}
