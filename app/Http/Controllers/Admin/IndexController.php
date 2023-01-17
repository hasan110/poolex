<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\View;
use App\Models\Transaction;
use App\Models\UserReferral;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    
    public function login()
    {
        if(Auth::check()){
            return redirect()->route('index');
        }
        return view('admin.login');
    }
    
    public function loginSubmit(Request $request)
    {
        $validation = $this->getValidationFactory()->make($request->all(), [
            'user_name' => ['required'],
            'password' => ['required']
        ]);
        
        if ($validation->fails()) {
            $request->session()->flash('Error', 'لطفا همه ی فیلد ها را پر کنید.');
            return back();
        }

        $admin = Admin::where('user_name', $request->user_name)->first();
        if($admin){
            if(Hash::check($request->password,$admin->password))
            {
                Auth::Login($admin);
                return redirect()->route('index');
            } else {
                $request->session()->flash('Error', 'رمز عبور وارد شده صحیح نمی باشد .');
                return back();
            }
        } else {
            $request->session()->flash('Error', ' نام کاربری وارد شده اشتباه است');
            return back();
        }
        return redirect()->route('index');
    }

    public function LogOut()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function DashboardDetails(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');

        $gold_plan=$silver_plan=$bronze_plan=0;

        $user_plans = UserPlan::with(['plan' , 'user'])->get();
        foreach ($user_plans as $key => $item) {
            switch($item->plan_id){
                case 1 : $bronze_plan++; break;
                case 2 : $silver_plan++; break;
                case 3 : $gold_plan++; break;
            }
        }

        $all_users = User::count();
        $pending_users = User::whereStatus(1)->count();
        $confirmed_users = User::whereStatus(2)->count();
        $all_keys = User::all()->sum('ad_keys');

        $all_views = count(View::all());
        $today_watched_ads = View::where('type' , 'video')->where('created_at' , '>' , $today)->count();

        $total_cash = User::all()->sum('cash');
        $total_coin = User::all()->sum('coins');

        $allrefs = UserReferral::where('expires_at' , '>' , $today)->count();

        $final = [
            'bronze_plan' => $bronze_plan,
            'silver_plan' => $silver_plan,
            'gold_plan' => $gold_plan,
            'all_users' => $all_users,
            'pending_users' => $pending_users,
            'confirmed_users' => $confirmed_users,
            'all_keys' => $all_keys,
            'all_views' => $all_views,
            'today_watched_ads' => $today_watched_ads,
            'total_cash' => number_format($total_cash),
            'total_coin' => number_format($total_coin),
            'allrefs' => $allrefs,
        ];

        return response()->json([
            'data'=> $final,
            'message'=> 'آمار ها.',
            'errors' => null,
        ],200);
    }

    public function chartData(Request $req)
    {
        if($req->date)
        {
            $from = \Morilog\Jalali\CalendarUtils::createCarbonFromFormat('Y-m-d', $req->date );
        }else{
            $from = Carbon::today();
        }

        if(
            $req->period == 1 ||
            $req->period == 2 ||
            $req->period == 3
        )
        {
            $period = $req->period;
        }else{
            return response()->json([
                'data'=> null,
                'message'=> 'دوره نامعتبر.',
                'errors' => null,
            ],400);
        }

        $sections = [
            'total_user','confirmed_user','user_cash','user_coin','view'
        ];

        if(!in_array($req->section , $sections))
        {
            return response()->json([
                'data'=> null,
                'message'=> 'بخش نامعتبر.',
                'errors' => null,
            ],400);
        }

        $chart_data = $this->fetchData($from , $req->section , $period);

        $data = [
            'title' => ucwords(str_replace('_' , ' ' , $req->section)),
            'data' => $chart_data['data'],
            'labels'=> $chart_data['labels'],
        ];

        return response()->json([
            'data'=> $data,
            'message'=> 'اطلاعات نمودار.',
            'errors' => null,
        ],200);
    }

    public function fetchData($from , $section , $period)
    {
        $start = $from;
        switch($period)
        {
            case 1:
                $from->addDay();
                $end = Carbon::parse($from)->subDay();
            break;
            case 2:
                $from->addWeek();
                $end = Carbon::parse($from)->subWeek();
            break;
            case 3:
                $from->addMonth();
                $end = Carbon::parse($from)->subMonth();
            break;
        }

        $i = 0;
        $chart = [];
        $label = [];
        while($i < 10)
        {
            //------------
            switch($section)
            {
                case 'total_user':
                    $chart[] = User::where('created_at' , '<' , $start)
                    ->where('created_at' , '>' , $end)->count();
                break;
                case 'confirmed_user':
                    $chart[] = User::where('created_at' , '<' , $start)
                    ->where('created_at' , '>' , $end)
                    ->where('status' , 2)->count();
                break;
                case 'user_cash':
                    $chart[] = Transaction::where('type' , 'cash')
                    ->where('created_at' , '<' , $start)
                    ->where('created_at' , '>' , $end)->sum('amount');
                    break;
                case 'user_coin':
                    $chart[] = Transaction::where('type' , 'coin')
                    ->where('created_at' , '<' , $start)
                    ->where('created_at' , '>' , $end)->sum('amount');
                    break;
                case 'view':
                    $chart[] = View::where('type' , 'video')
                    ->where('created_at' , '<' , $start)
                    ->where('created_at' , '>' , $end)
                    ->count();
                break;
            }
            //------------
            $label[] = Jalalian::forge($end)->format('Y-m-d');
            $start = $end;
            $next = $end;
            switch($period)
            {
                case 1:
                    $end = Carbon::parse($next)->subDay();
                break;
                case 2:
                    $end = Carbon::parse($next)->subWeek();
                break;
                case 3:
                    $end = Carbon::parse($next)->subMonth();
                break;
            }
            $i++;
        }

        return [
            'data' => array_reverse($chart),
            'labels' => array_reverse($label)
        ];
    }
}
