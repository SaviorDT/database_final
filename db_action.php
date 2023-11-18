<?php
$host = 'p:localhost';
$user = 'root';
$password = '';
$database = 'database_final';

$link = new mysqli($host, $user, $password, $database);

if(!$link) {
	echo "{\"stat\":0,\"description\":\"資料庫連接錯誤\"}"
}


$result->free();
?>