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

function ajaxCheckSuivi(target,idSuivi,action){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/check-suivi",
	  data: { target: target, idSuivi: idSuivi, action: action }
	})
	.done(function(response) {
		ajax_stop();
		if (action == "done") {
			$("#listing-"+target+"-"+idSuivi).removeClass('done');
			$("#listing-"+target+"-"+idSuivi+' .cb-check-alerte').attr('value','');
		}else{
			$("#listing-"+target+"-"+idSuivi).addClass('done');
			$("#listing-"+target+"-"+idSuivi+' .cb-check-alerte').attr('value','done');
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

function ajaxShowEditSuivi(idSuivi){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/suivi/show-edit",
	  data: { idSuivi: idSuivi }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#editer-suivi-'+idSuivi).modal('show');
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

function ajaxAutocompMembreConjoint(idContact,container){
	
	$(container).find('.autocomp-result').html('');

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/search/contact',
	  data: { txtSearch:$(container).find('.autocomp-value').val(), idContact:idContact }
	})
	.done(function(response) {
		response = JSON.parse(response);
		ajax_stop();
		var lis = '';
		for(line in response){
			var id = response[line].id;
			var nom = response[line].nom;
			var prenom = response[line].prenom;
			var numAdh = response[line].numAdh;
			lis = lis + '<li class="list-group-item" id="result-amc-'+id+'"><a onclick="proceedAutocompMembreConjoint('+id+')" href="#">['+numAdh+'] '+prenom+' '+nom+'</a></li>';
		}
		$(container).find('.autocomp-result').html('<ul class="list-group">'+lis+'</ul>');
	});
}

function proceedAutocompMembreConjoint(id){
	$('#result-amc-'+id).addClass('active');
	$('#result-amc-'+id).append('<input type="hidden" name="idMembreConjoint" value="'+id+'" />');
	$('#result-amc-'+id).parents('ul').find('li:not(.active)').remove();
	$('#autocomp-membre-conjoint .autocomp-value').val($('#result-amc-'+id).find('a').html());
}

function toggleView(obj,show,hide){
	if($(obj).is('checked')){ 
		$(show).hide();
		$(hide).show();
	}else{ 
		$(show).show();
		$(hide).hide(); 
	}
}