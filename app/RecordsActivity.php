<?php

namespace App;

trait RecordsActivity
{
    public $oldAttributes = [];

    public static function bootRecordsActivity()
    {
    	
    	static::updating(function($model){
	        $model->oldAttributes = $model->getOriginal();
    	});

    	if(isset(static::$recordableEvents)){
    		$recordableEvents = static::$recordableEvents;
    	}else{ 
    		$recordableEvents = ['created', 'updated', 'deleted'];
   		}

    	foreach ($recordableEvents as $event){
    		static::$event(function($model) use($event){
    			if(class_basename($model) !== 'Project'){
    				$event = "{$event}_" . strtolower(class_basename($model)); 
    			}

				$model->recordActivity($event);
    		});
    	}
    }

	public function recordActivity($description)
    {
        // var_dump(
        //     array_diff($this->oldAttributes, $this->toArray()//toArray()で$project
        // ));
        $this->activity()->create([
            'description' => $description,
            'changes' => $this->activityChanges(),
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id//Project.phpのクラス定義の中で書いているうので、この$thisはProjectのインスタンスだから、project_idは自動的に生成されるから、本来は必要ない。ただしこの関数はtraitとして抽出しProjectとTaskの両方で参照させたいので、Taskが参照した場合にも使えるように、class_bassname()がProjectでない場合（Taskから参照した場合）にも、actibityのproject_idnにProjectインスタンスのidが生成されるようにしている。class_basenameは引数で指定したクラスから名前空間を除いたクラス名だけを返す。
        ]);
        //create(compact('description')) means create(['description' => $description])
       //  Activity::create([
       //      'project_id' => $this->id,
       //      'description' => $type
       // ]);
    }

	public function activity()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

	protected function activityChanges()
    {
        if($this->wasChanged()){
            return [
                'before' => array_except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'), //array_diff(array1, array2) これでarray1,array2の中身を比較し、array1にだけ存在するものを返す。getAttributes()で$projectのattributesを配列で返す。
                'after' => array_except($this->getChanges(), 'updated_at')//afterには変更点だけ保存。getChanges()で変更点を返す。array_except(配列,配列の除外キー)で'updated_at'以外のデータを返す。
            ];   
        }
    }
}

