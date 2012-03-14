$(document).ready(function(){
	$('a.switch').click(function() {
		var $dl = $(this).parent().parent();
		if ($dl.hasClass('expand')) {
			$dl.removeClass('expand');
		} else {
			$dl.addClass('expand');
		}
		return false;
	});
	
	$('form.newquestion').submit(function() {
		if ($('textarea.ititle', this).val().length > 50) {
			alertFail('标题太长了', '提示', function() {
				$('textarea.ititle').focus().each(function() {
					selectEnd(this);
				});
			});
			return false;
		}
		if ($('textarea.idesc', this).val().length > 280) {
			alertFail('描述太长了', '提示', function() {
				$('textarea.idesc').focus().each(function() {
					selectEnd(this);
				});
			});
			return false;
		}
		return true;
	})
	.find('textarea.ititle')
		.unbind("keyup paste").bind("keyup paste", function() {counterArray(this, $("dt.head span em", $(this).parents('dl.area')), 0, 50)})
		.unbind("keydown").bind("keydown", textareaEventHandler).keyup().focus().each(function() {
			selectEnd(this);
		}).end()
	.find('textarea.idesc')
		.unbind("keyup paste").bind("keyup paste", function() {counterArray(this, $("span em", $(this).parent()), 1)})
		.unbind("keydown").bind("keydown", textareaEventHandler).keyup().focus().each(function() {
			selectEnd(this);
		});
});