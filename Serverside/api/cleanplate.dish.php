<?php

	function listDishes() {
		$dbQuery = sprintf("SELECT 	`dishes`.`dish_id`,
									`dishes`.`dish_name`,
									`dishes`.`dish_description`,
									`dishes`.`dish_price`,
									`dishes`.`dish_image_url`,
									`dishes`.`dish_eaten`,
									`dishes`.`dish_points` 
							FROM dishes;");
		$result = getDBResultsArray($dbQuery);

		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getDish($dish_id) {
		$dbQuery = sprintf("SELECT 	`dishes`.`dish_id`,
									`dishes`.`dish_name`,
									`dishes`.`dish_description`,
									`dishes`.`dish_price`,
									`dishes`.`dish_image_url`,
									`dishes`.`dish_eaten`,
									`dishes`.`dish_points` 
							FROM dishes
							WHERE dish_id = '%s';",
				mysql_real_escape_string($dish_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function addDish($dish_name, $dish_description, $dish_price, $dish_image_url) {
		$dbQuery = sprintf("INSERT INTO `dishes` (`dish_name`,`dish_description`,`dish_price`,`dish_image_url` ) VALUES ('%s','%s','%s','%s')",
			mysql_real_escape_string($dish_name),
			mysql_real_escape_string($dish_description),
			mysql_real_escape_string($dish_price),
			mysql_real_escape_string($dish_image_url)
		);
		$result = getDBResultInserted($dbQuery,'dish_id');
		//header("Content-type: application/json");
		echo json_encode($result);
	}



	function getRandomDish() {
		$dbQuery = "SELECT 	`dishes`.`dish_id`,
							`dishes`.`dish_name`,
							`dishes`.`dish_description`,
							`dishes`.`dish_price`,
							`dishes`.`dish_image_url`,
							`dishes`.`dish_eaten`,
							`dishes`.`dish_points` 
					FROM dishes
					ORDER BY RAND()
					LIMIT 1;";
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getRandomOtherDish( $original_dish_id ) {
		$dbQuery = sprintf("SELECT 	`dishes`.`dish_id`,
									`dishes`.`dish_name`,
									`dishes`.`dish_description`,
									`dishes`.`dish_price`,
									`dishes`.`dish_image_url`,
									`dishes`.`dish_eaten`,
									`dishes`.`dish_points`
							FROM `dishes` 
							WHERE `dishes`.`dish_id` != '%s'
							ORDER BY RAND()
							LIMIT 1;",
				mysql_real_escape_string($original_dish_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

	function getRandomOtherDishFromMenu( $original_dish_id ) {
		$dbQuery = sprintf("SELECT  `foodmenu_dishes`.`foodmenu_id`,
									`foodmenu_dishes`.`dish_id`
							FROM `foodmenu_dishes`
							WHERE `foodmenu_dishes`.`dish_id`= '%s';",
						mysql_real_escape_string($original_dish_id));
		$result = getDBResultRecord($dbQuery, 'foodmenu_id');

		$dbQuery = sprintf("SELECT 	`dishes`.`dish_id`,
									`dishes`.`dish_name`,
									`dishes`.`dish_description`,
									`dishes`.`dish_price`,
									`dishes`.`dish_image_url`,
									`dishes`.`dish_eaten`,
									`dishes`.`dish_points` 
							FROM `dishes` INNER JOIN `foodmenu_dishes`
								ON `dishes`.`dish_id`=`foodmenu_dishes`.`dish_id`
							WHERE `foodmenu_dishes`.`foodmenu_id` = '%s'
								AND `dishes`.`dish_id` != '%s'
							ORDER BY RAND()
							LIMIT 1;",
				mysql_real_escape_string($result['foodmenu_id']),
				mysql_real_escape_string($original_dish_id));
		$result = getDBResultRecord($dbQuery);
		//header('Content-type: application/json');
		echo json_encode($result);
	}

?>
