$(window).load(function() {	
	$("#tag_nav li").hover(
		function() {
			var b = $(this).hasClass('active');
			if (! b) {
				$(this).addClass("on");
				$(this).children('ul').show();
			}
		},
		function() {
			var b = $(this).hasClass('active');
			if (! b) {
				$(this).removeClass("on");
				$(this).children('ul').hide();
			}
		}
	);

	$("#gameboard li.switch a").click(function(){
		if($(this).hasClass('open')){
			$(this).removeClass('open').addClass('close');
		} else {
			$(this).removeClass('close').addClass('open');
		}
	});	
});

var int4=self.setInterval(getGameNoticesByInterval,1000*60);

function getGameNoticesByInterval() {
	if($("#gameboard li.switch a").hasClass('close')) {
		return false;
	}
	var sinceId;
	if($(".notice").length > 0) {
		sinceId = $(".notice:first").attr('id').substring('notice-'.length);
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
					if(count){
						$timeline.prepend(notices.addClass("unbuffered"));
						$(".unbuffered", $timeline).css({display:'none'}).fadeIn(2500)
		    				.removeClass("unbuffered").each(function() {
		    					$(this).mouseover(function() {
		    						if (! $(this).hasClass('op_added')) {
		    							$(this).addClass('op_added');
		    							addNoticeOperation(this);
		    						}
		    					});
		    			});
					}
				}
			  }
		});
	}
}