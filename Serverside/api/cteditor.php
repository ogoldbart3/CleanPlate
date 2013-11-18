<?php
include 'db_helper.php';
header('Content-Type: text/html; charset=utf-8');

function checkCurrentUserCanModify($type, $type_id) {
	global $_USER, $_PLATFORM;
	$dbQuery = sprintf('SELECT COUNT(username)'
    					. ' AS canModify'
						. ' FROM group_usernames'
						. ' INNER JOIN groups'
						. ' ON group_usernames.group_id = groups.group_id'
						. ' INNER JOIN group_%1$ss'
						. ' ON group_usernames.group_id = group_%1$ss.group_id'
						. ' WHERE username = \'%2$s\''
						. ' AND %1$s_id = \'%3$s\''
						, mysql_real_escape_string($type)
						, mysql_real_escape_string($_USER['uid'])
						, mysql_real_escape_string($type_id));
	$result = getSingleRecord($dbQuery);
	if ($result['canModify'] == '0') {
		$_PLATFORM->sandboxHeader('HTTP/1.1 403 Forbidden');
		echo "just died";
		die();
	}
}

function checkUserIsLoggedIn() {
	global $_USER, $_PLATFORM;
	if (!array_key_exists('uid', $_USER)) {
		$_PLATFORM->sandboxHeader('HTTP/1.1 403 Forbidden');
		die();
	}
}

function checkCurrentUserInGroup($group_id) {
	global $_USER, $_PLATFORM;
	$dbQuery = sprintf("SELECT COUNT(username) AS canModify FROM `group_usernames` WHERE `group_id` = '%s' AND `username` = '%s';"
						, mysql_real_escape_string($group_id)
						, mysql_real_escape_string($_USER['uid']));
	$result = getSingleRecord($dbQuery);
	if ($result['canModify'] == '0') {
		$_PLATFORM->sandboxHeader('HTTP/1.1 403 Forbidden');
		echo "just died";
		die();
	}
}

function getSingleRecord($dbQuery) {
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

function getBuildingName($buildingNumber){
	$buildings=json_decode(file_get_contents("full_buildings.json"),TRUE);
	foreach ($buildings as $temp){
		if($buildingNumber==$temp["bId"]){
			return $temp["name"];
		}
	}
	return null;
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

function isFullRecordType(){
	if(isset($_GET['recordType'])){
		return ( strtolower($_GET['recordType']) == 'full' );
	}
	return FALSE;
}

function extensionForMimeType($mimeType){
	$extension = "";
	switch($mimeType){
		case 'image/jpg':
		case 'image/jpeg':
		case 'image/pjpeg':
			$extension = ".jpg";
			break;
		case 'image/gif':
			$extension = ".gif";
			break;
		case 'image/png':
			$extension = ".png";
			break;
		default: 
			//not a valid file type
			break;
	}
	return $extension;
}

function storeFileInShareFS($fileLocation,$extension){
	//Calculate the SHA1 HASH
	$sha1_name = sha1_file($fileLocation);
	
	if(!$sha1_name){
		throw new Exception('Unable to access requested file to store.');
	}
	$sha1_name .= $extension;
		
	//Check access to sharedfs
	$base_dir = '/service/rnoc/gtmob/campustour/files';
	if(!file_exists($base_dir)){
		throw new Exception('Unable to access sharedfs.');
	}
	
	//Change name to directory and name
	$sha1_dir = join(DIRECTORY_SEPARATOR,array(
		substr($sha1_name,0,2),
		substr($sha1_name,2,2)
	));
	$sha1_name = substr($sha1_name,4);
	
	$service_sha1_dir = join(DIRECTORY_SEPARATOR,array(
		$base_dir,
		$sha1_dir
	));
	
	//Make the recursive directories
	if(!file_exists($service_sha1_dir)){
		mkdir($service_sha1_dir,0777,TRUE);
	}
	
	//Create the final sha1 name
	$service_sha1_name = join(DIRECTORY_SEPARATOR,array(
		$service_sha1_dir,
		$sha1_name
	));
	
	//move the file
	if(!rename($fileLocation,$service_sha1_name)){
		throw new Exception('Unable to move access requested file to sharedfs.');
	}
	
	//Web dir
	$web_sha1_name = join(DIRECTORY_SEPARATOR,array(
		'/developer/rnoc/widget/campustour/content/files',
		$sha1_dir,
		$sha1_name
	));
	
	return $web_sha1_name;
}




include 'cteditor.media.php';
include 'cteditor.panorama.php';
//include 'cteditor.photo.php';
include 'cteditor.poi.php';
include 'cteditor.tour.php';
include 'cteditor.collection.php';
include 'cteditor.group.php';

//error_log(var_export($_REST,TRUE));
//error_log(var_export($_POST,TRUE));
?>
