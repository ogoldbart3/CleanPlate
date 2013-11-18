<?php

function addPoiPhoto($poi_id, $file_post) {
	error_log("adding photo for ".$poi_id);
	$photoid = getNewPhotoIdForPoi($poi_id);
	error_log("adding photo for ".$poi_id." photoid = ".$photoid);
	$result[0] = array("error" => "Not a valid object");
	for ($i = 0;$i < count($file_post["error"]); $i++) {
		$result[$i] = array("error" => "none");
		$error = $file_post["error"][$i];
		if($error == 4) { //No image uploaded by user
			error_log("no photo for ".$poi_id);
			$result[$i] = array("error" => 'No image uploaded');
		}
		else {
			$fType = $file_post["type"][$i];
			if ((($fType == "image/gif")|| ($fType == "image/png") ||
				($fType == "image/jpg")|| ($fType == "image/jpeg")||
				($fType == "image/pjpeg"))) {
				error_log("k");
				if ($error > 0) { // Invalid file passed
					$result[$i] = array("error" => $error);
					error_log("invalid photo for ".$poi_id);
					//disable confirm button
				}
				else { // store file in temporary folder "upload"
					$img = $file_post["name"][$i];
					$fname = "upload/".$img;
					$imgPath = "upload";
					if (file_exists($fname)) {
						$result[$i] = array("error" => "none");
						error_log("existing photo for ".$poi_id);
						//disable confirm button
					}
					else {
						$success = rename($file_post["tmp_name"][$i],"upload/" . $img);
						$fp = fopen($fname, "r");
						$data = addslashes(fread($fp, filesize($fname)));
						error_log("INSERT");
						$dbQuery = sprintf("INSERT INTO `PoiPhotos`(`photoid`,
								`poi_id`, `photo`, `mimeType`)
								VALUES (%s,%s,'%s','%s')", $photoid, $poi_id,
								$data, $fType);
					try {
						$ret = getDBResultInserted($dbQuery, NULL, false);
					} catch (Exception $e) {
						error_log($poi_id);
						error_log($e->getMessage());
					}
					unlink($fname);
					}
					$result[$i]["fileName"] = $fname;
				}
			}
		}
	}
}


function addPhoto($id,$title,$latitude, $internaldescrip, $longitude,$altitude, $globaltag, $externaldescrip) {
	$dbQuery = sprintf("INSERT INTO PhotoArchive (id, title, internaldescrip, latitude, longitude, altitude, globaltag, externaldescrip) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')",
			mysql_real_escape_string($id),
			mysql_real_escape_string($title),
			mysql_real_escape_string($internaldescrip),
			mysql_real_escape_string($latitude),
			mysql_real_escape_string($longitude),
			mysql_real_escape_string($altitude),
			mysql_real_escape_string($globaltag),
			mysql_real_escape_string($externaldescrip));		
	$result = getDBResultInserted($dbQuery,'personId');
	header("Content-type: application/json");
	echo json_encode($result);
}

function updatePhoto($id,$title,$internaldescrip, $latitude,$longitude,$altitude,$globaltag, $externaldescrip) {
	$dbQuery = sprintf("UPDATE PhotoArchive SET title = '%s', internaldescrip='%s', latitude = '%s', longitude='%s', altitude='%s', globaltag='%s', externaldescrip='%s' WHERE id = '%s'",
			mysql_real_escape_string($title),
			mysql_real_escape_string($internaldescrip),
			mysql_real_escape_string($latitude),
			mysql_real_escape_string($longitude),
			mysql_real_escape_string($altitude),
			mysql_real_escape_string($globaltag),
			mysql_real_escape_string($externaldescrip),
			mysql_real_escape_string($id));
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

function deletePhoto($id) {
	$dbQuery = sprintf("DELETE FROM PhotoArchive WHERE id = '%s'",
			mysql_real_escape_string($id));												
	$result = getDBResultAffected($dbQuery);
	header("Content-type: application/json");
	echo json_encode($result);
}

?>