$(document).ready(function(){
	$("#first_tag_select").change(function() {
		if($(this).val() == '0') {
			return false;
		} else if($(this).val() == 'define') {
			$("#second_tag_select").remove();
			$(this).after('<input type="text" class="text" style="width:100px;" name="second_tag" value="" id="second_tag_select"/>');
			return false;
		} else {
			$("#second_tag_select").remove();
		}
		$.ajax({
			dataType : 'json',
			url : '/ajax/getsecondtagbyfirsttag',
			data : {ftid : $(this).val()},
			success : function(json) {
				var html = '<select id="second_tag_select" name="second_tag"><option value="0">请选择</option>';
				for (var i = 0; i < json.length; i ++) {
					html += '<option value="' + json[i].name + '">' + json[i].name + '</option>';
				}
				html += '</select>';
				$("#first_tag_select").after($(html));
			}
		});
	});
	
	$('#video_upload_form').submit(function() {
		if (!selectVideo) {
			alert('请选择文件');
			return false;
		}
		if($('#first_tag_select option:selected').val() == "0" || $('#second_tag_select option:selected').val() == "0" ||
				$('#second_tag_select').val() == '') {
			alert('标签不能为空');
			return false;
		}
		if ($('#video_title').val() == '') {
			alert('标题不能为空');
			$('#video_title').focus();
			return false;
		} else if ($('#video_title').val().length > 48){
			alert('标题超过了48个字');
			$('#video_title').focus();
			return false;
		}
		if ($('#description').val() == '') {
			alert('简介不能为空');
			$('#description').focus();
			return false;
		} 
		var content = '[' + $('#second_tag_select option:selected').text() + ']' + $('#video_title').val() + ' ' + $('#description').val();
		if(content.length > 280) {
			alert('您的标题及介绍字数不能超过280个字.');
			$('#video_title').focus();
			return false;
		}
		
		UploadVideo.submit();
		
		return false;
	});
	
	swfobject.embedSWF('/js/upV1.1.swf', "uploader_video", '84', '25', '8', 
			'/js/expressInstall.swf', 
			{t : '1', m : '0', s : '819200', n : 'UploadVideo', c : '1', sid : $('#sid').val()},
			{allowScriptAccess : 'always', wmode : 'transparent'},
			{name: 'uploader_video'});
});


var selectVideo = false;
//var sid = '';// 通过getsid接口取到的SID

var UploadVideo = {
	isUploading : false,
	onSelect : function(args) {
		// 选择视频文件
		if (args[0].status == 1) {
			selectVideo = true;
			$('#uploadp').html(args[0].name);
			if ($('#video_title').val() == '') {
				$('#video_title').val(args[0].name.replace(args[0].type, ''));
			}
			$('#type').val(args[0].type);
		}
		if (args[0].status == '-1') {
			alert('文件超过最大尺寸');
		}
	},
	onProgress : function(args) {
		// 正在进行文件上传
		var width = parseInt((args.bytesLoaded / args.bytesTotal) * 100) + '%';
		$('#video_progress').css("width", width);
//		$('#video_progress').html( parseInt((args.bytesLoaded / args.bytesTotal) * 100) + "%");
		$('#uploadp span').text(parseInt(args.bytesLoaded / 1024) + 'KB / '
						+ parseInt(args.bytesTotal / 1024) + 'KB');
	},
	onComplete : function(args) {
		// 文件上传成功
		if (args) {
			if (args.status == '1') {
				document.getElementById('video_upload_form').submit();
				return true;
			}
			if (args.status == '-4') {
				alert('IO错误');
				return false;
			}
			if (args.status == '3') {
				alert('安全错误。');
				return false;
			}
			alert('对不起,服务器忙,请稍后重试.');
			return false;
		} else {
			alert('对不起,服务器忙,请稍后重试.');
			return false;
		}
	},
	onAllComplete : function(args) {
		// 批量上传时，所有文件上传完成
	},
	onAllRemove : function(args) {
		// 所有文件都移除
	},
	reset : function() {
		// 重新上传
	},
	submit : function() {
		// 上传视频
		if (this.isUploading) {
			alert('文件正在上传中');
			return false;
		}
		this.isUploading = true;
		
		document.getElementById('uploader_video').onSubmit(
				'http://upload.ku6.com/videoUpload.htm?type=1&sid=' + $('#sid').val());
		$('#uploadp').html('<p id="video_progress"></p><span></span>');
		$('#upload_flash_video').hide();
		$('#video_title').attr('readonly', 'readonly');
		$('#description').attr('readonly', 'readonly');
	}
}
