<?php
require_once 'constants.php';
require_once 'return_interface.php';
require_once 'sql_action.php';


//If invalid, stop this script and return error message.
function checkColValid($tables, $action_columns) {
	if(count($action_columns) <= 0) {
		returnException("column數量至少要是1");
	}
	
	$table_columns = $GLOBALS['table_columns'];
	$confusing_columns = $GLOBALS['confusing_columns'];
	
	$allow_cols = [];
	foreach($tables as $table) {
		$allow_cols = array_merge($allow_cols, $table_columns[$table]);
	}
	
	foreach($action_columns as $column) {
		if(in_array($column, $confusing_columns)) {
			returnException("出現易混淆column，請使用更名後的column");
		}
		if(!in_array($column, $allow_cols)) {
			returnException($column."，此column無法使用，請檢查是否有打錯字或是少join_table。");
		}
	}
}

//If invalid, stop this script and return error message.
function checkValValid($columns, $values) {
	if(count($columns) != count($values)) {
		returnException("欄位和值數量不同");
	}
	for($i=0; $i<count($columns); $i++) {
		//if invalid, stop script and echo error message.
		checkSingleValValid($columns[$i], $values[$i]);
	}
}

//If invalid, stop this script and return error message.
function checkValRangeValid($columns, $values) {
	if(count($columns) != count($values)) {
		returnException("欄位和值數量不同");
	}
	for($i=0; $i<count($columns); $i++) {
		$value = $values[$i];
		if(in_array($columns[$i], $GLOBALS['number_like_columns'])) {
			// start with [ or (, and then number, and then space or not, and then number, and then end with ) or ].
			if(preg_match("/^[\[\(][-0-9.]*,[-0-9.]*[\]\)]$/i", $value)) { 
				$value = explode(",", substr($values[$i], 1, -1));
				if($value[0] != "") {
					checkSingleValValid($columns[$i], $value[0]);
				}
				if($value[1] != "") {
					checkSingleValValid($columns[$i], $value[1]);
				}
				continue;
			}	
		}
		
		if(in_array($columns[$i], $GLOBALS['text_like_columns'])) {
			return;
		}
		//if invalid, stop script and echo error message.
		checkSingleValValid($columns[$i], $value);
	}
}

function checkSingleValValid($column, $value) {
	$text_columns = $GLOBALS['text_columns'];
	$id_columns = $GLOBALS['id_columns'];
	$foreign_id_columns = $GLOBALS['foreign_id_columns'];
	$int_columns = $GLOBALS['int_columns'];
	$double_columns = $GLOBALS['double_columns'];
	$date_columns = $GLOBALS['date_columns'];
	$value_rules = $GLOBALS['value_rules'];
	
	if(in_array($column, $text_columns)) {
		//no need to check valid.
	}
	else if(in_array($column, $id_columns)) {
		if(!preg_match("/^[0-9a-zA-Z+\/,=-]{22}$/i", $value)) {
			returnException($ccolumn."欄位傳入值為: ".$value."，應為僅包含英數且長度為22的字串");
		}
	}
	else if(in_array($column, $foreign_id_columns)) {
		if(!preg_match("/^[0-9a-zA-Z+\/,=-]{22}$/i", $value)) {
			returnException($column."欄位傳入值為: ".$value."，應為僅包含英數且長度為22的字串，如果想讓他null，直接不要把它放在action_columns裡面就好");
		}
		//for only one nullable foreign key in this database.
		if(!inSQL("audio_features", "audio_features_id", $value)) {
			returnException($column."欄位傳入值為: ".$value."，未在audio_features.id中找到對應外鍵");
		}
	}
	else if(in_array($column, $int_columns)) {
		$val = (int)$value;
		$rule = $value_rules[$column];
		
		if($val != $value) {
			returnException($column."欄位傳入值為: ".$value."，請輸入整數");
		}
		if($val < $rule[0] || $val > $rule[1]) {
			returnException($column."欄位傳入值為: ".$val."，數值範圍應介於".$rule[0]."和".$rule[1]."之間");
		}
	}
	else if(in_array($column, $double_columns)) {
		$val = (float)$value;
		$rule = $value_rules[$column];
		
		if($val != $value) {
			returnException($column."欄位傳入值為: ".$value."，請輸入數字");
		}
		if($val < $rule[0] || $val > $rule[1]) {
			returnException($column."欄位傳入值為: ".$val."，數值範圍應介於".$rule[0]."和".$rule[1]."之間");
		}
	}
	else if(in_array($column, $date_columns)) {
		if(!preg_match("/^\d{4}-\d{2}-\d{2}$/i", $value)) {
			returnException($column."欄位傳入值為: ".$value."，請輸入yyyy-mm-dd形式");
		}
		if(!checkDateValid($value)) {
			returnException($column."欄位傳入值為: ".$value."，請輸入合理的日期");
		}
	}
}

function checkDateValid($date) {
	$year = substr($date, 0, 4);
	$month = substr($date, 5, 2);
	$day = substr($date, 8, 2);
	return checkdate($month, $day, $year);
}

function checkCanJoin($tables) {
	if(count($tables) == 1) {
		return;
	}
	if(in_array('genres', $tables)) {
		if(!in_array('artists', $tables)) {
			returnException("檢測到輸入table包含genres，但並未包含artists，無法join");
		}
	}
	
	if(in_array('audio_features', $tables)) {
		if(!in_array('tracks', $tables)) {
			returnException("檢測到輸入table包含audio_features，但並未包含tracks，無法join");
		}
	}
}

function checkParaValid($para, $tables) {
	$to_ret = [];
	
	if($para['limit'] != (int)$para['limit']) {
		returnException("limit傳入值為: ".$para['limit']."，請輸入正整數");
	}
	if((int)$para['limit'] <= 0) {
		returnException("limit傳入值為: ".$para['limit']."，請輸入大於0的數字");
	}
	$to_ret['limit'] = (int)$para['limit'];
	
	if($para['page'] != (int)$para['page']) {
		returnException("page傳入值為: ".$para['page']."，請輸入正整數");
	}
	if((int)$para['page'] < 0) {
		returnException("page傳入值為: ".$para['page']."，請輸入不小於0的數字");
	}
	$to_ret['page'] = (int)$para['page'];
	
	if($para['order'] == -1) {
		unset($para['order']);
		unset($para['order_direction']);
		return $to_ret;
	}
	
	if($para['order_direction'] != 0 && $para['order_direction'] != 1) {
		returnException("order_direction傳入值為: ".$para['order_direction']."，請輸入0或1");
	}
	$in_table = false;
	foreach($tables as $table) {
		if(in_array($para['order'], $GLOBALS['table_columns'][$table])) {
			$in_table = true;
			break;
		}
	}
	if(!$in_table) {
		returnException("order傳入值為: ".$para['order']."，未找到此column，請確認是否輸入錯誤或是否join到");
	}
	
	$to_ret['order'] = $para['order'];
	$to_ret['order_direction'] = (int)$para['order_direction'];
	
	return $to_ret;
}
?>