<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use RecordsActivity;//traitはクラス定義の中にuse宣言を書く。
    protected $guarded = [];

    // public $old = [];//traitへ抽出

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

    public function activity() //traitへ抽出
    {
        return $this->hasMany(Activity::class)->latest();
    }

    // public function recordActivity($description)
    // {
    //     // var_dump(
    //     //     array_diff($this->old, $this->toArray()//toArray()で$project
    //     // ));
    //     $this->activity()->create([
    //         'description' => $description,
    //         'changes' => $this->activityChanges(),
    //         'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id//Project.phpのクラス定義の中で書いているうので、この$thisはProjectのインスタンスだから、project_idは自動的に生成されるから、本来は必要ない。ただしこの関数はtraitとして抽出しProjectとTaskの両方で参照させたいので、Taskが参照した場合にも使えるように、class_bassname()がProjectでない場合（Taskから参照した場合）にも、actibityのproject_idnにProjectインスタンスのidが生成されるようにしている。class_basenameは引数で指定したクラスから名前空間を除いたクラス名だけを返す。
    //     ]);
    //     //create(compact('description')) means create(['description' => $description])
    //    //  Activity::create([
    //    //      'project_id' => $this->id,
    //    //      'description' => $type
    //    // ]);
    // }

}
