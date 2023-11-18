<?php
require_once 'constants.php';
require_once 'return_interface.php';

function getPost($para_name) {
	if(!array_key_exists($para_name, $_POST)) {
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
		returnException("table wrong!");
	}
	
	return $table;
}

//DID NOT check valid.
function getActionColumns() {
	$json_columns = getPost('action_columns');
	$columns = json_decode($json_columns);
	if(!$columns) {
		returnException("錯誤JSON字串");
	}
	return $columns;
}

//DID NOT check valid.
function getValues() {
	$json_values = getPost('values');
	$values = json_decode($json_values);
	if(!$values) {
		returnException("錯誤JSON字串");
	}
	return $values;
}
?>