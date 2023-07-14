<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;
use App\Http\Controllers\Controller;

class UserRepository
{
  public $pagination = 50;

  public function createUser(Request $request): User
  {
    $hash = Controller::Cryptographer(true , $request->password);

    $user = User::create([
      'email'=>$request->email,
      'password'=>$hash,
      'referral_id'=>Str::random(5),
      'api_token'=>Str::random(120)
    ]);

    $user->user_plan()->create([
      'plan_id' => 1
    ]);

    return $user;
  }

  public function createUserByNumber($data): User
  {
    $hash = Controller::Cryptographer(true , $data['password']);

    $user = User::create([
      'mobile_number'=>$data['mobile_number'],
      'is_seller'=>$data['is_seller'],
      'password'=>$hash,
      'referral_id'=>Str::random(5),
      'api_token'=>Str::random(120)
    ]);

    $user->user_plan()->create([
      'plan_id' => 1
    ]);

    return $user;
  }

  public function updateUserData(Request $request , $user)
  {
    return $user->update($request->except('user_detail'));
  }

  public function getUsers(Request $request)
  {
    $users = User::latest();

    if($request->search_key){
      $search_key = $request->search_key;
      $users = $users->where(function($query) use ($search_key) {
        $query->where('fullname','like', '%' . $search_key . '%');
        // ->orWhere('email','=','johndoe@xyz.com');
      });
    }

    if($request->has('status')){
      $status = $request->status;

      $users = $users->whereHas('user_detail', function($q) use ($status){
        $q->where('user_status',$status);
      });

    }

    $users = $users->with('user_detail')->paginate($this->pagination);

    foreach($users as $user){
      $user['registered_at'] = Jalalian::forge($user->created_at)->format('%d / %m / %Y');

      $rr = $user->user_detail->reject_reason;
      if(strlen($rr) > 25){
        $user->user_detail->reject_reason_short = mb_substr($rr , 0 , 25) . '...';
      }else{
        $user->user_detail->reject_reason_short = $rr;
      }
    }
    return $users;
  }
}
