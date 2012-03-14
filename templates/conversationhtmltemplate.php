<?php
/**
 * Shaishai, the distributed microblog
 *
 * Display a conversation in the browser
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
 * Display a conversation in the browser
 *
 * @category Personal
 * @package  Shaishai
 * @author    Guofu Xie <guofu85@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

class ConversationHTMLTemplate extends PersonalHTMLTemplate
{
	var $conversation;
	var $list;
	var $replierlist;
	var $table;
	var $total;
	
	function show($args) {
		$this->conversation = $args['conversation'];
		$this->_buildList();
		sort($this->list);
		
		parent::show($args);
	}
	
    /**
     * Returns the page title
     *
     * @return string page title
     */

    function title()
    {
        return "查看会话";
    }

    /**
     * Show content.
     *
     * Display a hierarchical unordered list in the content area.
     * Uses ConversationTree to do most of the heavy lifting.
     *
     * @return void
     */

    function showContentLeft() {
    }
    
	//探寻树结构, 单树回溯, 通过notice的reply_to回溯, 应当可以完成.
    function _buildList()
    {
        $this->list = array();
        $this->table = array();
        $this->replierlist = array();
        $this->total = 0;
        $this->list[] = $this->conversation->id;
        $notice = clone($this->conversation);
        $this->table[$this->conversation->id] = $notice;
        $this->replierlist[] = $this->conversation->user_id;
        $this->total++;
        while($notice && !is_null($notice->reply_to)) {
        	$this->total++;
        	$this->list[] = $notice->reply_to;
        	$notice = Notice::staticGet('id', $notice->reply_to);
        	if ($notice) {
	        	$this->table[$notice->id] = $notice;
	        	if (! in_array($notice->user_id, $this->replierlist)) {
	        		$this->replierlist[] = $notice->user_id;
	        	}
        	}
        }
    }
    
    function showNoticeForm() {
//    	$this->tu->showTitleBlock('相关会话', 'conversation');
    	
//    	$this->elementStart('form', array('method' => 'post',
//    					'action' => common_local_url('newnotice'),
//    					'id' => 'message_form'));
//    	$this->elementStart('fieldset');
//    	$this->element('legend', null, '消息表单');
//    	
//    	$this->elementStart('div', array('class' => 'replyto'));
//    	$this->element('label', array('for' => 'replyto'), '回复: ');
//        
//       	$this->elementStart('select', array('id' => 'replyto', 'name' => 'replyto'));
//       	$this->element('option', array('value' => ''), '选择要回复的人');
//       	foreach ($this->replierlist as $repid) {
//       		$replier = User::staticGet('id', $repid);
//        	$this->element('option', array('value' => $replier->uname), $replier->nickname);
//        }
//		$this->elementEnd('select');
//    	$this->elementEnd('div');
//    	
//    	$this->elementStart('div', 'form');
//    	$this->element('textarea', array('id' => 'message_data-text',
//                                              'class' => 'rounded8',
//                                              'name' => 'status_textarea'));    	
//    		    	
//	    $this->elementStart('span', 'char');
//    	$this->element('em', null, '280');
//    	$this->text('字剩余');
//    	$this->elementEnd('span');
//    		
//    	$this->element('input', array('class' => 'submit button76 gray76',
////                                           'name' => 'status_submit',
//                                           'type' => 'submit',
//                                           'value' => '发送'));
//    		
//    	$this->hidden('token', common_session_token());
////	    $this->hidden('messageto2', null, 'messageto');
//    		
//    	$this->elementEnd('div');
//    	
//    	$this->elementEnd('fieldset');
//    	$this->elementEnd('form');
    }
    
