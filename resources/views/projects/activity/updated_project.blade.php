{{-- {{ dd($activity->changes) }} --}}
{{-- これをやると次のようにデバッグ結果が出力される。projectを変更した時の変更点が確認できる。 --}}
{{-- array:2 [▼
  "before" => array:1 [▼
    "notes" => "Notes here."
  ]
  "after" => array:1 [▼
    "notes" => "Notes here here."
  ]
]
 --}}

{{-- 変更点が1つだけの場合、それを表示する。1以上の場合は、単にYou updated the projectと表示 --}}
@if(count($activity->changes['after']) == 1)
	{{ $activity->user->name }} updated the  {{ key($activity->changes['after']) }} of the project {{-- key(配列)は配列（連想配列）のkey文字列を返す。 --}}
@else
	{{ $activity->user->name }} updated the project
@endif