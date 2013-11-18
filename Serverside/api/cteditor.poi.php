<?php

function listPois($useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT `poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url` from pois ORDER BY poi_id");
	}else{
		$dbQuery = sprintf("SELECT poi_id,title from pois ORDER BY poi_id");
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPoi($poi_id) {
	$dbQuery = sprintf("SELECT `poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url` FROM pois WHERE poi_id = '%s'",
		mysql_real_escape_string($poi_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPrevPoi($poi_id) {
	$dbQuery = sprintf("SELECT `poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url` FROM pois WHERE `poi_id` < '%s' ORDER BY `poi_id` DESC LIMIT 1",
			mysql_real_escape_string($poi_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getNextPoi($poi_id) {
	$dbQuery = sprintf("SELECT `poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url` FROM pois WHERE `poi_id` > '%s' ORDER BY `poi_id` LIMIT 1",
			mysql_real_escape_string($poi_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPoi($title, $description, $image_url, $group_id){
	checkCurrentUserInGroup($group_id);
	$dbQuery = sprintf("INSERT INTO `pois` (`title`,`base_description`,`base_image_url`) VALUES ('%s','%s','%s')",
			mysql_real_escape_string($title),
			mysql_real_escape_string($description),
			mysql_real_escape_string($image_url));
	$poi_id = getDBResultInserted($dbQuery,'poi_id');
	addTourToGroup($tour_id["poi_id"], $group_id);
	header("Content-type: application/json");
	echo json_encode($result);
}

function updatePoi($poi_id, $title, $description, $image_url) {
	checkCurrentUserCanModify('poi', $poi_id);
	$dbQuery = sprintf("UPDATE pois SET `title` = '%s', `base_description` = '%s', `base_image_url` = '%s' WHERE `poi_id` = '%s'",
		mysql_real_escape_string($title),
		mysql_real_escape_string($description),
		mysql_real_escape_string($image_url),
		mysql_real_escape_string($poi_id)
	);
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deletePoi($poi_id){
	checkCurrentUserCanModify('poi', $poi_id);
	$dbQuery = sprintf("DELETE FROM pois WHERE poi_id = '%s'",
			mysql_real_escape_string($poi_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//

function listPoiPhotos($poi_id) {
	$dbQuery = sprintf("SELECT `photo_id`,`image_url`,`base_caption` AS `caption` FROM `poi_photos` JOIN `photos` USING(`photo_id`) WHERE `poi_id` = '%s'", $poi_id);
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPoiPhoto($poi_id, $photo_id) {
	$dbQuery = sprintf("SELECT `photo_id`,`image_url`,`base_caption` AS `caption` FROM `poi_photos` JOIN `photos` USING(`photo_id`) WHERE `poi_id` = '%s' AND `photo_id` = '%s'",
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($photo_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPoiPhoto($poi_id, $photo_id) {
	$dbQuery = sprintf("INSERT INTO `poi_photos` (`poi_id`,`photo_id`) VALUES ('%s','%s')",
			mysql_real_escape_string($poi_id),
			mysql_real_escape_string($photo_id)
	);
	$result = getDBResultInserted($dbQuery,'poi_photo_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

/*
function updatePoiPhoto($poi_id, $photo_id, $caption) {
	$dbQuery = sprintf("UPDATE `poi_photos` SET ?='%s' WHERE `poi_id` = '%s' AND `photo_id` = '%s'",
		mysql_real_escape_string(),
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($photo_id),
	);
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}
*/

function deletePoiPhoto($poi_id, $photo_id){
	checkCurrentUserCanModify('poi', $poi_id);
	$dbQuery = sprintf("DELETE FROM `poi_photos` WHERE `poi_id` = '%s' AND `photo_id` = '%s'",
			mysql_real_escape_string($poi_id),
			mysql_real_escape_string($photo_id)
	);
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

?>