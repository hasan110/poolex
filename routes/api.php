<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// poolex api routes

Route::namespace('App\Http\Controllers\Api')->group(function () {

    Route::post('/registerByNumber', 'AuthController@registerByNumber');
    Route::post('/numberVerify' , 'AuthController@numberVerify');
    Route::post('/send_password_by_number' , 'AuthController@send_password_by_number');
    Route::post('/register', 'AuthController@register');
    Route::post('/login' , 'AuthController@login');
    Route::post('/verify' , 'AuthController@verify');
    Route::post('/forget_password' , 'AuthController@forget_password');

    Route::middleware('CheckApiAuth')->group(function () {

        Route::get('/DashboardDetails', 'IndexController@DashboardDetails');

        Route::post('/change_password', 'AuthController@change_password');
        Route::post('/update_profile', 'AuthController@update_profile');
        Route::post('/fast_update_profile', 'AuthController@fast_update_profile');
        Route::post('/seller_update_profile', 'AuthController@seller_update_profile');

        Route::post('/getUserData', 'UserController@get_user');
        Route::post('/change_credit', 'UserController@change_credit');
        Route::post('/convert', 'UserController@convert');

        Route::post('/save_fcm_refresh_token', 'UserController@saveFcmRefreshToken');

        Route::post('/harvest/add', 'HarvestController@add_harvest');
        Route::get('/harvest/list', 'HarvestController@harvest_list');
        Route::get('/harvest/all', 'HarvestController@all_harvests');

        Route::get('/tickets', 'TicketController@tickets');
        Route::post('/add_ticket', 'TicketController@add_ticket');
        Route::get('/get_ticket/{id}', 'TicketController@get_ticket');
        Route::post('/reply_ticket', 'TicketController@reply_ticket');

        Route::get('/converting', 'ConvertorController@converting');
        Route::post('/addConvert', 'ConvertorController@addConvert');
        Route::post('/moveToWallet', 'ConvertorController@moveToWallet');

        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/notificationDelete/{id}', 'NotificationController@notificationDelete');

        Route::get('/award_list', 'AwardController@list');
        Route::get('/get_random_award', 'AwardController@get_random_award');
        Route::post('/confirm_award', 'AwardController@confirm_award');
        Route::post('/pay_award', 'AwardController@pay_award');
        Route::get('/pay_random_award', 'AwardController@pay_random_award');

        Route::get('/get_ads', 'AdvertiseController@list');
        Route::get('/get_ad', 'AdvertiseController@get_ad');
        Route::post('/get_ad_data', 'AdvertiseController@get_ad_data');
        Route::post('/ad_watched', 'AdvertiseController@ad_watched');

        Route::post('/get_referral', 'ReferralController@get_referral');
        Route::get('/referrals', 'ReferralController@referrals');
        Route::post('/update_referral', 'ReferralController@update_referral');
        Route::post('/pay_referral', 'ReferralController@pay_referral');

        Route::post('/get_videos', 'VideoController@get_videos');
        Route::post('/search_videos', 'VideoController@search_videos');
        Route::post('/get_video', 'VideoController@get_video');
        Route::get('/get_categories', 'VideoController@get_categories');
        Route::post('/get_videos_by_category', 'VideoController@get_videos_by_category');
        Route::post('/get_trailers', 'VideoController@get_trailers');

        Route::post('/video/feedback', 'VideoController@feedback_video');

        Route::post('/buy_plan', 'PlanController@buy_plan');

        Route::post('/buy_product', 'ProductController@buy_product');
        Route::get('/buy_offer', 'ProductController@buy_offer');

        Route::post('/get_game_data', 'GameController@get');
        Route::post('/start_game', 'GameController@start');
        Route::post('/finish_game', 'GameController@finish');

        Route::get('/get_slideshows', 'SlideShowController@get_slideshows');
        Route::get('/get_movie_banners', 'SlideShowController@get_movie_banners');
        Route::get('/get_movie_slideshows', 'SlideShowController@get_movie_slideshows');
        Route::get('/get_messages', 'PostController@get_messages');
    });

    Route::get('/plan_list', 'PlanController@list');
    Route::get('/product_list', 'ProductController@list');

    Route::get('/get_helps', 'PostController@get_helps');
    Route::get('/get_sliders', 'PostController@get_sliders');
    Route::get('/get_rules', 'PostController@get_rules');

    Route::get('/get_settings', 'SettingController@get_settings');

    // STORE ROUTES

    Route::post('/seller/login' , 'AuthController@seller_login');

    Route::get('/get_store_home_data', 'IndexController@get_store_home_data');
    Route::get('/get_category_products/{id}', 'CategoryController@get_category_products');
    Route::get('/get_product_data/{id}', 'StoreProductController@get_product_data');
    Route::get('/get_store_data/{id}', 'StoreController@get_store_data');

    Route::middleware('CheckApiAuth')->group(function () {
        
        Route::post('/product/toggle_favorite', 'StoreProductController@toggle_favorite');
        Route::get('/product/favorites', 'StoreProductController@favorites');
        
        Route::post('/product/add_comment', 'StoreProductController@add_comment');
        
        Route::post('/add_credit', 'UserController@add_credit');
        
        Route::post('/get_cart_products', 'StoreProductController@get_cart_products');
        
        Route::post('/submit_order', 'InvoiceController@submit_order');
        
        Route::get('/getChatList', 'ChatController@getChatList');
        Route::get('/getChatDetails/{chat_id}', 'ChatController@getChatDetails');
        Route::post('/sendChatMessage', 'ChatController@sendChatMessage');
        Route::post('/newChat', 'ChatController@newChat');
        
        // seller panel
        Route::middleware('CheckSeller')->group(function () {

            Route::get('/store/list', 'StoreProductController@store_list');
            Route::get('/store/data/{id}', 'StoreProductController@store_data');
            Route::post('/store/add', 'StoreProductController@add_store');
            Route::post('/store/add-product', 'StoreProductController@add_product');

        });

    });

});
