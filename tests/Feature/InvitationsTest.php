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
	function non_owners_may_not_invite_users()
	{
		// $this->withoutExceptionHandling();
		$this->actingAs(factory(User::class)->create())
			->post(ProjectFactory::create()->path() . '/invitations')
			->assertStatus(403);
	}

	/** @test*/
	function a_project_owner_can_invite_a_user()
	{
		$project = ProjectFactory::create();

		$userToInvite = factory(User::class)->create();

		$this->actingAs($project->owner)
			->post($project->path() . '/invitations', [
				'email' => $userToInvite->email	
		])
		->assertRedirect($project->path());

		$this->assertTrue($project->members->contains($userToInvite));
	}

	/** @test */
	function the_email_address_must_be_associated_with_a_valid_birdboard_account()
	{
		$project = ProjectFactory::create();

		$this->actingAs($project->owner)->post($project->path() . '/invitations', [
			'email' => 'notauser@example.com'	
		])
		->assertSessionHasErrors([
			'email' => 'The user you are inviting must have a birdboard account.'
		]);
	}

	/** @test */
	function invited_users_may_update_project_details()
	{
		$project = ProjectFactory::create();

		$project->invite($newUser = factory(User::class)->create());

		$this->signIn($newUser);
		$this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'Foo task']);//action(controller@method, data)で、web.phpで指定したコントローラメソッドごとのインポイントurlを生成する。同時にパラメータも渡せる。

		$this->assertDatabaseHas('tasks', $task);
	}
}
