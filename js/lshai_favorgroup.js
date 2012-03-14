$(window).load(function() {	
	$my.fav_nav = $("#fav_nav");
	
    $("a.rename", $my.fav_nav).click(renameFavorGroup);
    $("a.confirm", $my.fav_nav).click(submitRenameFavorGroup);
    $("a.cancel", $my.fav_nav).click(cancelFavorGroup);
    $("a.fg_name", $my.fav_nav).click(getFavorGroup);
    $("a.delete", $my.fav_nav).click(deleteConfirm);
    
    $("a.raw", $my.fav_nav).click(function(){return false;});
    $("a.forward", $my.fav_nav).click(function(){return false;});
});

function renameFavorGroup() {
	$(this).closest('div.op').hide();
	$(this).closest('td').find('a.fg_name').hide();
	$(this).closest('td').find('div.editing').show();
	$(this).closest('td').find('div.editing input.text').focus();
	return false;
}

function cancelFavorGroup() {	
	$(this).closest('td').find('div.op').show();
	$(this).closest('td').find('a.fg_name').show();
	$(this).closest('div.editing').hide();
	$(this).closest('div.editing').find('input.text').val("");
	return false;
}

function submitRenameFavorGroup() {
	var name = $(this).closest('div.editing').find('input.text').val();
	var $this = $(this).closest('td');
	$.ajax({
		  beforeSend: function() {
			if(/^(　|\s)*$/.test(name) == true ){
			   alertFail('收藏夹名不能为空');
			   return false;
			}else if(name.length > 6){
				alertFail('收藏夹名称请控制在6个字以内');
			   return false;
			}
	      },
		  type: "post",
		  url: $(this).attr('href'), 
		  data: ({name: name, ajax: 1, token: $('body').attr('token')}),
		  dataType: 'json',
		  success: function(json){	  		
	  		$this.find('div.op').show();
	  		$this.find('a.fg_name').text(json.name).show();
	  		$this.find('div.editing').hide();
	  		$this.find('div.editing input.text').val("");
	  		
	  		fadeSuccess('重命名成功');
	  	  }
		});
	return false;
}

function getFavorGroup() {
	window.$this = $(this).closest('td');
	$.ajax({
		  type: "get",
		  url: $(this).attr('href'),
		  dataType: 'json',
		  data : {ajax : 1},
		  success: function(json){	
	  		var $td_active = $("td.active", $my.fav_nav);
	  		$("a.fg_name", $td_active).show();
	  		$("div.op", $td_active).hide();
	  		$("editing", $td_active).hide();
	  		$("editing input.text", $td_active).val("");
	  		$td_active.removeClass('active');
	  		
	  		$this.addClass('active');
	  		$this.find('div.op').show();
	  		$this.find('a.fg_name').show();
	  		$this.find('div.editing').hide();
	  		$this.find('div.editing input.text').val("");
	  		
	  		if ($('#notices').length > 0) {
	  			$('#notices').replaceWith(json.notices);
	  		} else {
	  			$('#fav_nav').after(json.notices);
	  		}
	  		
	  		$('#notices').css({display:'none'}).fadeIn(2500);

			if (json.pg) {
				if ($('#notice_more').length > 0) {
					$('#notice_more').replaceWith(json.pg);
				} else {
					$('#notices').after(json.pg);
				}
			} else {
				if ($('#notice_more').length > 0) {
					$('#notice_more').remove();
				}
			}
			
			$('#notices li.notice').each(function() {
				addNoticeOperation($(this));
			});
	  	  }
		});
	return false;
}

function deleteConfirm() {
	if(!confirm("删除此收藏夹会删除其所有的收藏, 确定删除?")) {
		return false;
	}
	return true;
}