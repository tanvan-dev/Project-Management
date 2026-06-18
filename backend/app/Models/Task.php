<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'project_id',
        'sprint_id',
        'parent_task_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'position',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) str()->uuid();
            }
        });

        // Auto update task history
        static::updating(function ($model) {
            $original = $model->getOriginal();
            foreach ($model->getChanges() as $field => $newValue) {
                if ($field !== 'updated_at') {
                    /** @var \Illuminate\Contracts\Auth\Authenticatable|null $user */
                    $user = Auth::user();
                    TaskHistory::create([
                        'task_id' => $model->id,
                        'changed_by' => $user ? $user->getAuthIdentifier() : $model->created_by,
                        'field_name' => $field,
                        'old_value' => $original[$field] ?? null,
                        'new_value' => $newValue,
                    ]);
                }
            }
        });
    }

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class, 'sprint_id');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees', 'task_id', 'user_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function checklists()
    {
        return $this->hasMany(TaskChecklist::class, 'task_id');
    }

    public function subtaskItems()
    {
        return $this->hasMany(TaskSubtask::class, 'task_id');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class, 'task_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id');
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class, 'task_id');
    }

    // Scopes
    public function scopeTodo($query)
    {
        return $query->where('status', 'todo');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'done');
    }

    // Accessors
    public function getChecklistProgressAttribute()
    {
        $total = $this->checklists()->count();
        if ($total === 0) return 0;

        $completed = $this->checklists()->where('is_checked', true)->count();
        return round(($completed / $total) * 100, 2);
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'done';
    }

    public function getSubtasksProgressAttribute()
    {
        $total = $this->subtaskItems()->count();
        if ($total === 0) return 0;

        $completed = $this->subtaskItems()->where('is_completed', true)->count();
        return round(($completed / $total) * 100, 2);
    }
}
