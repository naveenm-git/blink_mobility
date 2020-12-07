$(function(){
		// document.addEventListener('contextmenu', event => event.preventDefault());
		$('.select2').val(null).select2({
			placeholder: "Select an Option",
			allowClear: false
		});
	
		$('.tags').select2({
			placeholder: "Select an Option",
			tags: true,
			allowClear: false
		});
	
	$('.date').datepicker({format: "mm/dd/yyyy",autoclose: true});
	
	$(".date").on('keyup keydown keypress', function(e){
		if(e.keyCode == 9) return;
		if(e.keyCode == 8) e.preventDefault();
		e.preventDefault();
	});
	
	$('.clickable').click(function(){
		href = $(this).data('href');
		if(href!='undefined'){
			window.location.href = href;
		}
	});
	
	$("input[type=number]").on('mousewheel',function(e){ e.preventDefault(); });
	$("input[type=number]").on('keyup keydown keypress', function(e){
		if (e.which === 189 || e.which === 69 || e.which === 38 || e.which === 40) {
			e.preventDefault();
    }
		
		maxlength = ($(this).attr('maxlength')!='undefined')?$(this).attr('maxlength'):10;
		if(e.keyCode != 8 && $(this).val().length == maxlength){
			if(e.keyCode != 9) e.preventDefault();
		} else {
			return;
		}
	});
		

	$('.subscribe-sumbit').click(function(){
		email = $('#subs-email').val();
		mobile = $('#subs-mobile').val();
		sport = $('#subs-sport').val();
		event = $('#subs-event').val();
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		if(email=='' || mobile=='' || sport=='' || event==''){
			if(!emailPattern.test(email)) text = 'Please enter valid email!!!';
			else if(mobile.length < 10) text = 'Please enter valid mobile number!!!';
			else text = 'Please fill all fields!!!';
			$('#subs-err').html(text).css('color', '#f00').show(0).delay(1500).hide(0);
		} else {
			if(!emailPattern.test(email)) { 
				text = 'Please enter valid email!!!';
				$('#subs-err').html(text).css('color', '#f00').show(0).delay(1500).hide(0);
			} else if(mobile.length < 10) {
				text = 'Please enter valid mobile number!!!';
				$('#subs-err').html(text).css('color', '#f00').show(0).delay(1500).hide(0);
			} else {
				$.ajax({
					url: baseurl+'site/main/subscribe_submit',
					type: 'POST',
					data: {email: email, mobile: mobile, sport: sport, event: event},
					success: function(res){
						res = JSON.parse(res);
						if(res.status=='err'){
							$('#subs-err').html(res.message).css('color', '#f00').show(0).delay(1500).hide(0);
						} else {
							$('#subs-email, #subs-mobile, #subs-sport, #subs-event').val('');
							$('#subs-err').html(res.message).css('color', 'green').show(0).delay(1500).hide(0);
						}
					}
				});
			}
		}
	});

	$('.newsletter-sumbit').click(function(){
		email = $('#nl-email').val();
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		if(email==''){
			$('#nl-err').html('Please enter valid email!!!').css('color', '#f00').show(0).delay(1500).hide(0);
		} else {
			if(!emailPattern.test(email)) { 
				text = 'Please enter valid email!!!';
				$('#nl-err').html(text).css('color', '#f00').show(0).delay(1500).hide(0);
			} else {
				$.ajax({
					url: baseurl+'site/main/newsletter_submit',
					type: 'POST',
					data: {email: email},
					success: function(res){
						res = JSON.parse(res);
						if(res.status=='err'){
							$('#nl-err').html(res.message).css('color', '#f00').show(0).delay(1500).hide(0);
						} else {
							$('#nl-email').val('');
							$('#nl-err').html(res.message).css('color', 'green').show(0).delay(1500).hide(0);
						}
					}
				});
			}
		}
	});
	
});
