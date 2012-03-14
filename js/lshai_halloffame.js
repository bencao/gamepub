$(document).ready(function(){
	window.getVip = function() {
		$.ajax({
			url : '/ajax/hofrandvip',
			type : 'GET',
			dataType : 'json',
			success : function(json) {
				if (json != null) {
					$('div.hofwrap dl.star dd').fadeOut('slow', function() {
						$(this).html(json.html);
					}).fadeIn('slow');
				}
			}
		});
	};
	window.starddhandle = setInterval("window.getVip()", 5000);
	
	$('div.hofwrap dl.star dd').mouseover(function() {
		clearInterval(window.starddhandle);
	}).mouseout(function() {
		window.starddhandle = setInterval("getVip()", 5000);
	});
	
	$("#game_select").click(function() {
		if (! $('#game_more').is(":visible")) {
			$('#game_more').hide().show().bgiframe();
		} else {
			$('#game_more').hide();
		}
		return false;
	});
	
	$("#game_more div.filter a").click(function() {
		$.ajax({
			dataType : 'json',
			url : '/ajax/getgamebycategory',
			data : {c: $(this).attr('class')},
			success : function(json) {
				var html = '';
				for (var i = 0; i < json.length; i ++) {
					html += (json[i].hot ? '<li class="hot">' : '<li>') 
						+ '<a href="#" gid="' + json[i].id + '">' + json[i].name + '</a></li>';
				}
				$("#game_more ul").html(html).find("a").click(function() {
					window.location = '/halloffame?gid=' + $(this).attr('gid');
					return false;
				});
			}
		});
		return false;
	});
	
	$("#game_more ul a").click(function() {
		window.location = '/halloffame?gid=' + $(this).attr('gid');
		return false;
	});
});