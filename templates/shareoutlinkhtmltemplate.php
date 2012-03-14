<?php
if (!defined('SHAISHAI')) {
	exit (1);
}
//分享页面由对话框，登录框（或欢迎框）,注册框，以及图片框组成
class ShareoutlinkHTMLTemplate extends BasicHTMLTemplate {

	/**
	 * Title of the page
	 *
	 * @return page title, including page number if over 1
	 */

	function title() {
		return '外站分享';
	}
	//载入Javascript
	function showScripts()
    {   
    	parent::showScripts();
		$this->script('js/lshai_shareoutlink.js');
    }
    
	//载入CSS
	function showStylesheets()
    {
        if (Event::handle('EndShareoutlinkStylesheets', array($this))) {
			
			$this->cssLink('css/shareout.css', 'default');

       		Event::handle('EndShareoutlinkStylesheets', array($this));
        }
    }
    
    function showUAStylesheets() {
    	
    }
    
	function showBody()
    {
        $this->elementStart('body', array('token' => common_session_token(), 'anonymous' => $this->cur_user ? '0' : '1'));
        
		$this->elementStart('div', array('id' => 'wrap', 'class' => 'rounded5 clearfix'));
        $this->showCore();
        $this->elementEnd('div');
        
        $this->elementEnd('body');
    }
    
