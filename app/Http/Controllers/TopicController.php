<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    public function show(Topic $topic)
    {
        if (! $topic->is_approved && (! auth()->check() || ! auth()->user()->isModerator())) {
            abort(404);
        }

        $topic->incrementViews();

        $posts = $topic->posts()
            ->with(['user', 'attachments'])
            ->visible()
            ->paginate(setting('posts_per_page', 20));

        return view('topics.show', compact('topic', 'posts'));
    }

    public function create(Category $category)
    {
        if (!auth()->check() || (!auth()->user()->is_admin && !auth()->user()->is_moderator)) {
            abort(403, 'Только администраторы и модераторы могут создавать темы');
        }

        return view('topics.create', compact('category'));
    }

    public function store(Request $request, Category $category)
    {
        if (!auth()->check() || (!auth()->user()->is_admin && !auth()->user()->is_moderator)) {
            abort(403, 'Только администраторы и модераторы могут создавать темы');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'attachments.*' => 'file|max:'.(setting('max_attachment_size', 10) * 1024),
        ]);

        $topic = $category->topics()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => auth()->id(),
            'is_approved' => ! setting('require_post_approval', false) || auth()->user()->isModerator(),
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $topic->attachments()->create([
                    'filename' => $file->hashName(),
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Тема успешно создана!');
    }

    public function edit(Topic $topic)
    {
        $this->authorize('edit', $topic);

        // Get all categories for topic reassignment (for moderators)
        $categories = Category::main()->with('children')->orderBy('order')->get();

        return view('topics.edit', compact('topic', 'categories'));
    }

    public function update(Request $request, Topic $topic)
    {
        $this->authorize('edit', $topic);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $topic->update($validated);

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Тема успешно обновлена!');
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('delete', $topic);

        $category = $topic->category;
        $topic->delete();

        return redirect()->route('categories.show', $category)
            ->with('success', 'Тема успешно удалена!');
    }

    public function lock(Topic $topic)
    {
        $this->authorize('lock topics');

        $topic->update(['is_locked' => ! $topic->is_locked]);

        return back()->with('success', $topic->is_locked ? 'Тема закрыта' : 'Тема открыта');
    }

    public function pin(Topic $topic)
    {
        $this->authorize('pin topics');

        $topic->update(['is_pinned' => ! $topic->is_pinned]);

        return back()->with('success', $topic->is_pinned ? 'Тема закреплена' : 'Тема откреплена');
    }
}
