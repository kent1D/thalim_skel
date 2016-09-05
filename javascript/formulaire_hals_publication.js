jQuery(document).ready(function(){
	var hide_fields = function(){
		if($('#champ_resume').size() > 0 && $('#champ_resume').val()==''){
			$('.editer_resume').remove();
		}
		if($('#champ_commentaire').size() > 0 && $('#champ_commentaire').val()==''){
			$('.editer_commentaire').remove();
		}
	}
	hide_fields();
	onAjaxLoad(hide_fields);
});
