<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'subject',
        'content',
        'is_read',
        'sent_at',
        'read_at',
        'sender_deleted',
        'recipient_deleted',
        'archived_by_sender',
        'archived_by_recipient',
        'is_archived',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sender_deleted' => 'boolean',
        'recipient_deleted' => 'boolean',
        'archived_by_sender' => 'boolean',
        'archived_by_recipient' => 'boolean',
        'is_archived' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeInbox($query, $userId)
    {
        return $query->where('recipient_id', $userId)
            ->where('recipient_deleted', false);
    }

    public function scopeSent($query, $userId)
    {
        return $query->where('sender_id', $userId)
            ->where('sender_deleted', false);
    }

    public function scopeArchived($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($inner) use ($userId) {
                $inner->where('sender_id', $userId)
                    ->where('archived_by_sender', true)
                    ->where('sender_deleted', false);
            })->orWhere(function ($inner) use ($userId) {
                $inner->where('recipient_id', $userId)
                    ->where('archived_by_recipient', true)
                    ->where('recipient_deleted', false);
            });
        });
    }

    public function scopeDeleted($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($inner) use ($userId) {
                $inner->where('sender_id', $userId)
                    ->where('sender_deleted', true);
            })->orWhere(function ($inner) use ($userId) {
                $inner->where('recipient_id', $userId)
                    ->where('recipient_deleted', true);
            });
        });
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function getIsArchivedAttribute()
    {
        $userId = auth()->id();
        if ($userId === $this->sender_id) {
            return $this->archived_by_sender;
        } elseif ($userId === $this->recipient_id) {
            return $this->archived_by_recipient;
        }
        return false;
    }

    public function markAsRead()
    {
        if (!$this->is_read && auth()->id() === $this->recipient_id) {
            $this->is_read = true;
            $this->read_at = now();
            $this->save();
        }

        return $this;
    }

    public function deleteForUser($userId)
    {
        if ($userId === $this->sender_id) {
            $this->sender_deleted = true;
        } elseif ($userId === $this->recipient_id) {
            $this->recipient_deleted = true;
        }

        $this->save();
        return $this;
    }

    public function restoreForUser($userId)
    {
        if ($userId === $this->sender_id) {
            $this->sender_deleted = false;
        } elseif ($userId === $this->recipient_id) {
            $this->recipient_deleted = false;
        }

        $this->save();
        return $this;
    }

    public function isCompletelyDeleted()
    {
        return $this->sender_deleted && $this->recipient_deleted;
    }
}
