(function ($){
	$(document).ready(function (){
		var btn_val='';
		var problem ='';
		var post_id  = jQuery('.droidmax-post-id').val();
		var feed_thank_text = $('.thank-you').html();

		function cookie_check(){
			if(getCookie("helpful_id_"+post_id)){
				return true;
			}
		}

		function resetForms(){
			$(".droidmax-response").val('You look so pretty today');
			$(".droidmax-feedback,.droidmax-name,.droidmax-email").val('');
		}


		function setCookie(name, value, days) {
			var expires = "";
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
			document.cookie = name + "=" + (value || "")  + expires + "; path=/";
		}

		function getCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}

			return null;

		}


		function re_check() {
			var check = true;
			for(var i=0; i<input.length; i++) {
				if(validate(input[i]) == false){
					showValidate(input[i]);
					check=false;
				}
			}
			return check;
		}

		$('.droidmax-feedback-form').submit(function(e){
			e.preventDefault();

			if(!cookie_check()){
				if(btn_val == 'btn_no'){
					if(!re_check()){
						return false;
					}
					openPopup('.droidmax-popup-3');
					submitForm();
				}
			}else{
				make_inactive();
			}
		});

		function submitForm(){
			if(btn_val == 'btn_no'){
				if(!re_check()){
					return false;
				}
			}

			if(cookie_check()){
				console.log('No shit buisness here');
				$('.droidmax-container').html(feed_thank_text);
				make_inactive();
				return true;
			}
			var email    = jQuery('.droidmax-email').val();
			var name     = jQuery('.droidmax-name').val();
			var feedback = jQuery('.droidmax-feedback').val();



			var data={};
			if(btn_val == 'btn_no'){
				data = {
					action: 'droidmax_feedback',
					name:name,
					email: email,
					feedback:feedback,
					post_id:post_id,
					response:problem,
					btn:btn_val
				};
			}   else if(btn_val == 'btn_yes'){
				data = {
					action: 'droidmax_feedback',
					post_id:post_id,
					btn:btn_val
				};
			}
			jQuery.post(DroidmaxFeedback.ajaxurl, data,function(response){
				$('.droidmax-container').html(feed_thank_text);
				make_inactive();
				setCookie("helpful_id_"+post_id, "1","3");
			});
		}


		$('.droidmax-response-select').click(function(e){
			e.preventDefault();
			openPopup('.droidmax-popup-2');
			problem = $(this).text();
		});



		function closePopups(){
			$('.droidmax-popup-1,.droidmax-popup-2,.droidmax-popup-3').hide();
		}

		function openPopup(popupSelector){
			$('.droidmax-popup-1,.droidmax-popup-2,.droidmax-popup-3').hide();
			$(popupSelector).show();
		}


		function make_inactive(){
			$('.droidmax-container').addClass('droidmax-inactive');
			$('.droidmax-container').removeClass('droidmax-container');
		}
		//button yes click
		$('.droidmax-yes').click(function (e){
			e.preventDefault();

			if($(this).hasClass('droidmax-inactive'))
			{
				return true;
			}

			if(!cookie_check()){
				btn_val = 'btn_yes';
				resetForms();
				openPopup('.droidmax-popup-3');
				submitForm();
				return true;
			}else{
				make_inactive();
			}
		});



		$('.modal').click(function(e){
			e.stopPropagation();
		});



		$('.droidmax-close-popup').click(function(e){
			e.preventDefault();
			closePopups();
		});

		//button no click
		$('.droidmax-no').click(function (e){
			e.preventDefault();
			if($(this).hasClass('droidmax-inactive'))
			{
				return true;
			}
			if(!cookie_check()){
				btn_val = 'btn_no';
				resetForms();
				openPopup('.droidmax-popup-1');
			}else{
				make_inactive();
			}

		});



		$('.input100').each(function(){
			$(this).on('blur', function(){
				if($(this).val().trim() != "") {
					$(this).addClass('has-val');
				}
				else {
					$(this).removeClass('has-val');
				}
			})
		})

		var input = $('.validate-input .input100');


		$('.validate-form .input100').each(function(){
			$(this).focus(function(){
				hideValidate(this);
			});
		});

		function validate (input) {
			if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
				if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
					return false;
				}
			}else if($(input).attr('name') == 'message') {
				if($(input).val().length < 20){
					$($(input).parent()).attr('data-validate','Write in about more than 20 charecters');
					return false;
				}else if($(input).val().length > 200){
					$($(input).parent()).attr('data-validate','Write in about not more than 200 charecters');
					return false;
				}else if($(input).val() == ''){
					$($(input).parent()).attr('data-validate','Feedback is required');
					return false;
				}
			}else if($(input).attr('name') == 'name') {
				if($(input).val().length < 2){
					$($(input).parent()).attr('data-validate','Too shor name');
					return false;
				}else if($(input).val().length > 20){
					$($(input).parent()).attr('data-validate','Too large name');
					return false;
				}else if($(input).val() == ''){
					$($(input).parent()).attr('data-validate','Name is required');
					return false;
				}
			}
			else {
				if($(input).val().trim() == ''){
					return false;
				}
			}

		}

		function showValidate(input) {
			var thisAlert = $(input).parent();

			$(thisAlert).addClass('alert-validate');
		}

		function hideValidate(input) {
			var thisAlert = $(input).parent();

			$(thisAlert).removeClass('alert-validate');
		}

	});
})(jQuery);