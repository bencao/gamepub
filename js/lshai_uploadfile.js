$(document).ready(function() {
	var uploadifyErrorHandler = function(event, queueID, fileObj, errorObj) {
		if (errorObj.type == 'File Size') {
			alert('您选择的文件太大，不能超过1MB');
			//$("#avatarfile").uploadifyClearQueue();
//			$("#avatarfile").parents("td").next().html('<label>文件太大，不能超过1MB</label>');
		};
		return false;
	};
	
	var uploadifyCompleteHandler = function(event, queueID, fileObj, response, data) {
//		$("#avatarfile").parents("td").next().html('<label>头像已上传</label>');
		$("#avatarfilename").val(response);
		$("#upload").click();
	};
	
	$("#avatarfile").uploadify({
		'uploader'       : '/js/uploadify.swf',
		'script'         : '/ajax/uploadfile?uid=' + $("#fileQueue").attr('uid'),
		'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
		'buttonImg'      : '/theme/default/images/uploadify/upload.png',
		'width'          : 90,
		'height'         : 24,
		'folder'         : '/file/tmp',
		'queueID'        : 'fileQueue',
		'auto'           : true,
		'multi'          : false,
		'buttonText'     : ' ',
		'fileDesc'       : 'JPG/PNG/GIF文件',
		'fileExt'        : '*.jpg;*.png;*.jpeg;*.gif',
		'sizeLimit'      : '1048576',
		'onError'        : uploadifyErrorHandler,
		'onComplete'     : uploadifyCompleteHandler
	});
});