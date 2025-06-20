<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;

class PrivateMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $folder = $request->get('folder', 'inbox');
        $search = $request->get('search');
        $userId = auth()->id();

        // Get conversations for current user
        $query = auth()->user()->activeConversations();

        // Apply search if provided
        if ($search) {
            $messageIds = PrivateMessage::where(function ($q) use ($userId, $search) {
                $q->where(function ($inner) use ($userId, $search) {
                    $inner->where('sender_id', $userId)
                        ->where('sender_deleted', false);
                })->orWhere(function ($inner) use ($userId, $search) {
                    $inner->where('recipient_id', $userId)
                        ->where('recipient_deleted', false);
                });
            })
            ->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            })
            ->pluck('conversation_id')
            ->unique();
            
            $query->whereIn('id', $messageIds);
        }

        // Filter by folder if specified
        if ($folder === 'archived') {
            // Get conversation IDs that have archived messages for this user
            $conversationIds = PrivateMessage::where(function ($q) use ($userId) {
                $q->where(function ($inner) use ($userId) {
                    $inner->where('sender_id', $userId)
                        ->where('archived_by_sender', true)
                        ->where('sender_deleted', false);
                })->orWhere(function ($inner) use ($userId) {
                    $inner->where('recipient_id', $userId)
                        ->where('archived_by_recipient', true)
                        ->where('recipient_deleted', false);
                });
            })
            ->pluck('conversation_id')
            ->unique();
            
            $query->whereIn('id', $conversationIds);
        } elseif ($folder === 'deleted') {
            // Show conversations marked as deleted for the user
            if (auth()->user()->id === $query->getModel()->user_one_id) {
                $query->where('user_one_deleted', true);
            } else {
                $query->where('user_two_deleted', true);
            }
        } else {
            // For inbox, ensure the conversation is not deleted for the user
            if (auth()->user()->id === $query->getModel()->user_one_id) {
                $query->where('user_one_deleted', false);
            } else {
                $query->where('user_two_deleted', false);
            }
            
            // In inbox, exclude archived conversations unless specifically viewing archived
            if ($folder !== 'archived') {
                // Get conversation IDs that have archived messages for this user
                $archivedConversationIds = PrivateMessage::where(function ($q) use ($userId) {
                    $q->where(function ($inner) use ($userId) {
                        $inner->where('sender_id', $userId)
                            ->where('archived_by_sender', true)
                            ->where('sender_deleted', false);
                    })->orWhere(function ($inner) use ($userId) {
                        $inner->where('recipient_id', $userId)
                            ->where('archived_by_recipient', true)
                            ->where('recipient_deleted', false);
                    });
                })
                ->pluck('conversation_id')
                ->unique();
                
                if ($archivedConversationIds->isNotEmpty()) {
                    $query->whereNotIn('id', $archivedConversationIds);
                }
            }
        }

        // Get conversations with latest message first
        $conversations = $query->orderBy('last_message_at', 'desc')
            ->with(['userOne', 'userTwo', 'latestMessage'])
            ->paginate(20);

        // Count unread messages
        $inboxCount = PrivateMessage::where('recipient_id', $userId)
            ->where('recipient_deleted', false)
            ->where('is_read', false)
            ->count();

        // Count total conversations
        $totalCount = auth()->user()->activeConversations()->count();
        
        // Count archived messages
        $archivedCount = PrivateMessage::where(function ($q) use ($userId) {
            $q->where(function ($inner) use ($userId) {
                $inner->where('sender_id', $userId)
                    ->where('archived_by_sender', true)
                    ->where('sender_deleted', false);
            })->orWhere(function ($inner) use ($userId) {
                $inner->where('recipient_id', $userId)
                    ->where('archived_by_recipient', true)
                    ->where('recipient_deleted', false);
            });
        })->count();
        
        $deletedCount = PrivateMessage::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->where('sender_deleted', true)
                ->orWhere(function($q) use ($userId) {
                    $q->where('recipient_id', $userId)
                        ->where('recipient_deleted', true);
                });
        })->count();

        // Get recent contacts
        $recentContacts = $this->getRecentContacts();

        return view('messages.index', compact(
            'conversations', 'folder', 'inboxCount', 'totalCount', 
            'archivedCount', 'deletedCount', 'recentContacts'
        ));
    }

    /**
     * Start a conversation with a specific user
     */
    public function startConversationWith(User $user)
    {
        // Проверяем, можно ли писать этому пользователю
        if ($user->id === auth()->id()) {
            return redirect()->route('messages.index')
                ->with('error', __('main.cannot_message_self'));
        }

        // Проверяем, есть ли уже беседа с этим пользователем
        $senderId = auth()->id();
        $recipientId = $user->id;
        
        $conversation = Conversation::where(function($query) use ($senderId, $recipientId) {
            $query->where('user_one_id', $senderId)
                  ->where('user_two_id', $recipientId);
        })->orWhere(function($query) use ($senderId, $recipientId) {
            $query->where('user_one_id', $recipientId)
                  ->where('user_two_id', $senderId);
        })->first();
        
        // Если беседа уже существует, перенаправляем на неё
        if ($conversation) {
            return redirect()->route('messages.show', $conversation);
        }
        
        // Если беседы нет, создаём новую
        $conversation = Conversation::create([
            'user_one_id' => min($senderId, $recipientId),
            'user_two_id' => max($senderId, $recipientId),
            'last_message_at' => now(),
        ]);
        
        return redirect()->route('messages.show', $conversation);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id|not_in:'.auth()->id(),
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $senderId = auth()->id();
        $recipientId = $validated['recipient_id'];

        // Find or create conversation
        $conversation = Conversation::findOrCreateConversation($senderId, $recipientId);
        
        // Update last message timestamp
        $conversation->last_message_at = now();
        $conversation->save();

        // Create the message
        $message = PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'subject' => $validated['subject'],
            'content' => $validated['body'],
            'is_read' => false,
            'sender_deleted' => false,
            'recipient_deleted' => false,
        ]);

        // Redirect to the conversation
        return redirect()->route('messages.show', $conversation)
            ->with('success', __('main.message_sent'));
    }

    public function show(Conversation $message)
    {
        $conversation = $message; // For better readability
        $userId = auth()->id();
        
        // Check if user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        // Check if conversation is deleted for this user
        if ($conversation->isDeletedFor($userId)) {
            abort(404);
        }

        // Get the other user in the conversation
        $otherUser = $conversation->getOtherUser($userId);

        // Get messages in this conversation
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        $conversation->markAsRead($userId);

        return view('messages.show', compact('conversation', 'messages', 'otherUser'));
    }

    public function destroy(Conversation $message)
    {
        $conversation = $message; // For better readability
        $userId = auth()->id();

        // Check if user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        // Delete conversation for this user
        $conversation->deleteFor($userId);

        return redirect()->route('messages.index')
            ->with('success', __('main.conversation_deleted'));
    }

    public function markAllRead()
    {
        $userId = auth()->id();
        $count = 0;

        // Get all conversations for this user
        $conversations = auth()->user()->activeConversations()->get();
        
        // Mark all messages as read in each conversation
        foreach ($conversations as $conversation) {
            $count += $conversation->markAsRead($userId);
        }

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function markRead(Conversation $message)
    {
        $conversation = $message; // For better readability
        $userId = auth()->id();

        // Check if user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $conversation->markAsRead($userId);

        return response()->json(['success' => true]);
    }

    public function archive(Conversation $message)
    {
        $conversation = $message; // For better readability
        $userId = auth()->id();

        // Check if user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        // Archive conversation for this user
        $conversation->archiveFor($userId);

        return redirect()->route('messages.index')
            ->with('success', __('main.conversation_archived'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:read,delete,archive',
            'conversation_ids' => 'required|array',
            'conversation_ids.*' => 'exists:conversations,id',
        ]);

        $userId = auth()->id();
        $conversationIds = $request->conversation_ids;

        switch ($request->action) {
            case 'read':
                foreach ($conversationIds as $conversationId) {
                    $conversation = Conversation::find($conversationId);
                    if ($conversation && ($conversation->user_one_id === $userId || $conversation->user_two_id === $userId)) {
                        $conversation->markAsRead($userId);
                    }
                }
                break;
                
            case 'archive':
                foreach ($conversationIds as $conversationId) {
                    $conversation = Conversation::find($conversationId);
                    if ($conversation && ($conversation->user_one_id === $userId || $conversation->user_two_id === $userId)) {
                        $conversation->archiveFor($userId);
                    }
                }
                break;
                
            case 'delete':
                foreach ($conversationIds as $conversationId) {
                    $conversation = Conversation::find($conversationId);
                    if ($conversation && ($conversation->user_one_id === $userId || $conversation->user_two_id === $userId)) {
                        $conversation->deleteFor($userId);
                    }
                }
                break;
        }

        return response()->json(['success' => true]);
    }

    public function checkNew()
    {
        $userId = auth()->id();
        $unreadCount = PrivateMessage::where('recipient_id', $userId)
            ->where('recipient_deleted', false)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'has_new' => $unreadCount > 0,
        ]);
    }

    public function saveDraft(Request $request)
    {
        $request->validate([
            'recipient_id' => 'nullable|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
        ]);

        try {
            // Here you can implement logic to save drafts in the database
            // For now, we'll just return a success response
            
            return response()->json([
                'success' => true,
                'message' => 'Черновик сохранен',
                'timestamp' => now()->toISOString(),
                'draft_id' => 'draft_' . auth()->id() . '_' . time()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения черновика: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send a new message in an existing conversation
     */
    public function replyToConversation(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $userId = auth()->id();
        
        // Check if user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }
        
        // Determine recipient (the other user)
        $recipientId = $conversation->user_one_id === $userId 
            ? $conversation->user_two_id 
            : $conversation->user_one_id;
            
        // Get the latest message for subject
        $latestMessage = $conversation->latestMessage;
        $subject = $latestMessage ? 'Re: ' . $latestMessage->subject : 'New message';
        
        // Update last message timestamp
        $conversation->last_message_at = now();
        $conversation->save();
        
        // Create the message
        $message = PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'recipient_id' => $recipientId,
            'subject' => $subject,
            'content' => $validated['body'],
            'is_read' => false,
            'sender_deleted' => false,
            'recipient_deleted' => false,
        ]);
        
        return redirect()->route('messages.show', $conversation)
            ->with('success', __('main.message_sent'));
    }
    
    /**
     * Track message reading time
     */
    public function trackReadingTime(Conversation $message)
    {
        $conversation = $message;
        $userId = auth()->id();
        
        // Check if user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['success' => false], 403);
        }
        
        // Get reading time from request
        $data = json_decode(file_get_contents('php://input'), true);
        $readingTime = $data['reading_time'] ?? 0;
        
        // Here you could save the reading time to the database if needed
        // For now, we'll just log it
        \Log::info('Conversation reading tracked', [
            'conversation_id' => $conversation->id,
            'user_id' => $userId,
            'reading_time' => $readingTime
        ]);
        
        return response()->json(['success' => true]);
    }
    
    private function getRecentContacts($limit = 5)
    {
        $userId = auth()->id();
        
        // Get users from conversations ordered by last message date
        $userIds = Conversation::where(function($query) use ($userId) {
            $query->where('user_one_id', $userId)
                ->orWhere('user_two_id', $userId);
        })
        ->orderBy('last_message_at', 'desc')
        ->limit($limit * 2) // Get more to account for filtering
        ->get()
        ->map(function($conversation) use ($userId) {
            return $conversation->getOtherUser($userId)->id;
        })
        ->unique()
        ->take($limit)
        ->values();
        
        // Fetch user details
        return User::whereIn('id', $userIds)->get();
    }
}