	/**
	 * Fill the content area
	 *
	 * Shows a list of the notices of current type in the public stream
	 * controls.
	 *
	 * @return void
	 */
	//显示表单，由文本框，图片选择框，登录框，注册窗口组成
	function showContent() {

        $this->content = "";
		//载入消息题目与消息链接
		if ($this->args['otlinktitle']!= null || $this->args['otlinkurl'] != null)
			$this->content = $this->args['otlinktitle'] . " " . $this->args['otlinkurl'];
	

		if (Event :: handle('StartShowNoticeForm', array (
				$this
			))) {
			
			$this->tu->startFormBlock(array (
				'method' => 'post',
				'action' => common_local_url('shareoutlink'),
				'id' => 'notice_form'), '外站分享');

			//文本框
			$this->_showTextarea();
			//图片选择框
			$this->_showPicture();
			//判断用户是否已经登录
			if ($this->cur_user) {
				//欢迎框
				$this->_showWelcome();
			} else {
				//登录框
				$this->_showLogin();
			}
			
			$this->_hiddenAttach();
			
			$this->tu->endFormBlock();

			Event :: handle('EndShowNoticeForm', array (
				$this,
				$this->cur_user
			));
		}
	}
	//显示中心内容，整个页面不分左中右部分
	function showCore() {
		$this->elementStart('div', array (
			'id' => 'share_wrap'
		));
		//注册框
		$this->_showRegister();
		$this->showContent();
		$this->elementEnd('div');

	}
	//文本输入框，content中保存传递过来的title与url
	function _showTextarea() {

		$this->elementStart('h2');
		$this->text('转发给关注你的人');
		$this->elementEnd('h2');
		$this->elementStart('span', array (
			'class' => 'remain'
		));
		$this->text('还可以输入');
		$this->element('em', array (
			'id' => 'notice_text-count'
		),'280');
		$this->text('字');
		$this->elementEnd('span');

		$this->element('textarea', array (
			'id' => 'notice_data-text',
			'name' => 'status_textarea'
		),($this->content) ? $this->content : '');

	}
	//图片选择框，从otlinkimgarr传来的，各个图片的src地址，显示图片
	function _showPicture() {

		$this->elementStart('dl', array (
			'class' => 'photo'
		));
		$this->element('dt', array('id'=>'selecttext'), '请选择一张图片作为图片附件');
		$this->elementStart('dd');
		$this->elementStart('ul');

		$pic_index = 0;
		$cur_pageNum = 0;

		foreach ($this->args['otlinkimgarr'][0] as $picture) {
			$templink = "";
			$mode = "{(http://[^(\'|\"|\\s)]*)}";
			if (preg_match($mode, $picture, $templink) == true) {
				
				if (($pic_index ) % 3 == 0) {
					$cur_pageNum++;
				}
				if ($cur_pageNum == 1) {
					if ($pic_index == 0) {
						$this->elementStart('li', array (
							'class' => 'page' . $cur_pageNum . " active",
							'id' => $pic_index ,
							'name' => $pic_index ,
							
						));
					} else {
						$this->elementStart('li', array (
							'class' => 'page' . $cur_pageNum,
							'id' => $pic_index ,
							'name' => $pic_index ,
							
						));
					}
				} else {
					$this->elementStart('li', array (
						'class' => 'page' . $cur_pageNum,
						'id' => $pic_index ,
						'name' => $pic_index ,
						'style' => 'display:none;'
					));
				}
				$this->elementStart('a', array (
					'href' => '#'
				));

				$this->element('img', array (
					'src' => $templink[0],
					'id' => "img".($pic_index ),
				));
				$this->elementEnd('a');
				$this->elementEnd('li');
				
				$pic_index++;
			}
		}
		$this->element('input', array (
			'id' => 'picnum',
			'name' => 'picnum',
			'type' => 'hidden',
			'value' => $pic_index -1
		));
		$this->element('input', array (
			'id' => 'curindex',
			'name' => 'curindex',
			'type' => 'hidden',
			'value' => '0'
		));
		$this->element('input', array (
			'id' => 'noticefilename',
			'name' => 'photo',
			'type' => 'hidden',
			'value' => $this->trimmed('photo', '')
		));
		$this->element('input', array (
			'id' => 'source',
			'name' => 'source',
			'type' => 'hidden',
			'value' => $this->args['otlinksource']
		));
		$this->element('input', array (
			'id' => 'title',
			'name' => 'title',
			'type' => 'hidden',
			'value' => $this->args['otlinktitle']
		));
		$this->element('input', array (
			'id' => 'url',
			'name' => 'url',
			'type' => 'hidden',
			'value' => $this->args['otlinkurl']
		));

		$this->elementEnd('dd');

		$this->elementStart('div', array (
			'class' => 'op'
		));
		$this->element('a', array (
			'id' =>'cancelbutton',
			'href' => '#',
			'class' => 'cancel'
		), '不想附加图片');

	//	$this->elementStart('div', array (
	//		'id' => 'hideshowpicdiv'
	//	));
		$this->element('a', array (
			'id' =>'prevbutton',
			'href' => '#',
			'class' => 'prev'
		), '<<上一页');
		$this->element('a', array (
			'id' =>'nextbutton',
			'href' => '#',
			'class' => 'next'
		), '下一页>>');

		$this->elementStart('span', array (
			'id' => 'counttext',
			'class' => 'counts'
		));
		
		$this->text('共' . $pic_index . '张');
		
		$this->elementEnd('span');
	//	$this->elementEnd('div');
		$this->elementEnd('div');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
	//登录框
	function _showLogin() {

		$this->elementStart('div', array (
			'class' => 'extra'
		));

		$this->elementStart('dl');
		$this->elementStart('dt');
		$this->element('label', array (
			'for' => 'uname'
		), '用户名');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->element('input', array (
			'class' => 'text',
			'type' => 'text',
			'name' => 'uname',
			'id' => 'uname'
		));
		$this->elementEnd('dd');
		$this->elementStart('dt');
		$this->element('label', array (
			'for' => 'password'
		), '密码');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->element('input', array (
			'class' => 'text',
			'type' => 'password',
			'name' => 'password',
			'id' => 'password'
		));
		$this->elementEnd('dd');

		$this->elementStart('dd');
		$this->element('input', array (
			'class' => 'checkbox',
			'type' => 'checkbox',
			'name' => 'rememberme',
			'id' => 'rememberme'
		));
		$this->element('label', array (
			'for' => 'rememberme'
		), '下次自动登录');
		$this->element('a', array (
			'class' => 'forget',
			'href' => common_local_url('recoverpassword'),
			'target' => '_blank'
		), '忘记密码');

		$this->elementEnd('dd');
		$this->elementEnd('dl');

		$this->element('input', array (
			'id' => 'notice_action-submit',
			'class' => 'submit',
			'type' => 'button',
			'value' => '登录并转发'
		));

		$this->elementEnd('div');

	}
	//欢迎框
	function _showWelcome() {

        $this->cur_user = common_current_user();

		$this->elementStart('div', array (
			'class' => 'extra'
		));
/*
		$this->elementStart('dl');
		$this->elementStart('dt');
		$this->element('label', array (
			'for' => 'wel'
		), '欢迎你：');
		$this->elementEnd('dt');
		$this->elementStart('dd');
		$this->elementStart('strong');
		$this->element('label', array (
			'for' => 'welcome'
		), $this->cur_user->uname);

		$this->elementEnd('strong');
		$this->elementEnd('dd');

	*/
		$this->element('input', array (
			'id' => 'notice_action-submit',
			'class' => 'submit',
			'type' => 'button',
			'value' => '赶快分享吧！'
		));

		$this->elementEnd('div');
	}
	//注册框
	function _showRegister() {
		$this->element('a', array (
			'href' => common_local_url('index'),
			'class' => 'lg'
		));
		
		if (! $this->cur_user) {
			$this->element('a', array (
				'href' => common_local_url('register'),
				'target' => '_blank',
				'class' => 'reg'
			), '注册GamePub');
		}
	}
	
	function _hiddenAttach() {
		$this->hidden('audio', $this->trimmed('audio', ''));
		$this->hidden('video', $this->trimmed('video', ''));
	}
}