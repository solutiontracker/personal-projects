<?php

use App\Http\Controllers\Auth\Sales\LoginController;
use App\Http\Controllers\Auth\Sales\ForgotPasswordController;
use App\Http\Controllers\Auth\Sales\ResetPasswordController;
use App\Http\Controllers\Sales\AgentEventsController;
use App\Http\Controllers\Sales\EventsiteBillingOrderController;

/*
   *
   *  ALL SALES PORTAL ROUTES
   *
*/
Route::group(['prefix' => 'sales', 'middleware' => ['json.response', 'valid.interface.language'], 'as' => 'sales.'], function () {

    /*
       *
       *******************************************************************************
       |                         AUTHENTICATION ROUTES
       *******************************************************************************
       *
    */
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('/login', [LoginController::class, 'login'])
            ->name('login');
        Route::post('/password/reset-request', [ForgotPasswordController::class, 'forgotPassword'])
            ->name('password.request');
        Route::post('/password/reset-code/verify', [ForgotPasswordController::class, 'verifyResetCode'])
            ->name('reset-code.verify');
        Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword'])
            ->name('password');
    });


    /*
       *
       *******************************************************************************
       |                         AGENT AUTHORISED ROUTES
       *******************************************************************************
       *
    */
    Route::group(['middleware' => ['auth:agent'], 'prefix' => 'agent', 'as' => 'agent.'], function () {

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::post('/events', [AgentEventsController::class, 'index'])->name('events');

        Route::post('/events/{event_id}/data', [AgentEventsController::class, 'eventData'])->name('event');

        Route::post('/events/{event_id}/orders', [AgentEventsController::class, 'eventOrders'])->name('event.orders');

        Route::post('/events/{event_id}/orders/{order_id}/invoice', [AgentEventsController::class, 'orderInvoice'])->name('event.order.invoice');

        Route::post('/events/{event_id}/form-stats', [AgentEventsController::class, 'getFormBasedTicketingStats'])->name('event.formTicketing.stats');
        
        //Eventsite billing orders routes
        Route::group(['prefix' => 'billing', 'as' => 'billing-'], function () {
            Route::get("/{event_id}/delete-order/{orderId}", [EventsiteBillingOrderController::class, "deleteOrder"]);
            Route::post('send-order/{order_id}', [EventsiteBillingOrderController::class,'sendOrder']);
            Route::get('send-order-pdf/{order_id}/{order_type?}', [EventsiteBillingOrderController::class, 'sendOrderPdf']);
            Route::post('change-payment-status/{order_id}', [EventsiteBillingOrderController::class, 'changePaymentRecievedStatus']);
        });
    });

});
