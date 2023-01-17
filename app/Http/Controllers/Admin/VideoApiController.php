<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;

class VideoApiController extends Controller
{
    public function getFilms(Request $request)
    {
        $list = $this->sendCurl();

        $list = array_filter($list['items'], function($item) {
            return $item['type'] == 'انیمیشن' || $item['type'] == 'سریال' || $item['type'] == 'فیلم';
        });

        $final_list = [];
        foreach($list as $item)
        {
            $video = Video::where('relation_id' , $item['mid'])->first();

            if(!$video)
            {
                $final_list[] = $item;
            }
        }

        return Response::success($list , 'لیست ویدیو ها با موفقیت دریافت شد .');
    }

    public function moveFilms(Request $request)
    {
        $list = $this->sendCurl();

        $list = array_filter($list['items'], function($item) {
            return $item['type'] == 'فیلم' || $item['type'] == 'انیمیشن';
        });

        $newfilms = 0;
        foreach($list as $item)
        {
            $video = Video::where('relation_id' , $item['mid'])->first();

            if(!$video)
            {
                $lang = $item['language'];
                if($lang == "Farsi"){
                    $lang = 1;
                }elseif($lang == "Original-Sub"){
                    $lang = 2;
                }elseif($lang == "Farsi-Dub"){
                    $lang = 1;
                }else{
                    $lang = 3;
                }

                Video::create([
                    'relation_id' => $item['mid'],
                    'uuid' => uniqid(),
                    'title' => $item['title'],
                    'video_id' => null,
                    'type' => 1,
                    'translate_status' => $lang,
                    'episode_number' => null,
                    'file' => $item['quality']['Q_720p'],
                    'poster' => $item['poster'],
                    'rate' => 8,
                    'complete_link' => 2,
                    'description' => $item['shortDesc']
                ]);

                $newfilms++;
            }
        }

        return Response::success($list , $newfilms . ' ویدیو جدید با موفقیت منتقل شد .');
    }

    public function moveSeries(Request $request)
    {
            
        $serial = Video::where('id' , $request->video_id)->where('type' , 2)->first();
        if(!$serial)
        {
            return Response::error(null , 'سریال مورد نظر یافت نشد' , null ,  404);
        }
        if(!$request->videos)
        {
            return Response::error(null , 'سریالی انتخاب نشده است.' , null ,  400);
        }

        $selected_series = explode(",",$request->videos);

        $list = $this->sendCurl();

        foreach($list['items'] as $item)
        {
            foreach($selected_series as $episode_id)
            {
               
                if($item['mid'] == $episode_id)
                {
                     $video = Video::where('relation_id' , $item['mid'])->first();

                    if(!$video)
                    {
                        $lang = $item['language'];
                        if($lang == "Farsi"){
                            $lang = 1;
                        }elseif($lang == "Original-Sub"){
                            $lang = 2;
                        }elseif($lang == "Farsi-Dub"){
                            $lang = 1;
                        }else{
                            $lang = 3;
                        }
        
                        Video::create([
                            'relation_id' => $item['mid'],
                            'uuid' => uniqid(),
                            'title' => $item['title'],
                            'video_id' => $request->video_id,
                            'type' => 3,
                            'translate_status' => $lang,
                            'episode_number' => null,
                            'file' => $item['quality']['Q_720p'],
                            'poster' => $item['poster'],
                            'rate' => 8,
                            'complete_link' => 2,
                            'description' => $item['shortDesc']
                        ]);
                    }
                }
            }
           
        }

        return Response::success($list , ' قسمت های انتخاب شده با موفقیت به سریال اضافه شدند .');
    }

    public function sendCurl()
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.arnr.ir/v1/srv/movie",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "per_page=100000",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                'api-token: 7fa80f3f-e145-4a04-a235-bdd96849a6d8',
                'client-id: atabxm',
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response , true);
        return $response;
    }
}
