<?php
/**
 * Shaishai, the distributed microblog
 *
 * new notice form
 *
 * PHP version 5
 *
 * @category  Notice
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * New notice form
 *
 * @category Notice
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class NewnoticeAction extends ShaiAction
{
	/**
     * new notice view
     */
	var $new_notice_view = null;
	
	//video 
	var $video_id = null;
	
	//photo
	var $photo_id = null;
	
	//传入到saveNew的参数
	var $reply_to = null;
	var $retweet_from = null;
	var $reply_to_uname = null;
	var $is_banned = null;
	var $content_type = null;
	var $add_rendered = null;
	var $add_content = null;
	var $content_shortened = null;
	var $topic_type = null;
	var $content = null;
	
	var $nnl = null;
	
    function handle($args)
    {
		parent::handle($args);
		
		$this->handleNewNotice($args);
    }
    
	/**
     * Handle to new notice
     * Method for overriding
     *
     */
    function handleNewNotice($args)
    {
        $this->new_notice_view = TemplateFactory::get('NewnoticeHTMLTemplate');
		$error = new AjaxError();
		
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // check for this before token since all POST and FILES data
            // is losts when size is exceeded
            if (empty($_POST) && $_SERVER['CONTENT_LENGTH']) {
                $error->showError('您当前上传的文件超过规定的大小。');
            }
            
            try {
                $this->saveNewNotice($args);
            } catch (Exception $e) {            	
				$error->showError($e->getMessage());
                return;
            }
        } else {
            $this->new_notice_view->showForm($args);
        }
    }

    /**
     * Save a new notice, based on arguments
     *
     * If successful, will show the notice, or return an Ajax-y result.
     * If not, it will show an error message -- possibly Ajax-y.
     *
     * Also, if the notice input looks like a command, it will run the
     * command and show the results -- again, possibly ajaxy.
     *
     * @return void
     */
    function saveNewNotice($args)
    {        
        $this->preHandle();   
                 
		$this->nnl = new NewnoticeLib();
		$this->handleMime();
    
        $from_source = 'web';

        $options = array(
        	'reply_to' => $this->reply_to ? $this->reply_to : null, 
        	'uri' => null, 
			'created' => null, 
			'addRendered' => $this->add_rendered, 
			'is_banned' => $this->is_banned, 
			'content_type' => $this->content_type, 
			'topic_type' => $this->topic_type, 
			'retweet_from' => $this->retweet_from ? $this->retweet_from : 0, 
			'reply_to_uname' => $this->reply_to_uname);
        
        $replyinbox = 1;
        if($this->topic_type == 4) {
        	//group 不存在回复
        	$replyinbox = 0;
        } else {
	    	if ($this->trimmed('newreply')) {
	    		$replyinbox = $this->trimmed('replyinbox') == 1 ? 0 : 1;
				$options['replyonly'] = $replyinbox;
			}
  		}
		
        $notice = Notice::saveNew($this->cur_user->id, $this->content_shortened, $this->add_content, 
        							$from_source, 1, $options);
        
        $this->postHandle($notice);
        
        if ($this->trimmed('suggestwho')) {
        	$this->sendSuggestSysmessage($this->cur_user, $this->trimmed('suggestwho'), $this->content);
        }
        
        if ($this->boolean('ajax')) {
        	$this->new_notice_view->showAJAXNotice($args, $notice, $replyinbox); 
        } else {
            $returnto = $this->trimmed('returnto');
            $replyto = $this->trimmed('replyto');
            if ($returnto) {
                $url = common_local_url($returnto,
                                        array('uname' => $this->cur_user->uname));
            } else {
                $url = common_path('discussionlist/' . $notice->id);
            }
            common_redirect($url, 303);
        }
    }
    
    function sendSuggestSysmessage($fromUser, $toUserId, $suggest) {
		$content = '用户 ' . $fromUser->nickname . ' 向TA的关注者们推荐了您，TA的推荐语是:' . $suggest;
		$rendered = '用户 ' .  common_user_linker($fromUser->id) . ' 向TA的关注者们推荐了您，TA的推荐语是:<br />"' . $suggest . '"';
        System_message::saveNew($toUserId, $content, $rendered, 0);
    }
    
    function preHandle() 
    {
    	//topic_type : common=0, group=4
    	$this->topic_type = 0;
		
    	if (common_config('throttle', 'enabled') && !Notice::checkEditThrottle($this->cur_user->id)) {
			common_log(LOG_WARNING, 'Excessive posting by profile #' . $this->cur_user->id . '; throttled.');
			$this->clientError("您在短时间内发布了过多的消息, 请过几分钟再发消息. ");
		}
		
    	$banned = common_config('profile', 'banned');
		if ( in_array($this->cur_user->id, $banned) || in_array($this->cur_user->uname, $banned)) {
			common_log(LOG_WARNING, "Attempted post from banned user: $this->cur_user->uname (user id = $this->cur_user->id).");
			return '在这个网站您被禁止发布消息. ';
		}
		
        $this->content = $this->trimmed('status_textarea');
        if (is_null($this->content)) {
            $this->clientError('您没有输入内容');
        }
		
        // 将缩链和判断长度放到Notice::saveNew里,避免对于有附件的消息的逻辑错误
        $this->content_shortened = $this->content;

        // 来自群组的消息
        if ($this->trimmed('mode') == 'group'){
            $this->content_shortened = '!' . $this->arg('mode_identifier') . ' ' . $this->content_shortened;
            $this->topic_type = 4;
        }
        
        //回复哪条消息
        $this->reply_to = $this->trimmed('inreplyto');
    	if($this->reply_to && !is_numeric($this->reply_to)) {
            $this->clientError('您回复的原消息不存在.', 403);
            return;
        }
        
        //转载某条消息
        $this->retweet_from = $this->trimmed('inretweetfrom');
    	if($this->retweet_from && !is_numeric($this->retweet_from)) {
            $this->clientError('您转载的原消息不存在.', 403);
            return;
        }
        
        //@某个人
        $this->reply_to_uname = $this->trimmed('atuname');
        
        //屏蔽词
        $this->is_banned = 0;
//        $simple_content = common_filter_huoxing($this->content_shortened);
//		if (common_banwordCheck($simple_content)) {
//			$this->is_banned = 1;
//		}
		//通过屏蔽检查, 但现在要加审核
		if($this->is_banned == 0) {
			if($_SESSION['banned_count'] >= 3)
				$this->is_banned = 2;	//ban forever
		} else {
			$_SESSION['banned_count'] += 1;
		}
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
        		$this->clientError($paras);
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
				$this->clientError($paras);
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
	        	$this->clientError($paras);
	        }
        }
      
		//上传图片
		$attach_photo_upload = $this->trimmed('noticefilename');
       	if($attach_photo_upload) {
       		$this->content_type = 4;
       		$paras = $this->nnl->handleUploadPhoto($attach_photo_upload, $this->cur_user);
       		$this->add_content = $paras['add_content'];
			$this->add_rendered = $paras['add_rendered'];
			$this->photo_id = $paras['photo_id'];
       	}
    }

    function postHandle($notice)  
    {
        if ($this->is_banned == 1) {
        	$this->clientError('不要发布政治、反动、反社会言论，请重新修改您的内容。(恶意发送将会禁言，删除帐号)');
    	} else if ($this->is_banned == 2) {    	
        	$this->clientError('鉴于您前面发了一些敏感瞬间, 我们要对您发的消息进行敏感内容审核，审核通过后就会发出，请稍等。');
        }
    	                
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
            $this->clientError($notice);
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
        
        //common_broadcast_notice($notice);
    }
}