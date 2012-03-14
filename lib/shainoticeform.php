<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/widget.php';

class ShaiNoticeForm extends Widget
{
	
	/**
     * Current action, used for returning to this page.
     */

    var $action = null;

    /**
     * Pre-filled content of the form
     */

    var $content = null;
    
	 /**
     * The current user
     */

    var $user = null;
    
    // used for special module to direct conversion
    var $mode = null;
    var $mode_identifier = null;
    
	/**
     * Constructor
     *
     * @param HTMLOutputter $out     output channel
     * @param string        $action  action to return to, if any
     * @param string        $content content to pre-fill
     */

    function __construct($out=null, $action=null, $content=null, $user=null, $mode=null, $mode_identifier=null)
    {
        parent::__construct($out);

        $this->action  = $action;
        $this->content = $content;
        $this->mode = $mode;
        $this->mode_identifier = $mode_identifier;

        if ($user) {
            $this->user = $user;
        } else {
            $this->user = common_current_user();
        }
        
        if (common_config('attachments', 'uploads')) {
            $this->enctype = 'multipart/form-data';
        }
    }
	
	/**
     * Show the form
     *
     * Uses a recipe to output the form.
     *
     * @return void
     * @see Widget::show()
     */

    function show()
    {
    	if (Event::handle('StartShowNoticeForm', array($this->out))) {
	    	$this->out->elementStart('form', array('method' => 'post',
	    					'action' => common_path('notice/new'),
	    					'id' => 'notice_form'));
	    	$this->out->elementStart('fieldset');
	    	$this->out->element('legend', null, '消息表单');
	    	
	    	$this->showTopic();
	    	$this->showForm();
	    	
	    	$this->out->elementEnd('fieldset');
	    	$this->out->elementEnd('form');
	    	
    		Event::handle('EndShowNoticeForm', array($this->out, $this->user));
        }
    }
    
    function showForm() 
    {
    		
    	$this->content = '';	
    	
    		$this->out->elementStart('div', 'form');
    		$this->out->element('textarea', array('id' => 'notice_data-text',
                                              'name' => 'status_textarea'),
    										  //'onclick' => 'tellPoint();'),
        									  //,'style' => 'overflow-y: auto;'),
                            ($this->content) ? $this->content : '');    	
    	
    		$this->out->elementStart('ul', 'rounded5b clearfix');
    		$this->showPhoto();
	    	$this->showMusic();
    		$this->showVideo();
			$this->showEmotion();    		
    		
	    	$this->out->elementEnd('ul');
	    	
	    	$this->out->elementStart('span', 'char');
    		$this->out->element('em', array('id' => 'notice_text-count'), '280');
    		$this->out->text('字剩余');
    		$this->out->elementEnd('span');
    		
    		$this->out->element('input', array('id' => 'notice_action_submit',
                                           'class' => 'submit button76 gray76',
                                           'name' => 'status_submit',
                                           'type' => 'submit',
                                           'value' => '',
    										'title' => '发送'));
    		
    		$this->out->hidden('token', common_session_token());
    		$this->out->element('input', array('type' => 'hidden', 
                                               'value' => $this->mode,
        	                                   'name' => 'mode'));
	        $this->out->element('input', array('type' => 'hidden',
	                                               'value' => $this->mode_identifier,
	                                               'name' => 'mode_identifier'));
	         
	        if ($this->action) {
	            $this->out->hidden('notice_return-to', $this->action, 'returnto');
	        }	        
    		
    		$this->out->elementEnd('div');
    }
    
    function showTopic()
    {
    	$this->out->elementStart('dl', array('class' => 'topic clearfix'));	    	
    	$this->out->elementStart('dt');
    	$this->out->raw('&#160;');
    	$this->out->elementEnd('dt');
    	$this->out->elementStart('dd');
    	$this->out->elementStart('ul', 'clearfix');
	    
    	$first_tag = Second_tag::getGameTagsStruct($this->user->game_id);    
		$fts = First_tag::getFirstTags($this->user->game_id);
		
		$fts = array('交流', '秀场', '游戏', '火爆话题');
		$first_tag = array('交流' => array('技巧', 'PK', '教程', '求助', '攻略', '副本', '赚钱', '任务'),
			'秀场' => array('玩家靓照', '玩家作品', '公会宣传', '场景展示', '属性展示', '对战展示'),
			'游戏' => array('游戏新闻', '游戏公告', '游戏活动', '游戏八卦', '游戏意见', '下一版本', '游戏职业', '游戏心情', '游戏历程'),
			'火爆话题' => Hotwords::getHotWordtexts(10));
    	
       	foreach ($fts as $name) {
       		$lwidth = 14 * mb_strlen($name, 'utf-8');
        	$this->out->elementStart('li', array('class' => 'first_tag', 'style' => 'width:' . $lwidth . 'px;'));
    		$this->out->element('a', array('href' => '#', 'alt' => $name, 'class' => 'toggle'), $name);
    		$second_tags = $first_tag[$name];
    		$this->out->elementStart('ul', array('class' => 'rounded5 more clearfix', 'style' => 'display:none;'));
    		foreach ($second_tags as $second_tag) {
    			$this->out->elementStart('li');
    			$swidth = 14 * mb_strlen($second_tag, 'utf-8');
    			$this->out->element('a', array('href' => '#', 'style' => 'width:' . $swidth . 'px;'), $second_tag);
    			$this->out->elementEnd('li');
    		}
    		$this->out->element('li', 'pointer');
    		$this->out->elementEnd('ul');
    		$this->out->elementEnd('li');
        }
        $this->out->elementStart('li', 'self_define');
    	$this->out->element('a', array('href' => '#', 'alt' => '自定义', 'class' => 'toggle'), '自定义');
    	$this->out->elementEnd('li');
    	
    	$this->out->elementEnd('ul');
    	$this->out->elementEnd('dd');
    	$this->out->elementEnd('dl');
    }
    
