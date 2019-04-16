<?php

namespace App;

trait RecordsActivity
{
    public $oldAttributes = [];

    public static function bootRecordsActivity()//obserberに書いたイベントフックをbootメソッドで置き換える時は、こういう書き方をする。public static function bootTraitname()
    {
    	foreach (self::recordableEvents() as $event){//自身のメソッドを使用する時の書き方。self::method()
    		static::$event(function($model) use($event){//モデルが持つstatic変数やstaticメソッドを使用する時の書き方。$eventはcreated,update,deletedなどのイベントフックが入るので$event()という書き方をすれば$eventが展開された時にイベントフックメソッドが発動する。イベントフックの構文は下のstatic::updatingと同じ。フックの中でactivityDescription()に$eventを渡すのでuse($event)とする。 
				$model->recordActivity($model->activityDescription($event));
    		});

			if($event === 'updated'){//updatedイベントをレコードするのはprojectだけ。taskはレコードしない。
		    	static::updating(function($model){//bootの中でのイベントフックの書き方。
			        $model->oldAttributes = $model->getOriginal();
		    	});
	    	}
    	}
    }

    protected function activityDescription($description)
    {
		// if(class_basename($this) !== 'Project'){//最初、'created_task'のような書き方にするのは、Taskだけで、Projectは'created'とかだけだった。それだと一貫性がないので、統一した。
			return "{$description}_" . strtolower(class_basename($this));//created_taskのような文字列を返す。 
		// }   	

		// return $description;//最初、ProjectのactivityDescriptionは'created''updated''deleted'だけだった。それをTaskと同様に'created_project'のように'event_model'の形式に整えた。それに伴って、view/projects/activitiesのprojectに関するファイルの名前も修正した。
    }

    protected static function recordableEvents()
    {
    	if(isset(static::$recordableEvents)){//traitをuseするモデルクラスの中に、静的変数$recordableEventsが定義されている場合は、それを返す。Taskには定義されている。
    		return static::$recordableEvents;
    	} 
		return ['created', 'updated', 'deleted'];//デフォルトではこの3つを返す。
    }

	public function recordActivity($description)
    {
        // var_dump(
        //     array_diff($this->oldAttributes, $this->toArray()//toArray()で$project
        // ));
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id//Project.phpのクラス定義の中で書いている場合、この$thisはProjectのインスタンスだから、project_idは自動的に生成されるから、本来は必要ない。ただしこの関数はtraitとして抽出しProjectとTaskの両方で参照させたいので、Taskが参照した場合にも使えるように、class_bassname()がProjectでない場合（Taskから参照した場合）にも、activityのproject_idにProjectインスタンスのidが生成されるようにしている。class_basenameは引数で指定したクラスから名前空間を除いたクラス名だけを返す。
        ]);
        //create(compact('description')) means create(['description' => $description])
       //  Activity::create([
       //      'project_id' => $this->id,
       //      'description' => $type
       // ]);
    }

	public function activity()
    {
    	if (get_class($this) === Project::class){//Project は hasMany Activityなので、この条件が必要。get_class($this)の$thisはクラスインスタンスとしてのオブジェクト。これで$thisのクラス名を返す。Project::classは単にモデルファイルの中のクラス全体そのものを指す。このようにクラス名を返すだけの式と同じように使えるようだ。(Model::class)->get()などとしなければインスタンスは生成しない。
	        return $this->hasMany(Activity::class)->latest();
    	}
	        return $this->morphMany(Activity::class, 'subject')->latest();
    }

	protected function activityChanges()//モデルインスタンスのattributesに変更が起きた場合、変更が加えられたattributeだけ、前の状態をbefore,変更後の状態をafterに保存し、連想配列の形で返す。
    {
        if($this->wasChanged()){//この$thisは、traitをuseするモデルのインスタンス。traitファイル全体が各モデルに取り込まれると考えてよい。
            return [
                'before' => array_except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'), //array_diff(array1, array2) これでarray1,array2の中身を比較し、array1にだけ存在するものを返す。getAttributes()で$projectのattributesを配列で返す。
                'after' => array_except($this->getChanges(), 'updated_at')//afterには変更点だけ保存。getChanges()で変更点を返す。array_except(配列,配列の除外キー)で'updated_at'以外のデータを返す。
            ];   
        }
    }
}

