<?php
/**
 * Shaishai, the distributed microblog
 *
 * Show hot topic notices
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Show hot topic notices
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class HottopicsHTMLTemplate extends PublicthreecolumnHTMLTemplate
{
		/**
     * Title of the page
     *
     * @return page title, including page number if over 1
     */

    function title()
    {
        return common_config('site', 'name') . '玩家对“' . $this->args['tag_name'] . '”话题展开的热烈讨论';
    }
    
	function metaKeywords() {
		return $this->args['tag_name'] . '、' . $this->args['tag_name'] . '评论、' . $this->args['tag_name'] . '音乐、' . $this->args['tag_name'] . '视频、' . $this->args['tag_name'] . '图片';
	}
    
	function metaDescription() {
		return $this->args['tag_name'] . '是目前GamePub玩家中最流行的话题。我们汇集了大家发表的关于'. $this->args['tag_name'] . '的文字、音乐、视频、图片消息，让您即时了解最新的' . $this->args['tag_name'] . '动态。';
	}
	
	function showRightside() {
		$this->showSearchFormWidget();
    	
		//话题列表
		$this->tu->showHotWordList($this->args['hotwords'], '火热话题榜');
		
//    	$users = common_stream('userids:mosttalk', array("Notice", "getMosttalkUsers"), array(20), 3600 * 24);
//    	$users = common_random_fetch($users,5);
//    	$this->showUserListWidget($users, '游戏酒馆草根达人',common_local_url('rank',array('type' => 'user')));
    	
    	$users = common_stream('userids:mostvisit', array("Profile", "getMostvisitUsers"), array(20), 3600 * 24);
    	$users = common_random_fetch($users,5);
    	if($users)
    		$this->showUserListWidget($users, '游戏酒馆人气之星',common_local_url('rank',array('type' => 'user')));
    	
    	
		$subs = common_stream('userids:active', array("Grade_record", "getActiveUsers"), array(20), 3600 * 24);
    	$subs = common_random_fetch($subs,5);
    	if($subs)
    		$this->showUserListWidget($subs, '今日活跃用户',common_local_url('rank',array('type' => 'user')));
    	
    	if($this->cur_user) {
	    	$recommendids = $this->cur_user->getRecommmended();
	    	if($recommendids)
	    		$this->showUserListWidget($recommendids, '您可能感兴趣的人', common_local_url('rank',array('type' => 'user')), true);
    	}
    }
    
    function showEmptyList()
    {
        $message = '暂时没有讨论主题';

        $this->elementStart('div', 'b_ph guide');
        $this->raw(common_markup_to_html($message));
        $this->elementEnd('div');
    }

    /**
     * Fill the content area
     *
     * Shows a list of the notices in the public stream, with some pagination
     * controls.
     *
     * @return void
     */

	function showContent()
    {   
    	$this->elementStart('h2');
    	$this->text('火爆话题');
    	$this->element('span', null, '--  平台实时的最火热话题');
        $this->elementEnd('h2');        
        
        $this->elementStart('div', 'intro_hot');
        $this->element('strong', null, '热门话题推荐');
        //tag_name
        $this->text('--  ');
        $this->element('span',null,$this->arg('tag_name'));
        $this->elementStart('ul', 'op');
        $this->elementStart('li');
        if ($this->cur_user) {
        	$this->element('a', array('class' => 'say', 'href' => common_local_url('newnotice'), 'topic' => $this->arg('tag_name')), '我也说两句');
        } else {
        	$this->element('a', array('class' => 'say trylogin', 'href' => common_local_url('register')), '我也说两句');
        }
        $this->elementEnd('li');        
//         $this->elementStart('li');
//        $this->element('a', array('class' => 'seemore', 'href' => '#'), '查看更多');
//        $this->elementEnd('li');      
        $this->elementEnd('ul'); 
        $this->elementEnd('div'); 
        
        $notice = $this->args['notice'];
        if ($notice && is_array($notice) && count($notice) > 0) {
    		$nl = new HottopicNoticeList($notice, $this);
        	$cnt = $nl->show();
        } else if ($notice && $notice->N > 0) {
        	$nl = new NoticeList($notice, $this);
        	$cnt = $nl->show(); 
    	} else {
        	$this->showEmptyList();
        	$cnt = 0;
        }

        //其他的是推荐话题
        if($this->args['tag']) {
        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                     $this->cur_page, 'hottopics', array('tag' => $this->args['tag']));     
    	} else {
        	$this->pagination($this->cur_page > 1, $this->arg('total') > (NOTICES_PER_PAGE * $this->cur_page),
                     $this->cur_page, 'hottopics', array('name' => $this->args['tag_name']));  
        }  		
    }
    
	function showScripts() {
    	parent::showScripts();
 
	    $this->script('js/lshai_say.js');
	    $this->script('js/lshai_search.js');
    }
    
    function showPageNotices($args) {
    	$this->args = $args;
    	$this->cur_page = $args['cur_page'];

//    	$this->startHTML('text/xml;charset=utf-8');

    	$view = TemplateFactory::get('JsonTemplate');
        $view->init_document();

        $xs = new XMLStringer();
        
    	$notice = $this->args['notice'];
    	if ($notice && is_array($notice) && count($notice) > 0) {
    		$nl = new HottopicNoticeList($notice, $xs);
        	$cnt = $nl->show();
        } else if ($notice && $notice->N > 0) {
        	$nl = new ShowgameNoticeList($notice, $xs);
        	$cnt = $nl->show(); 
    	} else {
        	$message = '暂时没有讨论主题';

        	$xs->elementStart('div', 'guide');
        	$xs->raw(common_markup_to_html($message));
        	$xs->elementEnd('div');
        }
        
//       	if($this->args['tag'])
//        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
//                     $this->cur_page, 'hottopics', array('tag' => $this->args['tag']));        		       	
//       	else        	
//        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
//                     $this->cur_page, 'hottopics', array('name' => $this->args['tag_name']));             

    	$xs1 = new XMLStringer();
       	if ($cnt > NOTICES_PER_PAGE) {
        	if ($this->args['tag']) {
        		$attr = array('tag' => $this->args['tag']);
        	} else {
        		$attr = array('name' => $this->args['tag_name']);
        	}
       		$xs1->element('a', array('href' => common_local_url('hottopics', $attr, array('page' => $this->args['page'] + 1)),
                                   'id' => 'notice_more', 'rel' => 'nofollow'));
        }
	    
        $resultArray = array('result' => 'true', 'notices' => $xs->getString(), 'pg' => $xs1->getString());
       	      	 	       	 	
        $view->show_json_objects($resultArray);
        $view->end_document();
    }
    
}

class HottopicNoticeList extends ShowgameNoticeList {
	function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices'));
        $cnt = 0;
		$count = count($this->notice);
		
        for ($i = 0; $i < $count; $i ++) {
            $cnt++;
           
            if ($cnt > NOTICES_PER_PAGE) {
                break;
            }
            $item = $this->newListItem($this->notice[$i]);
            $item->show();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
}