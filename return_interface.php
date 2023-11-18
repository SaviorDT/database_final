<?php
function returnException($desc, $exit = true) {
	echo '{"stat":0,"description":"'.$desc.'"}';
	if($exit) {
		exit();
	}
}

function returnSimpleSuccess($desc) {
	echo '{"stat":1,"description":"'.$desc.'"}';
}
?>