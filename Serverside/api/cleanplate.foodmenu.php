<?php

	function listFoodmenus() {
		$dbQuery = sprintf("SELECT 	`foodmenus`.`foodmenu_id`,
									`foodmenus`.`foodmenu_start`, 
									`foodmenus`.`foodmenu_end`, 
									`foodmenus`.`foodmenu_days` 
							FROM foodmenus;");
		$result = getDBResultsArray($dbQuery);

		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getFoodmenu($foodmenu_id) {
		$dbQuery = sprintf("SELECT 	`foodmenus`.`foodmenu_id`, 
									`foodmenus`.`foodmenu_start`, 
									`foodmenus`.`foodmenu_end`, 
									`foodmenus`.`foodmenu_days` 
							FROM foodmenus
							WHERE foodmenu_id = '%s';",
				mysql_real_escape_string($foodmenu_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function addFoodmenu($foodmenu_start, $foodmenu_end, $foodmenu_days) {
		$dbQuery = sprintf("INSERT INTO `foodmenus` (`foodmenu_start`,`foodmenu_end`,`foodmenu_days` ) VALUES ('%s','%s','%s')",
			mysql_real_escape_string($foodmenu_start),
			mysql_real_escape_string($foodmenu_end),
			mysql_real_escape_string($foodmenu_days)
		);
		$result = getDBResultInserted($dbQuery,'foodmenu_id');
		//header("Content-type: application/json");
		echo json_encode($result);
	}

/************************************************************************************************************************************************/



	function listFoodmenuDishes($foodmenu_id) {
		$dbQuery = sprintf("SELECT 	`dishes`.`dish_id`,
									`dishes`.`dish_name`,
									`dishes`.`dish_description`,
									`dishes`.`dish_price`,
									`dishes`.`dish_image_url`,
									`dishes`.`dish_eaten`,
									`dishes`.`dish_points` 
							FROM `foodmenu_dishes` INNER JOIN `dishes` ON `foodmenu_dishes`.`dish_id`=`dishes`.`dish_id`
							WHERE `foodmenu_dishes`.`foodmenu_id`='%s';",
				mysql_real_escape_string($foodmenu_id));
		$result = getDBResultsArray($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getFoodmenuDish($foodmenu_id, $dish_id) {
		$dbQuery = sprintf("SELECT 	`dishes`.`dish_id`,
									`dishes`.`dish_name`,
									`dishes`.`dish_description`,
									`dishes`.`dish_price`,
									`dishes`.`dish_image_url`,
									`dishes`.`dish_eaten`,
									`dishes`.`dish_points`
							FROM `foodmenu_dishes` INNER JOIN `dishes` ON `foodmenu_dishes`.`dish_id`=`dishes`.`dish_id`
							WHERE `foodmenu_dishes`.`foodmenu_id`='%s'
								AND `foodmenu_dishes`.`dish_id`='%s';",
				mysql_real_escape_string($foodmenu_id),
				mysql_real_escape_string($dish_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function addFoodmenuDish($foodmenu_id, $dish_id) {
		$dbQuery = sprintf("INSERT INTO `foodmenu_dishes` (`foodmenu_id`,`dish_id` ) VALUES ('%s','%s')",
			mysql_real_escape_string($foodmenu_id),
			mysql_real_escape_string($dish_id)
		);
		$result = getDBResultInserted($dbQuery,'foodmenu_id');
		//header("Content-type: application/json");
		echo json_encode($result);
	}


?>
