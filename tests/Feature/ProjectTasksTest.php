<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function guests_cannot_add_tasks_to_projects()
	{
		$project = factory('App\Project')->create();

		$this->post($project->path() . '/tasks')->assertRedirect('login');	
	}

	/** @test */
	function only_the_owner_of_a_project_may_add_tasks()
	{
		$this->signIn();

		$project = factory('App\Project')->create();

		$this->post($project->path() . '/tasks', ['body' => 'Test task'])
			->assertStatus(403);

		$this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
	}

	/** @test */
	function only_the_owner_of_a_project_may_update_a_task()
	{
		$this->signIn();

		$project = ProjectFactory::withTasks(1)->create();

		$this->patch($project->tasks[0]->path(), ['body' => 'changed'])
			->assertStatus(403);

		$this->assertDatabaseMissing('tasks', ['body' => 'changed']);
	}

	/** @test */
	public function a_project_can_have_tasks()
	{
		// $this->signIn();

		// $project = auth()->user()->projects()->create(
		// 	factory(Project::class)->raw()
		// );

		$project = ProjectFactory::create();

		$this->actingAs($project->owner)
			->post($project->path() . '/tasks', ['body' => 'Test task']); // post(inpoint path , [array being submitted])

		$this->get($project->path())
			->assertSee('Test task');
	}

	/** @test */
	function a_task_can_be_updated()
	{
		// $this->withoutExceptionHandling();

		$project = ProjectFactory::withTasks(1)->create();

		// $this->signIn();

		// $project = auth()->user()->projects()->create(
		// 	factory(Project::class)->raw()
		// );

		// $task = $project->addTask('test task');

		$this->actingAs($project->owner)
			->patch($project->tasks[0]->path(), [
			'body' => 'changed',
		]);

		$this->assertDatabaseHas('tasks', [
			'body' => 'changed',
		]);
	}


	function a_task_can_be_completed()
	{
		$project = ProjectFactory::withTasks(1)->create();

		$this->actingAs($project->owner)
			->patch($project->tasks[0]->path(), [
			'body' => 'changed',
			'completed' => true
		]);

		$this->assertDatabaseHas('tasks', [
			'body' => 'changed',
			'completed' => true
		]);
	}

	function a_task_can_be_marked_as_incomplete()
	{
		$this->withoutExceptionHandling();
		
		$project = ProjectFactory::withTasks(1)->create();
		
		$this->actingAs($project->owner)
			->patch($project->tasks[0]->path(), [
			'body' => 'changed',
			'completed' => true
		]);
		
		$this->patch($project->tasks[0]->path(), [
			'body' => 'changed',
			'completed' => false
		]);

		$this->assertDatabaseHas('tasks', [
			'body' => 'changed',
			'completed' => false
		]);
	}

	/** @test */
	public function a_task_requires_a_body()
	{
		// $this->signIn();

		// $project = auth()->user()->projects()->create(
		// 	factory(Project::class)->raw()
		// );

		$project = ProjectFactory::create();
		$attributes = factory('App\Task')->raw(['body' => '']);

		$this->actingAs($project->owner)
			->post($project->path() . '/tasks', $attributes)
			->assertSessionHasErrors('body');
	}

}
