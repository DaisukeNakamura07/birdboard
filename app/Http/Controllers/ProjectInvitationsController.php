<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Http\Requests\ProjectInvitationRequest;
use App\Project;
use App\User;

class ProjectInvitationsController extends Controller
{
    public function store(Project $project, ProjectInvitationRequest $request)
    {
  //   	$this->authorize('update', $project);

		// request()->validate([
		// 	'email' => ['required', 'exists:users,email']//usersテーブルのemailカラムに、左辺のemailが値として存在するかどうかチェック。
		// ], [
		// 	'email.exists' => 'The user you are inviting must have a birdboard account.' //custom error message.existsという検証ルールでエラーが起きた際のメッセージを指定。
		// ]);

    	$user = User::whereEmail(request('email'))->first();//Userテーブルに登録されているユーザーしかinviteできない。

    	$project->invite($user);

    	return redirect($project->path());
    }
}
