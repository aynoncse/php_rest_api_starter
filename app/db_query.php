<?php

function dbConnection() {
	static $dbConnection = null;
	if (is_null($dbConnection)) {
		$dbConnection = require_once("app/db.php");

		if (!$dbConnection) {
			return sendJsonResponse(['error' => 'Database connection failed'], 500);
		}

		register_shutdown_function(function () use (&$dbConnection) {
			if ($dbConnection) {
				mysqli_close($dbConnection);
			}
		});
	}
	return $dbConnection;
}

function select($tableName, $conditions = [], $orderBy = null, $limit = null, $offset = null) {
	$dbConnection = dbConnection();
	$sql = "SELECT * FROM `$tableName`";

	if (!empty($conditions)) {
		$sql .= " WHERE ";
		$whereClauses = [];
		foreach ($conditions as $key => $condition) {
			$whereClauses[] = "`$key` = ?";
		}
		$sql .= "" . implode(" AND ", $whereClauses);
	}

	if ($orderBy) {
		$sql .= " ORDER BY $orderBy";
	}

	if ($limit) {
		$sql .= " LIMIT ?";
	}

	$stmt = mysqli_prepare($dbConnection, $sql);

	if (!$stmt) {
		return sendJsonResponse(['error' => 'Failed to prepare statement: ' . mysqli_error($dbConnection)], 500);
	}

	if (!empty($conditions) || $limit) {
		$types = '';
		$bindParams = [];

		foreach ($conditions as $value) {
			$types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
			$bindParams[] = $value;
		}

		if ($limit) {
			$types .= 'i';
			$bindParams[] = $limit;
		}

		mysqli_stmt_bind_param($stmt, $types, ...$bindParams);
	}

	if (!mysqli_stmt_execute($stmt)) {
		return sendJsonResponse(['error' => 'Failed to execute query: ' . mysqli_stmt_error($stmt)], 500);
	}

	$result = mysqli_stmt_get_result($stmt);

	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function findById($tableName, $id) {
	$dbConnection = dbConnection();
	$sql = "SELECT * FROM `$tableName` WHERE id = ?";
	$stmt = mysqli_prepare($dbConnection, $sql);

	if (!$stmt) {
		return sendJsonResponse(['error' => 'Failed to prepare statement: ' . mysqli_error($dbConnection)], 500);
	}

	mysqli_stmt_bind_param($stmt, "i", $id);

	if (!mysqli_stmt_execute($stmt)) {
		mysqli_stmt_close($stmt);
		return sendJsonResponse(['error' => 'Failed to execute query: ' . mysqli_stmt_error($stmt)], 500);
	}

	$result = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_assoc($result);
	mysqli_stmt_close($stmt);

	return $row;
}
