<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Categories;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Schedule;
use App\Services\GoalService;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends BaseController
{
    protected $goalService;
    protected $scheduleService;
    public function __construct(GoalService  $goalService, ScheduleController $scheduleService) {
        $this->goalService = $goalService;
        $this->scheduleService = $scheduleService;
    }
    //
    public function index(Request $request){

        $user = auth()->user();
        return $this->ResponseSuccess(Balance::where('user_id',$user->id)->where('status',1)->with('subCategory')->get(), "","Successfully fetched balances.");
    }

    public function income(Request $request){
        $user = Auth::user();
        $date = $request->query('date');
//        return $this->ResponseSuccess(Balance::where('user_id',$user->id)->where('type',2)->where('status',1)->with('category')->get(), "",$user);
        $query = Balance::where('user_id', $user->id)
            ->where('type', 2) // Type 2 for incomes
            ->where('status', 1)
            ->with(['sub_category', 'category']);

        if ($date) {
            $query->whereDate('date', '<=', Carbon::parse($date));
        }

        $incomes = $query->get();

        return $this->ResponseSuccess($incomes, "", $user);
    }

    public function expense(Request $request){
        $user = Auth::user();
//        return $this->ResponseSuccess(Balance::where('user_id',$user->id)->where('type',1)->where('status',1)->with('category')->get(), "","Successfully fetched income.");
        $date = $request->query('date');
        $query = Balance::where('user_id', $user->id)
            ->where('type', 1) // Type 1 for expenses
            ->where('status', 1)
            ->with(['sub_category', 'category']);

        if ($date) {
            $query->whereDate('created_at', '<=', Carbon::parse($date));
        }

        $expenses = $query->get();

        return $this->ResponseSuccess($expenses, "", "Successfully fetched expenses.");
    }

    public function getCategoriesData(){
        $userID = Auth::user()->id;
        $category1 = Categories::where('title', 'Wants')->first();
        $category2 = Categories::where('title', 'Needs')->first();
        $category3 = Categories::where('title', 'Investment and Savings')->first();


        if (!$category1 || !$category2 || !$category3) {
            return [
                'error' => 'One or more categories not found'
            ];
        }

        $expenses = Balance::where('type', 1)
            ->where('user_id', $userID)
            ->where('status', 1)->get();

        $savings =  $expenses->where('category_id', $category3->id);
        $wants = $expenses->where('category_id', $category1->id);
        $needs =  $expenses->where('category_id', $category2->id);


        $totalWants = $wants->sum('wage');
        $totalNeeds =  $needs->sum('wage');
        $totalSavings =  $savings->sum('wage');

        $total = $totalWants + $totalNeeds + $totalSavings;

        return [
            'total' => [
                'total' => $total,
                'wants' => $totalWants,
                'needs' => $totalNeeds,
                'savings' => $totalSavings
            ],
            'wants' => $wants,
            'needs' => $needs,
            'savings' => $savings,
            'wPerc' => $category1->percentage,
            'nPerc' => $category2->percentage,
            'invPerc' => $category3->percentage,
        ];
    }

    public function setUserBalance($userid,$wage){
       $user =  User::where('id', $userid)->first();
       $user->wage += $wage;
       $user->save();
    }

    public function calculateBalances($userId, $date = null) {
        $query = Balance::where('user_id', $userId)->where('status', 1);

        if ($date) {
            $query->whereDate('date', '<=', $date);
        }

        $balances = $query->get();

        $category1 = Categories::where('title', 'Wants')->first();
        $category2 = Categories::where('title', 'Needs')->first();
        $category3 = Categories::where('title', 'Investment and Savings')->first();


        $totalWants = $category1 ? $balances->where('category_id', $category1->id) : collect([]);
        $totalNeeds = $category2 ? $balances->where('category_id', $category2->id) : collect([]);
        $totalSavings = $category3 ? $balances->where('category_id', $category3->id) : collect([]);

        return [
            'wants' => $totalWants,
            'needs' => $totalNeeds,
            'savings' => $totalSavings
        ];
    }


    public function store(Request $request){
        $attributes = $this->validator($request);
        $balance = new Balance();
        $attempt = $this->attributeSetter($balance, $attributes, 'CREATE');
        $user = Auth::user();
        if($attempt){
            $attempt->type == 1 ? $user->balance -= $attempt->wage : $user->balance += $attempt->wage;
            $attempt->type == 1 ? $user->totalExpense += $attempt->wage : $user->totalIncome += $attempt->wage;
            $user->save();
            return $this->ResponseSuccess($attempt,'Balance created');
        }else{
            return $this->ResponseError($this->errors,'Balance creation failed',400);
        }

    }

    public function tester(Request $request){
        dd("TEST");
    }

    public function getMonthBalances(Request $request){

        $user = Auth::user();
        $year = $request['year'];
        $month = $request['month'];


        $incomes = Balance::whereMonth('date', $month)->whereYear('date', $year)->where('user_id',$user->id)->where('status',1)->where('type',2)->with('category')->get();
        $expenses = Balance::whereMonth('date',$month)->whereYear('date', $year)->where('user_id',$user->id)->where('status',1)->where('type',1)->with('category')->get();

        $data = [
            'incomes' => $incomes,
            'expenses' => $expenses
        ];
        if($incomes->isEmpty() && $expenses->isEmpty()){
            return $this->ResponseError($this->errors,'Data not found',400);
        }else{
            return $this->ResponseSuccess($data,"","Successfully fetched incomes.");
        }

    }

    public function getMonthInfo(Request $request){
        $user = Auth::user();
        $year = $request['year'];
        $monthName = $request['month']['monthName'];
        $month = Carbon::parse($monthName)->month;

        $incomes = Balance::whereMonth('date', $month)->whereYear('date', $year)->where('user_id',$user->id)->where('status',1)->where('type',2)->with('category')->get();
        $expenses = Balance::whereMonth('date',$month)->whereYear('date', $year)->where('user_id',$user->id)->where('status',1)->where('type',1)->with('category')->get();

        $data = [
            'incomes' => $incomes,
            'expenses' => $expenses
        ];
          if($incomes->isEmpty() && $expenses->isEmpty()){
              return $this->ResponseError($this->errors,'Incomes not found',400);
          }else{
              return $this->ResponseSuccess($data,"","Successfully fetched incomes.");
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

    public function realPieData(Request $request)
    {
        $user = Auth::user();
        $year = $request['year'];


         return $this->getCategoriesData($user->id,$year);

    }

    public function searchMatchMonth($array,$monthDate){
        $totalIncome = 0;
        $totalExpense = 0;
        $totalSavings = 0;
        $totalNeeds = 0;
        $totalBalance = 0;
        $totalWants = 0;
        foreach($array as $item){
            if($item->date == $monthDate){
                if($item->type == 2){
                    $totalIncome += $item->wage;
                    $totalBalance += $item->wage;
                }else if($item->type == 1){
                    $totalExpense += $item->wage;
                    $totalBalance -= $item->wage;
                }
                }if($item->category['title'] == 'Wants'){
                    $totalWants += $item->wage;
                }else if($item->category['title'] == 'Needs'){
                    $totalNeeds += $item->wage;
                }else if ($item->category['title'] == 'Savings'){
                    $totalSavings += $item->wage;
                }
            }

        return [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalSavings' => $totalSavings,
            'totalNeeds' => $totalNeeds,
            'totalBalance' => $totalBalance,
            'totalWants' => $totalWants,
        ];
    }


    public function getBalancesByYear($year){

        $user = Auth::user();
        $monthlyStats = $this->scheduleService->getAllMonthlySchedules($user->id);
        $fullStats = [];
        for($month = 1;$month <= 12; $month++){
            $monthData = $this->searchMatchMonth($monthlyStats,Carbon::create($year,$month,1)->format('Y-m'));
            $date = Carbon::create($year, $month, 1)->toDateString();
        $data = $this->goalService->getAllInformationMonthly($date,$user);
        $data2 = $this->goalService->getBalanceInformation($date,$user);
        $monthName = Carbon::create()->month($month)->monthName;
        $expenseObject = new \stdClass();
        $expenseObject->totalIncome = $data['totalIncome'] + $monthData['totalIncome']; ;
        $expenseObject->totalExpense = $data['totalExpense'] + $monthData['totalExpense'];
        $expenseObject->totalBalance = $data['totalBalance'] + $monthData['totalBalance'];
        $expenseObject->totalWants = $data['totalWants'] + $monthData['totalWants'];
        $expenseObject->totalNeeds = $data['totalNeeds'] + $monthData['totalNeeds'];
        $expenseObject->totalSavings = $data['totalSavings'] + $monthData['totalSavings'];
        $expenseObject->monthName = $monthName;
        $expenseObject->realBalance = $data2['realBalance'];



        $fullStats[$month] = $expenseObject;

//            $expenseObject = new \stdClass();
//            $expenseObject->expenses = Balance::whereYear('date', $year)->where('status',1)->where('type',2)->whereMonth('date', $month)->get();
//            $expenseObject->incomes = Balance::whereYear('date', $year)->where('status',1)->where('type',1)->whereMonth('date', $month)->get();
//            $expenseObject->goals = Goal::whereYear('start_date',$year)->where('status',1)->whereMonth('start_date',$month)->get();
//            $fullStats[$month] = $expenseObject;
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
            'category' => ['required'],
            'wage' => ['required', 'numeric'],
            'status' => ['numeric'],
            'type' => ['required', 'numeric'],
            'sub_category' => ['']
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
        $subCategory = $attributes['sub_category'];
        if($category == 'Income'){
            $balance->category_id = Category::where('title', $category)->first()->id;
            if(is_array($subCategory)){
                $balance->subCategory_id = $subCategory['id'];
            }else if ($subCategory != null){
                $newCategory = CategoriesController::store($subCategory, $balance->category_id,$user->id);
                if ($newCategory) {
                    $balance->subCategory_id = $newCategory->id;
                } else {
                    $this->errors = ['Duplicate category, you already have such category!'];
                    return false;
                }
            }
        }
        else {
             if (is_array($category) && is_array($subCategory)) {
               $balance->category_id = $category['id'];
                $balance->subCategory_id = $subCategory['id'];
            } else if (is_array($category) && $subCategory == null) {
                $balance->category_id = $category['id'];
            } else if (is_array($category) && $subCategory != null) {
                $newCategory = CategoriesController::store($subCategory, $category['id'],$user->id);
                if ($newCategory) {
                    $balance->category_id = $category['id'];
                    $balance->subCategory_id = $newCategory->id;
                } else {
                    $this->errors = ['Duplicate category, you already have such category!'];
                    return false;
                }
            }
        }

        if($balance->save()){
            $category = Categories::find($balance->category_id);
            $subCategory = Categories::find($balance->subCategory_id);
            $balance->sub_category = $subCategory;
            $balance->category = $category;
            return $balance;
        }
        else return false;

    }


}
