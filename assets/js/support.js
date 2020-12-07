$(document).ready(function(){
	$('input.filters, select.filters').val('');
   
	$(document).on('change', 'input.file-field', function(e){
		maxsize = 5;
		if(e.target.files.length>0) {
			inMb = Math.round(parseFloat(e.target.files[0].size) / (1024 * 1024));
			if(inMb <= maxsize){
				$('label[for="'+$(this).attr('id')+'"]').html('You have selected a file named ('+ e.target.files[0].name+')');
			} else {
				$(this).val('');
				$('#img-preview').hide();
				$('label[for="'+$(this).attr('id')+'"]').html('<i class="fa fa-upload">&nbsp;</i>Please upload a valid image');
				var $toast = toastr['error']('Please upload a file below '+maxsize+'MB of size!!!');
				$toastlast = $toast;
			}
		} else {
			$(this).val('');
			$('#img-preview').hide();
			$('label[for="'+$(this).attr('id')+'"]').html('Please select atleast 1 file to upload.');
		}
	});
   
   
   $('.filter-accordion').click(function(){
      $('.filter-panel').slideUp(350);
      panel = $(this).next('.filter-panel');
      if(panel.css('display')=='block'){
         $(this).html('Filter Data &nbsp;&nbsp;<i class="fa fa-angle-down"></i>');
         panel.slideUp(350); 
      } else {
         $(this).html('Filter Data &nbsp;&nbsp;<i class="fa fa-angle-up"></i>');
         panel.slideDown(350);
      }
   });
	
	$(".date, .date-time").on('keyup keydown keypress', function(e){ 
		if(e.keyCode == 9) return;
		if(e.keyCode == 8) e.preventDefault();
		e.preventDefault();
	});
	
	$('.date').datetimepicker({
		minView: 2,
		format: 'dd/mm/yyyy',
		todayHighlight : true,
		autoclose: true
	});
	
	$('.date-time').datetimepicker({
		format: 'dd/mm/yyyy hh:ii',
		weekStart: 0,
		todayHighlight : true,
		endDate: new Date(),
		autoclose: true
	});
	
	$('.date-and-time').datetimepicker({
		format: 'dd/mm/yyyy HH:ii P',
		showMeridian: true,
		todayHighlight : true,
		startDate: new Date(),
		autoclose: true
	});

	$('.select2').select2({
		allowClear: false
	});
				
	$("input[type=number]").on('mousewheel',function(e){ e.preventDefault(); });
	$("input[type=number]").on('keyup keydown keypress', function(e){
		if (e.which === 189 || e.which === 69 || e.which === 38 || e.which === 40) {
			e.preventDefault();
    }
		if(e.keyCode != 8 && $(this).val().length == 10){
			if(e.keyCode != 9) e.preventDefault();
		} else {
			return;
		}
	});
	
	$('.select-all').on('click', function(){
		if($(this).is(":checked")){
			$('input[type="checkbox"]').prop('checked', true);
		} else {
			$('input[type="checkbox"]').prop('checked', false);
		}
	});
	
	$('.bulk-status').click(function(){
		ids = []; 
		status = $(this).data('status');
		redirect = $(this).data('redirect');
		submitto = $(this).data('submitto');
		$('input[name="ids[]"]:checked').each(function () {
			ids.push($(this).val());
		});
		if(ids.length == 0){
			var $toast = toastr['error']('Please choose at least one row');
			$toastlast = $toast;
		} else {
			return swal({
					title: "Change Status",
					text: "Are you sure you want to change status for selected?",
					type: "warning",
					showCancelButton: !0,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, Do It!",
					cancelButtonText: "No, Cancel!",
					closeOnConfirm: !1,
					closeOnCancel: !0,
					confirmButtonColor: "#f60e0e"
			}, function(e) {
				if (e) {
					$.ajax({
						url: baseurl+'admin/'+submitto+"/bulk_status",
						type: 'POST',
						data: {status: status, ids: ids},
						success: function(res){
							if(res=='success'){
								e && swal({
									title: "Success",
									text: "Status Changed Successfully!!!",
									type: "success",
									confirmButtonColor: "#304ffe"
								}, function() {
									window.location = baseurl+"admin/"+redirect;
								})
							} else {
								e && swal({
									title: "Failed",
									text: res,
									type: "error",
									confirmButtonColor: "#304ffe"
								}, function() {
									window.location = baseurl+"admin/"+redirect;
								})
							}
						}
					});
				}
			}), !1
		}
	});
		
	$('.bulk-delete').click(function(){
		ids = []; 
		redirect = $(this).data('redirect');
		submitto = $(this).data('submitto');
		$('input[name="ids[]"]:checked').each(function () {
			ids.push($(this).val());
		});
		if(ids.length == 0){
			var $toast = toastr['error']('Please choose at least one row');
			$toastlast = $toast;
		} else {
			return swal({
					title: "Confirm Delete",
					text: "Are you sure you want to delete selected row(s)?",
					type: "warning",
					showCancelButton: !0,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, Do It!",
					cancelButtonText: "No, Cancel!",
					closeOnConfirm: !1,
					closeOnCancel: !0,
					confirmButtonColor: "#f60e0e"
			}, function(e) {
				if (e) {
					$.ajax({
						url: baseurl+'admin/'+submitto+"/bulk_delete",
						type: 'POST',
						data: {ids: ids},
						success: function(res){
							if(res=='success'){
								e && swal({
									title: "Success",
									text: "Deleted successfully!!!",
									type: "success",
									confirmButtonColor: "#304ffe"
								}, function() {
									window.location = redirect;
								})
							} else {
								e && swal({
									title: "Failed",
									text: res,
									type: "error",
									confirmButtonColor: "#304ffe"
								});
							}
						}
					});
				}
			}), !1
		}
	});
	
	$('#image, input[name="image[]"]').change(function() {
		readURL(this);
	});
	
	if($("#address").val()!=='undefined'){
		var autocomplete = new google.maps.places.Autocomplete($("#address")[0], {});
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
				var place = autocomplete.getPlace();
				var componentForm = {
					street_number: 'short_name',
					route: 'long_name',
					locality: 'long_name',
					administrative_area_level_1: 'long_name',
					country: 'long_name',
					postal_code: 'short_name'
				};
				var city = '', state = '', country = '';
				for (var i = 0; i < place.address_components.length; i++) {
					var addressType = place.address_components[i].types[0];
					if(addressType == 'locality')city = place.address_components[i][componentForm[addressType]];
					if(addressType == 'administrative_area_level_1')state = place.address_components[i][componentForm[addressType]];
					if(addressType == 'country')country = place.address_components[i][componentForm[addressType]];
				}
				$("#city").val(city);
				$("#state").val(state);
				$("#country").val(country);
		});
	}
	
});

