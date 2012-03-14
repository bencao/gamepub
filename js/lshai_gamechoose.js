$(document).ready(function(){
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
					updateGame($(this).attr('gid'), $(this).text());
					return false;
				});
			}
		});
		return false;
	});
	
	function hideAllChoose() {
		$('#game_more').hide();
		$('#big_zone_more').hide();
		$('#server_more').hide();
	}
	
	function updateBigzone(bzid, text) {
		$("#big_zone_more").hide();
		$("#game_big_zone_select").text(text);
		$("#game_big_zone").val(bzid);
		$("#game_server_select").text('选择服务器');
		$("#game_server").val('');
		$.ajax({
			type: "get",
			url: '/ajax/getserversbybzid',
		    data: {bid : bzid}, 
			dataType: "json",
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alertFail('获取信息时发生错误，请稍候再试');
			},
			success: function(json) {
				var html = "";
				for (var i = 0; i < json.length; i ++) {
					html += '<li><a href="#" sid="' + json[i].id + '">' + json[i].name + '</a></li>';
				}
				$("#server_more").show().bgiframe().find("ul").html(html).find("a").click(function() {
					$("#server_more").hide();
					$("#game_server_select").text($(this).text());
					$("#game_server").val($(this).attr('sid'));
					return false;
				});
		    }
		});
	}
	
	function updateGame(gid, text) {
		$("#game_more").hide();
		$("#game_select").text(text);
		$("#game").val(gid);
		$("#game_big_zone_select").text('选择大区');
		$("#game_big_zone").val('');
		$("#game_server_select").text('选择服务器');
		$("#game_server").val('');
		$.ajax({
			dataType : 'json',
			url : '/ajax/getbzsbygame',
			data : {gid : gid},
			success : function(json) {
				var html = '';
				for (var i = 0; i < json.length; i ++) {
					html += '<li><a href="#" bzid="' + json[i].id + '">' + json[i].name + '</a></li>';
				}
				$("#big_zone_more").show().bgiframe().find('ul').html(html).find("a").click(function() {
					updateBigzone($(this).attr('bzid'), $(this).text());
					return false;
				});
			}
		});
		if ($("#game_job").size() > 0) {
			$.ajax({
				dataType : 'json',
				url : '/ajax/getjobsbygame',
				data : {gid : gid},
				success : function(json) {
					var html = '';
					for (var i = 0; i < json.jobs.length; i ++) {
						html += '<option value="' + json.jobs[i] + '">' + json.jobs[i] + '</option>';
					}
					$("#game_job").html(html);
					$('label[for="game_job"]').text(json.jobname);
					$('label[for="game_org"]').text(json.groupname);
				}
			});
		}
	}
	
	$("#game_more ul a").click(function() {
		updateGame($(this).attr('gid'), $(this).text());
		return false;
	});
	
	$("#big_zone_more a").click(function() {
		updateBigzone($(this).attr('bzid'), $(this).text());
		return false;
	});
	
	$("#server_more a").click(function() {
		$("#server_more").hide();
		$("#game_server_select").text($(this).text());
		$("#game_server").val($(this).attr('sid'));
		return false;
	});
	
	$("#game_select").click(function() {
		if (! $('#game_more').is(":visible")) {
			hideAllChoose();
			$('#game_more').show().bgiframe();
		} else {
			$('#game_more').hide();
		}
		return false;
	});
	
	$("#game_big_zone_select").click(function() {
		if (! $('#big_zone_more').is(":visible")) {
			hideAllChoose();
			$('#big_zone_more').show().bgiframe();
		} else {
			$('#big_zone_more').hide();
		}
		return false;
	});
	
	$("#game_server_select").click(function() {
		if (! $('#server_more').is(":visible")) {
			hideAllChoose();
			$('#server_more').show().bgiframe();
		} else {
			$('#server_more').hide();
		}
		return false;
	});
});