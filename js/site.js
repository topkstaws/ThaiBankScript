$(document).ready(function() {

	$("body").on("click",".do-load", function (e) {
		$(this).removeClass('do-load');
		$(this).html('<img src="images/loading.gif" /> รอสักครู่...');
		var timestamp = $.now();
		var url = $(this).data("url")+"&x="+timestamp;
		$(this).parent().load(url);
	});

	$("body").on("submit",".ajax-form", function (e) {
		e.preventDefault();
		var form = $(this);
		var el = $(this).parent().parent();
		form.parent().html('<div class="load"><img src="images/loading.gif" /> รอสักครู่...</div>');
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "login-ktb-captcha.php",
			data: form.serialize(),
			success: function (response) {
				el.html(response);
			}
		});
	});



}); // ready