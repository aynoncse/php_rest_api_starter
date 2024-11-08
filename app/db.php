<?php

define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "php_rest_api");


$connection = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

return $connection;
