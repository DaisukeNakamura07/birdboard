<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'owner_id')->latest('updated_at');
    }

    public function accessibleProjects()
    {
        return Project::where('owner_id', $this->id)
            ->orWhereHas('members', function($query){//クエリを関数の形で指定したい場合はwhereHasやorWhereHasが使える。
                $query->where('user_id', $this->id);//project_membersテーブルのuser_idが$this->idと一致するものを検索。
            })
            ->get();
    //     $projectsCreated = $this->projects;

    //     $ids = \DB::table('project_members')->where('user_id', $this->id)->pluck('project_id');//user_idが$this_idと一致するレコードのproject_idを全て取得。
    //     $projectsSharedWith = Project::find($ids);

    //     return $projectsCreated->merge($projectsSharedWith);//配列を融合するにはmerge()を使う。
    }
}
