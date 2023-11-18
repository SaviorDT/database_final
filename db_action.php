<?php
require_once 'para_getter.php';
require_once 'sql_action.php';
require_once 'check.php';

//checked while getting.
$action_type = getActionType();
$table = getTable();

switch($action_type) {
	case 'delete':
		$ID = getPost('ID');
		deleteSQL($table, $ID);
		break;
	case 'insert':
		$action_columns = getActionColumns();
		$values = getValues();
		
		//if invalid, stop this script and echo error message.
		checkColValid($table, $action_columns, []);
		checkValValid($action_columns, $values);
		
		insertSQL($table, $action_columns, $values);
		break;
	case 'update':
		$ID = getPost('ID');
		$action_columns = getActionColumns();
		$values = getValues();
		
		//if invalid, stop this script and echo error message.
		checkColValid($table, $action_columns, []);
		checkValValid($action_columns, $values);
		
		updateSQL($table, $ID, $action_columns, $values);
		break;
}
?>