//    function showRightSection($templateutil, $page_owner_profile) {
//    	$this->navs = new NavList_Home($page_owner_profile);
//    	// We temporarily make replylist uses same navigation as all page
//    	$this->tu->showNavigationWidget($this->navs->lists(), 'home');	
//    }
    
    function showContentInfo()
    {
        //$notices = Notice::conversationStream($this->args['id'], null, null);

        //$ct = new ConversationTree($notices, $this);
        
    	$title = '';
    	foreach ($this->replierlist as $repid) {
       		$replier = User::staticGet('id', $repid);
       		if ($replier) {
	        	if (empty($title)) {
	        		$title .= $replier->nickname;	
	        	} else {
	        		$title .= '和' . $replier->nickname;
	        	}
       		}
    	}
        
    	$this->tu->showTitleBlock($title . '的相关会话', 'conversation');
        
    	$this->element('div', array('class' =>'split', 'style' => 'margin:0;'));
    	
        $ct = new ConversationList($this, $this->conversation, $this->list, $this->table);
        
        $cnt = $ct->show();
        
//        $this->showIllegal();
    }
    
    //移到下满去, 然后点击, 弹出显示, getProfile()为空
	function showIllegal() {
		$this->elementStart('div', array('id'=>'illegalreport', 'title'=>'非法举报', 'style'=>'display:none;'));
		
		$this->elementStart('dl', 'b_el');
//		$notice = Notice::staticGet('id', $this->args['id']);       
        $profile = $this->notice->getProfile();
		$this->element('dt', null, '您将要举报'. $profile->nickname. '的消息。');
		$this->elementEnd('dl');
		
		$this->elementStart('dl', 'b_el');
		$this->element('dt', null, '您的举报将被严格保密，我们将认真阅读您的举报信息并适当处理。');
		$this->elementEnd('dl');
		
		$this->elementStart('div', 'b_cbf');
		$form = new IllegalReportForm($this, 0, $this->notice->id);
        $form->show();
        $this->elementEnd('div');
        
        $this->elementEnd('div');
	}
}

class ConversationList extends NoticeList
{
	var $table = null;
	var $list = null;
	
	function __construct($out, $notice, $list, $table) {
		parent::__construct($notice, $out);
		$this->table = $table;
		$this->list = $list;
	}
	
	function show() 
	{
//        $cnt = $this->_buildList();        
        
        
        //第一条消息
//        $notice = $this->table[$this->list[0]];
//            
//        $profile = $notice->getProfile();
        
		
//        $this->out->element('div', 'instruction', '相关会话内容如下：');
        
        //顶部
//        $this->out->element('div', array('class' =>'b_t'), '查看会话');
//        $this->out->elementStart('div', array('style' => 'color: rgb(58, 103, 165);', 'class' => 'b_pis'));
//        $this->out->text($profile->nickname . ' (' . $profile->uname .')于' . common_date_iso8601($notice->created) . '所发消息的相关对话');
//        $user = common_current_user();
//        if ($notice->user_id != $user->id) {
//			$this->out->element('a', array('id' => 'get_illreport', 'class'=>'b_pis_a', 
//			                               'href' => '#', 'title'=>'非法消息举报'));
//    	}
//    	$this->out->elementEnd('div');
        
        $this->out->elementStart('ol', array('id' => 'notices'));
        
//        $item = new ConversationRootItem($notice, $this->out);
//        $item->show();
        
        //自己写HTML, 加入进去, 首个消息包含其他消息
        $cnt = count($this->list);
        for ($i = $cnt - 1; $i >= 0; $i --) {
           $this->showNoticePlus($this->list[$i]);
        }
             
        $this->out->elementEnd('ol');
             
//        if($this->cur_user && $this->cur_user->id != $profile->id) {
//			$this->out->elementStart('div', 'b_nl_i_rt');
//			$this->out->element('div', 'b_nl_i_rt_title'); 
//			$this->out->elementStart('div', 'b_nl_i_rt_cont');
//			$this->out->elementStart('form', array('method' => 'post',
//	    					'action' => common_local_url('newnotice', array('replyto' => $profile->uname)),
//	    					'id' => 'form_reply'));
//			$this->out->elementStart('div', 'b_nl_i_rt_text');
//	    	$this->out->element('textarea', array('id' => 'notice_data-text',
//	                                              'class' => 'textarea1',
//	                                              'name' => 'status_textarea',
//	    										  'maxlength' => '280'));
//	    	
//			$this->out->elementEnd('div');
//			$this->out->elementStart('div', 'b_nl_i_rt_s');
//			 $this->out->element('input', array('id' => 'notice_action_submit',
//	                                           'name' => 'status_submit',
//	                                           'type' => 'submit',
//	                                           'value' => ''));
//			
//			$this->out->elementEnd('div');
//			$this->out->element('div', 'cb'); 
//			$this->out->hidden('token', common_session_token());
//	    	$this->out->element('input', array('type' => 'hidden',
//	                                               'value' => $notice->id,
//	                                               'name' => 'inreplyto'));
//			$this->out->elementEnd('form');
//			$this->out->elementEnd('div');
//			$this->out->element('div', 'b_nl_i_rt_foot'); 
//			$this->out->elementEnd('div');
//        }
		
//        $this->out->elementEnd('div');
//        $this->out->element('div', array('class' => 'b_nl_i_foot'));
//        $this->out->elementEnd('div');
//        $this->out->elementEnd('div');     
//        
//        $this->out->notice = $notice;
        
        return $cnt;
    }

