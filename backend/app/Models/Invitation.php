<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'workspace_id',
        'project_id',
        'email',
        'invite_token',
        'invited_by',
        'role',
        'expires_at',
        'accepted_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) str()->uuid();
            }
            if (empty($model->invite_token)) {
                $model->invite_token = (string) str()->uuid();
            }
        });
    }

    // Relationships
    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return now()->gt($this->expires_at);
    }

    public function getIsAcceptedAttribute()
    {
        return !is_null($this->accepted_at);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->whereNull('accepted_at')
                     ->where('expires_at', '>', now());
    }
}