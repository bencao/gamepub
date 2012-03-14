$().ready(function(){
	
	swfobject.embedSWF('/js/player.swf', 'playermp3', '0', '0', '9', '/js/expressInstall.swf', {
		file : $("#bgmusicbtn").attr('url'),
		icons : 'false',
		autostart : 'true'
	}, {}, {
		id : 'myplayer1'
	});
	
	//create a javascript object to allow us send events to the flash player       
//	var player1 = document.getElementById("myplayer1");
//	var mute1 = 0;         
	
	//EVENTS for Mp3 files player     
	$("#bgmusicbtn").click(function(){
		if ($(this).hasClass('active')) {
			document.getElementById("myplayer1").sendEvent("PLAY", "false");
			$(this).removeClass('active');
		} else {
			document.getElementById("myplayer1").sendEvent("PLAY", "true");
			$(this).addClass('active');
		}
	});
});  