    /**
     * Shows a notice plus its list of children.
     *
     * @param integer $id ID of the notice to show
     *
     * @return void
     */

    function showNoticePlus($id)
    {
    	if (array_key_exists($id, $this->table)) {
	        $notice = $this->table[$id];
	
	        $item = $this->newListItem($notice);
	        $item->show();
    	}
    }

    /**
     * Override parent class to return our preferred item.
     *
     * @param Notice $notice Notice to display
     *
     * @return NoticeListItem a list item to show
     */

//    function newListItem($notice)
//    {
//        return new ReplyListItem($notice, $this->out);
//    }
}

/**
 * Conversation tree
 *
 * The widget class for displaying a hierarchical list of notices.
 *
 * @category Widget
 * @package  LShai
 */

class ConversationTree extends NoticeList
{
    var $tree  = null;
    var $table = null;

    /**
     * Show the tree of notices
     *
     * @return void
     */

    function show()
    {
        $cnt = $this->_buildTree();        
        
        //第一条消息
        if (array_key_exists('root', $this->tree)) {
            $rootid = $this->tree['root'][0];
            //$this->showNoticePlus($rootid);
            $notice = $this->table[$rootid];
            
	        $profile = $notice->getProfile();
	        //顶部
	        $this->out->element('div', array('class' =>'b_t'), '查看会话');
	        $this->out->elementStart('div', array('style' => 'color: rgb(58, 103, 165);', 'class' => 'b_pis'));
	        $this->out->text($profile->nickname . ' (' . $profile->uname .')于' . common_date_iso8601($notice->created) . '所发消息的相关对话');
	        $user = common_current_user();
	        if ($notice->user_id != $user->id) {
				$this->out->element('a', array('id' => 'get_illreport', 'class'=>'b_pis_a', 
				                               'href' => '#', 'title'=>'非法消息举报'));
	    	}
	    	$this->out->elementEnd('div');
	        
        	$item = new ConversationRootItem($notice, $this->out);
        	$item->show();
        	
        	$this->out->element('div', 'b_nl_i_r_d'); 
			$this->out->elementStart('ol', array('class' => 'b_nl_i_r', 'id' => 'replylist'));
        	
	        
        	//自己写HTML, 加入进去, 首个消息包含其他消息
             if (array_key_exists($rootid, $this->tree)) {
	            $children = $this->tree[$rootid];
	            sort($children);
	            foreach ($children as $child) {
	                $this->showNoticePlus($child);
	            }
             }
             
            $this->out->elementEnd('ol');
	        $this->out->element('div', 'b_nl_i_r_d'); 
             
	        if($user && $user->id != $profile->id) {
				$this->out->elementStart('div', 'b_nl_i_rt');
				$this->out->element('div', 'b_nl_i_rt_title'); 
				$this->out->elementStart('div', 'b_nl_i_rt_cont');
				$this->out->elementStart('form', array('method' => 'post',
		    					'action' => common_path('notice/replyat/' . $profile->uname),
		    					'id' => 'form_reply'));
				$this->out->elementStart('div', 'b_nl_i_rt_text');
		    	$this->out->element('textarea', array('id' => 'notice_data-text',
		                                              'class' => 'textarea1',
		                                              'name' => 'status_textarea',
		    										  'maxlength' => '280'));
		    	
				$this->out->elementEnd('div');
				$this->out->elementStart('div', 'b_nl_i_rt_s');
				 $this->out->element('input', array('id' => 'notice_action_submit',
		                                           'name' => 'status_submit',
		                                           'type' => 'submit',
		                                           'value' => ''));
				
				$this->out->elementEnd('div');
				$this->out->element('div', 'cb'); 
				$this->out->hidden('token', common_session_token());
		    	$this->out->element('input', array('type' => 'hidden',
		                                               'value' => $notice->id,
		                                               'name' => 'inreplyto'));
				$this->out->elementEnd('form');
				$this->out->elementEnd('div');
				$this->out->element('div', 'b_nl_i_rt_foot'); 
				$this->out->elementEnd('div');
	        }
			
	        $this->out->elementEnd('div');
	        $this->out->element('div', array('class' => 'b_nl_i_foot'));
	        $this->out->elementEnd('div');
	        $this->out->elementEnd('div');     
	        
	        $this->out->notice = $notice;
        }
        return $cnt;
    }

