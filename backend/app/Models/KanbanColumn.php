<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanColumn extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'project_id',
        'name',
        'display_name',
        'wip_limit',
        'position',
        'is_active'
    ];

    protected $casts = [
        'wip_limit' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
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

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getTasksCountAttribute()
    {
        return Task::where('project_id', $this->project_id)
                   ->where('status', $this->name)
                   ->count();
    }

    public function getIsWipLimitReachedAttribute()
    {
        if (is_null($this->wip_limit)) return false;
        return $this->tasks_count >= $this->wip_limit;
    }
}