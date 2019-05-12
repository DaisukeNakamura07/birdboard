<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
	use RefreshDatabase;
	/** @test */
	public function a_user_has_projects()
	{
		$user = factory('App\User')->create();

		$this->assertInstanceOf(Collection::class, $user->projects);//$user->projects が Collectionクラスのインスタンスであるというassertion。
	}	

	/** @test */
	function a_user_has_accessible_projects()
	{
		$john = $this->signIn();

		$project = ProjectFactory::ownedBy($john)->create();

		$this->assertCount(1, $john->accessibleProjects());

		$sally = factory(User::class)->create();
		$nick = factory(User::class)->create();

		$project = tap(ProjectFactory::ownedBy($sally)->create())->invite($nick);
		
		$this->assertCount(1, $john->accessibleProjects());

		$project->invite($john);
		$this->assertCount(2, $john->accessibleProjects());
	}
}
