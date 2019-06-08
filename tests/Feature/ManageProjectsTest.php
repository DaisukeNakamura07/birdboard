<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Project;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{
	use WithFaker, RefreshDatabase;


	/** @test **/
	function guests_cannot_manage_projects()
	{
		// $this->withoutExceptionHandling();
		$project = factory('App\Project')->create();
		$this->get('/projects')->assertRedirect('login');
		$this->get('/projects/create')->assertRedirect('login');
		$this->get($project->path() . '/edit')->assertRedirect('login');
		$this->get($project->path())->assertRedirect('login');
		$this->post('/projects', $project->toArray())->assertRedirect('login');
	}	

	/** @test */
	function unauthorized_users_cannot_delete_projects()
	{
		$project = ProjectFactory::create();

		$this->delete($project->path())
			->assertRedirect('/login');

		$user = $this->signIn();
		$this->delete($project->path())->assertStatus(403);

		$project->invite($user);
		$this->actingAs($user)->delete($project->path())->assertStatus(403);
	}

	/** @test **/
	function a_user_can_create_a_project()
	{
		// $this->withoutExceptionHandling();
		$this->signIn();

		$this->get('/projects/create')->assertStatus(200); //HTTP status code : Success
		
		// $attributes = [
		// 	'title' => $this->faker->sentence,
		// 	'description' => $this->faker->sentence,
		// 	'notes' => 'General notes here.'
		// ];



		// $project = Project::where($attributes)->first();

		// $response->assertRedirect($project->path());

		// $this->assertDatabaseHas('projects', $attributes);
			
		$this->followingRedirects()//これがないと302エラーが返る。302はリダイレクトが起きた時に表示される。get('/projects/create')が成功したらassertRedirect()をする設定なので、これが必要。
			->post('/projects', $attributes = factory(Project::class)->raw())//rawメソッドを使うと、データベースにpersistしない。
			->assertSee($attributes['title'])
			->assertSee($attributes['description'])
			->assertSee($attributes['notes']);	

		// $this->get($project->path())
		// 	->assertSee($attributes['title'])
		// 	->assertSee($attributes['description'])
		// 	->assertSee($attributes['notes']);
	}

	/** @test */
	function a_user_can_see_all_projects_they_have_been_invited_to_on_their_dashboard()
	{
		$user = $this->signIn();

		$project = tap(ProjectFactory::create())->invite($user);//tapを使えば下の2行を連結して1行で記述できる。tap()では常に1st argに渡したものがreturnされるので、一時変数を書く必要がなく、そのままメソッドチェーンを繋げられる。。
		// $project = ProjectFactory::create();
		// $project->invite($user);

		// $project = ProjectFactory::create();

		// $project->invite($user);

		$this->get('/projects')->assertSee($project->title);
	}

	/** @test */
	function a_user_can_delete_a_project()
	{
		$this->withoutExceptionHandling();

		$project = ProjectFactory::create();

		$this->actingAs($project->owner)
			->delete($project->path())
			->assertRedirect('/projects');

		// $project->fresh();//これでもOK.	
		$this->assertDatabaseMissing('projects', $project->only('id'));
	}

	/** @test */
	function a_user_can_update_a_project()
	{
		// $this->signIn();

		// $this->withoutExceptionHandling();

		// $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

		$project = ProjectFactory::create();

		$this->actingAs($project->owner)
			->patch($project->path(), $attributes = ['title' => 'Changed', 'description' => 'Changed', 'notes' => 'Changed']) // phpでは変数の定義・代入さらに関数への引き渡しをこのように一度でできる。jsでは不可。
			->assertRedirect($project->path());

		$this->get($project->path() . '/edit')->assertOk();

		$this->assertDatabaseHas('projects', $attributes);
	}	

	/** @test */
	function a_user_can_update_a_projects_general_notes()
	{
		$project = ProjectFactory::create();

		$this->actingAs($project->owner)
			->patch($project->path(), $attributes = ['notes' => 'Changed']) // phpでは変数の定義・代入さらに関数への引き渡しをこのように一度でできる。jsでは不可。
			->assertRedirect($project->path());


		$this->assertDatabaseHas('projects', $attributes);
	}

	/** @test */
	function a_user_can_view_their_project()
	{
		// $this->signIn();

		// $this->withoutExceptionHandling();

		// $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

		$project = ProjectFactory::create();

		$this->actingAs($project->owner)
			->get($project->path())
			->assertSee($project->title)
			->assertSee($project->description);
	}	

	/** @test */
	function an_authenticated_user_cannot_view_the_projects_of_others()
	{
		$this->signIn();

		// $this->withoutExceptionHandling();

		$project = factory('App\Project')->create();
		$this->get($project->path())->assertStatus(403);

	}

	/** @test */
	function an_authenticated_user_cannot_view_the_update_of_others()
	{
		$this->signIn();

		$project = factory('App\Project')->create();

		$this->patch($project->path())->assertStatus(403);

	}

	/** @test **/
	function a_project_requires_a_title()
		{
			$this->signIn();
			$attributes = factory('App\Project')->raw(['title' => '']);

			$this->post('/projects', $attributes)->assertSessionHasErrors('title');
		}	


	/** @test **/
	function a_project_requires_a_description()
		{
			$this->signIn();
			$attributes = factory('App\Project')->raw(['description' => '']);
			$this->post('/projects', $attributes)->assertSessionHasErrors('description');
		}	

}
