var addCountToDocumentTitle=function(A){
	document.title=(A?"("+A+") ":"")+document.title.replace(/\([^)]*[0-9]\)\s+/gi,"");
};

var updateTimeAgo = function(){
	$("li.notice span.timestamp", $my.notices).each(function(){
		var B=$(this);
		var A=timeAgo(B.attr('time'));
		if(A&&B.find("*").length==0){
			B.html(A);
		}
	});
};

var timeAgo = function(t){
	if(!t){
		return false;
	}
	var h=new Date();
	var now = Math.round(h.getTime()/1000);
	
    var diff = now - t;

    if (now < t) { 
        return false;
    } else if (diff < 10) {
        return '就在刚才'; 
    } else if (diff < 60) {
        return diff + " 秒钟前";
    } else if (diff < 3600) {
        return Math.round(diff/60) + " 分钟前";
    } else if (diff < 24 * 3600) {
        return Math.round(diff/3600) + " 小时前";
    } else { 
    	var todaydiff = h.getHours()*3600 + h.getMinutes()*60 + h.getSeconds();
    	var yesterday = now - todaydiff - 3600*24;
    	var twodays = yesterday - 3600*24;
    	
	    if (t > yesterday) {
	        return '昨天';
	    } else if (t > twodays) {
	        return '前天';
	    } else {
	        return false;
	    }
	}
    return false;
};

var int=self.setInterval(getNoticesByInterval,1000*120);

function getNoticesByInterval() {
	//更新时间
	updateTimeAgo();
	
	var sinceId = $("input.latestId").val();
	var maxId = '100000';
	if($(".notice").length > 0) {
		maxId = $(".notice:first").attr('id').substring('notice-'.length);
	}
	if(parseInt(maxId) > parseInt(sinceId)) {
		sinceId = parseInt(maxId);
	}
	
	var url = document.URL;
	if(url.substring(url.length-1) == '#') {
		url = url.substring(0, url.length-1);
	}
	var $timeline = $my.notices;
	if($timeline.length){
	$.ajax({
		  type: "get",		  
		  url: url,
		  data: {since_id: sinceId, ajax: 1},
		  dataType: 'json',
		  success: function(json){
			if (json.result == 'true') {					
				var noticelist = $(json.html).get(0);
				$(noticelist).find(".notice").each(function(){
					if($timeline.find("#"+this.id).length){
						$(this).remove()
					}
				});
				
				var notices=$(noticelist).find(".notice");
				var count=notices.length;
				var $notice_filter = $('#notify');
				if ($notice_filter.size() == 0) {
					$('#notices').prepend('<li><div id="notify" style="display:none;"></div></li>');
					$notice_filter = $('#notify');
				}
				var count2=($("#notices").data("count")||0)+count;
								
				if(count){
					$timeline.prepend(notices.addClass("buffered").css({display:'none'}));
					$notice_filter.html('<a id="results_update" href="#">您有' + count2 +  
							'条新消息，点击查看</a>').show();
					
					$("#results_update").unbind('click').click(function(){
						$(".buffered", $timeline).addClass("unbuffered").removeClass("buffered");
						$(".last-on-refresh", $timeline).removeClass("last-on-refresh");
						$(".unbuffered:last", $timeline).addClass("last-on-refresh");
						
			    		$(".unbuffered", $timeline).css({display:'none'}).fadeIn(2500)
			    			.removeClass("unbuffered").each(function() {
			    				$(this).mouseover(function() {
			    					if (! $(this).hasClass('op_added')) {
			    						$(this).addClass('op_added');
			    						addNoticeOperation(this);
			    					}
			    				});
			    			});
					    
						$('#notify').parents("li").remove();
						addCountToDocumentTitle();
						$("#notices").data("count",0);
						
						//更新sinceId
						$("input.latestId").val($(".notice:first").attr('id').substring('notice-'.length));
						
						return false;
					});
					$("#notices").data("count",count2);
					addCountToDocumentTitle(count2);
				}
			}			
		  }
		});
	}
}