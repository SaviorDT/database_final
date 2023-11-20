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
		checkColValid([$table], $action_columns);
		checkValValid($action_columns, $values);
		
		insertSQL($table, $action_columns, $values);
		break;
	case 'update':
		$ID = getPost('ID');
		$action_columns = getActionColumns();
		$values = getValues();
		
		//if invalid, stop this script and echo error message.
		checkColValid([$table], $action_columns, []);
		checkValValid($action_columns, $values);
		
		updateSQL($table, $ID, $action_columns, $values);
		break;
	case 'select':
		$join = array_merge(getJoin(), [$table]);
		checkCanJoin($join);
		
		$action_columns = getActionColumns(true);
		$display_columns = getDisplayColumns();
		$values = getValues(true);
		
		//if invalid, stop this script and echo error message.
		if(count($action_columns) > 0) {
			checkColValid($join, $action_columns);
			checkValRangeValid($action_columns, $values);
		}
		if(count($display_columns) > 0) {
			checkColValid($join, $display_columns);
		}
		
		$limit = getPost('limit', 100);
		$page = getPost('page', 0);
		$order = getPost('order', -1);
		$order_direction = getPost('order_direction', 0);
		$para = [
			'limit' => $limit,
			'page' => $page,
			'order' => $order,
			'order_direction' => $order_direction
		];
		$para = checkParaValid($para, $join);
		
		//echo in the function.
		selectSQL($join, $action_columns, $values, $para, $display_columns);
		break;
}
?>