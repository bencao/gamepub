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

require_once INSTALLDIR . '/lib/discusslist.php';


class NewdiscussAction extends ShaiAction
{
	var $notice_id;
	var $content;
	var $root_notice;
	var $discussion;
	
	var $error = null;
	
	function prepare($args) {
		$this->error = new AjaxError();
		
		try {
			if (! parent::prepare($args)) {return false;}
			
			if ($_SERVER['REQUEST_METHOD'] != 'POST') {
				 $this->clientError('需要POST方法');
				 return false;
			}
			
		 	$this->content = $this->trimmed('status_textarea'); //content
//			$simple_content = common_filter_huoxing($this->content);
//			if (common_banwordCheck($simple_content)) {
//				$_SESSION['banned_count'] += 1;
//				$this->clientError('您的评论含有非法关键词, 请重新修改您的内容.', 403);
//	            return;
//			}
		
	        $this->notice_id = $this->trimmed('indiscussto');
	
	        if(! $this->notice_id || ! is_numeric($this->notice_id)) {
	            $this->clientError('您评论的消息不存在.', 403);
	            return;
	        }
	        
	        $this->root_notice = Notice::staticGet('id', $this->notice_id);
	        
	        if (! $this->root_notice) {
	        	$this->clientError('回复的消息不存在');
	            return;
	        }
	     
	        if (is_null($this->content)) {
	            $this->clientError('没有内容');
	            return;
	        }
	        
			$this->content = common_shorten_links($this->content);
	        if (mb_strlen($this->content, 'utf-8') > 280) {
	            $this->clientError('此消息太长, 最大长度为280个字.');
	            return;
	        }
		} catch (Exception $e) {
			$this->error->showError($e->getMessage());
            return;
        }
        
		return true;
	}
	
    function handle($args)
    {
    	try {
	    	parent::handle($args);
	    	
			$this->discussion = Discussion::saveNewDis($this->notice_id, $this->cur_user->id, $this->content, null, 'web');
	        
			$orig = clone($this->root_notice);
			$this->root_notice->discussion_num ++;
			if($this->root_notice->topic_type == 4) {				
				$group_id = Group_inbox::getGroupId($this->root_notice->id);
				User_group::addHeat($group_id, 1);
				
				//清掉缓存就可
				$cache = common_memcache();
				if ($cache) {					
					$cache->delete(common_cache_key('user_group:notice_ids:' . $group_id));
					for($i=1; $i<5; $i++)
						$cache->delete(common_cache_key('user_group:notice_ids:'.$group_id.':'.$i));
						
					$group = User_group::staticGet('id', $group_id);
					$fts = First_tag::getFirstTags($group->game_id);
					foreach ($fts as $id => $name) {
						$cache->delete(common_cache_key('user_group:notice_ids:'.$group_id.':'.$id));
					}
					$cache->delete(common_cache_key('user_group:notice_ids:' . $group_id.';last'));
					for($i=1; $i<5; $i++)
						$cache->delete(common_cache_key('user_group:notice_ids:'.$group_id.':'.$i.';last'));
					foreach ($fts as $id => $name) {
						$cache->delete(common_cache_key('user_group:notice_ids:'.$group_id.':'.$id.';last'));
					}
					
				}
			}
			$this->root_notice->update($orig);
			
			// insert discuss_unread
			if ($this->root_notice->user_id != $this->cur_user->id) {
				$dn = new Discussion_unread();
				$dn->notice_id = $this->notice_id;
				$dn->receiver_id = $this->root_notice->user_id;
				$dn->sender_id = $this->cur_user->id;
				$dn->discussion_id = $this->discussion->id;
				$dn->created = common_sql_now();
				$dn->insert();
			}
        
	        if ($this->boolean('ajax')) {
	        	if ($this->trimmed('from') == 'detail') {
	        		$this->_showAjaxFromDetailPage();
	        	} else {
	        		$this->_showAjaxFromNoticePage();
	        	}
	//            	$this->newdiscussView->showExtraDis($args, $discussion);
	//            	$this->newdiscussView->showExtraDis($args, $discussion, 2); 
	        } else {
	            common_redirect(common_path('discussionlist/' . $this->notice_id), 303);
	        }
    	} catch (Exception $e) {
			$this->error->showError($e->getMessage());
            return;
        }
        
    }
    
    function _showAjaxFromDetailPage() {
//    	$this->view = TemplateFactory::get('HTMLTemplate');
//        $this->view->startHTML('text/xml;charset=utf-8');
		$stringer = new XMLStringer();
		$discusslistitem = new DiscussListItem($this->discussion, $stringer);
        $discusslistitem->show();
        $this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));
    }
    
	function _showAjaxFromNoticePage() {
//    	$this->view = TemplateFactory::get('HTMLTemplate');
//        $this->view->startHTML('text/xml;charset=utf-8');
        $stringer = new XMLStringer();
        $noticediscusslistitem = new NoticeDiscussListItem($stringer, $this->discussion, $this->root_notice, $this->cur_user);
        
        $noticediscusslistitem->show();
        
        $this->showJsonResult(array('result' => 'true', 'html' => $stringer->getString()));
//        $this->view->endHTML();
    }
}