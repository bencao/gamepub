<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Abstraction for an image file
 *
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * A wrapper on uploaded files
 *
 * Makes it slightly easier to accept an image file from upload.
 *
 * @category Image
 * @package  ShaiShai
 */

class ImageFile
{
    var $id;
    var $filepath;
    var $barename;
    var $type;
    var $height;
    var $width;

    function __construct($id=null, $filepath=null, $type=null, $width=null, $height=null)
    {
        $this->id = $id;
        $this->filepath = $filepath;
        
        $info = @getimagesize($this->filepath);
        $this->type = ($info) ? $info[2]:$type;
        $this->width = ($info) ? $info[0]:$width;
        $this->height = ($info) ? $info[1]:$height;
    }

    static function fromUpload($param='upload', $user=null)
    {
    	if (empty($user)) {
            $user = common_current_user();
        }
        
        switch ($_FILES[$param]['error']) {
         case UPLOAD_ERR_OK: // success, jump out
            break;
         case UPLOAD_ERR_INI_SIZE:
         case UPLOAD_ERR_FORM_SIZE:
            throw new Exception(sprintf('您上传的文件过大， 最大为 %d。',
                ImageFile::maxFileSize()));
            return;
         case UPLOAD_ERR_PARTIAL:
            @unlink($_FILES[$param]['tmp_name']);
            throw new Exception('不完整的上传。');
            return;
         case UPLOAD_ERR_NO_FILE:
            // No file; probably just a non-AJAX submission.
            return;
         default:
            throw new Exception('上传文件时系统错误。');
            return;
        }
        
        if (!Imagefile::respectsQuota($user, $param)) {
            // Should never actually get here
            @unlink($_FILES[$param]['tmp_name']);
            throw new ClientException('您上传的图片超过了1M, 我们最大支持1M.');
            return;
        }

        $info = @getimagesize($_FILES[$param]['tmp_name']);

        if (!$info) {
            @unlink($_FILES[$param]['tmp_name']);
            throw new Exception('不是图片或文件损坏。');
            return;
        }

        if ($info[2] !== IMAGETYPE_JPEG &&
            $info[2] !== IMAGETYPE_PNG
            && $info[2] !== IMAGETYPE_GIF ) { //&&  $info[2] !== IMAGETYPE_BMP     	

            @unlink($_FILES[$param]['tmp_name']);
            throw new Exception('不支持的图片文件格式。');
            return;
        }

        return new ImageFile(null, $_FILES[$param]['tmp_name']);
    }

    function resize($size, $x = 0, $y = 0, $w = null, $h = null, $group=null)
    {
        $w = ($w === null) ? $this->width:$w;
        $h = ($h === null) ? $this->height:$h;

        if (!file_exists($this->filepath)) {
            throw new Exception('文件丢失');
            return;
        }

        // Don't crop/scale if it isn't necessary
        if ($size === $this->width
            && $size === $this->height
            && $x === 0
            && $y === 0
            && $w === $this->width
            && $h === $this->height) {

            $outname = Avatar::filename($this->id,
                                        image_type_to_extension($this->type),
                                        $size,
                                        common_timestamp());
            if ($group) {
            	$outpath = Avatar::path($outname, Avatar::groupsubpath($this->id));
            }else {
                $outpath = Avatar::path($outname, Avatar::subpath($this->id));
            }
            @copy($this->filepath, $outpath);
            return $outname;
        }

        switch ($this->type) {
         case IMAGETYPE_GIF:
            $image_src = imagecreatefromgif($this->filepath);
            break;
         case IMAGETYPE_JPEG:
            $image_src = imagecreatefromjpeg($this->filepath);
            break;
         case IMAGETYPE_PNG:
            $image_src = imagecreatefrompng($this->filepath);
            break;
         case IMAGETYPE_BMP:
            $image_src = imagecreatefromwbmp($this->filepath);
            break;
         default:
            throw new Exception(_('未知文件类型'));
            return;
        }

        $image_dest = imagecreatetruecolor($size, $size);

        if ($this->type == IMAGETYPE_PNG) {// $this->type == IMAGETYPE_GIF ||

            $transparent_idx = imagecolortransparent($image_src);

            if ($transparent_idx >= 0) {

                $transparent_color = imagecolorsforindex($image_src, $transparent_idx);
                $transparent_idx = imagecolorallocate($image_dest, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_dest, 0, 0, $transparent_idx);
                imagecolortransparent($image_dest, $transparent_idx);

            } elseif ($this->type == IMAGETYPE_PNG) {

                imagealphablending($image_dest, false);
                $transparent = imagecolorallocatealpha($image_dest, 0, 0, 0, 127);
                imagefill($image_dest, 0, 0, $transparent);
                imagesavealpha($image_dest, true);

            }
        }

        imagecopyresampled($image_dest, $image_src, 0, 0, $x, $y, $size, $size, $w, $h);

        $outname = Avatar::filename($this->id,
                                    image_type_to_extension($this->type),
                                    $size,
                                    common_timestamp());

        
        if ($group) {
            	$outpath = Avatar::path($outname, Avatar::groupsubpath($this->id));
        }else {
                $outpath = Avatar::path($outname, Avatar::subpath($this->id));
        }

        switch ($this->type) {
         case IMAGETYPE_GIF:
            imagegif($image_dest, $outpath);
            break;
         case IMAGETYPE_JPEG:
            imagejpeg($image_dest, $outpath, 100);
            break;
         case IMAGETYPE_PNG:
            imagepng($image_dest, $outpath);
            break;
         case IMAGETYPE_BMP:
            imagewbmp($image_dest, $outpath);
            break;
         default:
            throw new Exception('未知文件类型');
            return;
        }

        imagedestroy($image_src);
        imagedestroy($image_dest);

        return $outname;
    }

