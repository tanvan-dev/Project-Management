<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'owner_id',
        'settings'
    ];

    protected $casts = [
        'settings' => 'json',
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
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'workspace_members', 'workspace_id', 'user_id')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public function workspaceMembers()
    {
        return $this->hasMany(WorkspaceMember::class, 'workspace_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'workspace_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'workspace_id');
    }
}