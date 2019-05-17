<?php

function gravatar_url($email)
{
	$email = md5($email);

	return "https://gravatar.com/avatar/{$email}?s=60" . http_build_query([
		's' => 60
	]);
}

