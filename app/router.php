<?php
function getRoutes() {
	return include('app/routes.php');
}

function matchRoutes($routes = []) {
	$requestUri = getRequestUri();
	$routes = empty($routes) ? getRoutes() : $routes;
	foreach ($routes as $index => $route) {
		$routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
		$routePattern = '#^' . $routePattern . '$#';
		if (preg_match($routePattern, $requestUri, $matches)) {
			array_shift($matches);
			$params = getRouteParams($route['path'], $matches);

			return [$index, $params];
		}
	}
	return false;
}

function getRouteParams($routePath, $matches) {
	$params = [];
	preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);

	foreach ($paramNames[1] as $index => $paramName) {
		$params[$paramName] = $matches[$index] ?? null;
	}

	return $params;
}
