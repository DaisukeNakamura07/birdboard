@extends('layouts.app')

@section('content')
	<div class="lg:w-1/2 lg:mx-auto bg-white py-12 px-16 roundeed shadow">
		<h1 class="text 2xl font-normal mb-10 text-center">Create Your Project</h1>
		<form 
			method="POST"
			action="/projects"
		>
			@include('projects.form', ['project' => new App\Project, 'buttonText' => 'Create Project'])
		</form>
	</div>
@endsection