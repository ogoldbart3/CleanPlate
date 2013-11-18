<?php

function listCollections() {
	$dbQuery = sprintf("SELECT `collections`.`collection_id`, `collections`.`collection_name`, count(`tour_id`) AS `collection_tours_count` FROM `collections` LEFT JOIN `collection_tours` ON `collections`.`collection_id` = `collection_tours`.`collection_id` GROUP BY `collections`.`collection_id` ORDER by `collections`.`collection_id`");
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addCollection($collection_name) {
	$dbQuery = sprintf("INSERT INTO collections (`collection_name`) VALUES ('%s')",
		mysql_real_escape_string($collection_name)
	);
	$result = getDBResultInserted($dbQuery,'collection_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

function updateCollection($collection_id, $collection_name) {
	checkCurrentUserCanModify('collections', $collection_id);
	$dbQuery = sprintf("UPDATE collections SET collection_name = '%s' WHERE collection_id = '%s'",
			mysql_real_escape_string($collection_name),
			mysql_real_escape_string($collection_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getCollection($collection_id) {

	$dbQuery = sprintf("SELECT collection_id, collection_name FROM collections WHERE collection_id = '%s'",
			mysql_real_escape_string($collection_id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteCollection($collection_id) {
	checkCurrentUserCanModify('collection', $collection_id);
	deleteAllToursFromCollection($collection_id);
	$dbQuery = sprintf("DELETE FROM collections WHERE collection_id = '%s'",
			mysql_real_escape_string($collection_id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

/****************************************************************************************/

function listToursByCollection($collection_id) {
	$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`title`, `tours`.`description`, `tours`.`route` FROM collection_tours INNER JOIN tours ON `collection_tours`.`tour_id`=`tours`.`tour_id` WHERE `collection_tours`.`collection_id`='%s';",
		mysql_real_escape_string($collection_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addTourToCollection($tour_id, $collection_id) {
	$dbQuery = sprintf("INSERT INTO `collection_tours` (`collection_id`, `tour_id`) VALUES ('%s', '%s');",
		mysql_real_escape_string($collection_id),
		mysql_real_escape_string($tour_id));
	$result = getDBResultInserted($dbQuery);
	header("Content-type: application/json");
}

function deleteTourFromCollection($tour_id, $collection_id) {
	checkCurrentUserCanModify('collection', $collection_id);
	$dbQuery = sprintf("DELETE FROM collection_tours WHERE tour_id=%s AND collection_id=%s;",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($collection_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteAllToursFromCollection($collection_id) {
	checkCurrentUserCanModify('collection', $collection_id);
	$dbQuery = sprintf("DELETE FROM collection_tours WHERE collection_id=%s;", 
		mysql_real_escape_string($collection_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getTourFromCollection($tour_id, $collection_id) {
	$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`title`, `tours`.`description` FROM collection_tours INNER JOIN tours ON `collection_tours`.`tour_id`=`tours`.`tour_id` WHERE `collection_tours`.`tour_id`='%s' AND `collection_tours`.`collection_id`='%s';",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($collection_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

/**************************************************************************************************************************************************************/
function autolistCollectionsByUsername() {
	global $_USER;
	$dbQuery = sprintf("SELECT DISTINCT `collections`.`collection_id`, `collections`.`collection_name` FROM `group_collections` INNER JOIN `collections` ON `group_collections`.`collection_id`=`collections`.`collection_id` INNER JOIN `group_usernames` ON `group_collections`.`group_id`=`group_usernames`.`group_id` WHERE `group_usernames`.`username`='%s';",
		mysql_real_escape_string($_USER['uid']));

	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}
?>
