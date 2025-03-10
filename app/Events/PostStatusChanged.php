<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $status;

    /**
     * Create a new event instance.
     *
     * @param  Post  $post
     * @param  string  $status
     * @return void
     */
    public function __construct(Post $post, string $status)
    {
        $this->post = $post;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' .$this->post->user_id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'post.status.changed';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'status' => $this->status,
            'message' => 'Your post "' . $this->post->title . '" has been ' . $this->status . '.'
        ];
    }
}