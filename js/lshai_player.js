$(document).ready(function(){
//	playerDetailPreInfo();
//	$('.user .unfold').click(hideplayerDetail);
	$('.user .fold').click(showplayerDetail);
});

// 初始隐藏的情况，输出页面时在要隐藏的元素上直接加style="display:none;"属性，否则用户会看到有东西一闪而过。
//function playerDetailPreInfo() {
//	$('.user .detail').css({display:'none'});
//	return false;
//}

function showplayerDetail() {
	$(this).parents('.user').addClass('on')
		.find('.detail').show(); // 用find，实现链式调用，减少多次调用选择器的开销 
	$(this).removeClass('fold').addClass('unfold')
		.unbind('click').click(hideplayerDetail);
	return false;
}

function hideplayerDetail() {
	$(this).parents('.user').removeClass('on')
		.find('.detail').hide(); // 用find，实现链式调用，减少多次调用选择器的开销 
	$(this).removeClass('unfold').addClass('fold')
		.unbind('click').click(showplayerDetail);
	return false;
}