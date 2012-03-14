<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetvideostatusAction extends ShaiAction
{	
	var $success = null;
	var $msg = null;
	var $video_id = null;
	
	function __construct()
	{
		parent::__construct();
		$this->no_anonymous = false;
	}
    
	function handle($args)  {
        parent::handle($args);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') { // || $_SERVER['REQUEST_METHOD'] == 'POST'       	
	        $vid = $this->trimmed('vid');
			$picpath = $this->trimmed('picpath');			
			$size = $this->trimmed('size');
			$timelen = $this->trimmed('timelen');
			$status = $this->trimmed('status');
			$md5 = $this->trimmed('md5');
			
			$pass = '073b3cba749816c388dc268b553a63af';
			
			//md5(vid.picpath.size.timelen.status.加密密钥)
			//验证是否正确
//			if(strtoupper(md5($vid.$picpath.$size.$timelen.$status.$pass)) == $md5) {
			if((intval($status) == 1) && (strtoupper(md5($vid.$picpath.$size.$timelen.$status.$pass)) == $md5)) {
				$video = Video::getVideoByVid($vid);
				if($video) {
					$origVideo = clone($video);
		    		//$video->notice_id = $notice->id;
		    		$video->picpath = $picpath;
		    		$video->size = $size;
		    		$video->timelen = $timelen;
		    		$video->status = $status;
		    		$video->update($origVideo);
		    		
		    		$notice = Notice::staticGet('id', $video->notice_id);
		    		if($notice) {
		    			$orig = clone($notice);
			    		$pos = strpos($notice->rendered, 'src="');
						if ($pos === false) {
						} else {
						   $notice_text = substr($notice->rendered, 0, $pos+strlen('src="'));
						   $notice->rendered =  $notice_text.$video->picpath.substr($notice->rendered, strlen($notice_text));
						}
//			    		$notice->rendered = preg_replace('/<img class="smallimage" src="(.*)"/i', $video->picpath,$notice->rendered);
		    			$notice->is_banned = 0;
		    			$notice->update($orig);
		    			$notice->blowCaches(true);
		    		}
				}
			}
//			}
        } 
	}
}