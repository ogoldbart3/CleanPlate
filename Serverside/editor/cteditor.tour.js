console.log('RUNNING CTEDITOR.TYPE JAVASCRIPT');

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
			url: "api/autolistgroup",
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
		var name = $('#new_tour_name').val();
		var description = $('#new_tour_description').val();
		var price = $('#new_tour_price').val();
		var imageURL = $('#new_tour_image_url').val();

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
				refreshTourList();
			},
			type: 'POST',
			error: ajaxError
		});
	});
	
	
});