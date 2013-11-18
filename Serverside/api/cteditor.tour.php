<?php

function listTours() {
	
	if ( isset($_GET["editable"] ) ) {
		if ( $_GET["editable"] == 1 ) {
		
		}

		else {

			$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`title`, count(`tour_panoramas`.`panorama_id`) AS `tour_panoramas_count` FROM `tours` LEFT JOIN `tour_panoramas` ON `tours`.`tour_id`=`tour_panoramas`.`tour_id` GROUP BY `tours`.`tour_id` ORDER BY `tours`.`tour_id`;");
			$result = getDBResultsArray($dbQuery);
			header("Content-type: application/json");
			echo json_encode($result);
		}
	}
	else {

		$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`title`, count(`tour_panoramas`.`panorama_id`) AS `tour_panoramas_count` FROM `tours` LEFT JOIN `tour_panoramas` ON `tours`.`tour_id`=`tour_panoramas`.`tour_id` GROUP BY `tours`.`tour_id` ORDER BY `tours`.`tour_id`;");
		$result = getDBResultsArray($dbQuery);
		header("Content-type: application/json");
		echo json_encode($result);
	}
}


/*function listTours() {
	$dbQuery = sprintf("SELECT `tours`.`tour_id`, `tours`.`tour_name`, count(`panorama_id`) AS `tour_panoramas_count` FROM `tours` LEFT JOIN `tour_panoramas` ON `tours`.`tour_id` = `tour_panoramas`.`tour_id` GROUP BY `tours`.`tour_id` ORDER by `tours`.`tour_id`");
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}*/


