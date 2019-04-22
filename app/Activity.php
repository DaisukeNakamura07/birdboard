<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
	protected $guarded = [];
	protected $casts = [
		'changes' => 'array'//activities_tableのchangesカラムはtextだが、Project::recordActivity()で生成するchangesのデータはarrayだから、そのままではArray to String conversionというエラーになる。$cast変数に['変換対象カラム' => 'データ型']のように変換内容を保存して、ふさわしいデータ型に変換する。
	];

	public function subject()//ActivityはProjectとTaskに対してbelongsToの関係。つまりbelongsTo先は複数のsubject_typeに変化(morph)する。
	{
		return $this->morphTo();
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
