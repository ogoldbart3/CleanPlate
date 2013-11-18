<?php

function listGroups() {
	$dbQuery = sprintf("SELECT group_id, group_name FROM groups ORDER by group_id");
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addGroup($group_name) {
	$dbQuery = sprintf("INSERT INTO groups (`group_name`) VALUES ('%s')",
		mysql_real_escape_string($group_name)
	);
	$result = getDBResultInserted($dbQuery,'group_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

function updateGroup($group_id, $group_name) {
	$dbQuery = sprintf("UPDATE groups SET group_name = '%s' WHERE group_id = '%s'",
			mysql_real_escape_string($group_name),
			mysql_real_escape_string($group_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getGroup($group_id) {
	$dbQuery = sprintf("SELECT group_id, group_name, GRS_role FROM groups WHERE group_id = '%s'",
			mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteGroup($group_id) {
	deleteAllCollectionsFromGroup($group_id);
	deleteAllToursFromGroup($group_id);
	deleteAllPanoramasFromGroup($group_id);
	deleteAllPoisFromGroup($group_id);
	
	$dbQuery = sprintf("DELETE FROM groups WHERE group_id = '%s'",
			mysql_real_escape_string($group_id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}


/***************************************************************************/

function listCollectionsByGroup($group_id) {
	$dbQuery = sprintf("SELECT `collections`.`collection_id`, `collections`.`collection_name` FROM group_collections INNER JOIN collections ON `group_collections`.`collection_id`=`collections`.`collection_id` WHERE `group_collections`.`group_id`='%s';",
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addCollectionToGroup($collection_id, $group_id) {
	$dbQuery = sprintf("INSERT INTO group_collections (group_id, collection_id) VALUES (%s, %s);",
		mysql_real_escape_string($group_id, $collection_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
}

function deleteCollectionFromGroup($collection_id, $group_id) {
	$dbQuery = sprintf("DELETE FROM group_collections WHERE collection_id=%s AND group_id=%s;",
		mysql_real_escape_string($collection_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteAllCollectionsFromGroup($group_id) {
	$dbQuery = sprintf("DELETE FROM group_collections WHERE group_id=%s;", 
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getCollectionFromGroup($collection_id, $group_id) {
	$dbQuery = sprintf("SELECT `collections`.`collection_id`, `collections`.`collection_id` FROM group_collections INNER JOIN collections ON `group_collections`.`collection_id`=`collections`.`collection_id` WHERE `group_collections`.`collection_id`='%s' AND `group_collections`.`group_id`='%s';",
		mysql_real_escape_string($collection_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

/********************************************************************************/

function listToursByGroup($group_id) {
	$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`title` FROM group_tours INNER JOIN tours ON `group_tours`.`tour_id`=`tours`.`tour_id` WHERE `group_tours`.`group_id`='%s';",
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addTourToGroup($tour_id, $group_id) {
	$dbQuery = sprintf("INSERT INTO `group_tours` (`group_id`, `tour_id`) VALUES ('%s', '%s');",
		mysql_real_escape_string($group_id),
		mysql_real_escape_string($tour_id));
	$result = getDBResultInserted($dbQuery);
	header("Content-type: application/json");
}

function deleteTourFromGroup($tour_id, $group_id) {
	$dbQuery = sprintf("DELETE FROM group_tours WHERE tour_id=%s AND group_id=%s;",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteAllToursFromGroup($group_id) {
	$dbQuery = sprintf("DELETE FROM group_tours WHERE group_id=%s;", 
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getTourFromGroup($tour_id, $group_id) {
	$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`title`, `tours`.`description`, `tours`.`route` FROM group_tours INNER JOIN tours ON `group_tours`.`tour_id`=`tours`.`tour_id` WHERE `group_tours`.`tour_id`='%s' AND `group_tours`.`group_id`='%s';",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}



/********************************************************************************/

function listPanoramasByGroup($group_id) {
	$dbQuery = sprintf("SELECT `panoramas`.`panorama_id`, `panoramas`.`title` FROM group_panoramas INNER JOIN panoramas ON `group_panoramas`.`panorama_id`=`panoramas`.`panorama_id` WHERE `group_panoramas`.`group_id`='%s';",
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPanoramaToGroup($panorama_id, $group_id) {
	$dbQuery = sprintf("INSERT INTO group_panoramas (group_id, panorama_id) VALUES (%s, %s);",
		mysql_real_escape_string($group_id, $panorama_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
}

function deletePanoramaFromGroup($panorama_id, $group_id) {
	$dbQuery = sprintf("DELETE FROM group_panoramas WHERE panorama_id=%s AND group_id=%s;",
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteAllPanoramasFromGroup($group_id) {
	$dbQuery = sprintf("DELETE FROM group_panoramas WHERE group_id=%s;", 
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}


function getPanoramaFromGroup($panorama_id, $group_id) {
	$dbQuery = sprintf("SELECT `panoramas`.`panorama_id`, `panoramas`.`title`, `panoramas`.`description` FROM group_panoramas INNER JOIN panoramas ON `group_panoramas`.`panorama_id`=`panoramas`.`panorama_id` WHERE `group_panoramas`.`panorama_id`='%s' AND `group_panoramas`.`group_id`='%s';",
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);

}

/********************************************************************************/

function listPoisByGroup($group_id) {
	$dbQuery = sprintf("SELECT `pois`.`poi_id`, `pois`.`title` FROM group_pois INNER JOIN pois ON `group_pois`.`poi_id`=`pois`.`poi_id` WHERE `group_pois`.`group_id`='%s';",
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPoiToGroup($poi_id, $group_id) {
	$dbQuery = sprintf("INSERT INTO group_pois (group_id, poi_id) VALUES (%s, %s);",
		mysql_real_escape_string($group_id, $poi_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
}

function deletePoiFromGroup($poi_id, $group_id) {
	$dbQuery = sprintf("DELETE FROM group_pois WHERE poi_id=%s AND group_id=%s;",
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteAllPoisFromGroup($group_id) {
	$dbQuery = sprintf("DELETE FROM group_pois WHERE group_id=%s;", 
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPoiFromGroup($poi_id, $group_id) {
	$dbQuery = sprintf("SELECT `pois`.`poi_id`, `pois`.`title`, `pois`.`base_description` FROM group_pois INNER JOIN pois ON `group_pois`.`poi_id`=`pois`.`poi_id` WHERE `group_pois`.`poi_id`='%s' AND `group_pois`.`group_id`='%s';",
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);

}


/**********************************************************************************************************************************************/

function listUsernamesByGroup($group_id) {
	$dbQuery = sprintf("SELECT `groups`.`group_name`, `group_usernames`.`group_id`, `group_usernames`.`username` FROM `groups` INNER JOIN `group_usernames` ON `groups`.`group_id`=`group_usernames`.`group_id` WHERE `group_id`='%s';",
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function listGroupsByUsername($username) {
	$dbQuery = sprintf("SELECT `groups`.`group_name`, `group_usernames`.`group_id`, `group_usernames`.`username` FROM `groups` INNER JOIN `group_usernames` ON `groups`.`group_id`=`group_usernames`.`group_id` WHERE `username`='%s';",
		mysql_real_escape_string($username));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function autolistGroupsByUsername() {
	global $_USER;
	listGroupsByUsername($_USER['uid']);
}

function addUsernameToGroup($username, $group_id) {
	$dbQuery = sprintf("INSERT INTO `group_usernames` (`group_id`, `username`) VALUES ('%s', '%s');",
		mysql_real_escape_string($group_id),
		mysql_real_escape_string($username));
	$result = getDBResultInserted($dbQuery);
	header("Content-type: application/json");
}

function addGroupToUsername($group_id, $username) {
	addUsernameToGroup($username, $group_id);
}

function deleteUsernameFromGroup($username, $group_id) {
	$dbQuery = sprintf("DELETE FROM group_usernames WHERE username=%s AND group_id=%s;",
		mysql_real_escape_string($username),
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteGroupFromUsername($group_id, $username) {
	deleteUsernameFromGroup($username, $group_id);
}

function deleteAllUsernamesFromGroup($group_id) {
	$dbQuery = sprintf("DELETE FROM group_usernames WHERE group_id=%s;", 
		mysql_real_escape_string($group_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getUsernameFromGroup($username, $group_id) {
	$dbQuery = sprintf("SELECT `group_id`,`usernames` FROM group_usernames WHERE `username`='%s' AND `group_id`='%s';",
		mysql_real_escape_string($username),
		mysql_real_escape_string($group_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getGroupFromUsername($group_id, $username) {
	getUsernameFromGroup( $username, $group_id);
}