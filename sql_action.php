<?php
require_once 'return_interface.php';
require_once 'constants.php';

$host = 'p:localhost';
$user = 'root';
$password = '';
$database = 'database_final';

$link = new mysqli($host, $user, $password, $database);


if(!$link) {
	returnException("資料庫連接錯誤");
}

function deleteSQL($table, $ID) {
	//table should be checked.
	$cmd = "DELETE FROM $table WHERE {$table}_id = ?;";
	if($table == "genres") {
		$cmd = "DELETE FROM $table WHERE genres = ?;";
	}
	
	$stmt = $GLOBALS['link']->prepare($cmd);
	$stmt->bind_param("s", $ID);
	$stmt->execute();
	$result = $GLOBALS['link']->affected_rows;
	
	if($result > 0) {
		returnSimpleSuccess("刪除成功");
	}
	else {
		returnException("並未找到符合的欄位", false);
	}
}

function insertSQL($table, $columns, $values) {
	//table and columns and values should be checked.
	$insert_command = getInsertCmd($table, $columns, $values);
	$stmt = $GLOBALS['link']->prepare($insert_command[0]);
	call_user_func_array([$stmt, 'bind_param'], $insert_command[1]);
	$stmt->execute();
	$result = $GLOBALS['link']->affected_rows;
	
	if($result > 0) {
		returnSimpleSuccess("插入成功");
	}
	else {
		returnException("插入欄位失敗。".$GLOBALS['link']->error, false);
	}
}

function updateSQL($table, $ID, $columns, $values) {
	//table and columns and values should be checked.
	$update_command = getUpdateCmd($table, $columns, $values);
	
	$where = " WHERE {$table}_id = ?;";
	if($table == "genres") {
		$where = " WHERE genres = ?;";
	}
	$update_command[0] .= $where;
	$update_command[1][0] .= "s";
	$update_command[1][] = &$ID;
	
	$stmt = $GLOBALS['link']->prepare($update_command[0]);
	call_user_func_array([$stmt, 'bind_param'], $update_command[1]);
	$stmt->execute();
	$result = $GLOBALS['link']->affected_rows;
	
	if($result > 0) {
		returnSimpleSuccess("修改成功");
	}
	else {
		returnException("修改欄位失敗。".$GLOBALS['link']->error, false);
	}
}

function inSQL($table, $column, $value) {
	//table and column should be checked.
	$stmt = $GLOBALS['link']->prepare("SELECT COUNT(*) AS cnt FROM ".$table." WHERE ".$column." = ?;");
	$stmt->bind_param("s", $value);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = mysqli_fetch_assoc($result);
	if($row['cnt'] >= 1) {
		return true;
	}
	return false;
}

function getInsertCmd($table, $columns, $values) {
	$int_columns = $GLOBALS['int_columns'];
	$double_columns = $GLOBALS['double_columns'];
	
	$columns_str = "";
	$values_str = "";
	$para_type_str = "";
	foreach($columns as $column) {
		$columns_str .= $column.", ";
		$values_str .= "?, ";
		if(in_array($column, $int_columns)) {
			$para_type_str .= "i";
		}
		else if(in_array($column, $double_columns)) {
			$para_type_str .= "d";
		}
		else {
			$para_type_str .= "s";
		}
	}
	
	$para_vals = [$para_type_str];
	for($i=0; $i < count($values); $i++) {
		$para_vals[] = &$values[$i];
	}
	
	$columns_str = substr($columns_str, 0, -2);
	$values_str = substr($values_str, 0, -2);
	$cmd = [
		"INSERT INTO $table ($columns_str) VALUES ($values_str);",
		$para_vals
	];
	return $cmd;
}

function getUpdateCmd($table, $columns, $values) {
	$int_columns = $GLOBALS['int_columns'];
	$double_columns = $GLOBALS['double_columns'];
	
	$sql_str = "UPDATE $table SET";
	$para_type_str = "";
	$para_vals = [&$para_type_str];
	for($i=0; $i < count($columns); $i++) {
		$sql_str .= " {$columns[$i]} = ?,";
		$para_vals[] = &$values[$i];
		
		if(in_array($columns[$i], $int_columns)) {
			$para_type_str .= "i";
		}
		else if(in_array($columns[$i], $double_columns)) {
			$para_type_str .= "d";
		}
		else {
			$para_type_str .= "s";
		}
	}
	
	$sql_str = substr($sql_str, 0, -1);
	$cmd = [
		$sql_str,
		$para_vals
	];
	return $cmd;
}


//$result->free();
?>