<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class UploadvideoAction extends ShaiAction
{	
	var $success = null;
	var $msg = null;
	var $video_id = null;
	var $vid = null;
	
	function handle($args)  {
        parent::handle($args);

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
           $this->displayWith('UploadvideoHTMLTemplate'); 
        } else {
        	$tag = $this->trimmed('second_tag');
			$title = $this->trimmed('video_title');			
			$description = $this->trimmed('description');
			$sid = $this->trimmed('sid');
			$cid = $this->trimmed('cid');
			$type = $this->trimmed('type');
        
			$this->vid = $this->uploadVideoInfo($sid, $title, $description, $tag, $cid, $type);
			
           if($this->vid) {
           		$this->success = true;
           		$this->msg = '上传成功. 视频正在审核中, 审核成功之后, 消息会自动显示在您空间的消息列表中.';
           } else {
           		$this->success = false;
           		$this->msg = '视频上传失败, 请重新再试.';
           }
           
           if($this->success) {
	           $add_rendered = $this->saveVideo($title, $this->vid);
	           
	           //保存到数据库中 notice
	           $notice = $this->saveNotice('[' . $tag . ']' . $title . ' ' . $description, $add_rendered);
           }    
           
           $this->addPassVariable('page_msg', $this->msg);
			$this->addPassVariable('page_success', $this->success);
           $this->displayWith('UploadvideoHTMLTemplate'); 
        }
        
        
    }
    
    function uploadVideoInfo($sid, $title, $description, $tag, $cid, $type) {
    	$skey = 'aff0a6a4521232970b2c1cf539ad0a19';
    	$pass = '073b3cba749816c388dc268b553a63af';
    	
    	$opts = array(
			  'http'=> array(
			    'method'=>   "POST",
			    'user_agent'=>    $_SERVER['HTTP_USER_AGENT']
			  )
			); 
		$context = stream_context_create($opts);

    	$str =  file_get_contents('http://v.ku6vms.com/phpvms/api/upLoad/skey/' . $skey . '/v/1/' . 
    		'sid/' . $sid . '/title/' . $title . '/description/' . $description . '/tag/' . $tag . '/cid/' . $cid . '/type/' . $type .
    		'/format/json/md5/'. 
			strtoupper(md5($skey . '1' . $sid . $title. $description . $tag . $pass)), false, $context);
		
		//通过var_dump($str);打印分析返回的结果, 看看是什么错误
		$obj = json_decode($str, true);
		
		if($obj['status'] == 1)
			return $obj['vid'];
		else {
			return null;
		}
    }
    
    function saveVideo($video_title, $vid) {
    	$nnl = new NewnoticeLib();
        $flashsrc = "http://v.ku6vms.com/phpvms/player/html/vid/" . $vid . "/style/le_Eqt_BTjA./";
    	$video_rendered = $nnl->getVideoPlayRendered($flashsrc);
    	
    	$photo_url = ""; //获取图片地址, 审核通过之后获取

    	$this->video_id = Video::saveNew($video_title, $video_rendered, '酷6上传', 100000, "", $flashsrc);
    	
    	return $nnl->getVideoRendered($this->video_id, $photo_url);  
    }
    
    function saveNotice($content, $add_rendered) {
    	
    	$topic_type = 0;
    	
    	// moder equals group means the notice is come from group
        if ($this->trimmed('mode') == 'group'){
            $content = '!' . $this->arg('mode_identifier') . ' ' . $content;
            $topic_type = 4;
        }
        
    	//is_banned=5视频信息待审核
    	$options = array('reply_to' => 'null', 'uri' => null, 
								'created' => null, 'addRendered' => $add_rendered, 
								'is_banned' => 5, 'content_type' => 3, 'topic_type' => $topic_type);
		
        $notice = Notice::saveNew($this->cur_user->id, $content, null, 
        							'web', 1, $options);

        $this->postHandle($notice);
    }
    
	function postHandle($notice)  
    { 
        if (is_string($notice)) {
            $video = new Video();
            $video->id = $this->video_id;
            $video->delete();
            $this->success = false;
           	$this->msg = '保存消息失败.';
            return;
        }
        
        //保存视频的Notice_id信息
        $video = Video::staticGet('id', $this->video_id);
      	if (!empty($video)) {
    		$origVideo = clone($video);
    		$video->notice_id = $notice->id;
    		$video->vid = $this->vid;
    		$video->update($origVideo);
      	}
    }
}