function getTour($id) {
	$dbQuery = sprintf("SELECT tour_id, title, description, route FROM tours WHERE tour_id = '%s'",
			mysql_real_escape_string($id));
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addTour($title, $description, $group_id, $collection_id) {
	checkCurrentUserInGroup($group_id);
	$dbQuery = sprintf("INSERT INTO tours (`title`,`description`) VALUES ('%s','%s')",
		mysql_real_escape_string($title),
		mysql_real_escape_string($description)
	);	
	$tour_id = getDBResultInserted($dbQuery,'tour_id');
	addTourToGroup($tour_id["tour_id"], $group_id);
	addTourToCollection($tour_id["tour_id"], $collection_id);
	header("Content-type: application/json");
	echo json_encode($tour_id["tour_id"]);
}

/*  FIXXXXX */
function updateTour($tour_id, $title, $description) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("UPDATE tours SET title = '%s', description='%s' WHERE tour_id = '%s'",
			mysql_real_escape_string($title),
			mysql_real_escape_string($description),
			mysql_real_escape_string($tour_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteTour($tour_id) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("DELETE FROM tours WHERE tour_id = '%s'",
			mysql_real_escape_string($tour_id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//
function listTourRoute($tour_id) {
    $dbQuery = sprintf("SELECT `latitude` AS 'lat',`longitude` AS 'lng' FROM `tour_panoramas` JOIN `panoramas` USING(`panorama_id`) WHERE tour_id = '%s' ORDER BY `order_id` ", mysql_real_escape_string($tour_id));
	$panoramas = getDBResultsArray($dbQuery);
	
	$result = array();
	$prevElement = NULL;
	
	foreach($panoramas as $panorama){
		if($prevElement){
			$result[] = array(
				"start" => $prevElement,
				"end" => $panorama,
				"waypoints" => array()
			);
		}
		$prevElement = $panorama;
	}
	
	header("Content-type: application/json");
	echo json_encode($result);	
}

//*************************************************************************************************//
function listTourPanoOrder($tour_id) {
	$dbQuery = sprintf("SELECT panorama_id FROM tour_panoramas WHERE tour_id = '%s' AND order_id IS NOT NULL ORDER BY order_id ASC",
		mysql_real_escape_string($tour_id)
	);
	$temp = getDBResultsArray($dbQuery);
	
	//FLATTEN THE ARRAY
	$result = array();
	array_map(function($value) use (&$result){
		$result[] = $value['panorama_id'];
	},$temp);
	
	header("Content-type: application/json");
	echo json_encode($result); 
}

//*************************************************************************************************//
function listTourPanoramas($tour_id,$useFullRecord = FALSE) {

	if($useFullRecord){
		$dbQuery = sprintf("SELECT tour_id, panorama_id, title, description, latitude, longitude, altitude, heading, tilt, roll FROM tour_panoramas JOIN panoramas USING (panorama_id) WHERE tour_id = '%s' ORDER BY panorama_id",
			mysql_real_escape_string($tour_id)
		);
	}else{
		$dbQuery = sprintf("SELECT tour_id, panorama_id, title FROM tour_panoramas JOIN panoramas USING (panorama_id) WHERE tour_id = '%s' ORDER BY panorama_id",
			mysql_real_escape_string($tour_id)
		);
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);

}

function getTourPanorama($tour_id,$panorama_id) {
	$dbQuery = sprintf("SELECT tour_id, panorama_id, title, description, latitude, longitude, altitude, heading, tilt, roll  FROM tour_panoramas JOIN panoramas USING (panorama_id) WHERE tour_id = '%s' AND panorama_id = '%s'",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addTourPanorama($tour_id, $panorama_id) {

	checkCurrentUserCanModify('tour', $tour_id);
	
	$dbQuery = sprintf("SELECT `tour_panoramas`.`tour_id`, `tour_panoramas`.`panorama_id`, `tour_panoramas`.`order_id` FROM `tour_panoramas` WHERE `tour_panoramas`.`tour_id`='%s' ORDER BY `tour_panoramas`.`tour_id` ASC,`tour_panoramas`.`order_id` DESC LIMIT 1;",
		mysql_real_escape_string($tour_id));
	$result = getSingleRecord($dbQuery, 'order_id');
	$order_id = $result["order_id"] + 1;

	$dbQuery = sprintf("INSERT INTO tour_panoramas (`tour_id`,`panorama_id`,`order_id`) VALUES ('%s','%s','%s')",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($order_id)
	);
	$result = getDBResultInserted($dbQuery,'tour_panorama_id');
	header("Content-type: application/json");
	echo json_encode($result);

	updateTourRoute( $tour_id );

}

function updateTourRoute( $tour_id ) {

		$dbQuery = sprintf("SELECT `tour_panoramas`.`panorama_id`, `tour_panoramas`.`order_id`, `panoramas`.`latitude`, `panoramas`.`longitude` 
		FROM `tour_panoramas`
		INNER JOIN `panoramas`
		ON `tour_panoramas`.`panorama_id`=`panoramas`.`panorama_id`
		WHERE `tour_panoramas`.`tour_id`='%s'
		ORDER BY `tour_panoramas`.`tour_id` ASC,`tour_panoramas`.`order_id`;",
		mysql_real_escape_string($tour_id)
	);
	

	$routeArray = getDBResultsArray($dbQuery);

//	$route = $routeArray[0];

	$waypointsCount = count($routeArray);

	$formattedRoute =
		'{ "routeLegs": [ { "start": { "lat":'.
		($routeArray[0]["latitude"]).
		',"lng":'.
		($routeArray[0]["longitude"]).
		'},';

	for ( $i = 1; $i < $waypointsCount - 1; $i++) {
		$formattedRoute .=
			'"end": { "lat": ' .
			( $routeArray[$i]["latitude"] ) .
			', "lng":' .
			( $routeArray[$i]["longitude"] ) .
			'}, "waypoints":[] }, { "start": { "lat": ' .
			( $routeArray[$i]["latitude"] ) .
			', "lng": ' .
			( $routeArray[$i]["longitude"] ) .
			'},';
	}

	$formattedRoute .=
		'"end": { "lat": ' .
		( $routeArray[$waypointsCount - 1]["latitude"] ) .
		', "lng":' .
		( $routeArray[$waypointsCount - 1]["longitude"] ) .
		'}, "waypoints":[] } ] }';

	$dbQuery = sprintf("UPDATE tours SET route='%s' WHERE tour_id = '%s'",
			mysql_real_escape_string($formattedRoute),
			mysql_real_escape_string($tour_id) );

	$routeUpdateResult = getDBResultAffected($dbQuery);
}	


function deleteTourPanorama($tour_id,$panorama_id) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("DELETE FROM tour_panoramas WHERE tour_id = '%s' AND panorama_id = '%s'",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);

	updateTourRoute( $tour_id );

}

//*************************************************************************************************//

function listRemainingTourPanoramas($tour_id){
	$dbQuery = sprintf("SELECT '%s' AS tour_id, panorama_id, title FROM panoramas WHERE panorama_id NOT IN (SELECT panorama_id FROM tour_panoramas WHERE tour_id = '%s') ORDER BY panorama_id",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($tour_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//
function listTourPanoramaPois($tour_id,$panorama_id,$useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT `tour_id`, `panorama_id`, `poi_id`, `title`, CASE WHEN ISNULL(`override_description`) THEN `base_description` ELSE `override_description`END AS `description`, CASE WHEN ISNULL(`override_image_url`) THEN `base_image_url` ELSE `override_image_url`END AS `image_url`, `latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll` FROM `tour_panorama_pois` JOIN `panorama_pois` USING (`panorama_id`,`poi_id`) JOIN `pois` USING (`poi_id`) WHERE `tour_id` = '%s' AND `panorama_id` = '%s' ORDER BY poi_id",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id)
		);
	}else{
		$dbQuery = sprintf("SELECT `tour_id`, `panorama_id`, poi_id, title FROM tour_panorama_pois JOIN pois USING (poi_id) WHERE tour_id = '%s' AND panorama_id = '%s' ORDER BY poi_id",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id)
		);
	}
	
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getTourPanoramaPoi($tour_id,$panorama_id,$poi_id) {
	$dbQuery = sprintf("SELECT `tour_id`, `panorama_id`, `poi_id`, `title`, CASE WHEN ISNULL(`override_description`) THEN `base_description` ELSE `override_description`END AS `description`, CASE WHEN ISNULL(`override_image_url`) THEN `base_image_url` ELSE `override_image_url`END AS `image_url`, `latitude`,`longitude`,`altitude`,`heading`,`tilt`,`roll` FROM `tour_panorama_pois` JOIN `panorama_pois` USING (`panorama_id`,`poi_id`) JOIN `pois` USING (`poi_id`) WHERE `tour_id` = '%s' AND `panorama_id` = '%s' AND `poi_id` = '%s'",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id)
       );
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addTourPanoramaPoi($tour_id, $panorama_id, $poi_id) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("INSERT INTO tour_panorama_pois (`tour_id`,`panorama_id`,`poi_id`) VALUES ('%s','%s','%s')",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id)
	);	
	$result = getDBResultInserted($dbQuery,'tour_panorama_poi_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

function updateTourPanoramaPoi($tour_id, $panorama_id, $poi_id, $description, $image_url) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("UPDATE tour_panorama_pois SET `title` = '%s', `override_description` = '%s', `override_image_url` = '%s' WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s'",
			mysql_real_escape_string($description),
			mysql_real_escape_string($image_url),
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function updateTourPanoramaPoiContent($description, $tour_id, $panorama_id, $poi_id) {
	checkCurrentUserCanModify('tour', $tour_id);
	if($description){
		$dbQuery = sprintf("UPDATE tour_panorama_pois SET `override_description` = '%s' WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s'",
				mysql_real_escape_string($description),
				mysql_real_escape_string($tour_id),
				mysql_real_escape_string($panorama_id),
				mysql_real_escape_string($poi_id));
	}else{
		$dbQuery = sprintf("UPDATE tour_panorama_pois SET `override_description` = NULL WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s'",
				mysql_real_escape_string($tour_id),
				mysql_real_escape_string($panorama_id),
				mysql_real_escape_string($poi_id));	
	}
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteTourPanoramaPoi($tour_id, $panorama_id, $poi_id) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("DELETE FROM tour_panorama_pois WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s'",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//

function listRemainingTourPanoramaPois($tour_id,$panorama_id){
	$dbQuery = sprintf("SELECT '%s' AS `tour_id`, '%s' AS `panorama_id`, poi_id, title FROM panorama_pois JOIN pois USING (poi_id) WHERE `panorama_id` = '%s' AND `poi_id` NOT IN (SELECT poi_id FROM tour_panorama_pois WHERE tour_id = '%s' AND panorama_id = '%s') ORDER BY poi_id",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//

function listTourPanoramaPoiMediaItems($tour_id,$panorama_id,$poi_id) {
	$dbQuery = sprintf("SELECT `media_item_id`, `content_url`, `thumbnail_url`, CASE WHEN ISNULL(`override_caption`) THEN `base_caption` ELSE `override_caption` END AS `caption` FROM `tour_panorama_poi_media_items` JOIN `media_items` USING(`media_item_id`) WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s'", 
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id)
	);
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function getTourPanoramaPoiMediaItem($tour_id,$panorama_id,$poi_id, $media_item_id) {
	$dbQuery = sprintf("SELECT `media_item_id`, `content_url`, `thumbnail_url`, CASE WHEN ISNULL(`override_caption`) THEN `base_caption` ELSE `override_caption` END AS `caption` FROM `tour_panorama_poi_media_items` JOIN `media_items` USING(`media_item_id`) WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s' AND `media_item_id` = '%s'",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultRecord($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addTourPanoramaPoiMediaItem($tour_id,$panorama_id,$poi_id,$media_item_id) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("INSERT INTO `tour_panorama_poi_media_items` (`tour_id`,`panorama_id`,`poi_id`,`media_item_id`) VALUES ('%s','%s','%s','%s')",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultInserted($dbQuery,'tour_panorama_poi_media_item_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

function updatePoiMediaItem($tour_id, $poi_id, $media_item_id, $caption) {
	checkCurrentUserCanModify('tour', $tour_id);
	$dbQuery = sprintf("UPDATE `tour_panorama_poi_media_items` SET `override_caption` = '%s', WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s' AND `media_item_id` = '%s'",
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($media_item_id)
	);
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}
//*************************************************************************************************//
function getTourMediaItemContent($tour_id,$panorama_id,$poi_id, $media_item_id){
	$dbQuery = sprintf("SELECT `content_url` FROM `tour_panorama_poi_media_items` JOIN `media_items` USING(`media_item_id`) WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s' AND `media_item_id` = '%s'", 
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($media_item_id)
	);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$image['content_url']);
}
function getTourMediaItemThumbnail($tour_id,$panorama_id,$poi_id, $media_item_id){
	$dbQuery = sprintf("SELECT `thumbnail_url` FROM `tour_panorama_poi_media_items` JOIN `media_items` USING(`media_item_id`) WHERE tour_id = '%s' AND panorama_id = '%s' AND `poi_id` = '%s' AND `media_item_id` = '%s'", 
		mysql_real_escape_string($tour_id),
		mysql_real_escape_string($panorama_id),
		mysql_real_escape_string($poi_id),
		mysql_real_escape_string($media_item_id)
	);
	$image = getDBResultRecord($dbQuery);

	$GLOBALS["_PLATFORM"]->sandboxHeader('HTTP/1.1 302 Found');
	$GLOBALS["_PLATFORM"]->sandboxHeader('Location: '.$image['thumbnail_url']);
}
//*************************************************************************************************//

function listRemainingTourPanoramaPoiMediaItems($tour_id,$panorama_id,$poi_id){
	$dbQuery = sprintf("SELECT `media_item_id`, `content_url`, `thumbnail_url`, CASE WHEN ISNULL(`override_caption`) THEN `base_caption` ELSE `override_caption` END AS `caption` FROM `tour_panorama_poi_media_items` JOIN `media_items` USING(`media_item_id`) WHERE media_item_id NOT IN (SELECT media_item_id FROM tour_panorama_poi_media_items WHERE tour_id = '%s' AND panorama_id = '%s' AND poi_id = '%s') ORDER BY media_item_id",
			mysql_real_escape_string($tour_id),
			mysql_real_escape_string($panorama_id),
			mysql_real_escape_string($poi_id));
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//
function listTags() {
	$dbQuery = sprintf("SELECT `tag_id`, `tag_name` FROM `tags`"
	);
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);

}

function listPoiTags($poi_id,$useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT `tag_id`,`tag_name` FROM `tags` WHERE `tag_id` IN (SELECT `tag_id` FROM `poi_media_items` JOIN `media_item_tags` USING (`media_item_id`) WHERE `poi_id` = '%s') ORDER BY `tag_id` ASC",
			mysql_real_escape_string($poi_id)
				
		);
	}else{
		$dbQuery = sprintf("SELECT `tag_id`, `tag_name` FROM `poi_media_items` JOIN `media_item_tags` USING (`media_item_id`) JOIN `tags` USING (`tag_id`) WHERE `poi_id` = '%s' ORDER BY tag_name",
			mysql_real_escape_string($poi_id)
		);
	}

	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//

function listPoiMediaItems($poi_id,$useFullRecord = FALSE) {
	if($useFullRecord){
		$dbQuery = sprintf("SELECT poi_id, media_item_id, content_url, thumbnail_url, base_caption FROM poi_media_items JOIN media_items USING (media_item_id) WHERE poi_id = '%s' ORDER BY media_item_id",
			mysql_real_escape_string($poi_id)
		);
	}else{
		$dbQuery = sprintf("SELECT poi_id, media_item_id, thumbnail_url FROM poi_media_items JOIN media_items USING (media_item_id) WHERE poi_id = '%s' ORDER BY media_item_id",
			mysql_real_escape_string($poi_id)
		);
	}
	$result = getDBResultsArray($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function addPoiMediaItem($poi_id,$media_item_id){
	$dbQuery = sprintf("INSERT INTO poi_media_items (poi_id, media_item_id) VALUES ('%s','%s')",
			mysql_real_escape_string($poi_id),
			mysql_real_escape_string($media_item_id));
	$result = getDBResultInserted($dbQuery,'poi_media_item_id');
	header("Content-type: application/json");
	echo json_encode($result);
}

//*************************************************************************************************//



//*************************************************************************************************//
//*************************************************************************************************//
//**NOT SURE BELOW**//
//*************************************************************************************************//
//*************************************************************************************************//
function listTourPanos($tour_type) {
	$dbQuery = sprintf("SELECT PanoTour.panoramaid, title FROM PanoTour
			JOIN Panoramas WHERE type = '%s' AND
			PanoTour.panoramaid = Panoramas.panoramaid",
			mysql_real_escape_string($tour_type));
	$panos = getMultipleRecords($dbQuery);
	header("Content-type: application/json");
	echo json_encode($panos);
}

function addTypeForPano($id,$type){
	$dbQuery = sprintf("INSERT INTO PanoTour (type,panoramaid) VALUES ('%s','%s')",
			mysql_real_escape_string($type),
			mysql_real_escape_string($id));
	$result = getDBResultInserted($dbQuery,'globaltag');
	header("Content-type: application/json");
	echo json_encode($result);
}

function getAllTypesForPano($id){
	$dbQuery = sprintf("SELECT type,panoramaid FROM PanoTour WHERE panoramaid = '%s'",
			mysql_real_escape_string($id));
	$result = getMultipleRecords($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deleteType($id,$type){
	$dbQuery = sprintf("DELETE FROM PanoTour WHERE panoramaid = '%s' AND type = '%s'",
			mysql_real_escape_string($id),
			mysql_real_escape_string($type));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function listTourTypes() {
	$dbQuery = sprintf("SELECT DISTINCT type FROM PanoTour");
	$tour_types = getMultipleRecords($dbQuery);
	header("Content-type: application/json");
	echo json_encode($tour_types);
}

?>
