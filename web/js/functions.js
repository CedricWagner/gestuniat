function toggleAlertes(classes){
	$('.listing-item').fadeOut();
	for(let _class of classes){
		$('.listing-item'+_class).fadeIn();
	}
}

$ajaxs = 0;

function ajax_start(){
	$('body').addClass('loading');
	$ajaxs++;
}

function ajax_stop(){
	$ajaxs--;
	console.log($ajaxs);
	if($ajaxs == 0){
		$('body').removeClass('loading');
	}
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


function ajaxShowEditContratAgrr(idAgrr){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/agrr/show-edit",
	  data: { idAgrr: idAgrr }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#editer-agrr-'+idAgrr).modal('show');
	});
}

function ajaxShowEditContratObseque(idObseque){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/obseque/show-edit",
	  data: { idObseque: idObseque }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#editer-obseque-'+idObseque).modal('show');
	});
}

function ajaxShowEditDocument(idDocument){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/document/show-edit",
	  data: { idDocument: idDocument }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#editer-document-'+idDocument).modal('show');
	});
}

function ajaxShowEditDocumentSection(idDocument){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/section/document/show-edit",
	  data: { idDocumentSection: idDocument }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#editer-documentSection-'+idDocument).modal('show');
	});
}

function ajaxShowEditOrganisme(idOrganisme){
	ajax_start();
	$.ajax({
	  method: "POST",
	  url: "/organisme/show-edit",
	  data: { idOrganisme: idOrganisme }
	})
	.done(function(response) {
		ajax_stop();
		$('body').append(response);
		$('#edit-organisme-'+idOrganisme).modal('show');
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


function ajaxApplyFilter(formFilter,ajaxUrl,page,orderby){
	
	if (page==null) {
		page = 1;
	}

	var order = 'ASC';

	if(orderby){
		if(orderby==$('.sortable.sorted').data('attr')){
			if($('.sortable.sorted').hasClass('ASC')){
				order='DESC';
			}else{
				order='ASC';
			}
		}
	}else{
		orderby = $('.sortable.sorted').data('attr');
	}

	var fields = new Array();
	$(formFilter).find('.filter-section input, .filter-section select').each(function(){
		if(!$(this).hasClass('ignore')){
			//case cb
			if ($(this).attr('type')&&$(this).attr('type')=='checkbox') {
				if($(this).is(':checked')){

					// var inputName = $(this).attr('name');
					// var values = '';
					// $(formFilter).find('*[name="'+inputName+'"]:checked').each(function(){
					// 	values+=$(this).val()+',';
					// });
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
	  data: { context: $(formFilter).data('context'), fields:fields, nb:$('#sel-nb-per-page').val(), page:page, orderby:orderby, order:order }
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
			if (id != idContact) {
				lis = lis + '<li class="list-group-item" id="result-amc-'+id+'"><a onclick="proceedAutocompMembreConjoint('+id+')" href="#">['+numAdh+'] '+prenom+' '+nom+'</a></li>';
			}
		}
		$(container).find('.autocomp-result').html('<ul class="list-group">'+lis+'</ul>');
	});
}

function ajaxAutocompDelegue(container){
	
	$(container).find('.autocomp-result').html('');

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/search/contact',
	  data: { txtSearch:$(container).find('.autocomp-value').val() }
	})
	.done(function(response) {
		ajax_stop();
		response = JSON.parse(response);
		var lis = '';
		for(line in response){
			var id = response[line].id;
			var nom = response[line].nom;
			var prenom = response[line].prenom;
			var numAdh = response[line].numAdh;
			lis = lis + '<li class="list-group-item" id="result-delegue-'+id+'"><a onclick="proceedAutocompDelegue('+id+')" href="#">['+numAdh+'] '+prenom+' '+nom+'</a></li>';
		}
		$(container).find('.autocomp-result').html('<ul class="list-group">'+lis+'</ul>');
	});
}

function ajaxAutocompSearchContact(container){
	if($(container).find('.autocomp-value').val()!=''){
		$(container).find('.autocomp-result').html('');

		ajax_start();
		$.ajax({
		  method: "POST",
		  url: '/search/contact',
		  data: { txtSearch:$(container).find('.autocomp-value').val() }
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
				var path = response[line].path;
				lis = lis + '<li class="list-group-item" id="result-amc-'+id+'"><a href="'+path+'">['+numAdh+'] '+prenom+' '+nom+'</a></li>';
			}
			$(container).find('.autocomp-result').html('<ul class="list-group">'+lis+'</ul>');
		});
	}else{
		$(container).find('.autocomp-result').html('');
	}
}

function proceedAutocompMembreConjoint(id){
	$('#result-amc-'+id).addClass('active');
	$('#result-amc-'+id).append('<input type="hidden" name="idMembreConjoint" value="'+id+'" />');
	$('#result-amc-'+id).parents('ul').find('li:not(.active)').remove();
	$('#autocomp-membre-conjoint .autocomp-value').val($('#result-amc-'+id).find('a').html());
}

function proceedAutocompDelegue(id){
	$('#result-delegue-'+id).addClass('active');
	$('#result-delegue-'+id).append('<input type="hidden" name="idDelegue" value="'+id+'" />');
	$('#result-delegue-'+id).parents('ul').find('li:not(.active)').remove();
	$('#autocomp-delegue .autocomp-value').val($('#result-delegue-'+id).find('a').html());
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

function removeRentierSection(idContact){
	var line = $('#dest-rentiers-'+idContact);
	if(!$(line).hasClass('removal')){
		$(line).addClass('removal');
		$(line).css('opacity','0.4');
		$(line).append('<input type="hidden" class="removal-field" value="'+idContact+'" name="idRemoval[]"/>');
	}else{
		$(line).removeClass('removal');
		$(line).css('opacity','1');
		$(line).find('.removal-field').remove();	
	}
}

function doContactListingAction(value){

	var selection = new Array();	
	$('#list-contact .cb-select-line:checked').parents('.contact-line').each(function(){
		selection.push($(this).data('id'));
	});

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/contact/listing/action',
	  data: { action:value, selection:selection }
	})
	.done(function(response) {
		ajax_stop();
		location.href = response;
	});
}

function doSectionListingAction(value){

	var selection = new Array();	
	$('#list-section .cb-select-line:checked').parents('.section-line').each(function(){
		selection.push($(this).data('id'));
	});

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/section/listing/action',
	  data: { action:value, selection:selection }
	})
	.done(function(response) {
		ajax_stop();
		location.href = response;
	});
}

function doOrganismeListingAction(value){

	var selection = new Array();	
	$('#list-organisme .cb-select-line:checked').parents('.organisme-line').each(function(){
		selection.push($(this).data('id'));
	});

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/organisme/listing/action',
	  data: { action:value, selection:selection }
	})
	.done(function(response) {
		ajax_stop();
		location.href = response;
	});
}

function printSuivisAction(container){

	var selection = new Array();
	$(container+' .suivi input:checked').parents('.suivi').each(function(){
		selection.push($(this).data('id'));
	});

	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/print-suivi',
	  data: { selection:selection }
	})
	.done(function(response) {
		ajax_stop();
		location.href = response;
	});
}

function applyPermission(obj){

	var role = $(obj).data('role');
	var idPerm = $(obj).data('perm');
	var checked = $(obj).is(':checked');


	ajax_start();
	$.ajax({
	  method: "POST",
	  url: '/permission/apply',
	  data: { role:role, idPerm:idPerm, checked:checked }
	})
	.done(function(response) {
		ajax_stop();
	});
}