    //探寻树结构, 单树回溯, 通过notice的reply_to回溯, 应当可以完成.
    function _buildTree()
    {
        $this->tree  = array();
        $this->table = array();

        $cnt = 0;        
        while ($this->notice->fetch()) {

            $cnt++;

            $id     = $this->notice->id;
            $notice = clone($this->notice);

            $this->table[$id] = $notice;

            //第一个
            if (is_null($notice->reply_to)) {
                $this->tree['root'] = array($notice->id);
            } else if (array_key_exists($notice->reply_to, $this->tree)) {
                $this->tree[$notice->reply_to][] = $notice->id;
            } else {
                $this->tree[$notice->reply_to] = array($notice->id);
            }
        }

        return $cnt;
    }

    /**
     * Shows a notice plus its list of children.
     *
     * @param integer $id ID of the notice to show
     *
     * @return void
     */

    function showNoticePlus($id)
    {
        $notice = $this->table[$id];

        // We take responsibility for doing the li

//        $this->out->elementStart('li', array('class' => 'hentry notice',
//                                             'id' => 'notice-' . $id));

        $item = $this->newListItem($notice);
        $item->show();

        if (array_key_exists($id, $this->tree)) {
            $children = $this->tree[$id];

//            $this->out->elementStart('ol', array('class' => 'notices'));

            sort($children);

            foreach ($children as $child) {
                $this->showNoticePlus($child);
            }

//            $this->out->elementEnd('ol');
        }

//        $this->out->elementEnd('li');
    }

    /**
     * Override parent class to return our preferred item.
     *
     * @param Notice $notice Notice to display
     *
     * @return NoticeListItem a list item to show
     */

//    function newListItem($notice)
//    {
//        return new NoticeListItem($notice, $this->out);
//    }
}

//class ReplyListItem
//{
//    /** The notice this item will show. */
//
//    var $notice = null;
//
//    /** The profile of the author of the notice, extracted once for convenience. */
//
//    var $profile = null;
//    
//    var $out = null;
//    
//    function __construct($notice, $out=null)
//    {
//        $this->notice  = $notice;
//        $this->profile = $notice->getProfile();
//        $this->out = $out;
//    }
//								
//    function show() {
//    	$this->out->elementStart('li', 'b_nl_i_r_l');
//    	$this->out->element('div', 'b_nl_i_r_l_title');
//    	$this->out->elementStart('div', 'b_nl_i_r_l_cont');
//    	$this->showImage();
//    	$this->out->elementStart('p', 'b_nl_i_r_l_t');
//    	$this->out->raw($this->notice->rendered);
//    	$this->out->elementEnd('p');
//    	$this->out->elementEnd('div');
//    	$this->out->element('div', 'b_nl_i_r_l_foot');
//    	$this->out->elementEnd('li');
//    }
//    
//   function showImage() {
//    	    	
//    	$avatar = $this->profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
//    	    	
//    	$this->out->elementStart('div', array('class' => 'b_nl_i_r_l_i'));
//        $attrs = array('href' => $this->profile->profileurl);
//        if (!empty($this->profile->nickname)) {
//            $attrs['title'] = $this->profile->nickname . ' (' . $this->profile->uname . ') ';
//        }
//    	$this->out->elementStart('a', $attrs);
//    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE),
//                                         'class' => 'userphotom',
//                                         'width' => '50',
//                                         'height' => '50',
//                                         'alt' => $this->profile->uname));
//    	$this->out->elementEnd('a');
//    	$this->out->element('p', 'b_nl_i_r_l_n', $this->profile->nickname);
//    	$this->out->elementEnd('div');
//    }
//}


