<?php

function dd(...$data) {
	dump(...$data);
	exit;
}

function dump(...$data) {

	foreach ($data as $argument) {
		echo '<pre style="background-color: #222; color:  #fff; padding: 1rem; font-family: monospace; border-radius: 5px; overflow-x: auto;">';

		if (is_string($argument)) {
			echo htmlspecialchars($argument);
		} elseif (is_bool($argument)) {
			echo $argument ? 'true' : 'false';
		} elseif (is_null($argument)) {
			echo 'null';
		} else {
			echo '<code>';
			print_r($argument);
			echo '</code>';
		}
		echo '</pre>';
	}
}


function processIncomingRequest() {
	include('app/request_handler.php');
}

function sendJsonResponse($data, $status = 200) {
	http_response_code($status);
	header('Content-Type: application/json');
	echo json_encode($data, JSON_PRETTY_PRINT);
	exit;
}
