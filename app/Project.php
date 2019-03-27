<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];
    public $old = [];

    public function path()
    {
    	return "/projects/{$this->id}";
    }

    public function owner()
    {
    	return $this->belongsTo(User::class);
    }

    public function tasks()
    {
    	return $this->hasMany(Task::class);
    }

    public function addTask($body)
    {
    	return $this->tasks()->create(compact('body'));
    }

    public function activity()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function recordActivity($description)
    {
        // var_dump(
        //     array_diff($this->old, $this->toArray()
        // ));
        $this->activity()->create([
            'description' => $description,
            'changes' => [
                'before' => array_diff($this->old, $this->getAttributes()),//array_diff(array1, array2) これでarray1, array2の中身を比較し、array1にだけ存在するものを返す。
                'after' => array_diff($this->getAttributes(), $this->old)//afterには変更点だけ保存。
            ]
        ]); //create(compact('description')) means create(['description' => $description])
       //  Activity::create([
       //      'project_id' => $this->id,
       //      'description' => $type
       // ]);
    }
}
