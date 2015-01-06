function thalim_email(a,b){
	x = "/spip.php?page=auteur_email&a="+a+"&b="+b;
	return x;
}

$(document).ready(function(){
	$('li.type_publication').hover(function(){
		if($(this).find('ul').is(':hidden'))
			$(this).find('ul').slideDown();
	});
	$('li.editer_statut_fonction').detach().insertBefore('.editer_email');
	$(document).on('scroll',function(e){
		var top = $(document).scrollTop();
		if(top > 100 && $('.retour_haut').is(':hidden'))
			$('.retour_haut').fadeIn();
		else if(top < 100&& $('.retour_haut').is(':visible'))
			$('.retour_haut').fadeOut();
	});
});

function barrebouilles_thalim(){
	$('.formulaire_spip input.inserer_barre_simple').barre_outils('simple');
}

$(window).load(function(){
	barrebouilles_thalim();
	onAjaxLoad(barrebouilles_thalim);
});