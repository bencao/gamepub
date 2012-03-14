$(function(){

    var img_width = $("#avatar_original img").attr("width");
    var img_height = $("#avatar_original img").attr("height");
	
    var initSelectionSize = img_width < img_height? img_width : img_height;

    jQuery("#avatar_original img").Jcrop({
        onChange: showPreview,
        setSelect: [ 0, 0, initSelectionSize, initSelectionSize],
        onSelect: updateCoords,
        aspectRatio: 1,
        boxWidth: img_width,
        boxHeight: img_height,
        bgColor: '#000',
        bgOpacity: .4
    });
});

function showPreview(coords) {
    var rx = 96 / coords.w;
    var ry = 96 / coords.h;

    var img_width = $("#avatar_original img").attr("width");
    var img_height = $("#avatar_original img").attr("height");
    
    $('#avatar_preview img').css({
        width: Math.round(rx *img_width) + 'px',
        height: Math.round(ry * img_height) + 'px',
        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
        marginTop: '-' + Math.round(ry * coords.y) + 'px'
    });
};

function updateCoords(c) {
    $('#avatar_crop_x').val(c.x);
    $('#avatar_crop_y').val(c.y);
    $('#avatar_crop_w').val(c.w);
    $('#avatar_crop_h').val(c.h);
};
