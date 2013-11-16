<?php

	function listRestaurants() {
		$dbQuery = sprintf("SELECT 	`restaurants`.`restaurant_id`,
									`restaurants`.`restaurant_name`, 
									`restaurants`.`restaurant_address`, 
									`restaurants`.`restaurant_email`, 
									`restaurants`.`restaurant_phone_number` 
							FROM restaurants;");
		$result = getDBResultsArray($dbQuery);

		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getRestaurant($restaurant_id) {
		$dbQuery = sprintf("SELECT 	`restaurants`.`restaurant_id`, 
									`restaurants`.`restaurant_name`, 
									`restaurants`.`restaurant_address`, 
									`restaurants`.`restaurant_email`, 
									`restaurants`.`restaurant_phone_number` 
							FROM restaurants
							WHERE restaurant_id = '%s';",
				mysql_real_escape_string($restaurant_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function listRestaurantFoodmenus($restaurant_id) {
		$dbQuery = sprintf("SELECT 	`foodmenus`.`foodmenu_id`,
									`foodmenus`.`foodmenu_start`,
									`foodmenus`.`foodmenu_end`,
									`foodmenus`.`foodmenu_days` 
							FROM `restaurant_foodmenus` INNER JOIN `foodmenus` ON `restaurant_foodmenus`.`foodmenu_id`=`foodmenus`.`foodmenu_id`
							WHERE `restaurant_foodmenus`.`restaurant_id`='%s';",
				mysql_real_escape_string($restaurant_id));
		$result = getDBResultsArray($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getRestaurantFoodmenu($restaurant_id, $foodmenu_id) {
		$dbQuery = sprintf("SELECT 	`foodmenus`.`foodmenu_id`,
									`foodmenus`.`foodmenu_start`,
									`foodmenus`.`foodmenu_end`,
									`foodmenus`.`foodmenu_days` 
							FROM `restaurant_foodmenus` INNER JOIN `foodmenus` ON `restaurant_foodmenus`.`foodmenu_id`=`foodmenus`.`foodmenu_id`
							WHERE `restaurant_foodmenus`.`restaurant_id`='%s'
								AND `restaurant_foodmenus`.`foodmenu_id`='%s';",
				mysql_real_escape_string($restaurant_id),
				mysql_real_escape_string($foodmenu_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

?>
