<?php

use App\Models\Test;
use Illuminate\Support\Facades\Broadcast;
use \Illuminate\Support\Facades\Log;
use \Illuminate\Support\Facades\Route;
use \Illuminate\Broadcasting\BroadcastController;

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

//Broadcast::channel('private_user.{userId}', function ($user, $userId) {
////    Log::info('user:', ['user_id' => $user->id, 'requested_id' => $userId]);
////    return (int) $user->id === (int) $userId;
//    return (int) $user->id === (int) $userId;
//});

Broadcast::channel('private', function ($user) {
    return true;
});
