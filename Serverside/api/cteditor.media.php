<?php
function listMediaItems() {
	$dbQuery = sprintf("SELECT `media_item_id`,`content_url`,`thumbnail_url`,`base_caption` AS `caption` FROM `media_items`");
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addMediaItem($mediaFiles) {	
	if($mediaFiles["error"][0] == 0){
		$extension = extensionForMimeType($mediaFiles["type"][0]);
		$web_sha1_name = storeFileInShareFS($mediaFiles["tmp_name"][0],$extension);
		
		$dbQuery = sprintf("INSERT INTO media_items (`content_url`) VALUES ('%s')",
			$web_sha1_name
		);	
		$result = getDBResultInserted($dbQuery,'media_item_id');				
	}
	header("Content-type: application/json");
	echo json_encode($result);
}

function getMediaItem($media_item_id) {
	$dbQuery = sprintf("SELECT `media_item_id`,`content_url`,`thumbnail_url`,`base_caption` AS `caption` FROM `media_items` WHERE `media_item_id` = '%s'",
		mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getPrevMediaItem($media_item_id) {
	$dbQuery = sprintf("SELECT `media_item_id`,`content_url`,`thumbnail_url`,`base_caption` AS `caption` FROM `media_items` WHERE `media_item_id` < '%s' ORDER BY `media_item_id` DESC LIMIT 1",
			mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getNextMediaItem($media_item_id) {
	$dbQuery = sprintf("SELECT `media_item_id`,`content_url`,`thumbnail_url`,`base_caption` AS `caption` FROM `media_items` WHERE `media_item_id` > '%s' ORDER BY `media_item_id` LIMIT 1",
			mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//

function listMediaItemPois($media_item_id,$useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT `poi_id`,`title`, `base_description` AS `description`, `base_image_url` AS `image_url` FROM `poi_media_items` JOIN `pois` USING(`poi_id`) WHERE `media_item_id` = '%s'", $media_item_id);
	}else{
		$dbQuery = sprintf("SELECT `poi_id`,`title` FROM `poi_media_items` JOIN `pois` USING(`poi_id`) WHERE `media_item_id` = '%s'", $media_item_id);
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getMediaItemPoi($media_item_id,$poi_id) {
	$dbQuery = sprintf("SELECT `poi_id`,`title`,`base_description` AS `description`, `base_image_url` AS `image_url` FROM `poi_media_items` JOIN `pois` USING(`poi_id`) WHERE `poi_id` = '%s' AND `media_item_id` = '%s'",
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}
/* ADD, UPDATE, AND DELETE CAN BE SEEN IN Poi/MediaItem */

//*************************************************************************************************//
function getMediaItemContent($media_item_id){
	$dbQuery = sprintf("SELECT `content_url` FROM `media_items` WHERE media_item_id = '%s'", $media_item_id);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$image['content_url']);
}

function getPoiMediaItemContent($poi_id,$media_item_id){
	$dbQuery = sprintf("SELECT `content_url` FROM media_items JOIN poi_media_items USING (media_item_id) WHERE poi_id = '%s' AND media_item_id = '%s'", $poi_id,$media_item_id);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$image['content_url']);
}
//*************************************************************************************************//
function getMediaItemThumbnail($media_item_id){
	$dbQuery = sprintf("SELECT `thumbnail_url` FROM `media_items` WHERE media_item_id = '%s'", $media_item_id);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$image['thumbnail_url']);
}

function getPoiMediaItemThumbnail($poi_id,$media_item_id){
	$dbQuery = sprintf("SELECT `thumbnail_url` FROM media_items JOIN poi_media_items USING (media_item_id) WHERE poi_id = '%s' AND media_item_id = '%s'", $poi_id,$media_item_id);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$image['thumbnail_url']);
}
//*************************************************************************************************//

//*************************************************************************************************//
//***************************************NOT SURE BELOW********************************************//
//*************************************************************************************************//

function addTagForMediaItem($id,$globaltag){
	$dbQuery = sprintf("INSERT INTO media_item_tags (media_item_id,globaltag) VALUES ('%s','%s')",
			mysql_real_escape_string($id),
			mysql_real_escape_string($globaltag));
	$result = getDBResultInserted($dbQuery,'globaltag');
	header("Content-type: application/json");
	echo json_encode($result);
}


function deleteTag($id,$tag){
	$dbQuery = sprintf("DELETE FROM media_item_tags WHERE media_item_id = '%s' AND globaltag = '%s'",
			mysql_real_escape_string($id),
			mysql_real_escape_string($tag));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getAllTagsForMediaItem($id){
	$dbQuery = sprintf("SELECT media_item_id,globaltag FROM media_item_tags WHERE media_item_id = '%s'",
			mysql_real_escape_string($id));
	$result = getMultipleRecords($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

?>