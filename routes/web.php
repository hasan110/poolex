<?php

use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Admin')->group(function () {

    Route::get('/login', 'IndexController@login')->name('login');
    Route::post('/login', 'IndexController@loginSubmit')->name('loginSubmit');
    Route::get('/logout', 'IndexController@logout')->name('logout');

    Route::get('/pay_referral', 'PayController@pay_referral')->name('pay.referral');
    Route::get('/buy_plan', 'PayController@buy_plan')->name('pay.buy_plan');
    Route::get('/buy_product', 'PayController@buy_product')->name('pay.buy_product');
    Route::get('/buy_offer', 'PayController@buy_offer')->name('pay.buy_offer');
    Route::get('/buy_award', 'PayController@buy_award')->name('pay.buy_award');
    Route::get('/add_credit', 'PayController@add_credit')->name('pay.add_credit');
    Route::get('/pay_order', 'PayController@pay_order')->name('pay.pay_order');

    Route::middleware('CheckAdminAuth')->group(function () {

        Route::prefix('/users')->group(function () {
            Route::post('/list', 'UserController@list')->name('usersList');
            Route::get('/user/{id}', 'UserController@get')->name('getUser');
            Route::post('/edit', 'UserController@edit')->name('editUser');
            Route::post('/operate', 'UserController@operate')->name('operateUser');
            Route::post('/charge', 'UserController@chargeUser')->name('chargeUser');
            Route::post('/giveAward', 'UserController@giveAward')->name('giveAward');
            Route::post('/giveReferal', 'UserController@giveReferal')->name('giveReferal');
            Route::get('/updateKeys', 'UserController@updateKeys')->name('updateKeys');
            Route::post('/checkoutSeller', 'UserController@checkoutSeller')->name('checkoutSeller');
        });

        Route::prefix('/user_awards')->group(function () {
            Route::post('/list', 'UserAwardController@list')->name('UserAwardList');
            Route::get('/user_award/{id}', 'UserAwardController@get')->name('getUserAward');
            Route::post('/operate', 'UserAwardController@operate')->name('OperateUserAward');
        });

        Route::prefix('/account')->group(function () {
            Route::post('/operate', 'AccountController@OperateUserBankAccount')->name('OperateUserBankAccount');
        });

        Route::prefix('/admins')->group(function () {
            Route::get('/all', 'AdminController@all')->name('admin.all');
            Route::post('/add', 'AdminController@add')->name('admin.add');
            Route::get('/get/{id}', 'AdminController@get')->name('admin.get');
            Route::post('/edit', 'AdminController@edit')->name('admin.edit');
            Route::post('/delete', 'AdminController@delete')->name('admin.delete');
        });

        Route::prefix('/harvest')->group(function () {
            Route::post('/list', 'HarvestController@list')->name('HarvestList');
            Route::get('/harvest/{id}', 'HarvestController@get')->name('getHarvest');
            Route::post('/operate', 'HarvestController@operate')->name('OperateHarvest');
        });

        Route::prefix('/ticket')->group(function () {
            Route::post('/list', 'TicketController@list')->name('ticketList');
            Route::get('/ticket/{id}', 'TicketController@get')->name('getTicket');
            Route::post('/operate', 'TicketController@operate')->name('operateTicket');
            Route::post('/reply', 'TicketController@reply')->name('replyTicket');
        });

        Route::prefix('notifications')->group(function () {
            Route::post('/create', 'NotificationController@addNotification')->name('notifications.create');
            Route::post('/send', 'NotificationController@sendFCM')->name('notifications.send');
            Route::post('/list', 'NotificationController@getNotifications')->name('notifications.list');
        });

        Route::prefix('/plans')->group(function () {
            Route::get('/list', 'PlanController@list')->name('planList');
            Route::get('/plan/{id}', 'PlanController@get')->name('getPlan');
            Route::post('/edit', 'PlanController@edit')->name('editPlan');
        });

        Route::prefix('/awards')->group(function () {
            Route::get('/list', 'AwardController@list')->name('awardList');
            Route::post('/add', 'AwardController@add')->name('addAward');
            Route::get('/award/{id}', 'AwardController@get')->name('getAward');
            Route::post('/edit', 'AwardController@edit')->name('editAward');
            Route::post('/delete', 'AwardController@delete')->name('deleteAward');
        });

        Route::prefix('/products')->group(function () {
            Route::get('/list', 'ProductController@list')->name('productList');
            Route::post('/add', 'ProductController@add')->name('addProduct');
            Route::get('/product/{id}', 'ProductController@get')->name('getProduct');
            Route::post('/edit', 'ProductController@edit')->name('editProduct');
            Route::post('/delete', 'ProductController@delete')->name('deleteProduct');
        });

        Route::prefix('/posts')->group(function () {
            Route::get('/list/{type}', 'PostController@list')->name('postList');
            Route::post('/add', 'PostController@add')->name('addPost');
            Route::get('/post/{id}', 'PostController@get')->name('getPost');
            Route::post('/edit', 'PostController@edit')->name('editPost');
            Route::post('/delete', 'PostController@delete')->name('deletePost');
        });

        Route::prefix('/advertises')->group(function () {
            Route::get('/list', 'AdvertiseController@list')->name('advertiseList');
            Route::post('/add', 'AdvertiseController@add')->name('addAdvertise');
            Route::get('/advertise/{id}', 'AdvertiseController@get')->name('getAdvertise');
            Route::post('/edit', 'AdvertiseController@edit')->name('editAdvertise');
            Route::post('/delete', 'AdvertiseController@delete')->name('deleteAdvertise');
            Route::get('/renew_all', 'AdvertiseController@renewAll')->name('renewAllAdvertise');
        });
        Route::prefix('/videos')->group(function () {
            Route::post('/list', 'VideoController@list')->name('videoList');
            Route::post('/add', 'VideoController@add')->name('addVideo');
            Route::get('/video/{id}', 'VideoController@get')->name('getVideo');
            Route::post('/edit', 'VideoController@edit')->name('editVideo');
            Route::post('/delete', 'VideoController@delete')->name('deleteVideo');
            Route::get('/serial_list', 'VideoController@serials')->name('serialsVideo');
        });

        Route::prefix('/video-api')->group(function () {
            Route::get('/list', 'VideoApiController@getFilms')->name('getFilms');
            Route::get('/move-films', 'VideoApiController@moveFilms')->name('moveFilms');
            Route::post('/move-series', 'VideoApiController@moveSeries')->name('moveSeries');
        });

        Route::prefix('/categories')->group(function () {
            Route::get('/list', 'CategoryController@list')->name('categoryList');
            Route::post('/add', 'CategoryController@add')->name('addCategory');
            Route::get('/category/{id}', 'CategoryController@get')->name('getCategory');
            Route::post('/edit', 'CategoryController@edit')->name('editCategory');
            Route::post('/delete', 'CategoryController@delete')->name('deleteCategory');
        });

        Route::prefix('/slideshows')->group(function () {
            Route::get('/list', 'SlideShowController@list')->name('slideShowList');
            Route::post('/add', 'SlideShowController@add')->name('addSlideShow');
            Route::get('/slideshow/{id}', 'SlideShowController@get')->name('getSlideShow');
            Route::post('/edit', 'SlideShowController@edit')->name('editSlideShow');
            Route::post('/delete', 'SlideShowController@delete')->name('deleteSlideShow');
        });

        Route::prefix('/stores')->group(function () {
            Route::post('/list', 'StoreController@list')->name('storeList');
            Route::post('/add', 'StoreController@add')->name('addStore');
            Route::get('/store/{id}', 'StoreController@get')->name('getStore');
            Route::post('/edit', 'StoreController@edit')->name('editStore');
            Route::post('/delete', 'StoreController@delete')->name('deleteStore');
            Route::post('/ChangeStatus', 'StoreController@ChangeStatus')->name('ChangeStatusStore');
        });

        Route::prefix('/store_products')->group(function () {
            Route::post('/list', 'StoreProductController@list')->name('storeProductList');
            Route::post('/add', 'StoreProductController@add')->name('addStoreProduct');
            Route::get('/store_product/{id}', 'StoreProductController@get')->name('getStoreProduct');
            Route::post('/edit', 'StoreProductController@edit')->name('editStoreProduct');
            Route::post('/delete', 'StoreProductController@delete')->name('deleteStoreProduct');
            Route::post('/ChangeStatus', 'StoreProductController@ChangeStatus')->name('ChangeStatusStoreProduct');
        });

        Route::prefix('/blogs')->group(function () {
            Route::post('/list', 'BlogController@list')->name('blogList');
            Route::post('/add', 'BlogController@add')->name('addBlog');
            Route::get('/blog/{id}', 'BlogController@get')->name('getBlog');
            Route::post('/edit', 'BlogController@edit')->name('editBlog');
            Route::post('/delete', 'BlogController@delete')->name('deleteBlog');
        });

        Route::prefix('/invoices')->group(function () {
            Route::post('/list', 'InvoiceController@list')->name('invoicesList');
            Route::get('/invoice/{id}', 'InvoiceController@get')->name('getInvoice');
            Route::post('/edit', 'InvoiceController@edit')->name('editInvoice');
            Route::post('/operate', 'InvoiceController@operate')->name('operateInvoice');
        });

        Route::get('/settings' , 'SettingController@setting')->name('settings');
        Route::post('/settings/edit' , 'SettingController@edit_setting')->name('edit_setting');
        Route::post('/settings/upload_app_file' , 'SettingController@upload_app_file')->name('upload_app_file');
        Route::post('/settings/edit_ads' , 'SettingController@edit_ads')->name('setting_edit_ads');

        Route::get('/dashboard_details' , 'IndexController@DashboardDetails')->name('DashboardDetails');
        Route::post('/get_chart_data', 'IndexController@chartData')->name('get_chart_data');
        Route::get('/views/ranking' , 'ViewController@viewRanking')->name('view_ranking');

        Route::get('/{path}' , 'IndexController@index')->where('path' , '.*');
        Route::get('/' , 'IndexController@index')->name('index');
    });

});
