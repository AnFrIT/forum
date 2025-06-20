<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'description',
        'status',
        'reporter_id',
        'moderator_id',
        'moderator_notes',
        'resolved_at',
        'priority',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'priority' => 'string',
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'normal',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeByReporter($query, $reporterId)
    {
        return $query->where('reporter_id', $reporterId);
    }

    public function scopeByModerator($query, $moderatorId)
    {
        return $query->where('moderator_id', $moderatorId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->latest();
    }

    public function resolve($moderatorId, $notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'moderator_id' => $moderatorId,
            'moderator_notes' => $notes,
            'resolved_at' => now(),
        ]);

        // If it's a post/topic report, maybe take additional actions
        if ($this->reportable && method_exists($this->reportable, 'markAsReviewed')) {
            $this->reportable->markAsReviewed();
        }

        return $this;
    }

    public function reject($moderatorId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'moderator_id' => $moderatorId,
            'moderator_notes' => $notes,
            'resolved_at' => now(),
        ]);

        return $this;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isHighPriority()
    {
        return $this->priority === 'high';
    }

    public function setHighPriority()
    {
        $this->update(['priority' => 'high']);
        return $this;
    }

    public function setNormalPriority()
    {
        $this->update(['priority' => 'normal']);
        return $this;
    }

    public function getReportableUrl()
    {
        if (!$this->reportable) {
            return null;
        }

        if ($this->reportable instanceof Post) {
            return route('topics.show', [
                'topic' => $this->reportable->topic_id,
                'post' => $this->reportable->id
            ]) . '#post-' . $this->reportable->id;
        }

        if ($this->reportable instanceof Topic) {
            return route('topics.show', $this->reportable);
        }

        return null;
    }

    public function getReportableType()
    {
        if (!$this->reportable) {
            return null;
        }

        if ($this->reportable instanceof Post) {
            return 'post';
        }

        if ($this->reportable instanceof Topic) {
            return 'topic';
        }

        return null;
    }

    public function getReportableTitle()
    {
        if (!$this->reportable) {
            return null;
        }

        if ($this->reportable instanceof Post) {
            return "Post in: " . $this->reportable->topic->title;
        }

        if ($this->reportable instanceof Topic) {
            return $this->reportable->title;
        }

        return null;
    }
}
