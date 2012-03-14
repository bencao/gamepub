function  uploadifyErrorHandler(event, queueID, fileObj, errorObj) {
	if (errorObj.type == 'File Size') {
		alert('您选择的文件太大，不能超过1MB');
	};
	return false;
}

function uploadifyQueueFullHandler(event,queueSizeLimit){
	alert("上传文件的个数不能超过"+queueSizeLimit+"个");
	return false;
}

function uploadifyCompleteHandler(event,queueID,fileObj,response,data){
	var url = $('#fileurl').val();
	$('#fileurl').val(url+response+'*');
}

function uploadifyAllCompleteHandler(event, data) {
	if (data.filesUploaded > 0) {
		$('#filenum').val(data.filesUploaded);
		$('#applyvip').find('div.uploadfile').hide();
		$('#applyvip').find('dl dd:last').append('<span class="success">已上传' + data.filesUploaded + '张图片附件</span>');
	}
}


$(document).ready(function(){
	$('#applyvip').validate({
		errorElement: 'span',
		errorClass: 'error',
		submitHandler: function(form){
			showLoading();
			form.submit();
		},
		success: function(label) {
			label.removeClass('error').addClass('success').html('&#160;');
		},
		errorPlacement: function(error, element) {
			var par = $(element).parents("dd");
			if ($('span', par).size() > 0) {
				$('span', par).remove();
			}
			par.append(error);
		},
		rules:{
			phone_number:{
				required: true,
				digits: true,
				rangelength: [11,11]
			},
			email:{
				required: true,
				email: true,
				maxlength: 100
			},
			description:{
				required: true,
				maxlength: 1000
			},
			filenum:{
				required: true
			}
		},
		messages:{
			phone_number:{
				required: "请输入手机号码",
				digits: "电话号码必须为数字",
				rangelength: "电话号码长度为11"
			},
			email:{
				required: "请输入您的电子邮件地址",
				email: "请输入正确的电子邮件地址",
				maxlength: "邮件太长了，最长100字"
			},
			description:{
				required: "请输入说明",
				maxlength: "最长1000字"
			},
			filenum:{
				required: "请上传可证明您资质的图片"
			}
		}
	});
	
	$("#uploadify").uploadify({
		'uploader'       : '/js/uploadify.swf',
		'script'         : '/ajax/uploadfile?uid=' + $("#fileQueue").attr('uid'),
		'cancelImg'      : '/theme/default/images/uploadify/cancel.png',
		'buttonImg'      : '/theme/default/images/uploadify/upload.png',
		'width'          : 90,
		'height'         : 24,
		'folder'         : '/file/tmp',
		'queueID'        : 'fileQueue',
		'auto'           : true,
		'multi'          : true,
		'buttonText'     : ' ',
		'queueSizeLimit' : 3,
		'fileDesc'       : 'JPG/PNG/GIF文件',
		'fileExt'        : '*.jpg;*.png;*.jpeg;*.gif',
		'sizeLimit'      : '1048576',
		'onQueueFull'    : uploadifyQueueFullHandler,
		'onError'        : uploadifyErrorHandler,
		'onComplete'	 : uploadifyCompleteHandler,
		'onAllComplete'  : uploadifyAllCompleteHandler
	});
	
});