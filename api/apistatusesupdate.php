<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Post a notice (update your status) through the API
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

/**
 * Updates the authenticating user's status (posts a notice).
 *
 * @category  API
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ApiStatusesUpdateAction extends ApiAuthAction
{
    var $source                = null;
    var $status                = null;
    var $in_reply_to_status_id = null;
    var $notice				   = null;

    //video 
	var $video_id = null;
	
	//photo
	var $photo_id = null;

	var $mimetype= null;
		
	//传入到saveNew的参数
	var $reply_to = null;
	var $is_banned = null;
	var $content_type = null;
	var $add_rendered = null;
	var $add_content = null;
	var $content_shortened = null;
	var $topic_type = null;
	var $content = null;
	
	var $nnl = null;
	
    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $this->user = $this->auth_user;

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return false;
        }

        $this->status = $this->trimmed('status');

        if (empty($this->status)) {
            $this->clientError(
                '客户端应当提供 参数\'status\'的值.',
                400,
                $this->format
            );
            return false;
        }

        $this->source = $this->trimmed('source', 'api');

        $this->in_reply_to_status_id  = intval($this->trimmed('in_reply_to_status_id'));  
		
        return true;
    }

    /**
     * Handle the request
     *
     * Make a new notice for the update, save it, and show it
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if(!$this->preHandle())
        	return;
        
        $this->nnl = new NewnoticeLib();
		if(!$this->handleMime())
			return;
        
		$options = array('reply_to' => ($this->reply_to == 'false') ? null : $this->reply_to, 'uri' => null, 
								'created' => null, 'addRendered' => $this->add_rendered, 
								'is_banned' => $this->is_banned, 'content_type' => $this->content_type, 'topic_type' => $this->topic_type);
		
		$replyinbox = 0;
		if(intval($this->trimmed('reply_only')) == 1)
			$replyinbox = 1;
		$options['replyonly'] = $replyinbox;
        $notice = Notice::saveNew($this->user->id, html_entity_decode($this->content_shortened, ENT_NOQUOTES, 'UTF-8'), $this->add_content, 
        							$this->source, 1, $options);

        if(!$this->postHandle($notice))
        	return;

        $this->showNotice($notice);
    }
    
	function preHandle() 
    {		
		//topic_type : common=0, group=4
		$this->topic_type = 0;
		
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->clientError('这个方法需要POST.',
                400, $this->format
            );
            return  false;
        }
        
    	if (common_config('throttle', 'enabled') && !Notice::checkEditThrottle($this->user->id)) {
			common_log(LOG_WARNING, 'Excessive posting by profile #' . $this->user->id . '; throttled.');
			$this->clientError("您在短时间内发布了过多的消息, 请过几分钟再发消息. ", 400, $this->format);
			return false;
		}
		
    	$banned = common_config('profile', 'banned');
		if ( in_array($this->user->id, $banned) || in_array($this->user->uname, $banned)) {
			common_log(LOG_WARNING, "Attempted post from banned user: $this->user->uname (user id = $this->user->id).");
			$this->clientError( '在这个网站您被禁止发布消息. ', 400, $this->format);
			return false;
		}
		
		// 将缩链和判断长度放到Notice::saveNew里,避免对于有附件的消息的逻辑错误
		$this->content_shortened = $this->status;
        
        $this->reply_to  = null;
        if (!empty($this->in_reply_to_status_id)) {
			// Check whether notice actually exists
            $this->reply_to = Notice::staticGet($this->in_reply_to_status_id);
            if ($this->reply_to) {
                $this->reply_to = $this->in_reply_to_status_id;
            } else {
                $this->clientError('未找到',$code = 404, $this->format );
                    return false;
                }
        }            
        
        //屏蔽词
        $this->is_banned = 0;
//        $simple_content = common_filter_huoxing($this->content_shortened);
//		if (common_banwordCheck($simple_content)) {
//			//$this->is_banned = 1;
//			$this->clientError('不要发布政治、反动、反社会言论，请重新修改您的内容。(恶意发送将会禁言，删除帐号)', 400, $this->format);
//			return false;
//		}
		return true;
    }
    
    function handleMime()
    {
    	//content_type all =0, text=1, audio=2, video=3, image=4
		$this->content_type = 1;
		
        $attach_photourl = $this->trimmed('photo');
        $attach_videourl = $this->trimmed('video');
        $attach_audiourl = $this->trimmed('audio');
        
        //图片链接
        if ($attach_photourl && $attach_photourl != '' && $attach_photourl != 'http://') {
        	$this->content_shortened = str_replace($attach_photourl, '', $this->content_shortened);
        	$paras = $this->nnl->handlePhotoUrl($attach_photourl, $this->cur_user);
        	if (is_array($paras)) {	        		
        		$this->content_type = 4; 
				$this->add_content = $paras['add_content'];
				$this->add_rendered = $paras['add_rendered'];
				$this->photo_id = $paras['photo_id'];
        	} else {
        		$this->clientError($paras, 400, $this->format);
        	}
        } 
        //视频链接
        if ($attach_videourl && $attach_videourl != '' && $attach_videourl != 'http://') {
        	$this->content_shortened = str_replace($attach_videourl, '', $this->content_shortened);
        	$paras = $this->nnl->handleVideoUrl($this->content_shortened, $attach_videourl);
			if (is_array($paras)) {
				$this->content_type = 3;
				$this->add_content = $paras['add_content'];
				$this->add_rendered = $paras['add_rendered'];
				$this->video_id = $paras['video_id'];
			} else {
				$this->clientError($paras, 400, $this->format);
			}
        } 
        //音乐链接
        if ($attach_audiourl && $attach_audiourl != '' && $attach_audiourl != 'http://') {
        	$this->content_shortened = str_replace($attach_audiourl, '', $this->content_shortened);		
        	$paras = $this->nnl->handleAudioUrl($attach_audiourl);    
        	if (is_array($paras)) {    	
	        	$this->content_type = 2;
				$this->add_content = $paras['add_content'];
				$this->add_rendered = $paras['add_rendered'];
	        } else {
	        	$this->clientError($paras, 400, $this->format);
	        }
        }
      
        //上传图片
      	if (isset($_FILES['photofile']['error'])) {
	        try {
	            $imagefile = ImageFile::fromUpload('photofile', $this->user);
	        } catch (Exception $e) {
	        	$this->clientError($e->getMessage(), 400, $this->format);
	            return false;
	        }
	        $this->mimetype = image_type_to_mime_type($imagefile->type);
        }

        if (isset($this->mimetype)) {
        	$this->content_type = 4; 
			$paras = $this->nnl->handleMimePhoto($this->mimetype, $this->user);
			$this->add_content = $paras['add_content'];
			$this->add_rendered = $paras['add_rendered'];
			$this->photo_id = $paras['photo_id'];
        }
        
        return true;
    }
    
    function postHandle($notice)  
    {
//        if ($this->is_banned == 1) {
//        	$this->clientError('不要发布政治、反动、反社会言论，请重新修改您的内容。(恶意发送将会禁言，删除帐号)', 400, $this->format);
//    	} else if ($this->is_banned == 2) {    	
//        	$this->clientError('鉴于您前面发了一些敏感瞬间, 我们要对您发的消息进行敏感内容审核，审核通过后就会发出，请稍等。', 400, $this->format);
//        }
    	                
        if (is_string($notice)) {
        	//删除图片
            if (isset($this->photo_id)) {
                $this->nnl->deletePhoto($this->photo_id);
            }
            //删除视频
            if ($this->video_id) {
            	$video = new Video();
            	$video->id = $this->video_id;
            	$video->delete();
        	}
            $this->clientError($notice, 400, $this->format);
            return false;
        }
        
        //保存视频的Notice_id信息
        if ($this->video_id) {
        	 $video = Video::staticGet('id', $this->video_id);
      		if (!empty($video)) {
	    		$origVideo = clone($video);
	    		$video->notice_id = $notice->id;
	    		$video->update($origVideo);
      		}
        }
        return true;
    }
    
    /**
     * Show the resulting notice
     *
     * @return void
     */

    function showNotice($notice)
    {
        if (!empty($notice)) {
            if ($this->format == 'xml') {
                $this->showSingleXmlStatus($notice);
            } elseif ($this->format == 'json') {
                $this->show_single_json_status($notice);
            }
        }
    }

    /**
     * Is this command supported when doing an update from the API?
     *
     * @param string $cmd the command to check for
     *
     * @return boolean true or false
     */

    function supported($cmd)
    {
        static $cmdlist = array('MessageCommand', 'SubCommand', 'UnsubCommand',
            'FavCommand', 'OnCommand', 'OffCommand');

        if (in_array(get_class($cmd), $cmdlist)) {
            return true;
        }

        return false;
    }

}
