<?php
function listPhotos() {
	$dbQuery = sprintf("SELECT `photo_id`,`image_url`,`base_caption` AS `caption` FROM `photos`");
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPhoto($photo_id) {
	$dbQuery = sprintf("SELECT `photo_id`,`image_url`,`base_caption` AS `caption` FROM `photos` WHERE `photo_id` = '%s'",
		mysql_real_escape_string($photo_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPrevPhoto($photo_id) {
	$dbQuery = sprintf("SELECT `photo_id`,`image_url`,`base_caption` AS `caption` FROM `photos` WHERE `photo_id` < '%s' ORDER BY `photo_id` DESC LIMIT 1",
			mysql_real_escape_string($photo_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getNextPhoto($photo_id) {
	$dbQuery = sprintf("SELECT `photo_id`,`image_url`,`base_caption` AS `caption` FROM `photos` WHERE `photo_id` > '%s' ORDER BY `photo_id` LIMIT 1",
			mysql_real_escape_string($photo_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//

function listPhotoPois($photo_id,$useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT `poi_id`,`title`, `base_description` AS `description`, `base_image_url` AS `image_url` FROM `poi_photos` JOIN `pois` USING(`poi_id`) WHERE `photo_id` = '%s'", $photo_id);
	}else{
		$dbQuery = sprintf("SELECT `poi_id`,`title` FROM `poi_photos` JOIN `pois` USING(`poi_id`) WHERE `photo_id` = '%s'", $photo_id);
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPhotoPoi($photo_id,$poi_id) {
	$dbQuery = sprintf("SELECT `poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url` FROM `poi_photos` JOIN `pois` USING(`poi_id`) WHERE `poi_id` = '%s' AND `photo_id` = '%s'",
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($photo_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}
/* ADD, UPDATE, AND DELETE CAN BE SEEN IN Poi/Photo */

//*************************************************************************************************//
function getPhotoImage($photo_id){
	$dbQuery = sprintf("SELECT `mime_type`,`blob` FROM photo_images WHERE photo_id = '%s'", $photo_id);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('Content-type: '.$image['mime_type']);
	$GLOBALS["_PLATFORM"]->sandboxHeader('Content-Disposition: inline');
	echo $image['blob'];
}

function getPoiPhotoImage($poi_id,$photo_id){
	$dbQuery = sprintf("SELECT `mime_type`,`blob` FROM photo_images JOIN poi_photos USING (photo_id) WHERE poi_id = '%s' AND photo_id = '%s'", $poi_id,$photo_id);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('Content-type: '.$image['mime_type']);
	$GLOBALS["_PLATFORM"]->sandboxHeader('Content-Disposition: inline');
	echo $image['blob'];
}
//*************************************************************************************************//

//*************************************************************************************************//
//***************************************NOT SURE BELOW********************************************//
//*************************************************************************************************//

function addTagForPhoto($id,$globaltag){
	$dbQuery = sprintf("INSERT INTO PhotoTag (photo_id,globaltag) VALUES ('%s','%s')",
			mysql_real_escape_string($id),
			mysql_real_escape_string($globaltag));
	$result = getDBResultInserted($dbQuery,'globaltag');
	header("Content-type: application/json");
	echo json_encode($result);
}


function deleteTag($id,$tag){
	$dbQuery = sprintf("DELETE FROM PhotoTag WHERE photo_id = '%s' AND globaltag = '%s'",
			mysql_real_escape_string($id),
			mysql_real_escape_string($tag));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getAllTagsForPhoto($id){
	$dbQuery = sprintf("SELECT photo_id,globaltag FROM PhotoTag WHERE photo_id = '%s'",
			mysql_real_escape_string($id));
	$result = getMultipleRecords($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

?>