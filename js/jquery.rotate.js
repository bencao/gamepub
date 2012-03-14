jQuery.fn.rotate = function(parentDem,angle,whence) {
	var p = this.get(0);

	// we store the angle inside the image tag for persistence
	if (!whence) {
		p.angle = ((p.angle==undefined?0:p.angle) + angle) % 360;
	} else {
		p.angle = angle;
	}

	if (p.angle >= 0) {
		var rotation = Math.PI * p.angle / 180;
	} else {
		var rotation = Math.PI * (360+p.angle) / 180;
	}
	var costheta = Math.cos(rotation);
	var sintheta = Math.sin(rotation);
	
	if (document.all && !window.opera) {
		var canvas = document.createElement('img');

		canvas.src = p.src;
		canvas.height = p.height;
		canvas.width = p.width;

		canvas.style.filter = "progid:DXImageTransform.Microsoft.Matrix(M11="+costheta+",M12="+(-sintheta)+",M21="+sintheta+",M22="+costheta+",SizingMethod='auto expand')";
		if(Math.abs(p.angle)==90||Math.abs(p.angle)==270)
		{
			imgDem = {};
			imgDem.w = p.height;
			imgDem.h = p.width;
			imgDem = $.imgResize({"w": parentDem.width() ,"h": parentDem.height()},{"w":imgDem.w,"h":imgDem.h});
			canvas.style.width = imgDem.h;
			canvas.style.height = imgDem.w;
		}else{
			var pl = new Image()
			pl.src = p.src;
			imgDem = {};
			imgDem.w = pl.width;
			imgDem.h = pl.height;
			imgDem = $.imgResize({"w": parentDem.width() ,"h": parentDem.height()},{"w":imgDem.w,"h":imgDem.h});
			canvas.style.width = imgDem.w;
			canvas.style.height = imgDem.h;
		}
		
	} else {
		var canvas = document.createElement('canvas');
		if (!p.oImage) {
			canvas.oImage = new Image();
			canvas.oImage.src = p.src;
			$.data(document.body, "p"+parentDem.attr("id"), {pwidth:p.width, phigh:p.height});
		} else {
			canvas.oImage= p.oImage;
		}
		
		var imgObj = $.data(document.body, "p"+parentDem.attr("id"));
		if(Math.abs(p.angle)==90||Math.abs(p.angle)==270)
		{
			imgDem = {};
			imgDem.w = imgObj.phigh;
			imgDem.h = imgObj.pwidth;
			imgDem = $.imgResize({"w": parentDem.width() ,"h": parentDem.height()},{"w":imgDem.w,"h":imgDem.h});
			canvas.oImage.width = imgDem.h;
			canvas.oImage.height = imgDem.w;
		}else{
			imgDem = {};
			imgDem.w = imgObj.pwidth;
			imgDem.h = imgObj.phigh;
			imgDem = $.imgResize({"w": parentDem.width() ,"h": parentDem.height()},{"w":imgDem.w,"h":imgDem.h});
			canvas.oImage.width = imgDem.w;
			canvas.oImage.height = imgDem.h;
		}
		
		canvas.style.width = canvas.width = Math.abs(costheta*canvas.oImage.width) + Math.abs(sintheta*canvas.oImage.height);
		canvas.style.height = canvas.height = Math.abs(costheta*canvas.oImage.height) + Math.abs(sintheta*canvas.oImage.width);

		var context = canvas.getContext('2d');
		context.save();
		if (rotation <= Math.PI/2) {
			context.translate(sintheta*canvas.oImage.height,0);
		} else if (rotation <= Math.PI) {
			context.translate(canvas.width,-costheta*canvas.oImage.height);
		} else if (rotation <= 1.5*Math.PI) {
			context.translate(-costheta*canvas.oImage.width,canvas.height);
		} else {
			context.translate(0,-sintheta*canvas.oImage.width);
		}
		context.rotate(rotation);
		context.drawImage(canvas.oImage, 0, 0, canvas.oImage.width, canvas.oImage.height);
		context.restore();
	}

	canvas.id = p.id;
	canvas.className = p.className;
	canvas.angle = p.angle;
	p.parentNode.replaceChild(canvas, p);
}

jQuery.fn.rotateRight = function(parentDem) {
	this.rotate(parentDem,90);
}

jQuery.fn.rotateLeft = function(parentDem) {
	this.rotate(parentDem,-90);
}

jQuery.imgResize = function(parentDem,imgDem){
	if(imgDem.w>0 && imgDem.h>0){
		var rate = parentDem.w/imgDem.w;
		if(rate <= 1){   
			imgDem.w = imgDem.w*rate;
			imgDem.h = imgDem.h*rate;
		}else{
			imgDem.w = imgDem.w;
			imgDem.h = imgDem.h;
		}
	}
	return imgDem;
}