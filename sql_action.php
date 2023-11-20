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

function selectSQL($tables, $action_columns, $values, $para, $display_columns) {
	//table and columns and values should be checked.
	$select_command = getSelectCmd($tables, $action_columns, $values, $para, $display_columns);
	
	$stmt = $GLOBALS['link']->prepare($select_command[0]);
	call_user_func_array([$stmt, 'bind_param'], $select_command[1]);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$fst_row = $result->fetch_assoc();
	mysqli_data_seek($result, 0);
	$all_rows = $result->fetch_all();
	
	$to_ret = [
		'stat' => 1,
		'description' => array_keys($fst_row),
		'rows' => $all_rows
	];
	
	echo json_encode($to_ret);
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

function getSelectCmd($tables, $action_columns, $values, $para, $display_columns) {
	$sql_str = "SELECT ";
	$para_type_str = "";
	$para_vals = [&$para_type_str];
	
	if(count($display_columns) == 0) {
		$sql_str = "SELECT *, ";
	}
	foreach($display_columns as $column) {
		$sql_str .= $column.", ";
	}
	
	
	$sql_str = substr($sql_str, 0, -2)." FROM ";
	$tables = sortTable($tables);
	$last_join_table = "";
	foreach($tables as $table) {
		if($last_join_table == "") {
			$sql_str .= $table." ";
		}
		else {
			switch($table) {
				case "genres":
					$sql_str .= "JOIN r_artists_genres USING (genres) ";
					break;
				case "audio_features":
					$sql_str .= "JOIN audio_features ON audio_features_id = audio_feature_id ";
					break;
				default:
					$sql_str .= "JOIN r_{$last_join_table}_{$table} USING({$last_join_table}_id) JOIN $table USING({$table}_id) ";
				break;
			}
		}
		$last_join_table = $table;
	}
	
	if(count($action_columns) > 0) {
		$sql_str .= "WHERE ";
		for($i=0; $i<count($action_columns); $i++) {
			$condition = analysisValue($action_columns[$i], $values[$i]);
			$sql_str .= "$action_columns[$i] {$condition[0]} AND ";
			$para_type_str .= $condition[1];
			$para_vals = my_array_merge($para_vals, $condition[2]);
		}
		
		$sql_str = substr($sql_str, 0, -4);
	}
	
	if(array_key_exists('order', $para)) {
		$sql_str .= "ORDER BY {$para['order']} ";
		if($para['order_direction'] == 0) {
			$sql_str .= "DESC ";
		}
	}
	
	$limit = (int)$para['limit'];
	$offset = (int)$para['page'] * $limit;
	$sql_str .= "LIMIT $limit OFFSET $offset;";
	
	return [$sql_str, $para_vals];
}

function my_array_merge($arr1, $arr2) {
	$to_ret = [];
	for($i=0; $i<count($arr1); $i++) {
		$to_ret[] =& $arr1[$i];
	}
	for($i=0; $i<count($arr2); $i++) {
		$to_ret[] =& $arr2[$i];
	}
	
	return $to_ret;
}

function sortTable($tables) {
	$accept_tables = ['albums', 'tracks', 'artists', 'genres', 'audio_features'];
	$to_ret = [];
	foreach($accept_tables as $table) {
		if(in_array($table, $tables)) {
			$to_ret[] = $table;
		}
	}
	
	return $to_ret;
}

function analysisValue($column, $value) {
	$int_columns = $GLOBALS['int_columns'];
	$double_columns = $GLOBALS['double_columns'];
	$date_columns = $GLOBALS['date_columns'];
	$text_like_columns = $GLOBALS['text_like_columns'];
	
	$para_type = "";
	$conditions = "";
	$bind_param = [];
	$range_para_types = "";
	
	if(in_array($column, $text_like_columns)) {
		if($value[0] != "[" && $value[0] != "(") {
			return ["LIKE ?", "s", [&$value]];
		}
	}
	
	if(in_array($column, $int_columns)) {
		$para_type = "i";
	}
	if(in_array($column, $double_columns)) {
		$para_type = "d";
	}
	if(in_array($column, $date_columns)) {
		$para_type = "s";
	}
	
	if($value[0] != "[" && $value[0] != "(") {
		return ["= ?", $para_type, [&$value]];
	}
	
	$value_list = explode(",", substr($value, 1, -1));
	if($value_list[0] != "") {
		$conditions = ">";
		if($value[0] == "[") {
			$conditions .= "=";
		}
		$conditions .= " ?";
		$range_para_types .= $para_type;
		$bind_param[] = &$value_list[0];
	}
	if($value_list[1] != "") {
		if(count($bind_param) > 0) {
			$conditions .= " AND $column ";
		}
		$conditions .= "<";
		if($value[-1] == "]") {
			$conditions .= "=";
		}
		$conditions .= " ?";
		$range_para_types .= $para_type;
		$bind_param[] = &$value_list[1];
	}
	
	if(count($bind_param) == 0) {
		returnException($column."傳入的範圍值為".$value."，請指定至少指定上限或下限其中之一");
	}
	
	return [$conditions, $range_para_types, $bind_param];
}

//$result->free();
?>