//class ConversationTreeItem2
//{
//    /** The notice this item will show. */
//
//    var $notice = null;
//
//    /** The profile of the author of the notice, extracted once for convenience. */
//
//    var $profile = null;
//    
//    var $out = null;
//    
//    function __construct($notice, $out=null)
//    {
//        $this->notice  = $notice;
//        $this->profile = $notice->getProfile();
//        $this->out = $out;
//    }
//    
//    function show() {
//    	$this->out->elementStart('div', 'Reply');
//    	$this->out->element('div', 'x_line1');
//    	$this->out->elementStart('div', 'Reply_cont');
//    	$this->showImage();
//    	$this->out->elementStart('div', 'Reply_text');
//    	$this->out->raw($this->notice->rendered);
//    	$this->out->elementEnd('div');
//    	$this->out->element('div', 'high01');
//    	$this->out->elementEnd('div');
//    	$this->out->elementEnd('div');
//    }
//    
//   function showImage() {
//    	    	
//    	$avatar = $this->profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
//    	    	
//    	$this->out->elementStart('div', array('class' => 'Reply_pic'));
//        $attrs = array('href' => $this->profile->profileurl);
//        if (!empty($this->profile->nickname)) {
//            $attrs['title'] = $this->profile->nickname . ' (' . $this->profile->uname . ') ';
//        }
//    	$this->out->elementStart('a', $attrs);
//    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE),
//                                         'class' => 'userphotom',
//                                         'width' => '50',
//                                         'height' => '50',
//                                         'alt' => $this->profile->uname));
//    	$this->out->text($this->profile->nickname);
//    	$this->out->elementEnd('a');
//    	$this->out->elementEnd('div');
//    }
//}

///**
// * Conversation tree list item
// *
// * Special class of NoticeListItem for use inside conversation trees.
// *
// * @category Widget
// * @package  LShai
// */
//
//class ConversationTreeItem extends NoticeListItem
//{
//    /**
//     * start a single notice.
//     *
//     * The default creates the <li>; we skip, since the ConversationTree
//     * takes care of that.
//     *
//     * @return void
//     */
//
//    function showStart()
//    {
//        return;
//    }
//
//    /**
//     * finish the notice
//     *
//     * The default closes the <li>; we skip, since the ConversationTree
//     * takes care of that.
//     *
//     * @return void
//     */
//
//    function showEnd()
//    {
//        return;
//    }
//
//    /**
//     * show link to notice conversation page
//     *
//     * Since we're only used on the conversation page, we skip this
//     *
//     * @return void
//     */
//
//    function showContext()
//    {
//        return;
//    }
//}

//class ConversationRootItem extends NoticeListItem
//{
////    function show()
////    {
////        $this->showStart();
////        $this->out->element('div', array('class' => 'title2'));
////        
////        $this->out->elementStart('div', array('class' => 'cont3'));
////        
////        $this->out->element('span', array('class' => 'name'), $this->profile->nickname);
////        $this->out->element('span', array('class' => 'uname', 'style' => 'display:none;'), $this->profile->uname);
////        
////        $this->showNoticeOptions();
////        
////        $this->out->element('div', 'high01');
////        
////        $this->showImage();
////        
////        $this->showNoticeInfo();
////    }
//    
//    function show()
//    {
//    	$this->out->elementStart('div', array('class' => 'b_nl notices', 'id' => 'timeline'));
//        $this->showStart();
//        
//        $this->out->element('div', array('class' => 'b_nl_i_title'));
//        
//        $this->out->elementStart('div', array('class' => 'b_nl_i_cont'));
//        $this->out->elementStart('div', array('class' => 'b_nl_i_o')); 
//        
//        $this->out->elementStart('span', array('class' => 'b_nl_i_o_n b_nl_i_o_nr'));
//        $this->out->element('a', array('href' => common_local_url('showstream', array('uname' => $this->profile->uname)),
//        			'class' => 'name'), $this->profile->nickname);
//        $this->out->elementEnd('span');
//                
//        $this->showNoticeOptions();
//        
//        $this->out->element('div', 'cb');
//        $this->showImage();
//        $this->showNoticeInfo();
//        $this->out->element('div', array('class' => 'cb'));
//        $this->out->element('input', array('class' => 'uname', 'type' => 'hidden', 'value' => $this->profile->uname));      
//        $this->out->elementEnd('div');
//        //差3个end div  
//    }
//	
//    function showReplyLink()
//    {
//        if ($this->user && $this->notice->user_id != $this->user->id) {            
//            $this->out->elementStart('li', 'replylist');
//    		$this->out->elementStart('a', array('href' => '#', 'title' => '回复', 'class' => 'b_nl_i_o_o_rp'));
//    		$this->out->hidden('token', common_session_token());
//    		$this->out->hidden('nid', $this->notice->id);
//    		$this->out->hidden('url', common_local_url('newnotice', array('replyto' => $this->profile->uname)));
//    		$this->out->elementEnd('a');
//    		$this->out->elementEnd('li');
//        }
//    }
//}