<?php
include_once("app/db_query.php");

function users() {
	$users = select('users');
	return sendJsonResponse($users);
}

function user($id) {
	$user = findById('users', $id);
	return sendJsonResponse($user);
}