    function unlink()
    {
        @unlink($this->filename);
    }

    static function maxFileSize()
    {
        $value = ImageFile::maxFileSizeInt();

        if ($value >= 1024 * 1024) {
            return ($value/(1024*1024)).'Mb';
        } else if ($value > 1024) {
            return ($value/(1024)).'kB';
        } else {
            return $value;
        }
    }

    static function maxFileSizeInt()
    {
    	return common_config('attachments', 'file_quota');
//        return min(ImageFile::strToInt(ini_get('post_max_size')),
//                   ImageFile::strToInt(ini_get('upload_max_filesize')),
//                   ImageFile::strToInt(ini_get('memory_limit')));
    }

    static function strToInt($str)
    {
        $unit = substr($str, -1);
        $num = substr($str, 0, -1);

        switch(strtoupper($unit)){
         case 'G':
            $num *= 1024;
         case 'M':
            $num *= 1024;
         case 'K':
            $num *= 1024;
        }

        return $num;
    }
    
    //creates directory tree recursively
	static function mkdirs($path, $mode = 0777) 
	{
		$dirs = explode('/',$path);
		$pos = strrpos($path, ".");
		if ($pos === false) { // note: three equal signs
			// not found, means path ends in a dir not file
			$subamount=0;
		}
		else {
			$subamount=1;
		}
		
		for ($c=0;$c < count($dirs) - $subamount; $c++) {
			$thispath="";
			for ($cc=0; $cc <= $c; $cc++) {
				$thispath.=$dirs[$cc].'/';
			}
			if (!file_exists($thispath)) {
			//print "$thispath<br>";
				mkdir($thispath,$mode);
			}
		}
	}
	
	public function imagecreatefrombmp($p_sFile)
    {
        //    Load the image into a string
        $file    =    fopen($p_sFile,"rb");
        $read    =    fread($file,10);
        while(!feof($file)&&($read<>""))
            $read    .=    fread($file,1024);
       
        $temp    =    unpack("H*",$read);
        $hex    =    $temp[1];
        $header    =    substr($hex,0,108);
       
        //    Process the header
        //    Structure: http://www.fastgraph.com/help/bmp_header_format.html
        if (substr($header,0,4)=="424d")
        {
            //    Cut it in parts of 2 bytes
            $header_parts    =    str_split($header,2);
           
            //    Get the width        4 bytes
            $width            =    hexdec($header_parts[19].$header_parts[18]);
           
            //    Get the height        4 bytes
            $height            =    hexdec($header_parts[23].$header_parts[22]);
           
            //    Unset the header params
            unset($header_parts);
        }
       
        //    Define starting X and Y
        $x                =    0;
        $y                =    1;
       
        //    Create newimage
        $image            =    imagecreatetruecolor($width,$height);
       
        //    Grab the body from the image
        $body            =    substr($hex,108);

        //    Calculate if padding at the end-line is needed
        //    Divided by two to keep overview.
        //    1 byte = 2 HEX-chars
        $body_size        =    (strlen($body)/2);
        $header_size    =    ($width*$height);

        //    Use end-line padding? Only when needed
        $usePadding        =    ($body_size>($header_size*3)+4);
       
        //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
        //    Calculate the next DWORD-position in the body
        for ($i=0;$i<$body_size;$i+=3)
        {
            //    Calculate line-ending and padding
            if ($x>=$width)
            {
                //    If padding needed, ignore image-padding
                //    Shift i to the ending of the current 32-bit-block
                if ($usePadding)
                    $i    +=    $width%4;
               
                //    Reset horizontal position
                $x    =    0;
               
                //    Raise the height-position (bottom-up)
                $y++;
               
                //    Reached the image-height? Break the for-loop
                if ($y>$height)
                    break;
            }
           
            //    Calculation of the RGB-pixel (defined as BGR in image-data)
            //    Define $i_pos as absolute position in the body
            $i_pos    =    $i*2;
            $r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
            $g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
            $b        =    hexdec($body[$i_pos].$body[$i_pos+1]);
           
            //    Calculate and draw the pixel
            $color    =    imagecolorallocate($image,$r,$g,$b);
            imagesetpixel($image,$x,$height-$y,$color);
           
            //    Raise the horizontal position
            $x++;
        }
       
        //    Unset the body / free the memory
        unset($body);
       
        //    Return image-object
        return $image;
    } 
    
    static function respectsQuota($user, $param)
    {
        $file = new File;
        $result = $file->isRespectsQuota($user,$_FILES[$param]['size']);
        if ($result === true) {
            return true;
        } else {
            throw new ClientException($result);
        }
    }
    
    static function imageSize($user, $url) {
    	$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data === false) {
		  echo 'cURL failed';
		  exit;
		}
		
		$contentLength = 'unknown';
//		$status = 'unknown';
//		if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
//		  $status = (int)$matches[1];
//		}
		if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
		  $contentLength = (int)$matches[1];
		}
		
//		if($status != 200) {
//			throw new ClientException('您提供的图片链接不可访问.');
//		}
		
		$file = new File;
    	$result = $file->isRespectsQuota($user, $contentLength);
        if ($result === true) {
            return true;
        } else {
            throw new ClientException('您链接图片超过了1M, 我们最大支持1M.');
        }
    }
}