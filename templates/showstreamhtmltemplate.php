<?php
/**
 * Shaishai, the distributed microblog
 *
 * User profile page
 *
 * PHP version 5
 *
 * @category  Login
 * @package   Shaishai
 * @author    Andray Ma <andray09@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';

class ShowstreamHTMLTemplate extends ProfileHTMLTemplate
{
    var $filter_content;
    var $tag;
    var $owner_profile;
    var $notice;
    var $total;
    
    function show($args) {
    	$this->filter_content = $args['filter_content'];
    	$this->tag = $args['tag'];
    	$this->owner_profile = $args['profile'];
    	$this->notice = $args['notice'];
    	$this->total = $args['total'];
    	parent::show($args);
    }
    
    function title()
    {
    	if ($this->is_own)
    		return '我的' . common_config('site', 'name') . '个人主页';
        else
        	return $this->owner->nickname . '的' . common_config('site', 'name') . '个人主页';
    }
    
	function metaKeywords() {
		return $this->owner_profile->nickname . '的个人主页、' . $this->owner_profile->nickname . '的图片、' . $this->owner_profile->nickname . '的音乐、' . $this->owner_profile->nickname . '的生活动态';
	}
	
	function metaDescription() {
		return '这是GamePub玩家' . $this->owner_profile->nickname . '的个人主页，TA在这里与您分享生活动态、游戏截图、喜爱的音乐等有趣内容。想与TA交朋友，一起玩游戏吗？快开始关注TA吧。';
	}

   	function showContent()
    {
    	$this->tu->showUserSummaryBlock($this->owner_profile, $this->cur_user, $this->owner);
    	
    	$this->tu->showNewContentFilterBoxBlock($this->owner_profile, $this->filter_content, $this->tag, 'showstream', array('uname' => $this->owner_profile->uname));
    	
    	if ($this->notice->N == 0) {
    		$this->showEmptyList();
    	} else {
	    	$pnl = new StreamNoticeList($this->notice, $this);
	        $cnt = $pnl->show();
	        
	        if ($this->args['filter_content']) {
				$this->numpagination($this->total, 'showstream', array('uname' => $this->owner->uname), 
					array('filter_content' => $this->filter_content), NOTICES_PER_PAGE, $this->owner->id);
	        } else if ($this->tag) {
				$this->numpagination($this->total, 'showstream', array('uname' => $this->owner->uname), 
					array('tag' => $this->tag), NOTICES_PER_PAGE, $this->owner->id);
	        } else {
				$this->numpagination($this->total, 'showstream', array('uname' => $this->owner->uname),
					array(), NOTICES_PER_PAGE, $this->owner->id);
	        }
    	}
    	
    	if ($this->is_own) {
    		$this->tu->showMakeYourTheme($this->cur_user);
    	}
    	
    	if ($this->is_own
    		&& $this->trimmed('theme') == 'true') {
    		$this->_showThemeRoller();
    	}
    }
    
    function showShaishaiStylesheets() {
    	parent::showShaishaiStylesheets();
    	if ($this->is_own
    		&& $this->trimmed('theme') == 'true') {
    		$this->cssLink('css/farbtastic.css', 'default', 'screen, projection, tv');
    		$this->cssLink('css/themeroller.css', 'default', 'screen, projection, tv');
    	}
    }
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	if ($this->is_own
    		&& $this->trimmed('theme') == 'true') {
    		$this->script('js/farbtastic/farbtastic.js');
    		$this->script('js/lshai_designsettings.js');
    	}
    }
    
    function _showThemeRoller() {
    	$this->elementStart('div', array('id' => 'theme_roller'));
    	$this->elementStart('div', 'theme_title');
    	$this->text('皮肤设置面板');
    	$this->element('a', array('href' => '#', 'class' => 'close'), '关闭');
    	$this->element('a', array('href' => '#', 'class' => 'min'), '最小化');
    	$this->elementEnd('div');
    	$this->elementStart('div', 'theme_body');
    	$this->design = $this->arg('design');
    	$this->_showTemplates();
    	$this->_showSelfDesigns();
    	$this->_showTheDesign();
    	$this->elementEnd('div');
    	$this->elementEnd('div');
    }
    
    function _showTemplates() {
    	$this->templates = $this->trimmed('official_designs');
    	
    	if ($this->templates && $this->templates->N > 0) {
	    	
    		$this->elementStart('dl', array('id' => 'template_designs'));
    	
        	$this->elementStart('dt');
        	$this->element('a', array('href' => '#'), '官方皮肤');
        	$this->elementEnd('dt');
        	
        	$this->elementStart('dd');
        	
    		$this->elementStart('ul', 'clearfix');
    		
	    	while ($this->templates->fetch()) {
	    		
	    		if ($this->templates->id == $this->design->id) {
	    			$this->elementStart('li', array('dsid' => $this->templates->id, 'class' => 'active'));
	    		} else {
	    			$this->elementStart('li', array('dsid' => $this->templates->id));
	    		}
	    		
	    		$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'class' => 'self_design_settings',
                                          'action' => common_path('settings/design')), '外观自定义');
	    		if ($this->templates->backgroundimage) {
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤'));
	    			$this->element('img', array('alt' => '点击应用这个皮肤', 
	    				'src' => $this->templates->backgroundimage, 'height' => '75', 'width' => '100'));
	    		} else {
	    			$bgcolor = new WebColor($this->design->backgroundcolor);
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤', 'style' => 'background-color:#' . $bgcolor->hexValue() . ';'));
	    			$this->text('点击应用这个皮肤');
	    		}
	   			$this->elementEnd('a');
	   			
	   			$this->element('input', array('type' => 'hidden', 'name' => 'design_id', 'value' => $this->templates->id));
	   			$this->element('input', array('type' => 'hidden', 'name' => 'apply', 'value' => '1'));
	   			
	   			$this->tu->endFormBlock();
	   			
	   			$this->elementStart('p');
	   			$this->text($this->templates->name);
	   			$this->elementEnd('p');
	   			
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
    }
    
    function _showBackgroundUpload() {
        $this->elementStart('div', 'bg_panel');
        $this->elementStart('p');
        $this->text('上传背景图片');
        $this->element('span', null, '(支持1M以内的JPG,PNG图片)');
        $this->elementEnd('p');
        
        $this->elementStart('p');
        $this->element('input', array('type' => 'file', 'name' => 'bgfile', 'id' => 'bgfile'));
		$this->element('div', array('id' => 'fileQueue', 'uid' => $this->cur_user->id));
		$this->element('input', array('type' => 'hidden', 'name' => 'backgroundimage', 'id' => 'backgroundimage'));
		$this->element('input', array('type' => 'hidden', 'name' => 'showimage', 'id' => 'showimage'));
        $this->elementEnd('p');
        
        $this->elementStart('p', 'attachment');
        
        $this->element('input', array('class' => 'radio', 'type' => 'radio', 'id' => 'bgl', 'name' => 'disposition', 'value' => '1'));
        $this->element('label', array('for' => 'bgl'), '居左');
        
        $this->element('input', array('class' => 'radio', 'type' => 'radio', 'id' => 'bgc', 'name' => 'disposition', 'value' => '2'));
        $this->element('label', array('for' => 'bgc'), '居中');
        
        $this->element('input', array('class' => 'radio', 'type' => 'radio', 'id' => 'bgr', 'name' => 'disposition', 'value' => '3'));
        $this->element('label', array('for' => 'bgr'), '居右');
        
        $this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'id' => 'bgf', 'name' => 'bgrepeat'));
        $this->element('label', array('for' => 'bgf'), '平铺');
        
        $this->element('input', array('class' => 'checkbox', 'type' => 'checkbox', 'id' => 'bgfix', 'name' => 'bgfix'));
        $this->element('label', array('for' => 'bgfix'), '背景固定');
        
        $this->elementEnd('p');
        
        $this->elementStart('a', array('class' => 'do_not_show_bg', 'href' => '#'));
        $this->element('img', array('src' => common_path('theme/default/i/selfdesign_donotshowbg.png')));
        $this->elementEnd('a');
        
        $this->elementStart('a', array('class' => 'show_bg', 'href' => '#'));
        $this->element('img', array('src' => common_path('theme/default/i/selfdesign_showbg.png')));
        $this->elementEnd('a');
        
        $this->elementEnd('div');
    }
    
    function _showColorChooser() {
    	try {

            $bgcolor = new WebColor($this->design->backgroundcolor);

            $this->elementStart('div', array('id' => 'settings_design_color', 'class' => 'color_panel'));
            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-1'), '背景');
            $this->element('input', array('name' => 'backgroundcolor',
                                          'type' => 'text',
                                          'id' => 'swatch-1',
                                          'class' => 'swatch text',
                                          'maxlength' => '7',
                                          'size' => '7',
                                          'value' => '#' . $bgcolor->hexValue()));
            $this->elementEnd('p');

            $ccolor = new WebColor($this->design->contentcolor);

            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-2'), '内容文字');
            $this->element('input', array('name' => 'contentcolor',
                                          'type' => 'text',
                                          'id' => 'swatch-2',
                                          'class' => 'swatch text',
                                          'maxlength' => '7',
                                          'size' => '7',
                                          'value' => '#' . $ccolor->hexValue()));
            $this->elementEnd('p');

            $sbcolor = new WebColor($this->design->sidebarcolor);

            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-3'), '侧栏');
            $this->element('input', array('name' => 'sidebarcolor',
                                        'type' => 'text',
                                        'id' => 'swatch-3',
                                        'class' => 'swatch text',
                                        'maxlength' => '7',
                                        'size' => '7',
                                        'value' => '#' . $sbcolor->hexValue()));
            $this->elementEnd('p');

            $tcolor = new WebColor($this->design->textcolor);
            
            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-4'), '侧栏文字');
            
            $this->element('input', array('name' => 'textcolor',
                                        'type' => 'text',
                                        'id' => 'swatch-4',
                                        'class' => 'swatch text',
                                        'maxlength' => '7',
                                        'size' => '7',
                                        'value' => '#' . $tcolor->hexValue()));
            $this->elementEnd('p');

            $lcolor = new WebColor($this->design->linkcolor);

            $this->elementStart('p', 'clearfix');
            $this->element('label', array('for' => 'swatch-5'), '链接');
            $this->element('input', array('name' => 'linkcolor',
                                         'type' => 'text',
                                         'id' => 'swatch-5',
                                         'class' => 'swatch text',
                                         'maxlength' => '7',
                                         'size' => '7',
                                         'value' => '#' . $lcolor->hexValue()));
            $this->elementEnd('p');
            
            $this->elementStart('p', array('class' =>'clearfix', 'style' => 'padding-bottom:24px;'));
            $this->element('label', null, '导航条');
            $this->element('input', array('name' => 'navcolor',
            							  'id' => 'navcolor',
            							  'type' => 'hidden',
            							  'value' => strtolower($this->design->navcolor)));
            $this->elementEnd('p');
            
            $this->elementStart('ul', 'nav_pane');
            
            foreach (array('e5630e', 'e1c80c', '91e610', '23e20c', '10e766', '0ee4cd',
            	'e71025', 'e71090', 'c80ce1', '6610e7', '0e24e5', '0d8fe3') as $a) {
	            if (strtolower($this->design->navcolor) == $a) {
	            	$this->elementStart('li', array('style' => 'background-color:#b8510c;'));
	            } else {
	            	$this->elementStart('li');
	            }
	            $this->element('a', array('href' => '#', 'style' => 'background-color:#' . $a . ';', 'colorvalue' => $a));
            	$this->elementEnd('li');
            }
            $this->elementEnd('ul');
            
            $this->elementStart('p', 'clearfix');
		    $this->element('label', array('for' => 'design_name'), '皮肤名字');
	    	$this->element('input', array('type' => 'text', 'class' => 'text', 'name' => 'design_name', 'id' => 'design_name'));
	    	$this->elementEnd('p');
            
            $this->element('div', array('class' => 'circle', 'id' => 'color-picker', 'style' => 'display:none;'));
            
            $this->elementEnd('div');

        } catch (WebColorException $e) {
            common_log(LOG_ERR, 'Bad color values in design ID: ' . $this->design->id);
        }
    }
    
    function _showSelfDesigns() {
    	
    	$this->designs = $this->trimmed('self_designs');
    	
    	
    	if ($this->designs && $this->designs->N > 0) {
	    	
    		$this->elementStart('dl', array('id' => 'self_designs'));
    	
        	$this->elementStart('dt');
        	$this->element('a', array('href' => '#'), '我的DIY皮肤');
        	$this->elementEnd('dt');
        	
        	$this->elementStart('dd', array('style' => 'display:none;'));
        	
    		$this->elementStart('ul', 'clearfix');
    		
	    	while ($this->designs->fetch()) {
	    		
	    		if ($this->designs->id == $this->design->id) {
	    			$this->elementStart('li', array('dsid' => $this->designs->id, 'class' => 'active'));
	    		} else {
	    			$this->elementStart('li', array('dsid' => $this->designs->id));
	    		}
	    		
	    		$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'class' => 'self_design_settings',
                                          'action' => common_path('settings/design')), '外观自定义');
	    		if ($this->designs->backgroundimage) {
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤'));
	    			$this->element('img', array('alt' => '点击应用这个皮肤', 
	    				'src' => $this->designs->backgroundimage, 'height' => '75', 'width' => '100'));
	    		} else {
	    			$bgcolor = new WebColor($this->design->backgroundcolor);
	    			$this->elementStart('a', array('class' => 'apply', 'href' => '#', 'title' => '点击应用这个皮肤', 'style' => 'background-color:#' . $bgcolor->hexValue() . ';'));
	    			$this->text('点击应用这个皮肤');
	    		}
	   			$this->elementEnd('a');
	   			
	   			$this->element('input', array('type' => 'hidden', 'name' => 'design_id', 'value' => $this->designs->id));
	   			$this->element('input', array('type' => 'hidden', 'name' => 'apply', 'value' => '1'));
	   			
	   			$this->tu->endFormBlock();
	   			
	   			$this->elementStart('p');
	   			$this->text($this->designs->name);
	   			$this->elementEnd('p');
	   			
	   			$this->element('a', array('href' => common_path('settings/design'), 'class' => 'delete', 'title' => '删除这个皮肤'), 'X');
	    		
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ul');
	    	$this->elementEnd('dd');
	    	$this->elementEnd('dl');
    	}
    }
    
    function _showOperations() {
    	$this->elementStart('div', 'op');

    	$this->element('input', array('type' => 'submit', 'value' => '保存', 'name' => 'save', 'class' => 'submit button76 green76'));
    	
    	$this->element('input', array('type' => 'submit', 'value' => '恢复默认', 'name' => 'restoredefault', 'class' => 'restoredefault button76 silver76'));

//        $this->element('input', array('id' => 'settings_design_reset',
//                                     'type' => 'reset',
//                                     'value' => '重置',
//                                     'title' => '恢复默认',
//        							 'class' => 'reset button76 silver76')); 
        
    	$this->elementEnd('div');
    }
    
    function _showTheDesign() {
    	$this->tu->startFormBlock(array('method' => 'post',
                                          'enctype' => 'multipart/form-data',
                                          'id' => 'settings_self_design',
                                          'class' => 'form_settings',
                                          'action' => common_path('settings/design')), '自定义皮肤');
        
        $this->elementStart('dl');
    	
        $this->elementStart('dt');
        $this->element('a', array('href' => '#'), '自定义皮肤');
        $this->elementEnd('dt');
        
        $this->elementStart('dd', array('style' => 'display:none;'));
        
    	$this->_showBackgroundUpload();
    	
    	$this->_showColorChooser();
    	
    	$this->_showOperations();
    	
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
		
        $this->tu->endFormBlock();
    }

    function showEmptyList()
    {
        if (common_current_user()) {
            if ($this->is_own) {
                $message = '您最近看到什么有趣的? 如果您还没有贴, 现在正好 :)';
            } else {
                $message = sprintf('您可以用“@用户名”的格式发消息给他，或直接发送站内信。');
            }
        } else {
            $message = '快来<a href="' . common_path('register') 
            	. '">注册账号</a>， 然后发消息给' . Profile::displayName($this->owner_profile->sex, $this->is_own) . '吧。';
        }

        $emptymsg = array();
        if ($this->is_own) {
        	$emptymsg[] = '您还没有发送任何消息。';
        } else {
        	$emptymsg[] = Profile::displayName($this->owner_profile->sex, $this->is_own) . '还没有发送任何消息。';
        }
        $emptymsg[] = $message;
        $this->tu->showEmptyListBlock($emptymsg);
    }
}

class StreamNoticeList extends NoticeList {
	
	function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices', 'class' => 'noavatar nonickname'));
        $cnt = 0;

        while ($this->notice != null 
        	&& $this->notice->fetch() && $cnt <= NOTICES_PER_PAGE) {
            $cnt++;
           
            if ($cnt > NOTICES_PER_PAGE) {
                break;
            }
                        
            $item = $this->newListItem($this->notice);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }
    
	function newListItem($notice)
    {
        return new StreamNoticeListItem($notice, $this->out);
    }
}

class StreamNoticeListItem extends NoticeListItem {
	
	function showStart()
    {
        // XXX: RDFa
        // TODO: add notice_type class e.g., notice_video, notice_image
        $liClass = 'notice';
        
    	if ((!empty($this->notice->conversation)
            && $this->notice->conversation != $this->notice->id)) {
            $this->out->elementStart('li', array('class' => $liClass, 'id' => 'notice-' . $this->notice->id, 'nid' => $this->notice->id, 'style' => 'padding-top:31px;'));	
        } else {
        	$this->out->elementStart('li', array('class' => $liClass, 'id' => 'notice-' . $this->notice->id, 'nid' => $this->notice->id));
        }
    }
    
	function show()
    {
        $this->showStart();
        
        $this->showNoticeInfo();
        $this->showRoot();
        $this->showNoticeBar();
		$this->showContext();
		
        $this->showEnd();
    }
}