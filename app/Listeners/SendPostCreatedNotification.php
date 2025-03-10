<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostCreatedNotification implements ShouldQueue
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
    public function handle(PostCreated $event): void
    {
        $post = $event->post;
        
        // Create notification record in database for admin to see
        $notification = new Notification();
        $notification->user_id = $post->user_id;
        $notification->post_id = $post->id;
        $notification->type = 'post_pending';
        $notification->message = $post->user->name . ' has created a new post "' . $post->title . '" that needs approval.';
        $notification->is_read = false;
        $notification->save();
        
        // Notification is broadcasted by PostCreated event itself
        // since it implements ShouldBroadcast
    }
}
