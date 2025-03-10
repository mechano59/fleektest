<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction(); // Start the transaction

        try {
            $post = new Post();
            $post->user_id = auth()->id();
            $post->title = $validated['title'];
            $post->category_id = $validated['category_id'];
            $post->content = $validated['content'];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/posts'), $imageName);
                $post->image_path = 'images/posts/' . $imageName;
            }

            $post->save(); // Save the post *within* the transaction.

            DB::commit(); // Commit the transaction (make the changes permanent)

            return redirect()->back()->with('success', 'Post created successfully');

        } catch (\Throwable $e) {  // Catch *any* exception (or Throwable in PHP 7+)
            DB::rollBack(); // Rollback the transaction (undo the changes)

            // Log the error
            Log::error('Post creation failed: ' . $e->getMessage(), [
                'request' => $request->except('image'),
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // If an image was uploaded, delete it
            if (isset($imageName)) {
                @unlink(public_path('images/posts/' . $imageName));
            }

            return redirect()->back()->with('error', 'Post creation failed. Please try again. If the problem persists, contact support.');
        }
    }
}
