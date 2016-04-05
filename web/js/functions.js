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

function ajaxCheckAlerte(target,idAlerte,action){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/check-alerte",
	  data: { target: target, idAlerte: idAlerte, action: action }
	})
	.done(function(response) {
		ajax_stop();
		if (action == "done") {
			$("#listing-"+target+"-"+idAlerte).removeClass('done');
			$("#listing-"+target+"-"+idAlerte+' .cb-check-alerte').attr('value','');
		}else{
			$("#listing-"+target+"-"+idAlerte).addClass('done');
			$("#listing-"+target+"-"+idAlerte+' .cb-check-alerte').attr('value','done');
		}
	});
}

function ajaxShowEditAlerte(target,idAlerte){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/show-edit-alerte",
	  data: { target: target, idAlerte: idAlerte }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#editer-'+target+'-'+idAlerte).modal('show');
	});
}

function ajaxSaveFilter(formFilter,ajaxUrl){
	
	var fields = new Array();
	$(formFilter).find('.filter-section input, .filter-section select').each(function(){
		if(!$(this).hasClass('ignore')){
			//case cb
			if ($(this).attr('type')&&$(this).attr('type')=='checkbox') {
				if($(this).is(':checked')){
					fields.push({'type':'text','name':$(this).attr('name'),'value':$(this).val()});
				}
			}
			//case text
			if ($(this).attr('type')&&$(this).attr('type')=='text') {
				fields.push({'type':'text','name':$(this).attr('name'),'value':$(this).val()});
			}
			//case date
			if ($(this).attr('type')&&$(this).attr('type')=='date') {
				fields.push({'type':'date','name':$(this).attr('name'),'value':$(this).val()});
			}
			//case select
			if ($(this).prop('tagName')=='SELECT') {
				fields.push({'type':'select','name':$(this).attr('name'),'value':$(this).val()});
			}
		}
	});

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: ajaxUrl,
	  data: { context: $(formFilter).data('context'), name: $(formFilter).find('#filter-name').val(), fields:fields }
	})
	.done(function(response) {
		response = JSON.parse(response);
		ajax_stop();
		if(response.action=='add'){
			$(formFilter).find('.select-filtre-perso').append('<option selected="selected" value="'+response.id+'">'+response.label+'</option>');
		}
	});
}


function ajaxApplyFilter(formFilter,ajaxUrl,page){
	
	if (page==null) {
		page = 1;
	}

	var fields = new Array();
	$(formFilter).find('.filter-section input, .filter-section select').each(function(){
		if(!$(this).hasClass('ignore')){
			//case cb
			if ($(this).attr('type')&&$(this).attr('type')=='checkbox') {
				if($(this).is(':checked')){
					fields.push({'type':'text','name':$(this).attr('name'),'value':$(this).val()});
				}
			}
			//case text
			if ($(this).attr('type')&&$(this).attr('type')=='text') {
				fields.push({'type':'text','name':$(this).attr('name'),'value':$(this).val()});
			}
			//case date
			if ($(this).attr('type')&&$(this).attr('type')=='date') {
				fields.push({'type':'date','name':$(this).attr('name'),'value':$(this).val()});
			}
			//case select
			if ($(this).prop('tagName')=='SELECT') {
				fields.push({'type':'select','name':$(this).attr('name'),'value':$(this).val()});
			}
		}
	});

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/filter/render',
	  data: { context: $(formFilter).data('context'), fields:fields, nb:$('#sel-nb-per-page').val(), page:page }
	})
	.done(function(response) {
		response = JSON.parse(response);
		ajax_stop();
		history.pushState({filtre:response.idFiltre}, "", response.path);
		$('#list-'+$(formFilter).data('context')).html(response.html);
	});
}

$('form.filter select, form.filter input').change(function(){
	ajaxApplyFilter($(this).parents('form.filter'));
});

$('table.selectable .cb-select-all').change(function(){
	$(this).parents('table.selectable').find('tbody tr .cb-select-line').prop('checked', $(this).prop("checked"));
	$('table.selectable .cb-select-line').each(function(){
		toggleTableLine($(this));
	})
});

$('table.selectable .cb-select-line').change(function(){
	toggleTableLine($(this));
});

function toggleTableLine(elem){
	if($(elem).is(':checked')){
		$(elem).parents('tr').addClass('info');
	}else{
		$(elem).parents('tr').removeClass('info');
	}
}