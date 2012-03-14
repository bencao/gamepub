$(window).load(function() {	
	var flashvars = {
		tspeed: "150",
		tcolor: "0xfff",
		hicolor: "0xfff",
		distr: "true",
		mode: "tags",
		tagcloud: encodeURIComponent("<tags>") + $("#tagcloud").text() + encodeURIComponent("</tags>")
	};
	var params = {
		wmode : "transparent",
		allowScriptAccess : "always",
		quality: "high",
		bgcolor: "#fff"
	};
	var attributes = {
		id: "tagcloud",
		name: "dynamicTag"
	};
	var rnumber = Math.floor(Math.random()*9999999);
	swfobject.embedSWF('/js/tagcloud.swf?r=' + rnumber, 'tagcloud', '196', '236', '9.0.0', '/js/expressInstall.swf', flashvars, params, attributes);
});