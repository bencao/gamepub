<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class RandverifypicAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function handle($args) {
		parent::handle($args);
		$this->rand_createN();
		
	}
	
	function rand_createN() {
		
		srand((double)microtime()*1000000);
		
		while(($randval=rand()%100000)<10000);
	    
		$_SESSION["login_check_num"] = $randval;
		
		$fontfile=INSTALLDIR."/extlib/AnkeCalligraph.TTF";

		$size=1;
		$h=50;
		$w=200;
	
		$im  =  ImageCreate ( $w,  $h );
	
		$fill = ImageColorAllocate ( $im ,  184,  81, 12 );    
		//$light = ImageColorAllocate ( $im,  255,  255,  255 );    
		//$corners = ImageColorAllocate ( $im ,  153 , 153 ,  102 );    
		//$dark = ImageColorAllocate ( $im , 51, 51 , 0 );    
		//$black = ImageColorAllocate ( $im , 0, 0 , 0 );    
	
		$colors[1] = ImageColorAllocate ( $im ,  255 , 255 ,  255 );    
		$colors[2] = ImageColorAllocate ( $im ,  255*0.95 , 255*0.95 ,  255*0.95 );    
		$colors[3] = ImageColorAllocate ( $im ,  255*0.9 , 255*0.9 ,  255*0.9 );    
		$colors[4] = ImageColorAllocate ( $im ,  255*0.85 , 255*0.85 ,  255*0.85 );    
	
		   
	
		srand(time());
		$c=1;
		$anz=1;
		$step=3/$anz;
		for($i=0;$i<$anz;$i+=1)
		{
			$size=rand(26,35);
			$x=rand(10,80);
			$y=rand(36,42);
			$color=$colors[$c];
			$c+=$step;
			ImageTTFText ($im, $size, 0, $x, $y, $color, $fontfile, $randval);
			ImageTTFText ($im, $size, 0, $x, $y, $color, $fontfile, $randval);
		}
	
		//ImageLine ( $im , 15 ,30, $w-10, $h-30, $fill );    
		
		header("Cache-Control: no-cache");
		header("Pragma: NO-cache");
		header('Content-type: image/png');
		header('Connection: close');
		imagepng($im);
		imagedestroy($im);	
	}
	
}

?>