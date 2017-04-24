$(document).foundation();

$(function(){

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	function readURL(input, targetElement) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$(targetElement).attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	function isValidImageType(input){
		var ext = input.val().split('.').pop().toLowerCase();
		if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
			return false;
		}
		return true;
	}

	function imageCaching(input, imageName) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				localStorage.setItem(imageName, e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	function getBaseURL(){
		var l = window.location;
		var base_url = l.protocol + "//" + l.host + "/";
		return base_url;
	}

	// Round number of attendees, followers
	function round_to_thousands(number){
		if(number < 1000){
			return number;
		}
		return +(number/1000).toFixed(1) + 'K';
	}

	var loginForm = document.forms.login;
	if(loginForm){
		$(loginForm).on("invalid.zf.abide", function(ev,elem) {
			elem.next('span.server-side-error').remove();
		});
	}

	var resetLinkForm = document.forms.resetLink;
	if(resetLinkForm){
		$(resetLinkForm).on("invalid.zf.abide", function(ev,elem) {
			elem.next('span.server-side-error').remove();
		});
	}

	var resetPasswordForm = document.forms.resetPassword;
	if(resetPasswordForm){
		$(resetPasswordForm).on("invalid.zf.abide", function(ev,elem) {
			elem.next('span.server-side-error').remove();
		});
	}


	var signupForm = document.forms.signup;
	if(signupForm){
		var regionSelect = $(signupForm.elements.region),
		citySelect = $(signupForm.elements.city);

		$(signupForm).on("invalid.zf.abide", function(ev,elem) {
			elem.next('span.server-side-error').remove();
		});

		$(signupForm.elements.country).on('change', function(){
			/* Get the regions for the country */
			var selectedCountryID = $(this).val();

			var request = getCountryRegions(selectedCountryID);

			request.done(function(countryRegions){
				/* Clear previous populated options */
				regionSelect.find('option:not(:first)').remove();
				citySelect.find('option:not(:first)').remove();

				$.each(countryRegions, function(key, value){
					regionSelect.append(new Option(value.name, value.id));
				});
			});
		});

		$(signupForm.elements.region).on('change', function(){
			/* Get the cities for the region */
			var selectedRegionID = $(this).val();
			var citiesRequest = getRegionCities(selectedRegionID);

			citiesRequest.done(function(regionCities){
				citySelect.find('option:not(:first)').remove();

				if(regionCities){
					$.each(regionCities, function(key, value){
						citySelect.append(new Option(value.name, value.id));
					});
				}
			});
		});
	}

	// var createEventForm = document.forms.createEvent;

	// if(createEventForm){
	// 	$(createEventForm.elements.details).trumbowyg({
	// 		fullscreenable: false,
	// 		closable: false,
	// 		btns: [
	// 		  '|', 'formatting',
	// 		  '|', 'btnGrp-design',
	// 		  '|', 'link']
	// 	});
	// }


	function getCountryRegions(countryId){
		var request = $.ajax({
			type: "GET",
			dataType: "json",
			url: '/api/countryRegions/' + countryId
		});
		return request;
	}

	function getRegionCities(regionId){
		var request = $.ajax({
			type: "GET",
			dataType: "json",
			url: '/api/regionCities/' + regionId
		});
		return request;
	}

	function populateFormLocations(givenForm){
		if(givenForm){
			var regionSelect = $(givenForm.elements.region),
			citySelect = $(givenForm.elements.city);

			$(givenForm.elements.country).on('change', function(){
				/* Get the regions for the country */
				var selectedCountryID = $(this).val();

				/* Clear previous populated options */
				regionSelect.find('option:not(:first)').remove();
				citySelect.find('option:not(:first)').remove();

				if(!selectedCountryID){ return false; }

				var request = getCountryRegions(selectedCountryID);

				request.done(function(countryRegions){

					$.each(countryRegions, function(key, value){
						regionSelect.append(new Option(value.name, value.id));
					});
				});
			});

			$(givenForm.elements.region).on('change', function(){
				/* Get the cities for the region */
				var selectedRegionID = $(this).val();

				citySelect.find('option:not(:first)').remove();
				if(!selectedRegionID){ return false; }

				var citiesRequest = getRegionCities(selectedRegionID);

				citiesRequest.done(function(regionCities){

					if(regionCities){
						$.each(regionCities, function(key, value){
							citySelect.append(new Option(value.name, value.id));
						});
					}
				});
			});
		}
	}

	var createEventForm = document.forms.createNewEvent;
	if(createEventForm){
		function populateLocations(){
			var regionSelect = $(createEventForm.elements.region),
			citySelect = $(createEventForm.elements.city);

			$(createEventForm.elements.country).on('change', function(){
				/* Get the regions for the country */
				var selectedCountryID = $(this).val();

				var request = getCountryRegions(selectedCountryID);

				request.done(function(countryRegions){
					/* Clear previous populated options */
					regionSelect.find('option:not(:first)').remove();
					citySelect.find('option:not(:first)').remove();

					$.each(countryRegions, function(key, value){
						regionSelect.append(new Option(value.name, value.id));
					});
				});
			});

			$(createEventForm.elements.region).on('change', function(){
				/* Get the cities for the region */
				var selectedRegionID = $(this).val();
				var citiesRequest = getRegionCities(selectedRegionID);

				citiesRequest.done(function(regionCities){
					citySelect.find('option:not(:first)').remove();

					if(regionCities){
						$.each(regionCities, function(key, value){
							citySelect.append(new Option(value.name, value.id));
						});
					}
				});
			});
		}

		populateLocations();

		$('#starting-time').fdatepicker({
			format: 'yyyy-mm-dd hh:ii',
			disableDblClickSelection: true,
			pickTime: true
		});

		$('#ending-time').fdatepicker({
			format: 'yyyy-mm-dd hh:ii',
			disableDblClickSelection: true,
			pickTime: true
		});

		$('#venue_page_chosen .chosen-search input').on('input', function(){
			var pageOptions = "";
			var term = $(this).val();

			delay(function(){
				$.getJSON("/api/pages/search/" + term, {'type':'venue'}, function(pages){
					$.each(pages, function(index, page){
						pageOptions += "<option value='" + page.id + "'>" + page.name + "</option>";
					});
					$('#venue-page').empty();
					$("#venue-page").append(pageOptions);
					$('#venue-page').trigger("chosen:updated");
				});
			}, 500);
		});

		$('#creator_page_chosen .chosen-search input').on('input', function(){
			var pageOptions = "";
			var term = $(this).val();

			delay(function(){
				$.getJSON("/api/pages/search/" + term, {'type':'organization'}, function(pages){
					$.each(pages, function(index, page){
						pageOptions += "<option value='" + page.id + "'>" + page.name + "</option>";
					});
					$('#creator-page').empty();
					$("#creator-page").append(pageOptions);
					$('#creator-page').trigger("chosen:updated");
				});
			}, 500);
		});

		var selectAdmins = $('#select-admins-wrap');
		var uploadsDir = getBaseURL() + 'uploads';

		$('#create-event-select-admins').on('click', function(){
			selectAdmins.toggleClass('hide');
		});

		$(selectAdmins).find('.close').on('click', function(){
			selectAdmins.addClass('hide');
		});

		$('#select-admins-search').on('input', function(){
			var $this = $(this);
			var searchResults = $('.search-results');

			if($this.val().length > 0){
				var name = $this.val();
				var userAdded = false;
				delay(function(){
					searchResults.empty();
					$.ajax({
						type: "GET",
						url: "/api/users/search/" + name,
						success: function(data){
							if(data.length > 0){
								$.each(data, function(key, user){
									if($('input[name="admins[]"][value ="'+ user.id +'"]').length){
										userAdded = true;
									}
									if(!userAdded){
										searchResults.append(
											$('<div/>', {'class': 'column user', 'data-id': user.id}).append([
												$('<a/>', {'href': '/users/' + user.username, 'target': '_blank'}).append([
													$('<img>', {'src': '/images/small59/' + user.avatarFullPath})
													]),
												$('<div/>').append([
													$('<span/>', {'class':'name', 'text': user.name}),
													$('<button/>', {'class':'add-button', 'type': 'button', 'text': ' Add', 'data-id':user.id, 'data-name':user.name, 'data-username':user.username}).prepend(
														$('<span/>', {'class': 'fa fa-user-plus'})
														)
													])
												])
											);
									}
									userAdded = false;
								});
							}
							else{
								searchResults.append("<h5>No results.</h5>");
							}
						},
						error: function(){
						}
					});
}, 500);
}
else{
	searchResults.empty();
}
});

$('#select-admins-wrap').on('click', '.add-button', function(){
	var $this          = $(this);
	var userAdded      = false;
	var name           = $this.data('name');
	var username       = $this.data('username');
	var id             = $this.data('id');
	var userAvatarPath = $this.closest('.column').find('img:first').attr('src');

	if($('input[name="admins[]"][value ="'+ id +'"]').length){
		userAdded = true;
	}

	if(!userAdded){
		$('#selected-admins').append(
			$('<div/>', {'class': 'column user', 'data-id': id}).append([
				$('<a/>', {'href': '/users/' + username, 'target': '_blank'}).append([
					$('<img>', {'src': userAvatarPath})
					]),
				$('<div/>').append([
					$('<span/>', {'class':'name', 'text': name}),
					$('<button/>', {'class':'add-button added', 'type': 'button', 'text': ' Added', 'data-id':id}).prepend(
						$('<span/>', {'class': 'fa fa-check-circle'})
						)
					])
				])
			);
		$('.select-admins-block').append('<input type="hidden" name="admins[]" value="' + id + '">');
		$('#event-select-admin-following-followers button.add-button[data-id="' + id + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
		$this.parents('.column').remove();
	}
	else{
		if($this.hasClass('added')){
			$this.parents('.column').remove();
			$('input[name="admins[]"][value ="'+ id +'"]').remove();
			$('#event-select-admin-following-followers button.add-button[data-id="' + id + '"]').removeClass('added').html('<span class="fa fa-user-plus"></span>Add');
		}
	}
	$('.selected-label').text($('input[name="admins[]"]').length + ' selected');
});

$('#event-select-admin-following-followers').on('click', '.add-button', function(){
	var $this          = $(this);
	var userAdded      = false;
	var name           = $this.data('name');
	var username       = $this.data('username');
	var id             = $this.data('id');
	var userAvatarPath = $this.closest('.column').find('img:first').attr('src');

	if($('input[name="admins[]"][value ="'+ id +'"]').length){
		userAdded = true;
	}

	if(!userAdded){
		$('#selected-admins').append(
			$('<div/>', {'class': 'column user', 'data-id': id}).append([
				$('<a/>', {'href': '/users/' + username, 'target': '_blank'}).append([
					$('<img>', {'src': userAvatarPath})
					]),
				$('<div/>').append([
					$('<span/>', {'class':'name', 'text': name}),
					$('<button/>', {'class':'add-button added', 'type': 'button', 'text': ' Added', 'data-id':id}).prepend(
						$('<span/>', {'class': 'fa fa-check-circle'})
						)
					])
				])
			);
		$('.select-admins-block').append('<input type="hidden" name="admins[]" value="' + id + '">');
		$('#event-select-admin-following-followers button.add-button[data-id="' + id + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
		$('.select-admins-user-list.search-results').find('.column[data-id="' + id + '"]').remove();
	}
	else{
		if($this.hasClass('added')){
			$('#event-select-admin-following-followers button.add-button[data-id="' + id + '"]').removeClass('added').html('<span class="fa fa-user-plus"></span>Add');
			$('#selected-admins').find('.column[data-id="' + id + '"]').remove();
			$('input[name="admins[]"][value ="'+ id +'"]').remove();

		}
	}
	$('.selected-label').text($('input[name="admins[]"]').length + ' selected');
});

}





var createPageForm = document.forms.createPage;
if(createPageForm){
	var keyPeopleResults = $('.key-people-results');
	var resultsUL = keyPeopleResults.find('ul');

	$('#key-people-search').on('input', function(){
		var $this = $(this);

		if($this.val().length > 0){
			var name = $this.val();
			delay(function(){
				resultsUL.empty();
				$.ajax({
					type: "GET",
					url: "/api/users/search/" + name + "?includeUser=1",
					success: function(data){
						if(data.length > 0){
							$.each(data, function(key, user){
								resultsUL.append($('<li>').text(user.name).attr('data-id', user.id));
							});
							keyPeopleResults.show();
						}

					},
					error: function(){

					}
				});
			}, 500);
		}
		else{
			keyPeopleResults.hide();
		}
	});

	$(resultsUL).on('mousedown', 'li', function(){
		var keyPersonAdded = false;
		var name = $(this).html();
		var id = $(this).data('id');

		if($('input[name="key_people[]"][value ="'+ id +'"]').length){
			keyPersonAdded = true;
		}

		if(!keyPersonAdded){
			$('#selected-key-people').append('<span class="key-people-label" data-closable>'+ name + '<i class="fa fa-times key-people-remove" data-close data-id="'+ id +'"></i></span>');
			$('#selected-key-people').append('<input type="hidden" name="key_people[]" value="'+ id +'">');

			keyPeopleResults.hide();
		}
		else{
			keyPeopleResults.hide();
		}
	});


	$('#selected-key-people').on('click', '.key-people-remove', function(){
		var $this = $(this);
		var id    = $this.data('id');
		$this.parent().remove();
		$('input[name="key_people[]"][value ="'+ id +'"]').remove();
	});

	$('#key-people-search').on('focusin', function(){
		if(resultsUL.children().length > 0){
			keyPeopleResults.show();
		}
	});

	$('#key-people-search').on('focusout', function(){
		keyPeopleResults.hide();
	});

	var regionSelect = $(createPageForm.elements.region),
	citySelect = $(createPageForm.elements.city);

	$(createPageForm.elements.country).on('change', function(){
		/* Get the regions for the country */
		var selectedCountryID = $(this).val();

		var request = getCountryRegions(selectedCountryID);

		request.done(function(countryRegions){
			/* Clear previous populated options */
			regionSelect.find('option:not(:first)').remove();
			citySelect.find('option:not(:first)').remove();

			$.each(countryRegions, function(key, value){
				regionSelect.append(new Option(value.name, value.id));
			});
		});
	});

	$(createPageForm.elements.region).on('change', function(){
		/* Get the cities for the region */
		var selectedRegionID = $(this).val();
		var citiesRequest = getRegionCities(selectedRegionID);

		citiesRequest.done(function(regionCities){
			citySelect.find('option:not(:first)').remove();

			if(regionCities){
				$.each(regionCities, function(key, value){
					citySelect.append(new Option(value.name, value.id));
				});
			}
		});
	});

	var selectAdmins = $('#select-admins-wrap');

	$('#create-page-select-admins').on(
		'click',
		function(){
			selectAdmins.toggleClass('hide');
		}
		);

	$(selectAdmins).find('.close').on('click', function(){
		selectAdmins.addClass('hide');
	});

	var uploadsDir = getBaseURL() + 'uploads';

	$('#select-admins-search').on('input', function(){
		var $this = $(this);
		var searchResults = $('.search-results');

		if($this.val().length > 0){
			var name = $this.val();
			var userAdded = false;
			delay(function(){
				searchResults.empty();
				$.ajax({
					type: "GET",
					url: "/api/users/search/" + name + "?per_request=9",
					success: function(data){
						if(data.length > 0){
							$.each(data, function(key, user){
								if($('input[name="admins[]"][value ="'+ user.id +'"]').length){
									userAdded = true;
								}
								if(!userAdded){
									searchResults.append(
										$('<div/>', {'class': 'column user', 'data-id': user.id}).append([
											$('<a/>', {'href': '/users/' + user.username, 'target': '_blank'}).append([
												$('<img>', {'src': '/images/small59/' + user.avatarFullPath})
												]),
											$('<div/>').append([
												$('<span/>', {'class':'name', 'text': user.name}),
												$('<button/>', {'class':'add-button', 'type': 'button', 'text': ' Add', 'data-id':user.id, 'data-name':user.name, 'data-username': user.username}).prepend(
													$('<span/>', {'class': 'fa fa-user-plus'})
													)
												])
											])
										);
								}
								userAdded = false;
							});
						}
						else{
							searchResults.append("<h5>No results.</h5>");
						}

					},
					error: function(){

					}
				});
}, 500);
}
else{
	searchResults.empty();
}
});

$('#select-admins-wrap').on('click', '.add-button', function(){
	var $this          = $(this);
	var userAdded      = false;
	var name           = $this.data('name');
	var username       = $this.data('username');
	var id             = $this.data('id');
	var userAvatarPath = $this.closest('.column').find('img:first').attr('src');

	if($('input[name="admins[]"][value ="'+ id +'"]').length){
		userAdded = true;
	}

	if(!userAdded){
		$('#selected-admins').append(
			$('<div/>', {'class': 'column user', 'data-id': id}).append([
				$('<a/>', {'href': '/users/' + username, 'target': '_blank'}).append([
					$('<img>', {'src': userAvatarPath})
					]),
				$('<div/>').append([
					$('<span/>', {'class':'name', 'text': name}),
					$('<button/>', {'class':'add-button added', 'type': 'button', 'text': ' Added', 'data-id':id}).prepend(
						$('<span/>', {'class': 'fa fa-check-circle'})
						)
					])
				])
			);
		$('.select-admins-block').append('<input type="hidden" name="admins[]" value="' + id + '">');
		$('#page-select-admin-following-followers button.add-button[data-id="' + id + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
		$this.parents('.column').remove();
	}
	else{
		if($this.hasClass('added')){
			$this.parents('.column').remove();
			$('input[name="admins[]"][value ="'+ id +'"]').remove();
			$('#page-select-admin-following-followers button.add-button[data-id="' + id + '"]').removeClass('added').html('<span class="fa fa-user-plus"></span>Add');
		}
	}
	$('.selected-label').text($('input[name="admins[]"]').length + ' selected');
});



$('#page-select-admin-following-followers').on('click', '.add-button', function(){
	var $this          = $(this);
	var userAdded      = false;
	var name           = $this.data('name');
	var username       = $this.data('username');
	var id             = $this.data('id');
	var userAvatarPath = $this.closest('.column').find('img:first').attr('src');

	if($('input[name="admins[]"][value ="'+ id +'"]').length){
		userAdded = true;
	}

	if(!userAdded){
		$('#selected-admins').append(
			$('<div/>', {'class': 'column user', 'data-id': id}).append([
				$('<a/>', {'href': '/users/' + username, 'target': '_blank'}).append([
					$('<img>', {'src': userAvatarPath})
					]),
				$('<div/>').append([
					$('<span/>', {'class':'name', 'text': name}),
					$('<button/>', {'class':'add-button added', 'type': 'button', 'text': ' Added', 'data-id':id}).prepend(
						$('<span/>', {'class': 'fa fa-check-circle'})
						)
					])
				])
			);
		$('.select-admins-block').append('<input type="hidden" name="admins[]" value="' + id + '">');
		$('#page-select-admin-following-followers button.add-button[data-id="' + id + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
		$('.select-admins-user-list.search-results').find('.column[data-id="' + id + '"]').remove();
	}
	else{
		if($this.hasClass('added')){
			$('#page-select-admin-following-followers button.add-button[data-id="' + id + '"]').removeClass('added').html('<span class="fa fa-user-plus"></span>Add');
			$('#selected-admins').find('.column[data-id="' + id + '"]').remove();
			$('input[name="admins[]"][value ="'+ id +'"]').remove();

		}
	}
	$('.selected-label').text($('input[name="admins[]"]').length + ' selected');
});
}

	// Attend event button (home page + single event + page events + user profile events)
	$('.event, .events').on('click', '.attend-event', function(){
		var $this = $(this);
		var eventID = $this.attr('data-id');
		var eventType = $this.attr('data-type');
		if(typeof eventID !== 'undefined'){
			if($this.hasClass('attend-button-active')){
				var request = $.ajax({
					url: '/events/' + eventID + '/attendees',
					type: "DELETE"
				});
			}
			else{
				var request = $.ajax({
					url: '/events/' + eventID + '/attendees',
					type: "POST"
				});
			}
			request.done(function(data){
				if(data.status == 'success'){
					var numberOfAttendees = $this.closest('.row').find('button.number-of-attendees > span');
					if($this.hasClass('attend-button')){
						if(eventType == 'history'){
							$this.html('I was there <span class="fa fa-check-circle"></span>');
						}
						else{
							$this.html('I\'m going <span class="fa fa-check-circle"></span>');
						}
						$this.attr('class', 'attend-event attend-button-active');
						numberOfAttendees.html(round_to_thousands(numberOfAttendees.data('total') + 1));
						numberOfAttendees.data().total++;
						$this.closest('.event-attendees').find('.avatars').append(data.avatar);
						$this.closest('.event-attendees').find('.avatars').foundation();
					}
					else{
						if(eventType == 'history'){
							$this.html('I was there');
						}
						else{
							$this.html('Attend');
						}
						$this.attr('class', 'attend-event attend-button');
						numberOfAttendees.html(round_to_thousands(numberOfAttendees.data('total') - 1));
						numberOfAttendees.data().total--;
						$this.closest('.event-attendees').find('.avatars > div[data-id=' + data.id + ']').remove();
					}
				}
			});
}
});

$('.home-events-load-more').on('click', function(){
	var $this      = $(this);
	var eventsDiv  = $('.events');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('event-type');
	if(requestURL == 'upcoming'){
		requestURL = '/upcoming';
	}

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL + loadMoreParameters,
			type: "GET",
			data: {
				'page': nextPage,
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			eventsDiv.append(data);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$('.dashboard-events-load-more').on('click', function(){
	var $this      = $(this);
	var eventsDiv  = $('.my-events');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('request-url');
	var leftOff    = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL + loadMoreParameters,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			$this.data('left-off', data.leftOff);
			eventsDiv.append(data.html).foundation();
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$(document).on('click', '.delete-event-confirm', function(){
	$this = $(this);

	$.ajax({
		url: '/events/' + $this.data('id'),
		type: "POST",
		data: {'_method': 'DELETE'},
		success: function(data){
			if(data == 'Deleted'){
				$('.event-block[data-id="' + $this.data('id') + '"]').remove();
				$('#delete-event-' + $this.data('id')).foundation('close');
			}
			else{
				alert("Something went wrong.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});

});

$(document).on('click', '.remove-event-admin-confirm', function(){
	$this = $(this);

	$.ajax({
		url: '/events/' + $this.data('id') + '/admins/' + $this.data('admin-id'),
		type: "POST",
		data: {'_method': 'DELETE'},
		success: function(data){
			if(data == 'Removed'){
				$('.event-block[data-id="' + $this.data('id') + '"]').remove();
				$('#remove-event-admin-' + $this.data('id')).foundation('close');
			}
			else{
				alert("Something went wrong.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});

});

$(document).on('click', '.remove-page-admin-confirm', function(){
	$this = $(this);

	$.ajax({
		url: '/pages/' + $this.data('slug') + '/admins/' + $this.data('admin-id'),
		type: "POST",
		data: {'_method': 'DELETE'},
		success: function(data){
			if(data == 'Removed'){
				$('.page-block[data-id="' + $this.data('id') + '"]').remove();
				$('#remove-page-admin-' + $this.data('id')).foundation('close');
			}
			else{
				alert("Something went wrong.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});
});


$('.dashboard-pages-load-more').on('click', function(){
	var $this      = $(this);
	var pagesDiv  = $('.my-pages');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('request-url');
	var leftOff    = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL + loadMoreParameters,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			pagesDiv.append(data.html).foundation();
			$this.data('left-off', data.leftOff);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$(document).on('click', '.page-invite-lists-view-more', function(){
	var $this      = $(this);
	var listsDiv   = $this.closest('.tabs-panel').find('.page-invite-invite-lists-container');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('request-url');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			listsDiv.append(data);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$(document).on('click', '.event-invite-lists-view-more', function(){
	var $this      = $(this);
	var listsDiv   = $this.closest('.tabs-panel').find('.event-invite-invite-lists-container');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('request-url');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			listsDiv.append(data);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});




$(document).on('click', '.add-list-to-page-invitations, .add-list-to-event-invitations', function(){
	var $this = $(this);
	var requestURL = $this.closest('.tabs-panel ').data('request-url');
	if(!$this.data('is-invited')){
		var request = $.ajax({
			url: requestURL,
			type: 'POST',
			data: {'list_id': $this.data('list-id')},
		});
		request.done(function(data){
			$this.addClass('yellow').removeClass('hollow').text('Invited');
		});
	}
});



$(document).on('input', '.page-invite-search-users', function(){
	var $this = $(this);
	var searchResults = $this.closest('.tabs-content').find('.search-results');

	if($this.val().length > 0){
		var name = $this.val();
		var userAdded = false;
		delay(function(){
			searchResults.empty();
			$.ajax({
				type: "GET",
				url: "/pages/" + $this.data('page-slug') + "/invite-users-search/" + name,
				success: function(data){
					if(data.length > 0){
						searchResults.append(data);
					}
					else{
						searchResults.append("<h5>No results.</h5>");
					}
				},
				error: function(){
				}
			});
		}, 500);
	}
	else{
		searchResults.empty();
	}
});

$(document).on('input', '.event-invite-search-users', function(){
	var $this = $(this);
	var searchResults = $this.closest('.tabs-content').find('.search-results');

	if($this.val().length > 0){
		var name = $this.val();
		var userAdded = false;
		delay(function(){
			searchResults.empty();
			$.ajax({
				type: "GET",
				url: "/events/" + $this.data('event-id') + "/invite-users-search/" + name,
				success: function(data){
					if(data.length > 0){
						searchResults.append(data);
					}
					else{
						searchResults.append("<h5>No results.</h5>");
					}
				},
				error: function(){
				}
			});
		}, 500);
	}
	else{
		searchResults.empty();
	}
});




$(document).on('click', '.page-invite-search-user-list .add-button', function(){
	var $this = $(this);
	var pageSlug = $this.closest('.page-invite-search-user-list').data('page-slug');
	if(!$this.hasClass('added')){
		var request = $.ajax({
			url: "/pages/" + pageSlug + "/invite-a-user",
			type: "POST",
			data: {'user_id': $this.data('id')}
		});
		request.done(function(data){
			$this.addClass('added').text('Invited');
		});
	}
});


$(document).on('click', '.event-invite-search-user-list .add-button', function(){
	var $this = $(this);
	var eventID = $this.closest('.event-invite-search-user-list').data('event-id');
	if(!$this.hasClass('added')){
		var request = $.ajax({
			url: "/events/" + eventID + "/invite-a-user",
			type: "POST",
			data: {'user_id': $this.data('id')}
		});
		request.done(function(data){
			$this.addClass('added').text('Invited');
		});
	}
});




$(document).on('click', '.delete-page-confirm', function(){
	$this = $(this);

	$.ajax({
		url: '/pages/' + $this.data('slug'),
		type: "POST",
		data: {'_method': 'DELETE'},
		success: function(data){
			if(data == 'Deleted'){
				$('.page-block[data-id="' + $this.data('id') + '"]').remove();
				$('#delete-page-' + $this.data('id')).foundation('close');
			}
			else{
				alert("Something went wrong.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});

});


$('.dashboard-invite-lists-load-more').on('click', function(){
	var $this      = $(this);
	var listsDiv  = $('.list-container');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('request-url');
	var leftOff    = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL + loadMoreParameters,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			listsDiv.append(data.html);
			$this.data('left-off', data.leftOff);
			listsDiv.foundation();
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$.each($('#profile-tabs').children(), function(key, value){
	var tabElements = $(value).find('span').data('total');
	if(tabElements > 0){
		$(value).addClass('is-active');
		$('.profile-tabs-content').children().eq(key).addClass('is-active');
		return false;
	}
});

$('.profile-attending-load-more, .profile-attended-load-more').on('click', function(){
	var $this      = $(this);
	var eventType  = $this.data('event-type');
	var eventsDiv  = $('#profile-' + eventType + '-events');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var requestURL = $this.data('request-url');
	var leftOff    = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			eventsDiv.append(data.html);
			$this.data('left-off', data.leftOff);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$('.event-photos .load-more-button').on('click', function(){
	var $this      = $(this);
	var photosDiv  = $('#history-event-photos-container');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var eventID   = $(this).data('event-id');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: '/events/' + eventID + '/history-photos',
			type: "GET",
			data: {
				'page': nextPage,
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			photosDiv.append(data);
			$('.history-event-photo').magnificPopup({
				type:'image',
				gallery: {
					enabled:true
				}
			});
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

var filterEventsForm = document.forms.filterEvents;
if(filterEventsForm){
	populateFormLocations(filterEventsForm);
}

	// Follow user button from profile page
	$('.follow-user-button').on('click', function()
	{
		var $this = $(this);
		var postToURL = $this.attr('data-post-url') + '/follow';

		if($this.text() == 'Following'){
			postToURL = $this.attr('data-post-url') + '/unfollow';
		}

		var request = $.ajax({
			type: "POST",
			url: postToURL,
		});
		request.done(function(data){
			var totalFollowers = $('#total-followers');
			if($this.text() == 'Following'){
				totalFollowers.text(round_to_thousands(totalFollowers.data('total') - 1));
				totalFollowers.data().total--;
			}
			else{
				totalFollowers.text(round_to_thousands(totalFollowers.data('total') + 1));
				totalFollowers.data().total++;
			}
			$this.text(data.buttonText);
		});
	});

	$('.event').on('submit', 'form[name="eventPostComment"]', function() {
		var $this = $(this);
		var parent_id = this.elements.parent_id.value;
		if(this.elements.body.value.length <= 0){
			return false;
		}
		$this.find('button').prop('disabled', true);
		var request = $.ajax({
			type: "POST",
			url: $this.attr('data-post-url'),
			data: {'body': this.elements.body.value, 'parent_id': parent_id}
		});
		request.done(function(data){
			$this.find('textarea[name=body]').val('');
			$this.find('button').prop('disabled', false);
			if(parent_id.length > 0){
				$('#comment-replies-' + parent_id + '> div:first-child').append(data);
				$('#comment-replies-' + parent_id + '> div > div:last-child').show('fast');
			}
			else{
				$('#comments-container').append(data);
				$('#comments-container > div:last-child').show('fast');
			}
			$('.event #comments-label').text('Comments | ' + totalEventComments);
		});
		return false;
	});


	$('#comments-container').on('click', '.reply-button', function() {
		var commentID = $(this).data('comment-id');
		$('#comment-replies-' + commentID).slideToggle();
	});

	$('#comments-container').on('click', '.like-button', function() {
		var $this = $(this);
		var commentID = $this.data('comment-id');
		var unlike = $this.data('is-liked');
		var eventID = $this.closest('#comments-container').data('event-id');
		var request = $.ajax({
			type: "POST",
			url: "/events/" + eventID + "/comments/" + commentID + "/likes",
			data: {'unlike': unlike},
		});
		request.done(function(data){
			if(data.status == 'unliked'){
				$this.data('is-liked', 0);
				$this.removeClass('active');
				$this.text('Like');
				$this.closest('.comment-body-container').find('.comment-likes').text(data.totalLikes);
			}
			else if(data.status == 'liked'){
				$this.data('is-liked', 1);
				$this.addClass('active');
				$this.text('Liked');
				$this.closest('.comment-body-container').find('.comment-likes').text(data.totalLikes);
			}
		});
	});

	// Follow page (Single page + user profile following pages)
	$('.page, #profile-pages-container').on('click', '.follow-page-trigger', function()
	{
		var $this = $(this);
		var postToURL = $this.attr('data-post-url') + '/follow';

		if($this.text() == 'Following'){
			postToURL = $this.attr('data-post-url') + '/unfollow';
		}

		var request = $.ajax({
			type: "POST",
			url: postToURL,
		});
		request.done(function(data){
			var pageTotalFollowers = $('.page-total-followers');

			if($this.text() == 'Following'){
				pageTotalFollowers.text(round_to_thousands(pageTotalFollowers.data('total') - 1));
				pageTotalFollowers.data().total--;
			}
			else{
				pageTotalFollowers.text(round_to_thousands(pageTotalFollowers.data('total') + 1));
				pageTotalFollowers.data().total++;
			}
			$this.text(data.buttonText);

			if($this.hasClass('user-profile-page')){
				$this.toggleClass('follow-page-button-active');

				var currentUserAvatar = $this.closest('.page-followers').find('.avatars > div[data-id=' + data.id + ']');
				if(currentUserAvatar.length){
					currentUserAvatar.remove();
				}
				else{
					$this.closest('.page-followers').find('.avatars').append(data.avatar);
					$this.closest('.page-followers').find('.avatars').foundation();
				}
			}
			else{
				/* Toggle notifications switch */
				$('.page-notifications').toggle();
			}
		});

	});


$('#profile-favorites-load-more').on('click', function(){
	var $this        = $(this);
	var favoritesDiv = $('#favorites-container');
	var nextPage     = parseInt($this.attr('data-next-page'));
	var lastPage     = parseInt($this.attr('data-last-page'));
	var requestURL   = $(this).data('request-url');
	var leftOff      = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			favoritesDiv.append(data.html);
			$this.data('left-off', data.leftOff);
			$('.favorite-media-element').magnificPopup({
				type:'image',
				gallery: {
					enabled:true
				}
			});
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$('#app-main-search').on('input', function() {
	var $this = $(this);
	delay(function(){
		if($this.val().length <= 0){
			$('.search-term > .search-results').remove();
			return false;
		}
		var request = $.ajax({
			type: "GET",
			url: "/search/" +  $this.val()
		});
		request.done(function(data){
			$('.search-term > .search-results').remove();
			$('.search-term').append(data);
		});
	}, 500);
});

$('#app-main-search').on('focus', function(){
	var searchResults = $('.search-term > .search-results');
	if(searchResults.length > 0){
		searchResults.show();
	}
});

$('body').on('click', function(e){
	$('.search-term .search-results').hide();
});

$('.search-term').on('click', '.search-results, input', function(event){
	event.stopPropagation();
});

var profileSettingsForm = document.forms.profileSettings;
if(profileSettingsForm)
{
	populateFormLocations(profileSettingsForm);
}

var oldStatusContent = $('#editable-user-status').text();
$('.edit-status').on('click', function()
{
	var status = $('#editable-user-status');
	var isEditable = status.is('.editable');

	status.prop('contenteditable', !isEditable).toggleClass('editable');

	placeCaretAtEnd(document.getElementById('editable-user-status'));

	var editSaveButton = $('#edit-save-status-button');
	if(editSaveButton.hasClass('fa-save')){
		editSaveButton.removeClass('fa-save').addClass('fa-edit');
	}
	else{
		editSaveButton.removeClass('fa-edit').addClass('fa-save');
	}

	if(isEditable && oldStatusContent != status.text()){
		var request = $.ajax({
			url: '/users/' + username + '/status',
			type: 'POST',
			data: {'status': status.text()}
		});
		request.done(function(data){
			status.text(data);
			oldStatusContent = data;
		});
	}
});

function placeCaretAtEnd(el) {
	el.focus();
	if (typeof window.getSelection != "undefined"
		&& typeof document.createRange != "undefined") {
		var range = document.createRange();
	range.selectNodeContents(el);
	range.collapse(false);
	var sel = window.getSelection();
	sel.removeAllRanges();
	sel.addRange(range);
} else if (typeof document.body.createTextRange != "undefined") {
	var textRange = document.body.createTextRange();
	textRange.moveToElementText(el);
	textRange.collapse(false);
	textRange.select();
}
}


$('.invite-lists .create-new, .invite-lists #cancel-create-new').on('click', function(){
	$('#create-new-invite-list').toggleClass('hide');
});

var createInviteListClicked = false;

$('.invite-lists #submit-create-new').on('click', function(){
	var listName = $('input[name="list_name"]');

	if(createInviteListClicked){
		return false;
	}
	createInviteListClicked = true;

	$.ajax({
		url: '/dashboard/invite-lists',
		type: "POST",
		data: {'list_name':listName.val()},
		success: function(data){
			createInviteListClicked = false;
			if(data){
				$('#create-new-invite-list').after(data);
				$('.invite-lists .list-container').foundation();
				listName.val('');
				$('#create-new-invite-list').toggleClass('hide');
			}
			else{
				alert("Something went wrong.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			createInviteListClicked = false;
			alert(JSON.parse(jqXHR.responseText).list_name);
		}
	});
});


$(document).on('click', '.delete-invite-list-confirm', function(){
	$this = $(this);

	$.ajax({
		url: '/dashboard/invite-lists/' + $this.data('id') + '/delete',
		type: "POST",
		data: {'_method': 'DELETE', 'delete_list' : 'Yes'},
		success: function(data){
			if(data == 'Deleted'){
				$('#delete-list-' + $this.data('id')).foundation('close');
				$('.list-block[data-id="' + $this.data('id') + '"]').remove();
			}
			else{
				alert("Something went wrong.");
			}
		},
		error: function(jqXHR, textStatus, errorThrown){

		}
	});

});

saveListNameClicked = false;

$(document).on('click', '.save-invite-list-name', function(){
	if(saveListNameClicked){
		return false;
	}
	saveListNameClicked = true;

	$this = $(this);
	var listBlock = $this.closest('.list-block');
	var listNameField = listBlock.find('.invite-list-name-field');

	if(!listNameField.val().length){
		alert('Invite List name can not be empty!');
		return false;
	}

	$.ajax({
		url: '/dashboard/invite-lists/' + listBlock.data('id'),
		type: "POST",
		data: {'_method': 'PUT', 'list_name' : listNameField.val()},
		success: function(data){
			if(data == 'Updated'){
				$this.toggleClass('hide');
				listNameField.prop('readonly', true);
			}
			else{
				alert("Something went wrong.");
			}
			saveListNameClicked = false;
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert(JSON.parse(jqXHR.responseText).list_name);
			saveListNameClicked = false;
		}
	});

});


$('.invite-lists').on('click', '.change-invite-list-name', function(){
	var listBlock = $(this).closest('.list-block');
	var listNameField = listBlock.find('.invite-list-name-field');
	listNameField.prop('readonly', !listNameField.attr('readonly'));
	listBlock.find('.save-invite-list-name').toggleClass('hide');
});

var siteFeedbackClicked = false;

$('form[name="siteFeedback"]').on('submit', function(e){
	e.preventDefault();

	if(siteFeedbackClicked){
		return false;
	}
	siteFeedbackClicked = true;

	var $this = $(this);
	var formFields = ["name", "email", "comments"];

	$.each(formFields, function(key, value){
		var input = $('#' + value);
		input.removeClass('is-invalid-input');
		input.parent().removeClass('is-invalid-label');
		if(input.next().is('span.form-error')){
			input.next().remove();		
		}
	});


	$.ajax({
		url: $this.attr('action'),
		type: 'POST',
		data: $this.serialize(),
		success: function(data){
			siteFeedbackClicked = false;
			if(data.success){
				alert('Your feedback was sent.');
				$('#feedback-form').foundation('close');
				$('textarea[name="comments"]').val("");
			}
			else{
				alert('Something went wrong.');
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			siteFeedbackClicked = false;
			$.each(jqXHR.responseJSON, function(key, value){
				var input = $('#' + key);
				input.addClass('is-invalid-input');
				input.parent().addClass('is-invalid-label');
				input.after('<span class="form-error is-visible">' + value[0] + '</span>');	
			});
		}
	});

});


$('form[name="reportPage"], form[name="reportUser"]').on('submit', function(e){
	e.preventDefault();

	var $this = $(this);
	var formFields = ["comments"];

	$.each(formFields, function(key, value){
		var input = $('#report-' + value);
		input.removeClass('is-invalid-input');
		input.parent().removeClass('is-invalid-label');
		if(input.next().is('span.form-error')){
			input.next().remove();		
		}
	});


	$.ajax({
		url: $this.attr('action'),
		type: 'POST',
		data: $this.serialize(),
		success: function(data){
			if(data.success){
				alert('Your report was sent.');
				$('.report-page-user-reveal').foundation('close');
				$('textarea[name="comments"]').val("");
			}
			else{
				alert('Something went wrong.');
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			$.each(jqXHR.responseJSON, function(key, value){
				var input = $('#report-' + key);
				input.addClass('is-invalid-input');
				input.parent().addClass('is-invalid-label');
				input.after('<span class="form-error is-visible">' + value[0] + '</span>');	
			});
		}
	});

});


$('#publish-history-photos').on('click', function(){
	if(typeof publishHistoryPhotosURL != undefined){
		$this = $(this);
		$this.prop('disabled', true);

		$.ajax({
			url: publishHistoryPhotosURL,
			type: "POST",
			data: {"publish": true},
			complete: function(){
				$this.prop('disabled', false);
			},
			success: function(data){
				if(data){
					var photosContainer = $('#history-event-photos-container');
					var loadMore = $('.event-photos .load-more-button');
					photosContainer.empty();
					loadMore.attr({"data-next-page": data.next_page, "data-last-page": data.last_page});
					loadMore.text('Load more');
					if(data.last_page > 1){
						loadMore.parent().removeClass('hide');
					}
					$.each(data.photos, function(index, value){
						photosContainer.append(value);
					});
				}
				$('.history-event-photo').magnificPopup({
					type:'image',
					gallery: {
						enabled:true
					}
				});
				$('#history-event-photos-preview').empty();
				$('#publish-photos-buttons').addClass('hide');
				$('#add-event-photo').foundation('close');
				var totalPhotos = $('#history-event-photos-container > div').length;
				$('#photos-label > span').data('total', totalPhotos).text(totalPhotos);
			},
			error: function(jqXHR, textStatus, errorThrown){

			}
		});	
}
});

$('#history-event-photos-container').on('click', '.button-favorite', function() {
	var $this   = $(this);
	var photoID = $this.closest('.column').data('photo-id');
	var unlike  = $this.data('is-liked');
	var eventID = $('#history-event-photos-container').data('event-id');
	var request = $.ajax({
		type: "POST",
		url: "/events/" + eventID + "/history-photos/" + photoID + "/likes",
		data: {'unlike': unlike},
	});
	request.done(function(data){
		if(data.status == 'unliked'){
			$this.data('is-liked', 0);
			$this.removeClass('active');
			$this.next('span').text(data.totalLikes).data('total', data.totalLikes);
		}
		else if(data.status == 'liked'){
			$this.data('is-liked', 1);
			$this.addClass('active');
			$this.next('span').text(data.totalLikes).data('total', data.totalLikes);
		}
	});
});


$('#favorites-container').on('click', '.button-favorite', function() {
	var $this      = $(this);
	var favoriteID = $this.closest('.column').data('photo-id');
	var unlike     = $this.data('is-liked');
	var requestURL = $this.data('request-url');

	var request = $.ajax({
		type: "POST",
		url: requestURL,
		data: {'unlike': unlike},
	});
	request.done(function(data){
		if(data.status == 'unliked'){
			$this.data('is-liked', 0);
			$this.removeClass('active');
			$this.next('span').text(data.totalLikes).data('total', data.totalLikes);
		}
		else if(data.status == 'liked'){
			$this.data('is-liked', 1);
			$this.addClass('active');
			$this.next('span').text(data.totalLikes).data('total', data.totalLikes);
		}
	});
});


$('.reveal-event-attendance, .reveal-following-followers').on('click', '.follow-attendee, .follow-followable', function(){
	var $this       = $(this);
	var followURL   = $this.attr('data-follow-url');
	var unfollowURL = $this.attr('data-unfollow-url');
	var postToURL   = followURL;

	if($this.text() !== 'Follow'){
		postToURL = unfollowURL;
	}

	var request = $.ajax({
		type: "POST",
		url: postToURL,
	});

	request.done(function(data){
		if(data.buttonText == 'Following'){
			$this.text($this.data('user-fullname'));
			$this.addClass('active');
		}
		else{
			$this.text(data.buttonText);
			$this.removeClass('active');
		}
	});
});

$('#event-attendees-view-more').on('click', function(){
	var $this        = $(this);
	var attendeesDiv = $('#event-attendees-container');
	var nextPage     = parseInt($this.attr('data-next-page'));
	var lastPage     = parseInt($this.attr('data-last-page'));
	var eventID      = $(this).data('event-id');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: '/events/' + eventID + '/attendees',
			type: "GET",
			data: {
				'page': nextPage,
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			attendeesDiv.append(data);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$('#page-followers-view-more').on('click', function(){
	var $this        = $(this);
	var followersDiv = $('#page-followers-container');
	var nextPage     = parseInt($this.attr('data-next-page'));
	var lastPage     = parseInt($this.attr('data-last-page'));
	var pageSlug     = $(this).data('page-slug');
	var leftOff      = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: '/pages/' + pageSlug + '/followers',
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			followersDiv.append(data.html);
			$this.data('left-off', data.leftOff);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$('#page-notifications-switch').on('change', function(){
	var $this     = $(this);
	var isChecked = $this.is(':checked');
	var actionURL = $this.data('notifications-url');

	$.ajax({
		url: actionURL,
		type: "POST",
		data: {"switch": isChecked}
	});
});

	// $('#page-block').on('click', function(){
	// 	var $this          = $(this);
	// 	var newSwitchValue = !($this.data('is-blocked'));
	// 	var actionURL      = $this.data('page-block-url');

	// 	$.ajax({
	// 		url: actionURL,
	// 		type: "POST",
	// 		data: {"switch": newSwitchValue},
	// 		success: function(data){
	// 			$this.data('is-blocked', data.is_blocked);
	// 			$('#page-action-menu').removeClass('js-dropdown-active').parent().removeClass('is-active').attr('data-is-click', false);
	// 			if(data.is_blocked){
	// 				$this.text('Unblock');
	// 			}
	// 			else{
	// 				$this.text('Block');
	// 			}
	// 		}
	// 	});
	// });


$('.page-events-load-more').on('click', function(){
	var $this      = $(this);
	var eventMode  = $this.data('event-type');
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var eventsDiv  = $('#' + eventMode + ' .events');
	var requestURL = $this.data('request-url');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			eventsDiv.append(data);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$('#profile-pages-load-more').on('click', function(){
	var $this      = $(this);
	var nextPage   = parseInt($this.attr('data-next-page'));
	var lastPage   = parseInt($this.attr('data-last-page'));
	var pagesDiv  = $('#profile-pages-container');
	var requestURL = $this.data('request-url');
	var leftOff    = $this.data('left-off');
	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			pagesDiv.append(data.html);
			$this.data('left-off', data.leftOff);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$('#profile-following-view-more, #profile-followers-view-more').on('click', function(){
	var $this            = $(this);
	var type             = $this.data('type');
	var nextPage         = parseInt($this.attr('data-next-page'));
	var lastPage         = parseInt($this.attr('data-last-page'));
	var contentContainer = $('#profile-' + type + '-container');
	var requestURL       = $this.data('request-url');
	var leftOff          = $this.data('left-off');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'after': leftOff
			},
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			$this.data('left-off', data.leftOff);
			contentContainer.append(data.html);
		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$('#create-page-following-view-more, #create-page-followers-view-more').on('click', function(){
	var $this            = $(this);
	var type             = $this.data('type');
	var nextPage         = parseInt($this.attr('data-next-page'));
	var lastPage         = parseInt($this.attr('data-last-page'));
	var contentContainer = $('#create-page-' + type + '-container');
	var requestURL       = $this.data('request-url');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'type': 'addable'
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			contentContainer.append(data);
			var selectedUsers = $('input[name="admins[]"');
			var itemFound = ''; 
			$.each(selectedUsers, function(key, value){
				itemFound = $('#page-select-admin-following-followers').find('.column[data-id="' + value.value + '"]');
				if(itemFound){
					$('#page-select-admin-following-followers button.add-button[data-id="' + itemFound.data('id') + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
				}
			});

		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


$('#create-event-following-view-more, #create-event-followers-view-more').on('click', function(){
	var $this            = $(this);
	var type             = $this.data('type');
	var nextPage         = parseInt($this.attr('data-next-page'));
	var lastPage         = parseInt($this.attr('data-last-page'));
	var contentContainer = $('#create-event-' + type + '-container');
	var requestURL       = $this.data('request-url');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'type': 'addable'
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			contentContainer.append(data);
			var selectedUsers = $('input[name="admins[]"');
			var itemFound = ''; 
			$.each(selectedUsers, function(key, value){
				itemFound = $('#event-select-admin-following-followers').find('.column[data-id="' + value.value + '"]');
				if(itemFound){
					$('#event-select-admin-following-followers button.add-button[data-id="' + itemFound.data('id') + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
				}
			});

		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});


var manageInvitations       = $('#manage-invitations-wrap');
var toggleManageInvitations = $('#toggle-manage-invitations');
var receiveInvitationsOn    = $('#receive-invitations-on');
var receiveInvitationsOff   = $('#receive-invitations-off');
var uploadsDir              = getBaseURL() + 'uploads';

$(toggleManageInvitations).on('click', function(){
	manageInvitations.toggleClass('hide');
	$(this).toggleClass('secondary');
	receiveInvitationsOn.toggleClass('secondary');
	receiveInvitationsOff.toggleClass('orange');
});

$(manageInvitations).find('.close').on('click', function(){
	manageInvitations.addClass('hide');
	toggleManageInvitations.toggleClass('secondary');
});

$('#select-list-members-search').on('input', function(){
	var $this = $(this);
	var searchResults = $('.search-results');

	if($this.val().length > 0){
		var name = $this.val();
		var userAdded = false;
		delay(function(){
			searchResults.empty();
			$.ajax({
				type: "GET",
				url: "/api/users/search/" + name,
				success: function(data){
					if(data.length > 0){
						$.each(data, function(key, user){
							if($('input[name="members[]"][value ="'+ user.id +'"]').length){
								userAdded = true;
							}
							if(!userAdded){
								searchResults.append(
									$('<div/>', {'class': 'column user', 'data-id': user.id}).append([
										$('<a/>', {'href': '/users/' + user.username, 'target': '_blank'}).append([
											$('<img>', {'src': '/images/small59/' + user.avatarFullPath})
											]),
										$('<div/>').append([
											$('<span/>', {'class':'name', 'text': user.name}),
											$('<button/>', {'class':'add-button', 'type': 'button', 'text': ' Add', 'data-id':user.id, 'data-name':user.name, 'data-username':user.username}).prepend(
												$('<span/>', {'class': 'fa fa-user-plus'})
												)
											])
										])
									);
							}
							userAdded = false;
						});
					}
					else{
						searchResults.append("<h5>No results.</h5>");
					}
				},
				error: function(){
				}
			});
}, 500);
}
else{
	searchResults.empty();
}
});

$('#select-invlist-members-wrap').on('click', '.add-button', function(){
	var $this          = $(this);
	var userAdded      = false;
	var name           = $this.data('name');
	var username       = $this.data('username');
	var id             = $this.data('id');
	var userAvatarPath = $this.closest('.column').find('img:first').attr('src');

	if($('input[name="members[]"][value ="'+ id +'"]').length){
		userAdded = true;
	}

	if(!userAdded){
		$('#selected-list-members').append(
			$('<div/>', {'class': 'column user', 'data-id': id}).append([
				$('<a/>', {'href': '/users/' + username, 'target': '_blank'}).append([
					$('<img>', {'src': userAvatarPath})
					]),
				$('<div/>').append([
					$('<span/>', {'class':'name', 'text': name}),
					$('<button/>', {'class':'add-button added', 'type': 'button', 'text': ' Added', 'data-id':id}).prepend(
						$('<span/>', {'class': 'fa fa-check-circle'})
						)
					])
				])
			);
		$('.select-invlist-members-block').append('<input type="hidden" name="members[]" value="' + id + '">');
		$('#invite-list-members-select-following-followers button.add-button[data-id="' + id + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
		$this.parents('.column').remove();
	}
	else{
		if($this.hasClass('added')){
			$this.parents('.column').remove();
			$('input[name="members[]"][value ="'+ id +'"]').remove();
			$('#invite-list-members-select-following-followers button.add-button[data-id="' + id + '"]').removeClass('added').html('<span class="fa fa-user-plus"></span>Add');
		}
	}
	$('.selected-label').text($('input[name="members[]"]').length + ' selected');
});

$('#invite-list-members-followers-view-more, #invite-list-members-following-view-more').on('click', function(){
	var $this            = $(this);
	var type             = $this.data('type');
	var nextPage         = parseInt($this.attr('data-next-page'));
	var lastPage         = parseInt($this.attr('data-last-page'));
	var contentContainer = $('#invite-list-members-' + type + '-container');
	var requestURL       = $this.data('request-url');

	if(nextPage <= lastPage){
		var request = $.ajax({
			url: requestURL,
			type: "GET",
			data: {
				'page': nextPage,
				'type': 'addable'
			},
			dataType: 'html',
		});

		request.done(function(data){
			$this.attr('data-next-page', parseInt(nextPage) + 1);
			contentContainer.append(data);
			var selectedUsers = $('input[name="members[]"');
			var itemFound = ''; 
			$.each(selectedUsers, function(key, value){
				itemFound = $('#invite-list-members-select-following-followers').find('.column[data-id="' + value.value + '"]');
				if(itemFound){
					$('#invite-list-members-select-following-followers button.add-button[data-id="' + itemFound.data('id') + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
				}
			});

		});
	}
	else{
		$this.text('You\'ve reached the end');
	}
});

$('#invite-list-members-select-following-followers').on('click', '.add-button', function(){
	var $this          = $(this);
	var userAdded      = false;
	var name           = $this.data('name');
	var username       = $this.data('username');
	var id             = $this.data('id');
	var userAvatarPath = $this.closest('.column').find('img:first').attr('src');

	if($('input[name="members[]"][value ="'+ id +'"]').length){
		userAdded = true;
	}

	if(!userAdded){
		$('#selected-list-members').append(
			$('<div/>', {'class': 'column user', 'data-id': id}).append([
				$('<a/>', {'href': '/users/' + username, 'target': '_blank'}).append([
					$('<img>', {'src': userAvatarPath})
					]),
				$('<div/>').append([
					$('<span/>', {'class':'name', 'text': name}),
					$('<button/>', {'class':'add-button added', 'type': 'button', 'text': ' Added', 'data-id':id}).prepend(
						$('<span/>', {'class': 'fa fa-check-circle'})
						)
					])
				])
			);
		$('.select-invlist-members-block').append('<input type="hidden" name="members[]" value="' + id + '">');
		$('#invite-list-members-select-following-followers button.add-button[data-id="' + id + '"]').addClass('added').text('Added').prepend($('<span/>', {'class': 'fa fa-check-circle'}));
		$('.select-invlist-members-user-list.search-results').find('.column[data-id="' + id + '"]').remove();
	}
	else{
		if($this.hasClass('added')){
			$('#invite-list-members-select-following-followers button.add-button[data-id="' + id + '"]').removeClass('added').html('<span class="fa fa-user-plus"></span>Add');
			$('#selected-list-members').find('.column[data-id="' + id + '"]').remove();
			$('input[name="members[]"][value ="'+ id +'"]').remove();

		}
	}
	$('.selected-label').text($('input[name="members[]"]').length + ' selected');
});


$('#clear-all-notifications').on('click', function(){
	$this = $(this);

	var notifsDiv = $('.user-notifications');
	var request = $.ajax({
		url: "/notifications/clear",
		method: "POST",
	});

	request.done(function(data){
		$this.addClass('hide');
		$('.notification-item').remove();
		notifsDiv.append('<div class="notification-row"><h5>No notifications.</h5></div>');
	});
});

$('#notifications-bell-icon').on('click', function(){
	if($(this).data('has-new') == 1){
		var request = $.ajax({
			url: '/notifications/seen',
			type: "POST",
			data: {'seen': '1'},
			global:false
		});
		request.done(function(data){
			$('#new-notifications-count').text("").addClass('hide');
			$('#notifications-bell-icon').data('has-new', 0);
		});
	}
});

var notificationUpdateInterval = 1000 * 60 * 1;

setInterval(function(){
	$.ajax({
		url: "/notifications",
		success: function(data){
			if(data.total > 0) $('#new-notifications-count').text(data.total).removeClass('hide');
			if(data.notifications){
				$('#notifications-popup').html(data.notifications);
				$('#notifications-bell-icon').data('has-new', 1);
			}
			else{
				$('#notifications-bell-icon').data('has-new', 0);
			}
		}, 
		global: false,
	});
}, notificationUpdateInterval);


$(document).ajaxStart(function(){
	$('.loading-overlay').show();
});

$(document).ajaxStop(function(){
	$('.loading-overlay').hide();
});

$('.round-to-k').each(function(index, obj){
	$obj = $(obj);;
	var totalRounded = round_to_thousands($obj.data('total'));
	$obj.text(totalRounded);
});	

$('.round-to-k').on('change', function(){
	$this = $(this);
	var totalRounded = round_to_thousands($this.data('total'));
	$this.text(totalRounded);		
});

}); // DOM Ready ends