<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin-channel', function ($user) {
  return $user->isAdmin(); // Assuming isAdmin() method on your User model
});