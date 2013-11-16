<?php
include 'db_helper.php';

$GLOBALS["_PLATFORM"]->sandboxHeader("Content-Type: application/json");
//header('Content-Type: application/json; charset=utf-8');


function getSingleRecord($dbQuery) {
	//header('Content-type: application/json');
	$result = NULL;
	try {
		$result = getDBResultRecord($dbQuery, false);
	} 
	catch (Exception $e) {
		$msg1 = "Query: ".$dbQuery;
		$msg2 = "Exception thrown: ".$e->getMessage();
		//echo $msg1."<br/>".$msg2;
		error_log($msg1);
		error_log($msg2);
        $GLOBALS["_PLATFORM"]->sandboxHeader("HTTP/1.1 500 Internal Server Error");
        die();
	}
	return $result;
}

function getMultipleRecords($dbQuery) {
	//header('Content-type: application/json');
	$result = NULL;
	try {
		$result = getDBResultsArray($dbQuery, false);
	} 
	catch (Exception $e) {
		$msg1 = "Query: ".$dbQuery;
		$msg2 = "Exception thrown: ".$e->getMessage();
		//echo $msg1."<br/>".$msg2;
		error_log($msg1);
		error_log($msg2);
        $GLOBALS["_PLATFORM"]->sandboxHeader("HTTP/1.1 500 Internal Server Error");
        die();
	}
	return $result;
}

function performDebugging(){
	$param = isFullRecordType();
	var_dump($param);
	echo "<br/>";
	var_dump($_SERVER);
	echo "<br/>";
	var_dump($_GET);
	echo "<br/>";
	var_dump($_POST);
	echo "<br/>";
	var_dump($_FILES);
	echo "<br/>";
}


include 'cleanplate.restaurant.php';
include 'cleanplate.foodmenu.php';
include 'cleanplate.dish.php';
//error_log(var_export($_REST,TRUE));
//error_log(var_export($_POST,TRUE));
?>
