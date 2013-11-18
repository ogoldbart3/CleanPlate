<?php
function helper_getFaceForSide($side){
	$face = NULL;
	switch ($side){
		case "0":
			case "f":
			case "front":
			$face = "front";
			break;
		case "1":
			case "b":
			case "back":
			$face = "back";
			break;
		case "2":
			case "l":
			case "left":
			$face = "left";
			break;
		case "3":
			case "r":
			case "right":
			$face = "right";
			break;
		case "4":
			case "u":
			case "top":
			$face = "top";
			break;
		case "5":
			case "d":
			case "bottom":
			$face = "bottom";
			break;
		default:
			//something should go here
			break;
	}
	return $face;
}
/****************************************************************************************************/

function listPanoramas($useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT panorama_id, title, description, latitude, longitude, altitude, heading, tilt, roll FROM panoramas");
	}else{
		$dbQuery = sprintf("SELECT panorama_id, title FROM panoramas ORDER BY panorama_id");
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPanorama($panorama_id) {
	$dbQuery = sprintf("SELECT panorama_id, title, description, latitude, longitude, altitude, heading, tilt, roll FROM panoramas WHERE panorama_id = '%s'",
			mysql_real_escape_string($panorama_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPrevPanorama($id) {
	$dbQuery = sprintf("SELECT panorama_id, title, description, latitude, longitude, altitude, heading, tilt, roll FROM panoramas WHERE panorama_id < '%s' ORDER BY panorama_id DESC LIMIT 1",
			mysql_real_escape_string($id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getNextPanorama($id) {
	$dbQuery = sprintf("SELECT panorama_id, title, description, latitude, longitude, altitude, heading, tilt, roll FROM panoramas WHERE panorama_id > '%s' ORDER BY panorama_id LIMIT 1",
			mysql_real_escape_string($id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPanorama($title, $description, $latitude, $longitude, $altitude, $heading, $tilt, $roll, $files, $group_id) {
	checkCurrentUserInGroup($group_id);
	$dbQuery = sprintf("INSERT INTO panoramas (`title`,`description`,`latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",
		mysql_real_escape_string($title),
		mysql_real_escape_string($description),
		mysql_real_escape_string($latitude),
		mysql_real_escape_string($longitude),
		mysql_real_escape_string($altitude),
		mysql_real_escape_string($heading),
		mysql_real_escape_string($tilt),
		mysql_real_escape_string($roll)
	);
	
	$result = getDBResultInserted($dbQuery,'panorama_id');
	if($result['panorama_id']){
		for ($i = 0;$i < count($files["error"]); $i++) {
			if($files["error"][$i] == 0){
				$extension = extensionForMimeType($files["type"][$i]);
				$web_sha1_name = storeFileInShareFS($files["tmp_name"][$i],$extension);
				
				$side = helper_getFaceForSide($i);	
				$dbQuery = sprintf("INSERT INTO panorama_images_sha1 (`panorama_id`,`side`,`image_url`) VALUES ('%s','%s','%s')",
					mysql_real_escape_string($result['panorama_id']),
					mysql_real_escape_string($side),
					mysql_real_escape_string($web_sha1_name)
				);
				$panorama_id = getDBResultInserted($dbQuery,'panorama_id');
			}
		}
	}

	addTourToGroup($tour_id["panorama_id"], $group_id);

	header("Content-type: application/json");
	echo json_encode($result);
}

function updatePanorama($panorama_id, $title, $latitude, $longitude, $altitude, $heading, $tilt, $roll, $description) {
	
	checkCurrentUserCanModify('panorama', $panorama_id);
	$dbQuery = sprintf("UPDATE panoramas SET title = '%s', latitude = '%s', longitude='%s', altitude='%s', heading='%s', tilt='%s', roll='%s', description='%s' WHERE panorama_id = '%s'",
			mysql_real_escape_string($title),
			mysql_real_escape_string($latitude),
			mysql_real_escape_string($longitude),
			mysql_real_escape_string($altitude),
			mysql_real_escape_string($heading),
			mysql_real_escape_string($tilt),
			mysql_real_escape_string($roll),
			mysql_real_escape_string($description),
			mysql_real_escape_string($panorama_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deletePanorama($panorama_id) {
	checkCurrentUserCanModify('panorama', $panorama_id);
	$dbQuery = sprintf("DELETE FROM panoramas WHERE panorama_id = '%s'",
			mysql_real_escape_string($panorama_id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

/****************************************************************************************************/

function getPanoramaImage($id,$side){
	$side = strtolower($side);
	$face = helper_getFaceForSide($side);

	if(!$face){
		$GLOBALS["_PLATFORM"]->sandboxHeader("HTTP/1.1 400 Bad Request");
		die();
	}
	try {
		$dbQuery = sprintf("SELECT `image_url` FROM `panorama_images_sha1` WHERE panorama_id = '%s' AND side = '%s'", $id, $face);
		$panorama = getDBResultRecord($dbQuery);
	} catch (Exception $e) {
		echo $e->getMessage();
	}

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$panorama['image_url']);
}

/****************************************************************************************************/

function listsPanoramaPois($panorama_id,$useFullRecord = FALSE){
	if($useFullRecord){
		$dbQuery = sprintf("SELECT `panorama_id`,`panorama_pois`.`poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url`, `latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll` FROM panorama_pois JOIN pois USING (poi_id) WHERE panorama_id = '%s' ORDER BY panorama_pois.poi_id",
				mysql_real_escape_string($panorama_id));
	}else{
		$dbQuery = sprintf("SELECT panorama_id, panorama_pois.poi_id, title FROM panorama_pois JOIN pois USING (poi_id) WHERE panorama_id = '%s' ORDER BY panorama_pois.poi_id",
				mysql_real_escape_string($panorama_id));
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function listRemainingPanoramaPois($panorama_id){
	$dbQuery = sprintf("SELECT poi_id, title FROM pois WHERE poi_id NOT IN (SELECT poi_id FROM panorama_pois WHERE panorama_id = '%s') ORDER BY poi_id",
		mysql_real_escape_string($panorama_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPanoramaPoi($panorama_id,$poi_id) {
	$dbQuery = sprintf("SELECT `panorama_id`,`panorama_pois`.`poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url`, `latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll` FROM panorama_pois JOIN pois USING (poi_id) WHERE `panorama_id` = '%s' AND `poi_id` = '%s'",
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPrevPanoramaPoi($panorama_id,$poi_id) {
	$dbQuery = sprintf("SELECT `panorama_id`,`panorama_pois`.`poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url`, `latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll` FROM panorama_pois JOIN pois USING (poi_id) WHERE `panorama_id` = '%s' AND `poi_id` < '%s' ORDER BY `panorama_pois`.`poi_id` DESC LIMIT 1",
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getNextPanoramaPoi($panorama_id,$poi_id) {
	$dbQuery = sprintf("SELECT `panorama_id`,`panorama_pois`.`poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url`, `latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll` FROM panorama_pois JOIN pois USING (poi_id) WHERE `panorama_id` = '%s' AND `poi_id` > '%s' ORDER BY `panorama_pois`.`poi_id` LIMIT 1",
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPanoramaPoi($panorama_id,$poi_id){
	checkCurrentUserCanModify('panorama', $panorama_id);
	$dbQuery = sprintf("INSERT INTO panorama_pois (panorama_id,poi_id) VALUES ('%s','%s')",
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultInserted($dbQuery,'panorama_poi_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

function updatePanoramaPoi($panorama_id, $poi_id, $latitude, $longitude, $altitude, $heading, $tilt, $roll) {
	checkCurrentUserCanModify('panorama', $panorama_id);
	$dbQuery = sprintf("UPDATE panorama_pois SET latitude = '%s', longitude='%s', altitude='%s', heading='%s', tilt='%s', roll='%s' WHERE `panorama_id` = '%s' AND `poi_id` = '%s'",
			mysql_real_escape_string($latitude),
			mysql_real_escape_string($longitude),
			mysql_real_escape_string($altitude),
			mysql_real_escape_string($heading),
			mysql_real_escape_string($tilt),
			mysql_real_escape_string($roll),
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deletePanoramaPoi($panorama_id,$poi_id){
	checkCurrentUserCanModify('panorama', $panorama_id);
	$dbQuery = sprintf("DELETE FROM panorama_pois WHERE panorama_id = '%s' AND poi_id = '%s'",
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}


?>