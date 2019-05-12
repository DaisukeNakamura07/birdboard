<?php

namespace App\Http\Controllers;

use App\Project;

use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index()
    {
		$projects = auth()->user()->accessibleProjects();
        
		return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $this->authorize('update', $project);
    	// if (auth()->user()->isNot($project->owner)) {
    	// 	abort(403);
    	// }
    	
    	return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store()
    {
		//validate validateRequest()として関数化
		// $attributes = request()->validate([
		// 	'title' => 'required',
		// 	'description' => 'required',
  //           'notes' => 'min:3'
		// ]);

		// $attributes['owner_id'] = auth()->id();
		$project = auth()->user()->projects()->create($this->validateRequest());
		//ridirect
		return redirect($project->path());
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Project $project)
    {
        $this->authorize('update', $project);
        // if (auth()->user()->isNot($project->owner)) {
        //     abort(403);
        // }

        // $attributes = request()->validate([
        //     'title' => 'required',
        //     'description' => 'required',
        //     'notes' => 'min:3'
        // ]);

        $project->update($this->validateRequest());  

        return redirect($project->path());
    }

    public function destroy(Project $project)
    {
        $this->authorize('update', $project);
        $project->delete();
        return redirect('/projects');
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes | required',
            'description' => 'sometimes | required',
            'notes' => 'nullable'
        ]);
    }
}
