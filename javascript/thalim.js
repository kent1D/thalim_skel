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
});