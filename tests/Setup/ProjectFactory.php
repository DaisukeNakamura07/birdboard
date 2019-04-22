<?php

namespace Tests\Setup;

use App\Project;
use App\Task;
use App\User;

class ProjectFactory
{
	protected $tasksCount = 0;
	protected $user;

	public function withTasks($count)
	{
		$this->tasksCount = $count;
		return $this;
	}

	public function ownedBy($user)
	{
		$this->user = $user;
		return $this;
	}


	public function create()//Projectのダミーを生成する時、同時にTaskのダミーも生成。owner_idも同時に付与。
	{
		$project = factory(Project::class)->create([
			'owner_id' => $this->user ?? factory(User::class)
		]);		

		factory(Task::class, $this->tasksCount)->create([
			'project_id' => $project->id
		]);

		return $project;
	}
}