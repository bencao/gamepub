$().ready(function(){
	
	var fetchAndReplace = function(type, page) {
		$.ajax({
			url : '/ajax/wendatimeline',
			dataType : 'json',
			data : {type : type, page : page},
			success : function(json) {
				if (json.result == 'true') {
					var $target = null;
					if (json.type.match(/^my/)) {
						$target = $('#myqlist');
					} else {
						$target = $('#otherqlist');
					}
					$target.find('table').replaceWith(json.html).end()
						.find('li').removeClass('active').end()
						.find('a[type="' + json.type + '"]').parent().addClass('active');
				}
			}
		});
	};
	
	$('div.qlist ul a').live('click', function() {
		if (! $(this).parent().hasClass('active')) {
			fetchAndReplace($(this).attr('type'), '1');
		}
		return false;
	});
	
	$('div.qlist table tfoot a').live('click', function() {
		fetchAndReplace($(this).attr('type'), $(this).attr('page'));
		return false;
	});
	
	$('form.qsearch').submit(function searchCheck()
	{
		var qVal = $('input[name="q"]', this).val().trim();
		if (qVal == '') {
			$('input[name="q"]', this).focus();
			return false;
		}
		return true;
	});
	
	
});