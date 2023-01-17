<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\admin\UserController;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function addNotification(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'body' => ['required'],
        ]);

        $notification = new Notification();
        $notification->uuid = uniqid();
        $notification->title = $request->title;
        $notification->body = $request->body;
        $notification->status_send = 0;
        $notification->save();

        // if ($request->hasFile('image')) {
        //     $this->uploadFileById($request->image,"notifications", $notification->id);
        // }

        return Response::success($notification , 'نوتیفیکیشن با موفقیت ایجاد شد .');
    }

    public function editNotification(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'body' => ['required'],
            'type' => ['required']
        ]);

        $notification = $this->getNotificationById($request->id);
        $notification->title = $request->title;
        $notification->body = $request->body;
        $notification->type = $request->type;
        $notification->status_send = 1;
        $notification->save();

        // if ($request->hasFile('image')) {
        //     $this->uploadFileById($request->image,"notifications", $notification->id);
        // }

        return Response::success($notification , 'نوتیفیکیشن با موفقیت ویرایش شد .');
    }

    public static function getNotificationById($id): Notification
    {
        return  Notification::where('id', $id)->first();
    }

    public function getNotifications(Request $request)
    {
        $notifications = Notification::where('title', 'LIKE', "%{$request->search_key}%")->orderBy("id", "DESC")->paginate(50);

        return Response::success($notifications , 'نوتیفیکیشن ها با موفقیت دریافت شدند .');
    }

    public function sendFCM(Request $request)
    {
        $request->validate([
            'id' => ['required'],
        ]);

        $notif = Notification::find($request->id);

        $url = "https://fcm.googleapis.com/fcm/send";
        //serverKey
        $apiKey = "AAAAuhEiJCg:APA91bFXpYXm1Ht5-XIbNDG93TBidHmytTMFnPb9d4KJyFTafqnCNw0Unk8MmklIKkcSQDKj5JROmXXugKSdInBTqacm1PZRlyMSgHMPHzMDdXfCM_95fh0HD-jBv78tY8tOFb8Y-4Lc";
        $headers = array(
            'Authorization:key=' . $apiKey,
            'Content-Type: application/json'
        );

        //notification content
        $notificationData = [
            'title' => $notif->title,
            'body' => $notif->body,
            // 'image' => 'https://dl.lyricfa.app/uploads/notifications/'.$notif->id.'.jpg'
            // 'click_action' => 'activities.notifhandler'
        ];


        //Optional
        $dataPayload = [
            'to' => 'VIP',
            'date' => Carbon::now(),
            'other_data' => 'not important',
            "sound" => "default"
        ];

        $notifiable_users = User::whereNotNull('fcm_refresh_token')->pluck('fcm_refresh_token')->toArray();

        //Create Api body
        // if($notif->type == "all"){
            $notifBody = [
                'notification' => $notificationData,
                //data payload is optional
                'data' => $dataPayload,
                //optional in seconds max_time : 4 week
                'time_to_live' => 3600,
                // 'to' => token or Reg_id
                //            'to' =>
                // 'topics/newoffer'
                'registration_ids' => $notifiable_users
            ];
        // }

        $notif->status_send = 1;
        $notif->save();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notifBody));

        //Execute
        curl_exec($ch);
        curl_close($ch);

        return Response::success($notif , 'نوتیفیکیشن با موفقیت ارسال شد .');
    }
}
