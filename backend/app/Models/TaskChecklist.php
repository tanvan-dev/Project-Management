<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'task_id',
        'item',
        'is_checked',
        'position'
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'position' => 'integer',
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

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}