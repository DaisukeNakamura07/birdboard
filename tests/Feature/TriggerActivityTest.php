<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Task;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TriggerActivityTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function creating_a_project()
	{
		$project = ProjectFactory::create();

		$this->assertCount(1, $project->activity);

		tap($project->activity->last(), function($activity){
			$this->assertEquals('created', $activity->description);
			$this->assertNull($activity->changes);
		});
	}

	/** @test */
	function updating_a_project()
	{
		$project = ProjectFactory::create();
		$originalTitle = $project->title;

		$project->update(['title' => 'Changed']);
		$this->assertCount(2, $project->activity);

		tap($project->activity->last(), function($activity) use ($originalTitle){
			$this->assertEquals('updated', $activity->description);

			$expected = [
				'before' => ['title' => $originalTitle],
				'after' => ['title' => 'Changed']
			];

			$this->assertEquals($expected, $activity->changes);
		});
	}

	/** @test */
	public function creating_a_new_task()
	{
		$project = ProjectFactory::create();

		$project->addTask('Some Task');

		$this->assertCount(2, $project->activity);

		tap($project->activity->last(), function($activity){//tap(モデルのインスタンス,クロージャ)。ここでは1つのactivityの複数のプロパティ(description,subject)に値をassertするために使用。
			$this->assertEquals('created_task', $activity->description);
			$this->assertInstanceOf(Task::class, $activity->subject);
			$this->assertEquals('Some Task', $activity->subject->body);
		});
	}

	public function completing_a_task()
	{
		$project = ProjectFactory::withTasks(1)->create();

		$this->actingAs($project->owner)
			->patch($project->tasks[0]->path(), [
				'body' => 'foobar',
				'completed' => true
		]);

		// $project = addTask('Some Task');
		
		$this->assertCount(3, $project->activity);
		// $this->assertEquals('created_task', $project->activity->last()->description);
		tap($project->activity->last(), function($activity){//tap(モデルのインスタンス,クロージャ)。ここでは1つのactivityの複数のプロパティ(description,subject)に値をassertするために使用。
			$this->assertEquals('completed_task', $activity->description);
			$this->assertInstanceOf(Task::class, $activity->subject);
		});
	}

	public function incompleting_a_task()
	{
		$project = ProjectFactory::withTasks(1)->create();

		$this->actingAs($project->owner)
			->patch($project->tasks[0]->path(), [
				'body' => 'foobar',
				'completed' => true
		]);

		$this->assertCount(3, $project->activity);

		$this->actingAs($project->owner)
			->patch($project->tasks[0]->path(), [
				'body' => 'foobar',
				'completed' => false
		]);

		// $project = $project->fresh();//$project->activityは上で一度生成しているので、もう一度生成するにはfresh()をつける。
		$project->refresh();//これでもいい。

		$this->assertCount(4, $project->fresh()->activity);
		$this->assertEquals('incompleted_task', $project->fresh()->activity->last()->description);
	}

	public function deleting_a_task()
	{
		$project = ProjectFactory::withTasks(1)->create();
		$project->tasks[0]->delete();//Project hasMany Task::classだから、$project->tasks[0]のようにtasksはarrayとして書く。
		$this->assertCount(3, $project->fresh()->activity);
	}
}
