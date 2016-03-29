function toggleAlertes(classes){
	$('.listing-item').fadeOut();
	for(let _class of classes){
		$('.listing-item'+_class).fadeIn();
	}
}

function ajax_start(){
	$('body').addClass('loading');
}

function ajax_stop(){
	$('body').removeClass('loading');
}