$(document).ready(function(){
	//---SCROLL TO TOP
	$(window).scroll(function(){
		if ($(this).scrollTop() > 100){
			$('.scrollToTop').fadeIn();
			$('#main-header').addClass('navbar-fixed-top');
		}
		else {
			$('.scrollToTop').fadeOut();
			$('#main-header').removeClass('navbar-fixed-top');
		}
	});

	$('.scrollToTop').click(function(){
		$('html, body').animate({scrollTop: 0}, 800);
		return false;
	});
	//---/SCROLL TO TOP
});