$('form.form_submit').on('submit', function(e){
	form = this;
	e.preventDefault();
	if($(form).valid()){
		return swal({
				title: "Submit",
				text: "Are you sure you want to submit?",
				type: "warning",
				showCancelButton: !0,
				confirmButtonText: "Yes, Do It!",
				cancelButtonText: "No, Cancel!",
				closeOnConfirm: !1,
				closeOnCancel: !0,
				confirmButtonColor: "#f60e0e"
		}, function(e) {
			if(e){
				$('input[type="submit"]').addClass('disabled').prop('disabled', true);
				form.submit();
			}
		}), !1
	} else {
		e && swal({
			title: "Failed",
			text: "Please fill all mandatory fields and retry to submit.",
			type: "error",
			confirmButtonColor: "#304ffe"
		});
	}
});

function update_status(id, status, posturl, redirecturl){
	return swal({
			title: "Change Status",
			text: "Are you sure you want to change status?",
			type: "warning",
			showCancelButton: !0,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes, Do It!",
			cancelButtonText: "No, Cancel!",
			closeOnConfirm: !1,
			closeOnCancel: !0,
			confirmButtonColor: "#f60e0e"
	}, function(e) {
		if (e) {
			$.ajax({
				url: posturl,
				type: 'POST',
				data: {status: status, id: id},
				success: function(res){
					if(res=='success'){
						e && swal({
							title: "Success",
							text: "Status Changed Successfully!!!",
							type: "success",
							confirmButtonColor: "#304ffe"
						}, function() {
							window.location = redirecturl;
						})
					} else {
						e && swal({
							title: "Failed",
							text: res,
							type: "error",
							confirmButtonColor: "#304ffe"
						}, function() {
							window.location = redirecturl;
						})
					}
				}
			});
		}
	}), !1
}

function remove_draft(id, posturl, redirecturl){
	if(id!=''){
		return swal({
				title: "Delete",
				text: "Are you sure you want to delete?",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, Do It!",
				cancelButtonText: "No, Cancel!",
				closeOnConfirm: !1,
				closeOnCancel: !0,
				confirmButtonColor: "#f60e0e"
		}, function(e) {
			if (e) {
				$.ajax({
					url: posturl,
					type: 'POST',
					data: {id: id},
					success: function(res){
						if(res=='success'){
							e && swal({
								title: "Success",
								text: "Deleted successfully!!!",
								type: "success",
								confirmButtonColor: "#304ffe"
							}, function() {
								window.location = redirecturl;
							})
						} else {
							e && swal({
								title: "Failed",
								text: res,
								type: "error",
								confirmButtonColor: "#304ffe"
							});
						}
					}
				});
			}
		}), !1
	} else {
		e && swal({
			title: "Failed",
			text: 'Something went wrong!!!',
			type: "error",
			confirmButtonColor: "#304ffe"
		}, function() {
			window.location = redirecturl;
		})
	}
}

function countchars(field) {
	max = $(field).attr('maxlength');
	len = field.value.length;
	if (len > max) {
		field.value = field.value.substring(0, max);
	} else {
		$(field).next('span.note').text('** You have '+(max - len)+' words balance (Max. '+max+')');
	}
}

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			var image = new Image();
			image.src = e.target.result;
			image.onload = function () {
				var height = this.height;
				var width = this.width;
				if (height > 500 || width > 800) {
					$('label[for="'+$(input).attr('id')+'"]').html('<i class="fa fa-upload">&nbsp;</i>Please upload a valid image');
					$(input).val('');
					$('#img-preview').addClass('hidden');
					var $toast = toastr['error']('Width and Height must not exceed 800 * 500!!!');
					$toastlast = $toast;
					return false;
				} else {
					$('#img-preview').attr('src', e.target.result).removeClass('hidden');
				}
				return true;
			};			
		}
		reader.readAsDataURL(input.files[0]);
	}
}