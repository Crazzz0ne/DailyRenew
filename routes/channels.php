<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.Auth.User.{id}', function ($user, $id) {
    return true;
});

Broadcast::channel('lead.{leadId}', function ($user, $leadId){
   return true;
});
Broadcast::channel('customer.{customerId}', function ($user, $leadId){
    return true;
});

Broadcast::channel('Queue.{id}', function ($user, $id){
    return true;
});
Broadcast::channel('Queue', function (){
    return true;
});

Broadcast::channel('settings.eligibleCity.{id}', function (){
    return true;
});

