console.log('RUNNING CTEDITOR.TYPE JAVASCRIPT');

var numberOfEntryFields = 1;

function refreshTourList(){
	$.ajax({
		url: "api/dish/",
		dataType: "json",
		async: false,
		success: function(data, textStatus, jqXHR) {
			console.log(data);
			//Remove the old rows
			$( ".tour_list_row" ).remove();
			if(data!=null){
				//Create The New Rows From Template
				$( "#tour_list_row_template").tmpl( data ).appendTo( "#tour_list" );
			}
				$('#tour_list').listview('refresh');
			},
		error: function (jqXHR, textStatus, errorThrown){
			if(jqXHR.status == 404){
				//Remove the old rows
				$( ".tour_list_row" ).remove();
			}else{
					console.log("refresh tour list");
					ajaxError(jqXHR, textStatus, errorThrown);
			}
		}
	});

	console.log("failed before ajax");
}

//Rename

$(function() {
	// Handler for .ready() called.
	console.log('CTEDITOR.TYPE READY');

	$('#list_tour_page').bind('pagebeforeshow',function(event, ui){
		console.log('pagebeforeshow');

		//Remove the old rows
		$( ".tour_list_row" ).remove();

		//JQuery Fetch The New Ones
		$.ajax({
			url: "api/dish",
			dataType: "json",
			async: false,
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				//Create The New Rows From Template
				$( "#tour_list_row_template" ).tmpl( data ).appendTo( "#tour_list" );
			},
			error: ajaxError
		});

		$.ajax({
			url: "api/restaurant",
			dataType: "json",
        	async: false,
        	success: function(data, textStatus, jqXHR) {
				console.log("Dropdown menu data"+data);
				$(".group_option").remove();
        		$("#group_option_template").tmpl( data ).appendTo( "#group_select" );
        		$('#group_select').selectmenu({'refresh': true});
        	},
        	error: function (jqXHR, textStatus, errorThrown){
        	   	
        	   	console.log("Dropdown error before");
        	   	if(jqXHR.status != 404){
        	   		console.log("Dropdown error");
        	    	ajaxError(jqXHR, textStatus, errorThrown);
        	   	}
        	}
		});

		$.ajax({
			url: "api/autolistcollection",
			dataType: "json",
        	async: false,
        	success: function(data, textStatus, jqXHR) {
				console.log("Dropdown menu data"+data);
				$(".collection_option").remove();
        		$("#collection_option_template").tmpl( data ).appendTo( "#collection_select" );
        		$('#collection_select').selectmenu({'refresh': true});
        	},
        	error: function (jqXHR, textStatus, errorThrown){
        	   	
        	   	console.log("Dropdown error before");
        	   	if(jqXHR.status != 404){
        	   		console.log("Dropdown error");
        	    	ajaxError(jqXHR, textStatus, errorThrown);
        	   	}
        	}
		});

		$('#tour_list').listview('refresh');
	});
	
	$('#list_tour_panoramas_page').bind('pagebeforeshow',function(event, ui){
		var tour_id = $.url().fparam("tour_id");
		console.log('pagebeforeshow');

		refreshTourPanoramaList(tour_id);
	});
	
	//Cleanup of URL so we can have better client URL support
	$('#list_tour_panoramas_page').bind('pagehide', function() {
		$(this).attr("data-url",$(this).attr("type"));
		delete $(this).data()['url'];
	});
	
	//Cleanup of URL so we can have better client URL support
	$('#list_tour_panorama_pois_page').bind('pagehide', function() {
		$(this).attr("data-url",$(this).attr("type"));
		delete $(this).data()['url'];
	});
	
	//Cleanup of URL so we can have better client URL support
	$('#list_tour_panorama_poi_info_page').bind('pagehide', function() {
		$(this).attr("data-url",$(this).attr("type"));
		delete $(this).data()['url'];
	});
	
	/****************************************************************
	**Add Tour Button
	****************************************************************/
	
	 $('#new_tour_button').bind('click', function() {
		console.log("Add Tour Button");
		//var totalString = $('#')

		//var name = $('#new_tour_name').val();
		//var description = $('#new_tour_description').val();
		//var price = $('#new_tour_price').val();
		//var imageURL = $('#new_tour_image_url').val();
		//var group = $('#group_select').val();
		//var collection = $('#collection_select').val();

		var menuStart = $('#menu_start_time').value;
		var menuEnd = $('#menu_end_time').value;
		var menuDays = $('#menu_days').value;
		
		var menuID;
		var dishID;

		$.ajax({
			url: "api/foodmenu/",
			dataType: "json",
			data: {
				'foodmenu_start': menuStart,
				'foodmenu_end': menuEnd,
				'foodmenu_days': menuDays
			},
			async: false,
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				//refreshTourList();
				menuID = data['foodmenu_id'];
			},
			type: 'POST',
			error: ajaxError
		});
		//console.log(test);

		$(".new_tour_entry").each(function(){
        	//this.value = this.value.replace("AFFURL",producturl);
        	console.log(this.value);
        	var totalString = new String(this.value);
        	
        	var name = totalString.substring(0, totalString.indexOf(","));
        	totalString = totalString.slice(totalString.indexOf(",") + 2, totalString.length);

			var description = totalString.substring(0, totalString.indexOf(","));
        	totalString = totalString.slice(totalString.indexOf(",") + 2, totalString.length);

        	var price = totalString.substring(0, totalString.indexOf(","));
        	totalString = totalString.slice(totalString.indexOf(",") + 2, totalString.length);

        	var imageURL = totalString;

        	console.log(name + "-" + description + "-" + price + "-" + imageURL);

        	$.ajax({
				url: "api/dish/",
				dataType: "json",
				data: {
					'dish_name': name,
					'dish_description': description,
					'dish_price': price,
					'dish_image_url': imageURL
				},
				async: false,
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					//refreshTourList();
					dishID = data['dish_id'];
				},
				type: 'POST',
				error: ajaxError
			});

			$.ajax({
				url: "api/foodmenu_dish/",
				dataType: "json",
				data: {
					'foodmenu_id': menuID,
					'dish_id': dishID
				},
				async: false,
				success: function(data, textStatus, jqXHR) {
					console.log(data);
					//refreshTourList();
				},
				type: 'POST',
				error: ajaxError
			});




    	});



		/*$.ajax({
			url: "api/dish/",
			dataType: "json",
			data: {
				'dish_name': name,
				'dish_description': description,
				'dish_price': price,
				'dish_image_url': imageURL
			},
			async: false,
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				refreshTourList();
			},
			type: 'POST',
			error: ajaxError
		});*/
	});


	/****************************************************************
	**Add Entry Space Button
	****************************************************************/
	
	$('#new_entry_button').bind('click', function() {
		console.log("Add Entry Button");
		numberOfEntryFields++;
		var prevHTMLString = $('#data_entry_zone').html();
		var addString = "\<br\>\<textarea class=\"new_tour_entry\" rows=\"5\" placeholder=\"Comma Separated Entry\"\>\<\/textarea\>";
		
		$('#data_entry_zone').html(prevHTMLString + addString);
	});
	 
});