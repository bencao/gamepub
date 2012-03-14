$(document).ready(function() {
	var onSwfUploadError = function(event, queueID, fileObj, errorObj) {
		if (errorObj.type == 'File Size') {
			alert('您选择的文件太大，不能超过10MB');
		} else {
			alert(errorObj.type + errorObj.info);
		}
		return false;
	};
	
	var onSwfUploadComplete = function(event, queueID, fileObj, response, data) {
		$("#swffilename").val(response);
		$('#swffileUploader').remove();
		$('#swffile').replaceWith('<span>已上传</span>');
	};
	
	$("#swffile").uploadify({
		'uploader'       : '/js/uploadify.swf',
		'script'         : '/ajax/uploadfile?uid=' + $("#swfFileQueue").attr('uid'),
		'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
		'buttonImg'      : '/theme/default/images/uploadify/upload.jpg',
		'width'          : 60,
		'height'         : 23,
		'folder'         : '/file/tmp',
		'queueID'        : 'swfFileQueue',
		'auto'           : true,
		'multi'          : false,
		'buttonText'     : ' ',
		'fileDesc'       : 'SWF文件',
		'fileExt'        : '*.swf',
		'sizeLimit'      : '10048576',
		'onError'        : onSwfUploadError,
		'onComplete'     : onSwfUploadComplete
	});
	
	var onPicUploadError = function(event, queueID, fileObj, errorObj) {
		if (errorObj.type == 'File Size') {
			alert('您选择的文件太大，不能超过1MB');
		};
		return false;
	};
	
	var onPicUploadComplete = function(event, queueID, fileObj, response, data) {
		$("#picfilename").val(response);
		$('#picfileUploader').remove();
		$('#picfile').replaceWith('<span>已上传</span>');
	};
	
	$("#picfile").uploadify({
		'uploader'       : '/js/uploadify.swf',
		'script'         : '/ajax/uploadfile?uid=' + $("#picFileQueue").attr('uid'),
		'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
		'buttonImg'      : '/theme/default/images/uploadify/upload.jpg',
		'width'          : 60,
		'height'         : 23,
		'folder'         : '/file/tmp',
		'queueID'        : 'picFileQueue',
		'auto'           : true,
		'multi'          : false,
		'buttonText'     : ' ',
		'fileDesc'       : 'JPG/PNG/GIF文件',
		'fileExt'        : '*.jpg;*.png;*.jpeg;*.gif',
		'sizeLimit'      : '1048576',
		'onError'        : onPicUploadError,
		'onComplete'     : onPicUploadComplete
	});
	
	$("#flash_upload_form").validate({
		onkeyup: false ,
		submitHandler: function(form) {
			showLoading();
			form.submit();
		},
		highlight: function(error, element) {
			// do nothing
		},
		errorElement : 'span',
		errorClass : 'error',
		errorPlacement: function(error, element) {
			$(element).parent().append(error);
		},
		rules: {
			swffilename: {
				required: true
			},
			picfilename: {
				required: true
			},
			title: {
				required: true
			},
			type: {
				required: true,
				maxlength: 40
			},
			introduction: {
				required: true,
				maxlength: 280
			},
			detail: {
				required: true
			}
		},
		messages: {
			swffilename: {
				required: "请上传flash游戏文件"
			},
			picfilename: {
				required: "请上传flash游戏截图"
			},
			title: {
				required: "请输入标题",
				maxlength: "标题不能超过40字"
			},
			type: {
				required: "请选择游戏类别"
			},
			introduction: {
				required: "请输入游戏简介",
				maxlength: "简介不能超过280字"
			},
			detail: {
				required: "请输入操作说明"
			}
		}
	});
});