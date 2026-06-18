<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'workspace_id',
        'name',
        'description',
        'methodology',
        'owner_id',
        'settings',
        'is_archived'
    ];

    protected $casts = [
        'settings' => 'json',
        'is_archived' => 'boolean',
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
    }

    // Relationships
    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class, 'project_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function kanbanColumns()
    {
        return $this->hasMany(KanbanColumn::class, 'project_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'project_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeScrum($query)
    {
        return $query->where('methodology', 'scrum');
    }

    public function scopeKanban($query)
    {
        return $query->where('methodology', 'kanban');
    }
}