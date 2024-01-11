<?php
require_once 'constants.php';
require_once 'return_interface.php';

function getPost($para_name, $default_value = null) {
	if(!array_key_exists($para_name, $_POST)) {
		if($default_value !== null) {
			return $default_value;
		}
		returnException($para_name." not found!");
	}
	return $_POST[$para_name];
}

//checked valid.
function getActionType() {
	$action_type = getPost('action_type');
	if(!in_array($action_type, $GLOBALS['action_types'])){
		returnException("action_type wrong!");
	}
	
	return $action_type;
}

//checked valid.
function getTable() {
	$table = getPost('table');
	if(!in_array($table, $GLOBALS['tables'])){
		returnException("table wrong!, ".$table." not exists.");
	}
	
	return $table;
}

function getJoin() {
	$json_join = getPost('join', "[]");
	$join = json_decode($json_join);
	if($join === null) {
		returnException("join是錯誤JSON字串");
	}
	foreach($join as $table) {
		if(!in_array($table, $GLOBALS['tables'])){
			returnException("join table wrong: ".$table);
		}
	}
	
	return $join;
}

//DID NOT check valid.
function getActionColumns($can_null = false) {
	$json_columns = getPost('action_columns', $can_null ? "[]" : null);
	$columns = json_decode($json_columns);
	if($columns === null) {
		returnException("action_columns是錯誤JSON字串");
	}
	return $columns;
}

function getDisplayColumns() {
	$json_columns = getPost('display_columns', "[]");
	$columns = json_decode($json_columns);
	if(!$columns === null) {
		returnException("display_columns是錯誤JSON字串");
	}
	return $columns;
}

//DID NOT check valid.
function getValues($can_null = false) {
	$json_values = getPost('values', $can_null ? "[]" : null);
	$values = json_decode($json_values);
	if($values === null) {
		returnException("values是錯誤JSON字串");
	}
	return $values;
}
?>