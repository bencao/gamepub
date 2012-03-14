$(document).ready(function(){
	$('form select.choosegame').change(updateGame);
	$('form select.choosebigzone').change(updateBigzone);

	function updateGame() {
		$("select.chooseserver").html('<option value="">不限服务器</option>');
		$.ajax({
			dataType : 'json',
			url : '/ajax/getbzsbygame',
			data : {gid : $(this).val()},
			success : function(json) {
				var html = '<option value="">不限大区</option>';
				for (var i = 0; i < json.length; i ++) {
					html += '<option value="' + json[i].id + '">' + json[i].name + '</option>';
				}
				$('select.choosebigzone').html(html);
			}
		});
	}
	
	function updateBigzone() {
		bzid = $(this).val();
		if (bzid) {
			$.ajax({
				type: "get",
				url: '/ajax/getserversbybzid', 
			    data: {bid : bzid}, 
				dataType: "json",
				success: function(json) {
					var html = '<option value="">不限服务器</option>';
					for (var i = 0; i < json.length; i ++) {
						html += '<option value="' + json[i].id + '" class="exist">' + json[i].name + '</option>';
					}
					$('select.chooseserver').html(html);
			    }
			});
		} else {
			$('select.chooseserver').html('<option value="">不限服务器</option>');
		}
	}
});