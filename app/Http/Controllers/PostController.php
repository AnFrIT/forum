<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Topic $topic)
    {
        // Проверяем только авторизацию, без дополнительных прав
        if (!auth()->check()) {
            abort(403, 'Необходимо войти в систему для отправки сообщений');
        }

        if ($topic->is_locked && ! auth()->user()->isModerator()) {
            abort(403, 'Тема закрыта для новых сообщений');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5',
            'attachments.*' => 'file|max:'.(setting('max_attachment_size', 10) * 1024),
        ]);

        $post = $topic->posts()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'is_approved' => ! setting('require_post_approval', false) || auth()->user()->isModerator(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $post->attachments()->create([
                    'filename' => $file->hashName(),
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        // Calculate the last page number
        $postsPerPage = setting('posts_per_page', 20);
        $totalPosts = $topic->posts()->count();
        $lastPage = ceil($totalPosts / $postsPerPage);

        return redirect()->route('topics.show', ['topic' => $topic, 'page' => $lastPage])
            ->with('success', 'Сообщение добавлено!');
    }

    public function edit(Post $post)
    {
        $this->authorize('edit', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('edit', $post);

        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $post->update([
            'content' => $validated['content'],
            'is_edited' => true,
            'edited_at' => now(),
            'edited_by' => auth()->id(),
        ]);

        return redirect()->route('topics.show', $post->topic)
            ->with('success', 'Сообщение успешно обновлено!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $topic = $post->topic;
        $post->delete();

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Сообщение успешно удалено!');
    }
}
