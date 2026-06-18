<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'task_id',
        'changed_by',
        'field_name',
        'old_value',
        'new_value',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) str()->uuid();
            }
            if (empty($model->changed_at)) {
                $model->changed_at = now();
            }
        });
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}