<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Project;
use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityTest extends TestCase
{
	use refreshDatabase;

	/** @test */
	public function it_has_a_user()
	{
		$user = $this->signIn();

		$project = ProjectFactory::ownedBy($user)->create();//こうすることでowner_idを持ったプロジェクトを生成する。
		// $project = factory(Project::class)->create();		

		$this->assertEquals($user->id, $project->activity->first()->user->id);
		// $this->assertEquals(User::class, $project->activity->first()->user);
	}
}
