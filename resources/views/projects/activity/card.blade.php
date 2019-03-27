<div class ="card mt-3">
	<ul class="text-xs list-reset">
		@foreach($project->activity as $activity)
			<li class="{{ $loop->last ? '' : 'mb-1' }}"><!-- foreachループの中で繰り返す<li>タグが$loopに入る.-->
					@include("projects.activity.{$activity->description}")
					<span class="text-grey">{{ $activity->created_at->diffForHumans(null, true) }}</span><!-- diffForHumans()で読みやすく整形。引数にnull,trueを入れるとagoが消える。詳しくはCarbon.phpに-->
			</li>
		@endforeach
	</ul>
</div>