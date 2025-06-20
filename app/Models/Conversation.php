<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
        'user_one_deleted',
        'user_two_deleted',
    ];

    protected $casts = [
        'user_one_deleted' => 'boolean',
        'user_two_deleted' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * Get all messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(PrivateMessage::class)->orderBy('created_at');
    }

    /**
     * Get the most recent message in this conversation
     */
    public function latestMessage()
    {
        return $this->hasOne(PrivateMessage::class)->latest();
    }

    /**
     * Get user one
     */
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    /**
     * Get user two
     */
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Get the other user in conversation relative to given user
     */
    public function getOtherUser($userId)
    {
        if ($this->user_one_id == $userId) {
            return $this->userTwo;
        }
        return $this->userOne;
    }

    /**
     * Check if conversation is deleted for a user
     */
    public function isDeletedFor($userId)
    {
        if ($this->user_one_id == $userId) {
            return $this->user_one_deleted;
        } elseif ($this->user_two_id == $userId) {
            return $this->user_two_deleted;
        }
        return false;
    }

    /**
     * Delete conversation for a user
     */
    public function deleteFor($userId)
    {
        if ($this->user_one_id == $userId) {
            $this->user_one_deleted = true;
        } elseif ($this->user_two_id == $userId) {
            $this->user_two_deleted = true;
        }
        $this->save();
        return $this;
    }

    /**
     * Restore conversation for a user
     */
    public function restoreFor($userId)
    {
        if ($this->user_one_id == $userId) {
            $this->user_one_deleted = false;
        } elseif ($this->user_two_id == $userId) {
            $this->user_two_deleted = false;
        }
        $this->save();
        return $this;
    }

    /**
     * Archive conversation for a user
     */
    public function archiveFor($userId)
    {
        // Find all messages in this conversation and mark them as archived
        $this->messages()
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('recipient_id', $userId);
            })
            ->update([
                'archived_by_sender' => DB::raw("CASE WHEN sender_id = {$userId} THEN true ELSE archived_by_sender END"),
                'archived_by_recipient' => DB::raw("CASE WHEN recipient_id = {$userId} THEN true ELSE archived_by_recipient END"),
                'is_archived' => true
            ]);
        
        return $this;
    }

    /**
     * Unarchive conversation for a user
     */
    public function unarchiveFor($userId)
    {
        // Find all messages in this conversation and mark them as unarchived
        $this->messages()
            ->where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('recipient_id', $userId);
            })
            ->update([
                'archived_by_sender' => DB::raw("CASE WHEN sender_id = {$userId} THEN false ELSE archived_by_sender END"),
                'archived_by_recipient' => DB::raw("CASE WHEN recipient_id = {$userId} THEN false ELSE archived_by_recipient END"),
                'is_archived' => false
            ]);
        
        return $this;
    }

    /**
     * Find or create a conversation between two users
     */
    public static function findOrCreateConversation($userOneId, $userTwoId)
    {
        // Ensure consistent ordering of user IDs
        if ($userOneId > $userTwoId) {
            list($userOneId, $userTwoId) = [$userTwoId, $userOneId];
        }

        $conversation = self::where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId)
            ->first();

        if (!$conversation) {
            $conversation = self::create([
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
                'last_message_at' => now(),
            ]);
        }

        return $conversation;
    }
    
    /**
     * Get unread messages count for a user
     */
    public function unreadCount($userId)
    {
        return $this->messages()
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Mark all messages as read for a user
     */
    public function markAsRead($userId)
    {
        return $this->messages()
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
} 