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
class NewretweetAction extends ShaiAction
{
	var $error = null;
	
	function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}
        
    	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        	$this->clientError('消息转载只支持POST方法.');
        	return false;
        }

        return true;
    }
	
    function handle($args)
    {
    	parent::handle($args);
    	
        try {
            $this->saveNewNotice($args);
        } catch (Exception $e) {
			$this->error = new AjaxError();
			$this->error->showError($e->getMessage());
            return;
        }
    }
    
    function saveNewNotice($args)
    {
        $content = $this->trimmed('status_textarea');
        if (is_null($content)) {
            $this->clientError('您没有输入内容');
        }
        
        $content_shortened = common_shorten_links($content);
        if (mb_strlen($content_shortened, 'utf-8') > 280) {
            //直接反应到页面上最好
            $this->clientError('信息太长, 最大为280个字。');
        }
        
        $retweet_from = $this->trimmed('inretweetfrom');
        $oretweet_from = $this->trimmed('orinretweetfrom');
    	if(!$oretweet_from || is_numeric($oretweet_from)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }
   		if(!$retweet_from || !is_numeric($retweet_from)) {
            $this->clientError('您访问的链接错误.', 403);
            return;
        }

        
//        $simple_content = common_filter_huoxing($content_shortened);
//		if (common_banwordCheck($simple_content)) {
//			$_SESSION['banned_count'] += 1;		
//        	$this->clientError('不要发布政治、反动、反社会言论，请重新修改您的内容。(恶意发送将会禁言，删除帐号)');
//        	return;
//    	}
        $this->cur_user->query('BEGIN');
        
    	$retweeted = Notice::staticGet('id',$retweet_from);
    	$temp = clone($retweeted);
    	$retweeted->retweet_num ++;
    	$retweeted->update($temp);
    	
    	Notice_heat::addHeat($retweet_from,3);
    	User_grade::addScore($retweeted->user_id, 2);
    	
    	if($oretweet_from)
    	{
    		$oretweeted = Notice::staticGet('id',$oretweet_from);
    		if($oretweeted) {
	    		$temp = clone($oretweeted);
	    		$oretweeted->retweet_num ++;
	    		$oretweeted->update($temp);
	    		Notice_heat::addHeat($oretweet_from,3);
	    		User_grade::addScore($oretweeted->user_id, 2);
    		} else {
    			$oretweeted = Deleted_notice::staticGet('id',$oretweet_from);
    			if($oretweeted) {
	    			$temp = clone($oretweeted);
		    		$oretweeted->retweet_num ++;
		    		$oretweeted->update($temp);
    			}
    		}
    	}
    	///添加对跟消息retweet——num +1操作。
	    $notice = Notice::saveNew($this->cur_user->id, $content_shortened, null, 
        							'web', 1, array('retweet_from' => $oretweet_from ? $oretweet_from : $retweet_from, 
        							'content_type' => 1));
                              
        if (is_string($notice)) {
            $this->clientError($notice);
            return;
        }
    
    	if($this->trimmed('discusscurrent')){
    		$retweeted = Notice::staticGet('id',$retweet_from);
    		$temp = clone($retweeted);
    		$retweeted->discussion_num ++;
    		$retweeted->update($temp);
        	$discussion = Discussion::saveNewDis($retweet_from, $this->cur_user->id, $content_shortened, null, 'web');
//        	if(! $discussion) {
//        		$this->cur_user->query('ROLLBACK');
//        		return;
//        	}
    	}
    	if($this->trimmed('discussoriginal')){
    		$oretweeted = Notice::staticGet('id',$oretweet_from);
    		$temp = clone($oretweeted);
    		$oretweeted->discussion_num ++;
    		$oretweeted->update($temp);
    		$discussion = Discussion::saveNewDis($oretweet_from,$this->cur_user->id,$content_shortened, null, 'web');
//    		if(!$discussion) {
//        		$this->cur_user->query('ROLLBACK');
//        		return;
//        	}
    	}
    	Notice::addRetweetToInboxes($this->cur_user->id, $retweet_from, $notice->created);
    	$this->cur_user->query('COMMIT');
    	
       if ($this->boolean('ajax')) {
			$stringer = new XMLStringer();
	        $nli = new NoticeListItem($notice, $stringer);
        	$nli->show();
	        $this->showJsonResult(array('result' => 'true',
	        	'html' => $stringer->getString(),
	        	'nid' => $retweet_from,
	        	'add_discuss' => ($this->trimmed('discusscurrent') ? 'true' : 'false'),
	        	'add_origin_discuss' => ($this->trimmed('discussoriginal') ? 'true' : 'false'),
	        	'oid' => ($oretweet_from ? $oretweet_from : 0)
	        ));
       } else {
            common_redirect(common_path('discussionlist/' . $notice->id), 303);
       }
    }
}