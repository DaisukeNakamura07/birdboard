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
        //     array_diff($this->old, $this->toArray()//toArray()で$project
        // ));
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges($description)
        ]);
        //create(compact('description')) means create(['description' => $description])
       //  Activity::create([
       //      'project_id' => $this->id,
       //      'description' => $type
       // ]);
    }

    protected function activityChanges($description)
    {
        if($description == 'updated') {
            return [
                'before' => array_except(array_diff($this->old, $this->getAttributes()), 'updated_at'), //array_diff(array1, array2) これでarray1,array2の中身を比較し、array1にだけ存在するものを返す。getAttributes()で$projectのattributesを配列で返す。
                'after' => array_except($this->getChanges(), 'updated_at')//afterには変更点だけ保存。getChanges()で変更点を返す。array_except(配列,配列の除外キー)で'updated_at'以外のデータを返す。
            ];   
        }
    }
}
