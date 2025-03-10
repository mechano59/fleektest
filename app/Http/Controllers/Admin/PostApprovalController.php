<?php

namespace App\Http\Controllers\Admin;

use App\Events\PostStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApprovalController extends Controller
{
    public function approve($id)
    {
        $post = Post::findOrFail($id);
        $post->approval_status = 'approved';
        $post->save();
        
        // Create notification for user
        $notification = new Notification();
        $notification->user_id = $post->user_id;
        $notification->post_id = $post->id;
        $notification->type = 'post_approved';
        $notification->message = 'Your post "' . $post->title . '" has been approved.';
        $notification->save();
        
        // Broadcast event
        event(new PostStatusChanged($post, 'approved'));
        
        return redirect()->back()->with('success', 'Post approved');
    }
    
    public function reject($id)
    {
        $post = Post::findOrFail($id);
        $post->approval_status = 'rejected';
        $post->save();
        
        // Create notification for user
        $notification = new Notification();
        $notification->user_id = $post->user_id;
        $notification->post_id = $post->id;
        $notification->type = 'post_rejected';
        $notification->message = 'Your post "' . $post->title . '" has been rejected.';
        $notification->save();
        
        // Broadcast event
        event(new PostStatusChanged($post, 'rejected'));
        
        return redirect()->back()->with('success', 'Post rejected');
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        
        return response()->json(['success' => true]);
    }
}
