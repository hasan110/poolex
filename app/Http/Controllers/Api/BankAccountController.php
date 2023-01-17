<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Morilog\Jalali\Jalalian;
use App\Models\UserBankAccount;
use App\Models\AdminBankAccount;

class BankAccountController extends Controller
{
    public function all(Request $request)
    {
        $user = $this->getUserByToken($request);

        $bank_accounts = $user->user_bank_account;

        return response()->json([
            'data'=> $bank_accounts,
            'message'=> 'حساب های بانکی کاربر .',
            'errors' => null,
        ],200);
    }
    public function add(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'card_number' => 'required',
            'account_number' => 'required',
            'shaba_number' => 'required',
            'card_image' => 'required'
        ]);
        if($validation !== 'ok'){
            return $validation;
        }

        $user_bank_account = $user->user_bank_account()->create([
            'card_number' => $request->card_number,
            'account_number' => $request->account_number,
            'shaba_number' => $request->shaba_number,
            'card_image' => $this->uploadUserFile($request->card_image , $user),
        ]);

        return response()->json([
            'data'=> $user_bank_account,
            'message'=> 'حساب بانکی شما با موفقیت افزوده شد .',
            'errors' => null,
        ],200);
    }
    public function get(Request $request)
    {
        $user = $this->getUserByToken($request);

        $id = $request->id;

        $bank_account = UserBankAccount::where('user_id' , $user->id)->where('id' , $id)->first();

        return response()->json([
            'data'=> $bank_account,
            'message'=> 'حساب بانکی کاربر .',
            'errors' => null,
        ],200);
    }
    public function edit(Request $request)
    {
        $user = $this->getUserByToken($request);

        $validation = $this->validateData($request , [
            'id' => 'required',
            'card_number' => 'required',
            'account_number' => 'required',
            'shaba_number' => 'required'
        ]);
        if($validation !== 'ok'){
            return $validation;
        }

        $user_bank_account = $user->user_bank_account()->where('id' , $request->id)->update([
            'card_number' => $request->card_number,
            'account_number' => $request->account_number,
            'shaba_number' => $request->shaba_number,
            'status' => 0
        ]);

        return response()->json([
            'data'=> $user_bank_account,
            'message'=> 'حساب بانکی شما با موفقیت ویرایش شد .',
            'errors' => null,
        ],200);
    }
    public function delete(Request $request)
    {
        $user = $this->getUserByToken($request);

        $user->user_bank_account()->where('id' , $request->id)->delete();

        return response()->json([
            'data'=> null,
            'message'=> 'حساب بانکی شما با موفقیت حذف شد .',
            'errors' => null,
        ],200);
    }
    public function getAdminAccount(Request $request)
    {
        $user = $this->getUserByToken($request);
        $validation = $this->validateData($request , [
            'amount' => 'required'
        ]);
        if($validation !== 'ok'){
            return $validation;
        }

        $amount = $request->amount;

        $admin_bank_account = AdminBankAccount::where('status', 1)->first();

        if($amount < $admin_bank_account->max_value)
        {
            $final = $admin_bank_account;
        }else{
            $final = AdminBankAccount::where('max_value', '>=' , $amount)->first();

            if(!$final)
            {
                $final = $admin_bank_account;
            }
        }

        return response()->json([
            'data'=> $final,
            'message'=> 'حساب بانکی ادمین .',
            'errors' => null,
        ],200);
    }
    public function confirmed(Request $request)
    {
        $user = $this->getUserByToken($request);

        // $bank_accounts = UserBankAccount::whereHas('user', function($q) use ($user) {
        //     $q->where('user_id', $user->id)->where('status', 1);
        // })->pluck('card_number')->toArray();

        $bank_accounts = $user->user_bank_account()->where('status', 1)->pluck('card_number')->toArray();
        $admin_bank_account = AdminBankAccount::where('status', 1)->first();

        $data = [
            'bank_accounts' => $bank_accounts ,
            'admin_bank_account' => $admin_bank_account
        ];
        return response()->json([
            'data'=> $data,
            'message'=> 'حساب های بانکی کاربر .',
            'errors' => null,
        ],200);
    }
}