    function showPhoto() 
    {
    	$this->out->elementStart('li', 'insert_picture');
    	$this->out->element('a', array('href' => '#', 'class' => 'picture', 'title' => '插入本地图片'), '插入图片');
//    	
    	$this->out->elementStart('div', array('class' => 'insert rounded5', 'style' => 'display:none;'));
    	
    	$this->out->elementStart('p');
    	$this->out->text('请选择要上传的本地图片文件(不超过1MB)：');
    	$this->out->element('input', array('type' => 'hidden', 'name' => 'noticefilename', 'id' => 'noticefilename'));
    	$this->out->element('input', array('type' => 'file', 'name' => 'notice_file', 'id' => 'notice_file'));
    	$this->out->elementEnd('p');
    	$this->out->element('div', array('id' => 'noticeFileQueue', 'uid' => $this->user->id));
    	
//    	$this->out->elementStart('p', 'tab');
//    	$this->out->element('a', array('class' => 'local active', 'href' => '#'), '本地上传');
//    	$this->out->element('a', array('class' => 'remote', 'href' => '#'), '图片链接');
//    	$this->out->elementEnd('p');
//    	
//    	$this->out->elementStart('p', 'from_local');
//    	$this->out->element('input', array('type' => 'file', 'name' => 'photofile'));
//    	$this->out->elementEnd('p');
//    	    	
//    	$this->out->elementStart('p', array('class' => 'from_link', 'style' => 'display:none;'));
//    	$this->out->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'photo'));
//    	$this->out->element('a', array('class' => 'button60 silver60 addlink', 'href' => '#'), '添加');
//    	$this->out->elementEnd('p');
//    	
    	$this->out->element('a', array('class' => 'close', 'href' => '#'), 'X');
    	$this->out->element('span', array('class' => 'pointer'));
		
		$this->out->elementEnd('div');	
    	
    	$this->out->elementEnd('li');
    }
    
    function showMusic()
    {
    	$this->out->elementStart('li', 'insert_music');
    	$this->out->element('a', array('href' => '#', 'class' => 'music', 'title' => '插入音乐链接'), '插入音乐');
    	
    	$this->out->elementStart('div', array('class' => 'insert rounded5', 'style' => 'display:none;'));
    	$this->out->element('p', null, '请输入MP3格式的音乐链接地址：');
    	$this->out->elementStart('p');
    	$this->out->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'audio'));
    	$this->out->element('a', array('class' => 'button60 silver60 addlink', 'href' => '#'), '添加');
    	$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p', array('class' => 'error', 'style' => 'display:none;'));
    	$this->out->element('span', array('style' => 'color:#6E7F02;'), '该地址无法识别出MP3，');
    	$this->out->element('a', array('class' => 'insert_direct', 'href' => '#', 'style' => 'color:#b8510c;float:none;display:inline;'), '作为普通的链接发布');
    	$this->out->elementEnd('p');
    	
    	$this->out->element('a', array('class' => 'close', 'href' => '#'), 'X');
    	$this->out->element('span', array('class' => 'pointer'));
    	$this->out->elementEnd('div');
    	
    	$this->out->elementEnd('li');
    }
    
 	function showVideo()
    {
    	$this->out->elementStart('li', 'insert_video');
    	$this->out->element('a', array('href' => '#', 'class' => 'video', 'title' => '插入视频链接或上传本地视频'), '插入视频');
    	
    	$this->out->elementStart('div', array('class' => 'insert rounded5', 'style' => 'display:none;'));
    	$this->out->elementStart('p');
    	$this->out->text('请输入酷6、优酷、土豆、QQ、新浪、爱拍游戏、我乐、17173、激动网等视频网站的视频播放页链接'); 
    	$this->out->text('（或');
    	if (empty($this->mode)) {
    		$link = common_path('uploadvideo');
    	} else {
    		$link = common_path('uploadvideo?mode=' . $this->mode . '&mode_identifier=' . $this->mode_identifier);
    	}
    	$this->out->element('a', array('href' => $link, 'target' => '_blank', 'style' => 'color:#b8510c;float:none;display:inline;'), '上传视频');
    	$this->out->text('）');
    	$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p');
    	$this->out->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'video'));
    	$this->out->element('a', array('class' => 'button60 silver60 addlink', 'href' => '#'), '添加');
    	$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p', array('class' => 'error', 'style' => 'display:none;'));
    	$this->out->element('span', array('style' => 'color:#6E7F02;'), '该地址无法识别出视频，');
    	$this->out->element('a', array('class' => 'insert_direct', 'href' => '#', 'style' => 'color:#b8510c;float:none;display:inline;'), '作为普通的链接发布');
    	$this->out->elementEnd('p');
    	
    	$this->out->element('a', array('class' => 'close', 'href' => '#'), 'X');
    	$this->out->element('span', array('class' => 'pointer'));
    	$this->out->elementEnd('div');
    	
    	$this->out->elementEnd('li');
    }
    
    function showEmotion() {
    	$this->out->elementStart('li', 'insert_emotion');
    	$this->out->element('a', array('href' => '#', 'class' => 'emotion', 'title' => '插入表情'), '插入表情');
//		已使用另一种表情选择框
//    	$this->out->elementStart('div', array('class' => 'insert rounded5', 'style' => 'display:none;', 'id' => 'emotions'));
//    	$this->out->element('span', array('class' => 'intro'), '请选择表情');
//    	$this->out->element('a', array('class' => 'close', 'href' => '#'), 'X');
//    	$this->out->element('span', array('class' => 'pointer'));
//    	$this->out->elementEnd('div');
    	$this->out->elementEnd('li');
    }
}