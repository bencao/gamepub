$(document).ready(function(){
	$("#form_settings_interest").submit(function() {
		var interest = $("#user_self_define", this).val();
		if (interest.trim().length == 0) {
			alertFail('请先填写您的兴趣');
			return false;
		} else {
			showLoading();
		}
	});
});