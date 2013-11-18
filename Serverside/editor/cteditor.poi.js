console.log('RUNNING CTEDITOR.SPOT JAVASCRIPT');

$(function() {
	// Handler for .ready() called.
	console.log('CTEDITOR.SPOT READY');
	
	$('#list_spot_page').bind('pagebeforeshow',function(event, ui){
		console.log('pagebeforeshow');

		//Remove the old rows
		$( ".spot_list_row" ).remove();

		//JQuery Fetch The New Ones
		$.ajax({
			url: "api/spot",
			dataType: "json",
			async: false,
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				//Create The New Rows From Template
				$( "#spot_list_row_template" ).tmpl( data ).appendTo( "#spot_list" );
			},
			error: ajaxError
		});

		$('#spot_list').listview('refresh');
	});

	$('list_spotphoto_page').bind('pagebeforeshow', function() {
		console.log("Show Spot for photo");
		var photo_id =$.url().fparam("photo_id");
		var spot_id=$.url().fparam("spot_id")
		$('#edit_spotphoto_image')[0].src = "api/spot/"+spot_id+"/photo/"+photo_id;
	});

	$("#add_spotphoto_button").bind('click',function(event) {
		console.log("add button click");
		var filename = $("#s_file").val();
		console.log("filename = " + filename)
		if(filename) {
			var fd = new FormData();
			var spot_id = $.url().fparam("spot_id");
			console.log("spotid = " + spot_id);
			fd.append('userFiles[]',$("#s_file")[0].files[0]);
			console.log(fd);
			$.ajax({
			url:'api/spot/' + spot_id + "/photo",
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			async: false,
			cache: false,
			dataType:'json',
			success:function(response) {
				console.log("success");
				console.log(response);
							dataUrl = "#edit_spot_page&spot_id="+spot_id;
							$.mobile.changePage( dataUrl, { allowSamePageTransition: true , dataUrl: dataUrl});
				//location.reload();
			},
			error: function() {
			console.log("oops");
			}
			});
		}
	});

	//Bind the edit page init text SPOT
	$('#edit_spot_page').bind('pagebeforeshow', function() {
		console.log("Edit SPOT Page");
		var spot_id = $.url().fparam("spot_id");
				$(".spot_photo_list_row").remove();
		//Instead of passing around in JS I am doing AJAX so direct links work
		//JQuery Fetch The Comment
		$.ajax({
			url: "api/spot/"+spot_id,
			dataType: "json",
		async: false,
		success: function(data, textStatus, jqXHR) {
					console.log(data);
			//Remove the old rows
			$( ".edit_spot_static_info" ).remove();

			//Create The New Rows From Template
			//  $( "#edit_spot_static_info_template" ).tmpl( data ).appendTo( "#edit_spot_static_info_container" );
				
			$('#edit_spot_name')[0].value =data.name;
			$('#edit_spot_base')[0].value = data.base;
			$('#edit_spot_history')[0].value = data.history;
			$('#edit_spot_campuslife')[0].value = data.campuslife;
					$("#spot_add_photo_button").attr("href", "#add_spotphoto_page&spot_id="+spot_id);
					//$("#spot_photo_row_template").tmpl(obj).appendTo("#spot_photo_list");
				$.ajax({
					url:	"api/spot/"+spot_id+"/photo",
					dataType: "json",
					async: false,
					success: function(data) {
						console.log(data);
						$("#spot_photo_list_row_template").tmpl(data).appendTo("#spot_photo_list");
						$("#spot_photo_list").listview("refresh");
					}
				});
		},
		error: ajaxError
		});
	});

	//Bind the edit page save button
	$('#spot_save_button').bind('click', function() {
		console.log("Save Spot Button");
		var spot_id = $.url().fparam("spot_id");
		$.ajax({
			url: "api/spot/"+spot_id,
			dataType: "json",
		async: false,
			data: {
		'name': $('#edit_spot_name')[0].value,
		'base': $('#edit_spot_base')[0].value,
		'history': $('#edit_spot_history')[0].value,
		'campuslife': $('#edit_spot_campuslife')[0].value
		},
			headers: {'X-HTTP-Method-Override': 'PUT'},
			type: 'POST',
		error: ajaxError
		});
	});

	//Cleanup of URL so we can have better client URL support
	$('#edit_spot_page').bind('pagehide', function() {
		$(this).attr("data-url",$(this).attr("id"));
		delete $(this).data()['url'];
	});
	
	$("#spot_reset").bind('click',function(event) {
		// location.reload();
	});
	
	//Add Tag Button
	 $('#panorama_add_box_button').bind('click', function() {
		console.log("Add Box Button");
		var panorama_id = $.url().fparam("panorama_id");

		//JQuery Fetch The Comment
		$.ajax({
			url: "api/panorama/"+panorama_id+"/box",
			dataType: "json",
			data: {
				'box': $('#panorama_edit_box_info')[0].value
			},
			async: false,
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				refreshPanoPageBoxList(panorama_id);
				$('#panorama_edit_box_info')[0].value = "";
			},
			type: 'POST',
			error: ajaxError
		});
	});

	//Bind the edit page init text BOX
	$('#edit_box_page').bind('pagebeforeshow', function() {
		console.log("Edit BOX Page");
		var box_id = $.url().fparam("box_id");
		console.log(box_id);

		//Instead of passing around in JS I am doing AJAX so direct links work
		//JQuery Fetch The Comment
		$.ajax({
			url: "api/boxes/"+box_id,
			dataType: "json",
			async: false,
			success: function(data, textStatus, jqXHR) {
				console.log(data);
				//Remove the old rows
				$( ".edit_box_static_info" ).remove();
	
				//Create The New Rows From Template
				// $( "#edit_box_static_info_template" ).tmpl( data ).appendTo("#edit_box_static_info_container");
					
				$('#edit_box_boxid')[0].value =data.boxid;
				$('#edit_box_spotid')[0].value =data.spotid;
				$('#edit_box_title')[0].value =data.title;
				$('#edit_box_tourid')[0].value =data.tourid;
				$('#edit_box_latitude')[0].value =data.latitude;
				$('#edit_box_longitude')[0].value =data.longitude;
				$('#edit_box_altitude')[0].value =data.altitude;
				$('#edit_box_heading')[0].value =data.heading;
				$('#edit_box_tilt')[0].value =data.tilt;
				$('#edit_box_roll')[0].value =data.roll;
			},
			error: ajaxError
		});
	});

	//Bind the edit page save button
	$('#box_save_button').bind('click', function() {
		console.log("Save Box Button");
		var box_id = $.url().fparam("box_id");
		$.ajax({
			url: "api/boxes/"+box_id,
			dataType: "json",
			async: false,
			data: {
				'boxid': $('#edit_box_boxid')[0].value,
				'spotid': $('#edit_box_spotid')[0].value,
				'title': $('#edit_box_tourid')[0].value,
				'tourid': $('#edit_box_tourid')[0].value,
				'latitude': $('#edit_box_latitude')[0].value,
				'longitude': $('#edit_box_longitude')[0].value,
				'altitude': $('#edit_box_altitude')[0].value,
				'heading': $('#edit_box_heading')[0].value,
				'tilt': $('#edit_box_tilt')[0].value,
				'roll': $('#edit_box_roll')[0].value
			},
			headers: {'X-HTTP-Method-Override': 'PUT'},
			type: 'POST',
			error: ajaxError
		});
	});

	//Cleanup of URL so we can have better client URL support
	$('#edit_box_page').bind('pagehide', function() {
		$(this).attr("data-url",$(this).attr("id"));
		delete $(this).data()['url'];
	});

});