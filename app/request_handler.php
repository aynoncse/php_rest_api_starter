<?php

include_once('router.php');

init();

function getBasePath() {
	return rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
}

function getRequestUri() {
	$basePath = getBasePath();
	$uri = $basePath !== '' ? str_replace($basePath, '', $_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI'];
	return trim(parse_url($uri, PHP_URL_PATH), '/');
}

function init() {
	$routes = getRoutes();

	list($routeIndex, $params) = matchRoutes($routes);

	if (!is_null($routeIndex)) {
		$route = $routes[$routeIndex];
		if (!checkIfMethodAllowed($route['method'])) {
			sendJsonResponse(
				[
					'error' => 'Method Not Allowed',
					'message' => "Expected {$route['method']} but received {$_SERVER['REQUEST_METHOD']}"
				],
				405
			);
		}
		executeRouteAction($route['action'], $params);
	} else {

		sendJsonResponse(['error' => 'Not Found', 'message' => 'No matching route found'], 404);
	}
}

function executeRouteAction($action, $params) {
	if (is_callable($action)) {
		return call_user_func($action);
	} else {
		$actionArray = explode('@', $action);
		$file = $actionArray[0];
		$function = $actionArray[1];

		require_once("app/actions/{$file}.php");

		if (function_exists($function)) {
			call_user_func_array($function, $params);
		} else {
			return sendJsonResponse(['error' => 'Not found', 'message' => 'Method not found'], 404);
		}
	}
}


function checkIfMethodAllowed($method) {
	return strtoupper($method) == strtoupper($_SERVER['REQUEST_METHOD']);
}
