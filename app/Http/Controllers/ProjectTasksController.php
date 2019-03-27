<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Task;

class ProjectTasksController extends Controller
{
    public function store(Project $project)
    {
        $this->authorize('update', $project);
    	// if (auth()->user()->isNot($project->owner)) {
    	// 	abort(403);
    	// }

    	request()->validate(['body' => 'required']);
    	
    	$project->addTask(request('body'));

    	return redirect($project->path());
    }

    public function update(Project $project, Task $task)
    {
        $this->authorize('update', $task->project);
        // if (auth()->user()->isNot($project->owner)) {
        //     abort(403);
        // }

        $task->update(request()->validate(['body' => 'required']));

        // if(request('completed')){
        //     $task->complete();
        // } else {
        //     $task->incomplete();
        // }
        
        // $method = request('completed') ? 'complete' : 'incomplete';//PATCHリクエストに'completed'フィールドがあれば、complete,なければincompleteを返す。
        // $task->$method();//変数名に()をつけて関数を呼び出す。
        request('completed') ? $task->complete() : $task->incomplete();//$methodを使わない書き方。

        // $task->update([
        //     'body' => request('body'),
        //     'completed' => request()->has('completed')
        // ]);

        // $task->complete();

        return redirect($project->path());
    }
}
