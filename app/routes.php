<?php
return [
	[
		'method' =>  'GET',
		'path'   =>  'users',
		'action' =>  'users@users',
	],
	[
		'method' =>  'GET',
		'path'   =>  'user/{id}',
		'action' =>  'users@user',
	],
	[
		'method' =>  'POST',
		'path'   =>  'login',
		'action' =>  'users@login',
	],
];
