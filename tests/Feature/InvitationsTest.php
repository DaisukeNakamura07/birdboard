<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationsTest extends TestCase
{
	use RefreshDatabase;
	/** @test */
	function a_project_can_invite_a_user()
	{
		$project = ProjectFactory::create();

		$project->invite($newUser = factory(User::class)->create());

		$this->signIn($newUser);
		$this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'Foo task']);//action(controller@method, data)で、web.phpで指定したコントローラメソッドごとのインポイントurlを生成する。同時にパラメータも渡せる。

		$this->assertDatabaseHas('tasks', $task);
	}
}
