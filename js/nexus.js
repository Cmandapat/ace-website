function loadNexus() {
	var name = document.getElementById("name").value;
	var data = "name=" + name;

	console.log("starting load nexus");

	$.getJSON("../php/get-points.php", data, function(result){
		var service = 0;
		var fundraising = 0;
		var flex = 0;

		var serviceMax = 30;
		var fundraisingMax = 20;
		var flexMax = 50;

		console.log("returning from php");
		$.each( result, function( key, value ) {
			var type = value.type.charAt(0).toUpperCase() + value.type.substr(1);
			console.log("type: " + type);

			if (type === "Service")
				service += value.points;
			if (type === "Fundraising")
				fundraising += value.points;
			if (type === "Flex")
				flex += value.points;

			console.log("service: " + service + " fundraising: " + fundraising + " flex: " + flex);
	  	});

		if (service > serviceMax)
			var flexMax = flexMax - (service - serviceMax);
		if (fundraising > fundraisingMax)
			var flexMax = flexMax - (fundraising - fundraisingMax);

		var servicePerc = (service/serviceMax) * 100;
		var fundraisingPerc = (fundraising/fundraisingMax) * 100;
		var flexPerc = (flex/flexMax) * 100;

		console.log("Perentage. service: " + servicePerc + " fundraising: " + fundraisingPerc + " flex: " + flexPerc);

  		document.getElementById("service-bar").style = "min-width: 2em; max-width: 100%; width: " + servicePerc + "%;";
  		document.getElementById("fundraising-bar").style = "min-width: 2em; max-width: 100%; width: " + fundraisingPerc + "%;";		
  		document.getElementById("flex-bar").style = "min-width: 2em; max-width: 100%; width: " + flexPerc + "%;";	

  		document.getElementById("service-bar").innerHTML = "" + service;
  		document.getElementById("fundraising-bar").innerHTML = "" + fundraising;
  		document.getElementById("flex-bar").innerHTML = "" + flex;
	});

	$.getJSON("../php/get-signups.php", data, function(result){
		var signups = "";
		$.each( result, function( key, value ) {
			var date = moment(value.date, 'YYYY-MM-D').format('MMM Do');
			var type = value.type.charAt(0).toUpperCase() + value.type.substr(1);
	  		signups += `
				<button type='button' class='list-group-item' data-toggle='modal' data-target='#mySignUpsModal' data-name='${key}' data-id='${value.id}' data-uin='${value.uin}' data-officername='${value.officer_name}' data-date='${date}' data-freeze='${value.freeze}' data-type='${type}' data-points='${value.points}'>
					<span class='badge'>${type}</span>
					<h2 class='list-group-item-heading'>${key}</h2>
						<p class='list-group-item-text'>${date}  |  ${value.points} points</p>
				</button>`;

	  		document.getElementById("signups").innerHTML = signups;	
	  	});
	});

	$.getJSON("../php/get-createdevents.php", data, function(result){
		var myevents = "";
		$.each( result, function( key, value ) {
			var date = moment(value.date, 'YYYY-MM-D').format('MMM Do');
			var type = value.type.charAt(0).toUpperCase() + value.type.substr(1);
	  		myevents += `
				<button type='button' class='list-group-item' data-toggle='modal' data-target='#myEventsModal' data-name='${key}' data-id='${value.id}' data-officername='${value.officer_name}' data-date='${date}' data-freeze='${value.freeze}' data-type='${type}' data-points='${value.points}'>
					<span class='badge'>${type}</span>
					<h2 class='list-group-item-heading'>${key}</h2>
						<p class='list-group-item-text'>${date}  |  ${value.points} points</p>
				</button>`;

	  		document.getElementById("myevents").innerHTML = myevents;	
	  	});
	});

	return false;
}

function removeAttendee() { 

	var uin = document.getElementById("mySignUpsModal").uin;
	var eventId = document.getElementById("mySignUpsModal").eventId;

   	$.ajax({
            type: "POST",
            url: "../php/removeAttendee.php",
            data: {uins: uin, eventId: eventId},
            success: function(response) {
              alert( response );
              $('#mySignUpsModal').modal('hide');
              loadNexus();
                }
          });
}


function submitEvent() { 

	var uins = new Array();
	var eventId = document.getElementById("attendeesDiv").eventId;

   	$("input:checkbox:checked").each(function() {
   		uins.push($(this).val());

   		$(this).remove();
   		$('label[for="' + this.id + '"]').remove();
  	});

   	$.ajax({
            type: "POST",
            url: "../php/submit-event.php",
            data: {uins: JSON.stringify(uins), eventid: eventId},
            success: function(response) {
              alert( response );
                }
          });

	return false;
}


$('#mySignUpsModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Button that triggered the modal
  var modal = $(this);

  // Extract info from data-* attributes
  var name = button.data('name'); 
  var eventId = button.data('id');
  var uin = button.data('uin');
  var date = button.data('date');

  document.getElementById("mySignUpsModal").uin = uin;
  document.getElementById("mySignUpsModal").eventId = eventId;

  modal.find('.modal-title').text(name + " - " + date);
})


$('#myEventsModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Button that triggered the modal
  var modal = $(this);

  // Extract info from data-* attributes
  var name = button.data('name'); 
  var eventId = button.data('id');
  var date = button.data('date');

  var data = "id=" + eventId;
  var attendees = "";
  $.getJSON("../php/get-attendees.php", data, function(result){
  		var id = 0;
  		$.each( result, function( key, value ) {
  			attendees += `
	 		<label for="${id}" class="checkbox">
	 			<input type="checkbox" name"attendees[]" id="${id}" eventId="${eventId}" value="${key}">${value}
	 		</label>`;
	 		id++;
		});

  		document.getElementById("attendeesDiv").eventId = eventId;
		document.getElementById("attendeesDiv").innerHTML = attendees;
  });

  modal.find('.modal-title').text(name + " - " + date)
})