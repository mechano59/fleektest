<?php

namespace App\Listeners;

use App\Events\PostStatusChanged;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostStatusChangedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostStatusChanged $event): void
    {
        $post = $event->post;
        $status = $event->status;
        
        // Create notification record in database
        $notification = new Notification();
        $notification->user_id = $post->user_id;
        $notification->post_id = $post->id;
        $notification->type = 'post_' . $status;
        $notification->message = 'Your post "' . $post->title . '" has been ' . $status . '.';
        $notification->is_read = false;
        $notification->save();
        
        // Notification is broadcasted by PostStatusChanged event itself
        // since it implements ShouldBroadcast
    }
}