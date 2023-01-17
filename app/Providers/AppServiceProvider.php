<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;	
use Illuminate\Http\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(env('DEPLOYED')){
            $this->app->bind('path.public', function() {
                return realpath(base_path().'/../');
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function($data, $message , $status_code = 200) {
            return response()->json([
                'data'=> $data,
                'message'=> $message,
                'errors' => null,
            ] , $status_code);
        });

        Response::macro('error', function($data, $message, $errors , $status_code = 400) {
            return response()->json([
                'data'=> $data,
                'message'=> $message,
                'errors' => $errors,
            ] , $status_code);
        });
    }
}
