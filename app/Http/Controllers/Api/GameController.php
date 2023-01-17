<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class GameController extends Controller
{
    public function get(Request $request)
    {
        $validation = $this->validateData($request , [
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);

        $game = Game::where('uuid' , $request->id)->first();
        if(!$game){
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }
        
        return Response::success($game , 'اطلاعات بازی.');
    }

    public function start(Request $request)
    {
        $validation = $this->validateData($request , [
            'coin' => 'required|numeric'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);
        $now = Carbon::now();

        if($user->coins < $request->coin){
            return Response::error(null , 'سکه کافی ندارید.' , null);
        }

        $check = Game::where('status' , 0)
        ->where('starter_id' , '!=' , $user->id)
        ->where('incoming_coins' , $request->coin)
        ->where('expires_at' , '>' , $now)
        ->first();

        if($check)
        {
            $user->update([
                'coins' => $user->coins - $request->coin
            ]);
            $check->update([
                'acceptor_id'=>$user->id,
                'status'=>1
            ]);

            $starter = User::find($check->starter_id);
            $starter->update([
                'coins' => $starter->coins - $check->incoming_coins
            ]);

            $check = Game::find($check->id);

            return Response::success($check , 'شروع بازی');
        }

        $game = Game::create([
            'uuid'=>uniqid(),
            'starter_id'=>$user->id,
            'game_number'=>rand(1,4),
            'incoming_coins'=>(int)$request->coin,
            'received_coins'=>($request->coin * 2) - 1,
            'expires_at'=>Carbon::now()->addSeconds(30),
            'status'=>0
        ]);

        $game = Game::find($game->id);
        
        return Response::success($game , 'درانتظار حریف.');
    }

    public function finish(Request $request)
    {
        $validation = $this->validateData($request , [
            'score' => 'required|numeric',
            'id' => 'required'
        ]);
        if($validation){
            return $validation;
        }
        $user = $this->getUserByToken($request);

        $game = Game::where('uuid' , $request->id)->first();
        if(!$game){
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }
        $score = $request->score;
        if($user->id == $game->starter_id){
            $game->starter_score = $score;
        }elseif($user->id == $game->acceptor_id){
            $game->acceptor_score = $score;
        }else{
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }
        if($game->status == 1){
            $game->status = 2;
            $game->save();
            return Response::success($game , 'در انتظار اتمام بازی حریف');
        }elseif($game->status == 2){
            $game->status = 3;

            if($game->starter_score > $game->acceptor_score){
                $game->winner_id = $game->starter_id;
            }elseif($game->starter_score < $game->acceptor_score){
                $game->winner_id = $game->acceptor_id;
            }

            $winner = User::find($game->winner_id);
            $winner->update([
                'coins'=>$winner->coins + $game->received_coins
            ]);
            $game->save();
        }else{
            return Response::error(null , 'درخواست نامعتبر.' , null);
        }
    }
}
