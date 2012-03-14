<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class TemplateUtil {
	
	var $out;
	
	function __construct($out) {
		$this->out = $out;
	}
	
	function showProfileDetailWidget($profile, $havedetaillink=1) {
		$this->out->elementStart('div', 'widget owner_detail'); 
		
    	$game = Game::staticGet('id', $profile->game_id);
    	$server = Game_server::staticGet('id', $profile->game_server_id);
    	
		$this->out->elementStart('p');
		$this->out->element('strong', null, '游戏');
		$this->out->text($game->name);
		$this->out->elementEnd('p');
		
		$this->out->elementStart('p');
		$this->out->element('strong', null, '服务器');
		$this->out->text($server->name);
		$this->out->elementEnd('p');
		
		if($profile->game_job!=null){ 
			$this->out->elementStart('p');
			$this->out->element('strong', null, JOB_NAME());
			$this->out->text($profile->game_job);
			$this->out->elementEnd('p');
		}
		
		if($profile->game_org!=null){ 
			$this->out->elementStart('p');
			$this->out->element('strong', null, GROUP_NAME());  
			$this->out->text($profile->game_org);
			$this->out->elementEnd('p');
		}
		
		$this->out->elementStart('p');
		$this->out->element('strong', null, '兴趣爱好');
		$classifiedInterest = User_interest::getClassifiedInterestByUser($profile->id);
		$userInterest = User_interest::getSelfDefinedInterestStringByUser($profile->id);
		if ($userInterest) {
			$userInterest .= '，';
		}
		$userInterest .= implode('，', $classifiedInterest);
		
		if (mb_strlen($userInterest)>30) {
		    $this->out->text(common_cut_string($userInterest, 30).'...');
		} else if ($userInterest) {
			$this->out->text($userInterest);
		} else {
			$this->out->text('未填写兴趣 爱好');
		}
		$this->out->elementEnd('p');
		
		$this->out->elementStart('p');
		$this->out->element('strong', null, '个人简介');
		// we limit the length of self-intro to make page more clean
	    if ($profile->bio && mb_strlen($profile->bio)<=28) {
		    $this->out->text($profile->bio);
		}else if ($profile->bio && mb_strlen($profile->bio)>28) {
			$this->out->elementStart('span',array('class' => 'short'));
			$this->out->text(common_cut_string($profile->bio, 28).'...');	
			$this->out->element('a', array('class'=>'show_more', 'href'=>'#'),'[展开]');
			$this->out->elementEnd('span');
			$this->out->elementStart('span', array('class' => 'full', 'style' => 'display:none;'));
			$this->out->text($profile->bio);
			$this->out->element('a', array('class'=>'hide_more', 'href'=>'#'),'[折叠]');
			$this->out->elementEnd('span');
		}else {
			$this->out->text('未填写个人简介');
		}
		$this->out->elementEnd('p');
		
		$this->out->elementEnd('div');
	}
	
    function showUserSummaryBlock($profile, $cur_user, $pageowner) {
    	$displayName = Profile::displayName($profile->sex, $cur_user && $profile->id == $cur_user->id);
    	$this->out->elementStart('div', array('id'=>'owner_summary'));
    	// show the avatar
    	$this->out->elementStart('div', 'avatar');
    	$avatar = $profile->getAvatar(AVATAR_PROFILE_SIZE);
    	$this->out->element('img', array(
    		'src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_PROFILE_SIZE, $profile->id, $profile->sex), 
    		'alt' => $profile->nickname));
    	$this->out->elementEnd('div');
    	// show the name
    	$this->out->elementStart('p', 'nickname clearfix');
    	$width = 6 * (mb_strlen($profile->nickname, 'utf-8') + strlen($profile->nickname)) + 8;
    	$this->out->element('span', array('style' => 'width:' . $width . 'px;'), $profile->nickname);
    	$this->out->elementStart('span', 'titles clearfix');
    	
    	if ($profile->is_vip) {
			$this->out->element('em', array('class' => 'vip', 'title' => common_config('site', 'name') . '认证高玩'));
    	}
    	if ($profile->is_originuser) {
			$this->out->element('em', array('class' => 'ou', 'title' => common_config('site', 'name') . '元老玩家'));
    	}
		$this->out->elementEnd('span');
    	$this->out->elementEnd('p');

    	// show user info
    	$this->out->elementStart('p', 'homepage');
    	$this->out->element('a', array('id' => 'profile_url','href' => $profile->profileurl), $profile->profileurl);
    	$this->out->element('a',array('id' => 'get_copy','class' => 'copy', 'href' => '#', 'value' => $profile->profileurl),'[复制链接]'); 
    	$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p', 'infos');
		$this->out->elementStart('span','sex');
    	$this->out->text('性别：');
    	$this->out->element('em', null, $profile->sex == 'M' ? '男' : '女');
    	$this->out->elementEnd('span');
    	$this->out->elementStart('span','loc');
    	$this->out->text('所在地：');
    	$this->out->elementStart('em');
    	if ($profile->location && $profile->city != $profile->district){
			$this->out->text($profile->province.' - '.$profile->city.' - '.$profile->district);
		} else if ($profile->location) {
			$this->out->text($profile->province.' - '.$profile->city);
		}
		else{
			$this->out->text('未设置');
		}
		$this->out->elementEnd('em');
		$this->out->elementEnd('span');
		$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p', 'infos');
    	$this->out->elementStart('span','level');
    	$this->out->text('等级：');
    	$this->out->element('em', null, $profile->getUserGrade() . '级');
    	$this->out->elementEnd('span');
    	
    	$score = $profile->getUserScoreDetail();
    	
    	$this->out->elementStart('span','point');
    	$this->out->text('财富：');
    	$this->out->element('em', array('class' => 'gold', 'title' => '金G币'), $score['gold']);
    	$this->out->element('em', array('class' => 'silver', 'title' => '银G币'), $score['silver']);
    	$this->out->element('em', array('class' => 'bronze', 'title' => '铜G币'), $score['bronze']);
    	$this->out->elementEnd('span');
    	
    	$this->out->elementStart('span','visitors');
    	$this->out->text('被访问：');
    	$this->out->element('em','',$profile->visited_num);
    	$this->out->elementEnd('span');
    	$this->out->elementEnd('p');
    	
    	if ($cur_user && $cur_user->id != $profile->id){
	    	//关注
			
			if ($cur_user && ! $profile->hasBlocked($cur_user) ) {
				// add if blocked by the user, you can not subscribe to him
				if (!$cur_user->isSubscribed($profile)) {
					$this->out->tu->startFormBlock(array('method' => 'post',
                                           'class' => 'subscribe',
                                           'action' => common_path('main/subscribe')), '关注' . $displayName);
					$this->out->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => $profile->id));
                    $this->out->element('input', array('class' => 'button94 orange94', 'type' => 'submit', 'value' => '关注', 'title' => '开始关注' . $profile->nickname));
					$this->out->tu->endFormBlock();
				} else {
					$this->out->element('div', 'subscribed', '已关注');
				}
			}
			
			//推荐
			if ($cur_user && ! $profile->hasBlocked($cur_user) ) {
				$this->out->element('a', array('class'=>'suggest button76 silver76','href' => common_path('notice/new'), 
					'title' => '推荐', 'nickname' => $profile->nickname, 'link' => $profile->profileurl, 'who' => $profile->id), '推荐');
			}
			
			//更多操作
			if ($cur_user) {
				$this->out->elementStart('div','op');
				$this->out->elementStart('a',array('id'=>'get_more','class'=>'toggle button76 silver76','alt'=>'更多操作'));
				$this->out->text('更多操作');
				$this->out->element('small','','▼');
				$this->out->elementEnd('a');

				
				$this->out->elementStart('ul','more rounded5');				

				//取消关注				
                if ($cur_user->isSubscribed($profile)) {
					$this->out->elementStart('li');				
		    		$this->out->element('a', array('class' => 'unsubscribe', 'title' => '取消关注', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/unsubscribe')), '取消关注');
                	$this->out->elementEnd('li');
                }
					
				//只能对关注自己的人发悄悄话
				$user = User::staticGet($profile->id);
				if ($user->isSubscribed($cur_user)) {
	                $this->out->elementStart('li');
	                $this->out->element('a', array('href' => common_path('message/new'),
	                	'to' => $profile->id, 'title' => '发送悄悄话', 'class' => 'msg',
	                	'nickname' => $profile->nickname),'悄悄话');
	                $this->out->elementEnd('li');
                }
		
				//对TA说
				if (!$profile->hasBlocked($cur_user) ) {
					if ($profile->id != $cur_user->id) {
						$this->out->elementStart('li');
						$this->out->element('a', array('href' => common_path('notice/replyat/' . $profile->uname), 
								'class' => 'at', 'title' => '对' . $displayName . '说',
								'nickname' => $profile->nickname),
				    									'对' . $displayName . '说');
						$this->out->elementEnd('li');
					}
				}
				
				$this->out->elementStart('li');
				if ($cur_user->hasBlocked($profile)) {
			    	$this->out->element('a', array('class' => 'unblock', 'title' => '取消黑名单', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/unblock')), '取消黑名');
		    	} else {
			    	$this->out->element('a', array('class' => 'block', 'title' => '黑名单', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/block')), '黑名单');
		    	}
				$this->out->elementEnd('li');
				
				//非法举报
				$this->out->elementStart('li');
		    	$this->out->element('a', array('class' => 'illegal', 'title' => '非法举报', 'href' => '#', 'to' => $profile->id, 'url' => common_path('main/illegalreport')), '非法举报');
		    	$this->out->elementEnd('li');
				
				$this->out->elementEnd('ul');
				$this->out->elementEnd('div');
			}
    	} else if (! $cur_user) {
    		$this->out->element('a', array('class' => 'subscribe button94 orange94 trylogin', 'href' => common_path('register?ivid=' . $profile->id), 'title' => '开始在游戏酒馆上关注' . $profile->nickname, 'rel' => 'nofollow'), '关注');
    	}		
    	$this->out->elementEnd('div');
    }
	
    function showTitleBlock($title, $class) {
    	$this->out->elementStart('h2', $class);
    	$this->out->raw($title);
    	$this->out->elementEnd('h2');
    }
    
    function showPageHighlightBlock($text) {
    	$this->out->element('div', 'success', $text);
    }
    
	function showPageErrorBlock($text) {
    	$this->out->element('div', 'error', $text);
    }
    
	function showPageInstructionBlock($text) {
    	$this->out->elementStart('div', 'instruction');
    	$this->out->elementStart('p');
    	$this->out->raw($text);
    	$this->out->elementEnd('p');
    	$this->out->elementEnd('div');
    }
    
    function startFormBlock($formAttributes=array(), $desc="组合表单") {
    	$this->out->elementStart('form', $formAttributes);
        $this->out->elementStart('fieldset');
        $this->out->element('legend', null, $desc);
        if (array_key_exists('method', $formAttributes) 
        	&& strtolower($formAttributes['method']) == 'post') {
        	$this->out->hidden('token', common_session_token());
        }
    }
    
	function endFormBlock() {
		$this->out->elementEnd('fieldset');
        $this->out->elementEnd('form');
    }
	
	/**
	 * 根据传入的参数，显示无法找到结果，或者是没有记录项等空列表
	 * 
	 * 传入参数例1  '无法找到搜索结果'
	 * 传入参数例2  array('dt' => '无法找到搜索结果', 'dd' => '查看<a href="#">帮助</a>')
	 * 传入参数例3  array('dt' => '无法找到索索结果', 'dd' => array('1. 查看帮助', 
	 *                                                            '2. 拨打800电话', 
	 *                                                           '3. 请<a href="#">观众</a>帮忙解答'))
	 * @param $arr
	 * @return unknown_type
	 */
	function showEmptyListBlock($stringOrArray) {
		$this->out->elementStart('div', 'instruction guide');
		
		if (is_array($stringOrArray)) {			
				foreach ($stringOrArray as $di) {
					$this->out->elementStart('p');
					$this->out->raw($di);
					$this->out->elementEnd('p');
				}
		} else {
			$this->out->elementStart('p');
			$this->out->raw($stringOrArray);
			$this->out->elementEnd('p');
		}
		
		$this->out->elementEnd('div');
	}

	// white theme is suitable for a black background
	// when black theme better for a white background
	function showTagcloudWidget($theme="white") {
		$this->out->raw(common_stream('tu:tagcloud:' . $theme, array($this, "_showTagcloudWidget"), array($theme), 1800));
	}
	
    // show hot tags
    function _showTagcloudWidget($theme){
		    	
        $tags = new Notice_tag();
        $second_tag = new Second_tag();

        #Need to clear the selection and then only re-add the field
        #we are grouping by, otherwise it's not a valid 'group by'
        #even though MySQL seems to let it slide...
        $tags->joinAdd($second_tag);
        
        $tags->selectAdd();
        $tags->selectAdd('second_tag.name as tag');
        $tags->selectAdd('second_tag.id as tagid');

        #Add the aggregated columns...
        $tags->selectAdd('max(notice_id) as last_notice_id');
        $calc='sum(exp(-(now() - created)/%s)) as weight';
        $tags->selectAdd(sprintf($calc, common_config('tag', 'dropoff')));
        $tags->whereAdd('second_tag.id >= 1000000');
        $tags->groupBy('tag');
        $tags->orderBy('weight DESC');

        $tags->limit(40);

        $cnt = $tags->find();
        
        $xs = new XMLStringer();
        $xs->elementStart('div', 'widget tags');
		$xs->elementStart('p', array('id' => 'tagcloud', 'style' => 'display:none;'));
		
    	$tw = array();
        $sum = 0;
        while ($tags->fetch()) {
            $tw[] = array('tag' => $tags->tag, 'id' => $tags->tagid, 'weight' => $tags->weight);
            $sum += $tags->weight;
        }

//        ksort($tw);

        foreach ($tw as $t) {
            $this->_showTag($xs, $t['tag'], $t['id'], $t['weight'], $t['weight']/$sum, $theme);
        }
        $xs->elementEnd('p');
        $xs->elementEnd('div');
        
        return $xs->getString();
	}
	
    private function _showTag($out, $tag, $id, $weight, $relative, $theme)
    {
        if ($relative > 0.1) {
        	if ($theme == "white") {
        		// level 4 tag - extremly hot
				$color = '0xffffff';
        	} else if ($theme == "black") {
        		$color = "0x333333";
        	}
        } else if ($relative > 0.02) {
        	if ($theme == "white") {
        		// level 3 tag - very hot
				$color = '0xdddddd';
        	} else if ($theme == "black") {
        		$color = "0x444444";
        	}
        } else if ($relative > 0.005) {
        	if ($theme == "white") {
        		// level 2 tag - hot
				$color = '0xaaaaaa';
        	} else if ($theme  = "black") {
        		$color = "0x555555";
        	}
        } else {
        	if ($theme == "white") {
        		// level 1 tag - not so hot
				$color = '0x999999';
        	} else if ($theme == "black") {
        		$color = "0x666666";
        	}
        }

		$out->text(urlencode(('<a href="' . common_path('hottopics?tag=' . $id) . '" style="font-size:14px;" color="' . $color . '" hicolor="0xb8510c">'. $tag . '</a>')));
    }
    
    function showNewContentFilterBoxBlock($profile=null, $content_type=0, $tag=0, 
    		$action='home', $args=array(), $show_tag=true) {

		$this->out->elementStart('div', array('class' => 'rounded8 clearfix filter_content', 'id' => 'notice_filter_new')); //button_block3
    	$this->startFormBlock(array('style' => 'display:none;', 'class' => 'nfloat', 'method' => 'get', 'action' => common_local_url($action, $args)), '过滤消息');
    	if (array_key_exists('gtag', $args) && $args['gtag']) {
    		$this->out->element('input', array('type' => 'hidden', 'name' => 'gtag', 'value' => $args['gtag']));
    	}
    	$tagname = '全部';
    	$typename = '';
    	
    	if($show_tag) {
//	        $fts = First_tag::getFirstTags($profile->game_id);
			$fts = First_tag::getUniformFirstTags();
			$this->out->elementStart('p');	
	        $this->out->text('话题：');
	        $this->out->elementStart('select', array('name' => 'tag'));
	       	$this->out->element('option', array('value' => '0'), '全部');
	       	foreach ($fts as $id => $name) {
	       		if($tag && $tag == $id) {
	        		$this->out->element('option', array('value' => $id, 'selected' => 'selected'), $name);
	        		$tagname = $name;
	       		} else {
	        		$this->out->element('option', array('value' => $id), $name);
	       		}
	       	}
			$this->out->elementEnd('select');
        	$this->out->elementEnd('p');
    	}
    	

    	$contents = array(0 => '全部', 1 => '文字', 4 => '图片', 2 => '音乐', 3 => '视频', 5 => '小游戏');
    	$this->out->elementStart('p');
    	$this->out->text('类型：');
    	foreach($contents as $con_type => $con_name) {
    		if ($content_type == $con_type) {
    			$this->out->element('input', array('type' => 'radio', 'class' => 'radio', 'id' => 'fc'.$con_type, 'name' => 'filter_content', 'value' => $con_type, 'checked' => 'checked'));
    			$this->out->element('label', array('for' => 'fc'.$con_type), $con_name);
    			if ($content_type != 0)
    				$typename = $con_name;
    		} else {
    			$this->out->element('input', array('type' => 'radio', 'class' => 'radio', 'id' => 'fc'.$con_type, 'name' => 'filter_content', 'value' => $con_type));
    			$this->out->element('label', array('for' => 'fc'.$con_type), $con_name);
    		}
    	}
   		$this->out->elementEnd('p');
    	
    	$this->out->elementStart('p', 'last');
    	$this->out->element('input', array('type' => 'submit', 'class' => 'button76 silver76', 'value' => '查看'));
    	$this->out->elementEnd('p');
    	
    	$this->out->element('a', array('class' => 'close', 'href' => '#'), '关闭');
    	
    	$this->endFormBlock();
    	
    	$this->out->elementStart('p');
        $this->out->text('游友的' . $tagname . $typename . '消息');
        $this->out->elementStart('a', array('href' => '#', 'class' => 'toggle'));
        $this->out->raw('过滤选项<small>▼</small>');
        $this->out->elementEnd('a');
    	$this->out->elementEnd('p');
        
        $this->out->elementEnd('div');
    }
    
    function showContentFilterBoxBlock($profile=null, $content_type=0, $tag=0, 
    		$action='home', $args=array(), $show_tag=true) {
    	$this->out->elementStart('div', array('class' => 'rounded8 clearfix filter_content', 'id' => 'notice_filter')); //button_block3
        $this->out->element('p', null, '游友的实时动态');
         
        if($show_tag) {
//	        $fts = First_tag::getFirstTags($profile->game_id);
	        $fts = First_tag::getUniformFirstTags();
	        $this->out->elementStart('select');
	       	$this->out->element('option', array('value' => common_local_url($action, $args)), '全部');
	       	foreach ($fts as $id => $name) {
	       		$url = common_local_url($action, $args, array('tag' => $id));
	       		if($tag && $tag == $id)
	        		$this->out->element('option', array('value' => $url, 'selected' => 'selected'), $name);
	        	else
	        		$this->out->element('option', array('value' => $url), $name);
	        }
			$this->out->elementEnd('select');
        }
		
        $contents = array(0 => '全部', 1 => '文字', 4 => '图片', 2 => '音乐', 3 => '视频', 5 => '小游戏');
        $this->out->elementStart('ul');
        foreach ($contents as $con_type => $con_name) {
        	$class = '';
        	if ($content_type == $con_type) {
        		$class .= ' active';
        	}
        	$this->out->elementStart('li', $class);
    		$this->out->element('a', array('href' => common_local_url($action, $args, array('filter_content' => $con_type)), 'rel' => 'nofollow'), $con_name);
    		$this->out->elementEnd('li');
        }
    	$this->out->elementEnd('ul');

    	$this->out->elementEnd('div');
    }
    
    function startTable($extraAttrs = array()) {
    	$this->out->elementStart('table', array_merge(array('cellspacing' => '0', 'cellpadding' => '0', 'border' => '0'), $extraAttrs));
	    $this->out->elementStart('tbody');
    }
    
    function endTable() {
    	$this->out->elementEnd('tbody');
	    $this->out->elementEnd('table');
    }
    
    function showTabNav($navArray, $current_action_name)
    {
    	$this->out->elementStart('ul', array('id' => 'thirdary_nav', 'class' => 'clearfix'));
    	
    	for ($i = 0; $navArray && $i < count($navArray); $i ++) {
    		if ($navArray[$i]->action_name == $current_action_name) {
    			$this->out->elementStart('li', 'active');
    		} else {
    			$this->out->elementStart('li');
    		}
    		$this->out->element('a', array('href' => $navArray[$i]->href, 'alt' => $navArray[$i]->text), $navArray[$i]->text);
    		$this->out->elementEnd('li');
    	}
    	$this->out->elementEnd('ul');
    }

    function showOwnerInfoWidget($profile)
    {
    	$this->out->elementStart('div', 'widget owner_info clearfix');
    	
    	$this->out->elementStart('div', 'avatar');
    	$avatar = $profile->getAvatar(AVATAR_STREAM_SIZE);
    	if (!$avatar) {
    		$this->out->elementStart('a', array('href' => common_path('settings/avatar'), 'title' => '设置我的头像',
    					'alt' => $profile->nickname . '的头像'));
    	}else {
    		$this->out->elementStart('a', array('href' => $profile->profileurl, 'title' => '查看个人主页', 'alt' => $profile->nickname . '的头像'));
    	}
    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $profile->id, $profile->sex), 
    		'alt' => $profile->nickname));
    	
    	$this->out->elementEnd('a');    	
    	$this->out->elementEnd('div');
    	
    	$this->out->elementStart('div', 'profile');
    	$this->out->elementStart('p', 'nickname');
    	$this->out->element('a', array('href' => $profile->profileurl, 'title' => '访问个人主页'), $profile->nickname);
    	$this->out->elementEnd('p');
    	$this->out->elementStart('p');
    	$this->out->text(JOB_NAME() . ': ');
    	if ($profile->game_job) {
    		$this->out->text($profile->game_job);
    	} else {
    		$this->out->element('a', array('href'=>common_path('settings/game')), '未指定');
    	}
//    	if($profile->qq) {
//            $this->out->text($profile->qq);
//    	}else {
//    		$this->out->element('a', array('href'=>common_local_url('passwordsettings')), '未申请');
//    	}
    	$this->out->elementEnd('p');
    	
    	// 用户等级及积分进度
    	$gradeinfo = $profile->getUserUpgradePercent();
    	$this->out->elementStart('p', 'level');
    	$this->out->raw('<span>' . $gradeinfo['grade'] . '</span>级');
    	$scoreneed = ($gradeinfo['nextScore'] - $gradeinfo['score']) > 0 ? ($gradeinfo['nextScore'] - $gradeinfo['score']):0;
    	$follneed = ($gradeinfo['nextfollowers'] - $profile->followers) > 0 ? ($gradeinfo['nextfollowers'] - $profile->followers):0;
    	$this->out->elementStart('a', array('href'=>'#', 'title'=>'要升到'. ($gradeinfo['grade']+1). 
    	                         '级，您还需要' . ((int) ($scoreneed/10)) . '个铜G币和' . $follneed . '个关注者。', 'class' => 'progress'));
    	$this->out->element('em', array('style' => 'width: ' . $gradeinfo['percent'] . '%;'));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('p');
    	$this->out->elementEnd('div');
    	
    	$this->out->elementEnd('div');
    }
    
    function showSubInfoWidget($profile, $is_own = true, $addTryLogin = false)
    {
    	$this->out->elementStart('div', 'widget sub_info clearfix ' . ($is_own ? 'isown' : ''));
    	
    	if ($addTryLogin) {
	    	$this->out->elementStart('a', 
	    		array('href' => common_path('register'), 'class' => 'subscription trylogin', 'rel' => 'nofollow'));
    	} else {
    		$this->out->elementStart('a', 
	    		array('href' => common_path($profile->uname . '/subscriptions'), 'class' => 'subscription'));
    	}
    	$this->out->element('span', null, $profile->subscriptionCount());
    	if ($is_own) {
    		$this->out->text('我关注');
    	} else {
    		$this->out->text(($profile->sex == 'M' ? '他' : '她') . '关注');
    	}
    	$this->out->elementEnd('a');    	
    	
    	if ($addTryLogin) {
	    	$this->out->elementStart('a', array('href' => common_path($profile->uname . '/subscribers'), 'class' => 'subscriber trylogin'));
    	} else {
    		$this->out->elementStart('a', array('href' => common_path($profile->uname . '/subscribers'), 'class' => 'subscriber'));
    	}
    	$this->out->element('span', null, $profile->subscriberCount());
    	if ($is_own) {
    		$this->out->text('关注我');
    	} else {
    		$this->out->text('关注' . ($profile->sex == 'M' ? '他' : '她'));
    	}
    	$this->out->elementEnd('a');
    	
    	$this->out->elementStart('a', array('href' => $profile->profileurl, 'class' => 'moment', 
                     'title' => $profile->nickname . '在' . common_config('site', 'name') . '的消息'));
    	$this->out->element('span', null,$profile->noticeCount());
    	$this->out->text('消息数');
    	$this->out->elementEnd('a');
    	
    	$this->out->elementEnd('div');
    }
    
    function showToolbarWidget($profile, $tip='')
    {
    	$profile->query('BEGIN');
		$reply_result = Reply::unReadCount($profile->id);
		$reply_num = $reply_result['num'];
        $mes_result = Message::unReadCount($profile->id);
        $mes_num = $mes_result['num'];
        $sysmes_result = Receive_sysmes::unReadCount($profile->id);
        $sysmes_num = $sysmes_result['num'];
        $profile->query('COMMIT');
        
    	$this->out->elementStart('div', 'widget toolbox clearfix');
    	
    	$this->out->element('div', 'tbbg');
    	
    	$this->out->elementStart('span', array('id' => 'w_fav'));
    	$this->out->elementStart('a', array('href' => common_path($profile->uname . '/showfavorites'), 'alt' => '收藏'));
		$this->out->element('em');
		$this->out->text('收藏');    		
		$this->out->elementEnd('a');		
    	$this->out->elementEnd('span');
    	
    	if($reply_num > 0) 
    		$reply_max = $reply_result['maxid'];
    	else 
    		$reply_max = 0;
    	$this->out->elementStart('span', array('class' => 'replytip' . ($reply_num ? ' flick' : ''), 'id' => 'w_reply'));
    	
    	$this->out->elementStart('a', array('id' => 'reply', 'href' => common_path($profile->uname . '/replies'), 'alt' => '回复'));
    	
    	$this->out->element('em');
		$this->out->element('b', null, '回复');    		
		$this->out->elementEnd('a');		
    	$this->out->elementEnd('span');
    	
    	if($mes_num > 0) 
    		$mes_max = $mes_result['maxid'];
    	else 
    		$mes_max = 0;
    	$this->out->elementStart('span', array('class' => 'inboxtip' . ($mes_num ? ' flick' : ''), 'id' => 'w_msg'));
    	$this->out->elementStart('a', array('id' => 'inbox', 'href' => common_path($profile->uname . '/inbox'), 'alt' => '私信'));
    	$this->out->element('em');
		$this->out->element('b', null, '私信');    		
		$this->out->elementEnd('a');		
    	$this->out->elementEnd('span');

    	$this->out->elementStart('span', array('class' => 'sysmestip' . ($sysmes_num ? ' flick' : ''), 'id' => 'w_bst'));
    	$this->out->elementStart('a', array('id' => 'sysmes', 'href' => common_path('main/sysmessage'), 
    					'num' => $sysmes_num, 'alt' => '通知'));
    	$this->out->element('em');
		$this->out->element('b', null, '通知');    		
		$this->out->elementEnd('a');		
    	$this->out->elementEnd('span');
    	
    	$this->out->elementEnd('div');
    }
    
    function showNavigationWidget($navArray, $current_action_name, $addTryLogin = false) 
    {
    	$this->out->elementStart('ul', array('id' =>'w_nav', 'class' => 'widget'));
    	
    	for ($i = 0; $navArray && $i < count($navArray); $i ++) {
    		if ($navArray[$i]->action_name == $current_action_name) {
    			$this->out->elementStart('li', 'active');
    		} else {
    			$this->out->elementStart('li');
    		}
    		$this->out->element('span');
    		if ($navArray[$i]->action_name != $current_action_name 
    			&& $addTryLogin) {
    			$this->out->element('a', array('href' => common_path('register'), 'class' => 'trylogin', 'rel' => 'nofollow'), $navArray[$i]->text);
    		} else {
    			$this->out->element('a', array('href' => $navArray[$i]->href), $navArray[$i]->text);
    		}
    		$this->out->elementEnd('li');
    	}
    	$this->out->elementEnd('ul');    	    
	}
	
	function showTagNavigationWidget($cur_user, $from_action, $cur_tag, $actionParam1= array(), $actionParam2 = array()) 
    {
    	if (empty($cur_tag)) {
    		$cur_tag = '全部';
    	}
    	$this->navs = new NavList_Home($cur_user, $from_action, $actionParam1, $actionParam2);
    	$navArray = $this->navs->lists();
    	$this->out->elementStart('ul', array('id' =>'w_nav', 'class' => 'widget'));
    	
    	for ($i = 0; $navArray && $i < count($navArray); $i ++) {
    		if ($navArray[$i]->text == $cur_tag) {
    			$this->out->elementStart('li', 'active');
    		} else {
    			$this->out->elementStart('li');
    		}
    		$this->out->element('span');
    		$this->out->element('a', array('href' => $navArray[$i]->href), $navArray[$i]->text);
    		$this->out->elementEnd('li');
    	}
    	$this->out->elementEnd('ul');	    

	}
    
	function showGroupsWidget($profile, $limit = null, $is_own = true, $addTryLogin = false) {
		$this->out->elementStart('dl', 'widget grid-6');
		$this->out->elementStart('dt');
		$this->out->text(Profile::displayName($profile->sex, $is_own) . '加入的' . GROUP_NAME());
		$this->out->element('a', array('class' => 'unfold', 'href' => '#'));
		$this->out->elementEnd('dt');
		
		$this->out->elementStart('dd');
		$groups = $profile->getGroups(0,$limit);
		if ($groups && $groups->N > 0) {
			$this->out->elementStart('ul', 'clearfix');
			while ($groups->fetch()) {
				$this->out->elementStart('li');
				if ($addTryLogin) {
					$this->out->elementStart('a', array('href' => common_path('register'), 
				                         'title' => $groups->getBestName(), 'class' => 'trylogin', 'rel' => 'nofollow'));
				} else {
					$this->out->elementStart('a', array('href'=>$groups->homeUrl(), 
				                         'title' => $groups->getBestName()));
				}
				$savatar = ($groups->mini_logo) ?
		                       $groups->mini_logo : User_group::defaultLogo(GROUP_LOGO_MINI_SIZE);
		        $this->out->element('img', array('src' => $savatar, 
		    		    'alt' => $groups->getBestName()));
				$this->out->elementEnd('a');
				$this->out->elementEnd('li');
			}
			$this->out->elementEnd('ul');
		} else {
			$this->out->element('p', null, Profile::displayName($profile->sex, $is_own) . '还没有加入' . GROUP_NAME());
		}
		if ($is_own) {
			$this->out->elementStart('p', 'op');
			$this->out->element('a', array('id'=>'group_create', 'href'=>common_path('group/new'), 'title' => '创建' . GROUP_NAME()), '+ 创建' . GROUP_NAME());
			$this->out->text('|');
			$this->out->element('a', array('href'=>common_path('groups'), 'title' => '显示我加入的全部' . GROUP_NAME()), '全部');
			$this->out->elementEnd('p');
		}
		$this->out->elementEnd('dd');
		
		$this->out->elementEnd('dl');
	}
	
	function showUserListWidget($profiles, $title, $id=false) {
		if ($profiles && $profiles->N > 0) {
			$this->out->elementStart('dl', 'widget grid-6');
			$this->out->elementStart('dt');
			$this->out->text($title);
			$this->out->element('a', array('class' => 'unfold', 'href' => '#'));
			$this->out->elementEnd('dt');
			
			$this->out->elementStart('dd');
				
			$this->out->elementStart('ul', 'clearfix');
			while ($profiles->fetch()) {
				$profile = $profiles;	
		    	if($id)
		    		$profile = Profile::staticGet('id', $profile);
				$this->out->elementStart('li');
				$this->out->elementStart('a', array('href' => $profile->profileurl, 'title' => $profile->nickname));
	    		$savatar = $profile->getAvatar(AVATAR_MINI_SIZE);
				$this->out->element('img', array( 
	    			'src' => ($savatar) ? $savatar->displayUrl() : Avatar::defaultImage(AVATAR_MINI_SIZE, $profile->id, $profile->sex), 
	    			'alt' => $profile->nickname));
				$this->out->elementEnd('a');
				$this->out->elementEnd('li');
	    	}
	    	$this->out->elementEnd('ul');
		
	    	$this->out->elementEnd('dd');
			$this->out->elementEnd('dl');
		}
	}
	
	function showInviteWidget()
	{
		$this->out->element('div', 'split');
		$this->out->elementStart('dl', 'widget invite');
		$this->out->element('dt', null, '邀请朋友');
		$this->out->elementStart('dd');
		$this->out->element('p', null, '成功邀请朋友获得更多G币');
		$this->out->elementStart('p');
		$this->out->element('a', array('class' => 'button76 green76', 'alt' => '邀请朋友', 'href' => common_path('main/invite'), 'target' => '_blank'), '邀请朋友');
		$this->out->elementEnd('p');
		$this->out->elementEnd('dd');
		$this->out->elementEnd('dl');
	}
    
    function showIntroWidget($title, $content) {
    	$this->out->elementStart('dl', 'widget intro');
	    $this->out->element('dt', null, $title);
	    $this->out->element('dd', null, $content);
    	$this->out->elementEnd('dl');
    }
    
    function showMakeYourTheme($user) {
    	$this->out->elementStart('div', 'makeutheme');
    	$this->out->element('div', 'bg');
    	$this->out->element('a', array('href' => common_path($user->uname . '?theme=true'), 'title' => '发挥您的艺术天赋，DIY一下', 'class' => 'outbound'), '我要换肤');
    	$this->out->elementEnd('div');
    }
    
    function showHotWordList($hotwords, $title="", $url="#") {
    	$this->out->elementStart('dl', 'widget hot_board');
		$this->out->elementStart('dt');
		$this->out->text($title);
		$this->out->element('a', array('class' => 'toggle', 'href' => $url), '更多');
		$this->out->elementEnd('dt');
		
		$this->out->elementStart('dd');		
		$this->out->elementStart('ol');
    	$cnt = 0;
		foreach ($hotwords as $hotword){
			$this->out->elementStart('li');
			$this->out->element('a', array('href' => common_path('hottopics/' . trim($hotword->word))), 
					trim($hotword->word));
			$this->out->text('(' . intval($hotword->score) . ')');
			$this->out->elementEnd('li');
		}
		$this->out->elementEnd('ol');
    	$this->out->elementEnd('dd');
		$this->out->elementEnd('dl');
    }
    
    function showLeftNav($list, $classMap, $action, $is_anonymous = false) {
    	$this->out->elementStart('ul', array('class' =>'nav'));
		foreach ($list as $list_item) {
			if ($list_item->action_name == $action) {
    			$this->out->elementStart('li', array('class' => 'active'));
			} else {
    			$this->out->elementStart('li');
			}
			
    		if ($is_anonymous && $list_item->need_login) {
    			$this->out->elementStart('a', array('class' => $classMap[$list_item->action_name] . ' trylogin', 'href' => $list_item->href));
    		} else {
    			$this->out->elementStart('a', array('class' => $classMap[$list_item->action_name], 'href' => $list_item->href));
    		}

    		$this->out->text($list_item->text);
    		if ($list_item->is_new) {
    			$right_px = 'right:' . (67 - mb_strlen($list_item->text, 'utf-8') * 14) . 'px;';
    			$this->out->element('span', array('class' => 'new', 'style' => $right_px));
    		}
    		$this->out->elementEnd('a');
    		$this->out->elementEnd('li');
		}
    	$this->out->elementEnd('ul');
    }
		
	function updownpagination($total, $action, $args = array(), $params = array(), $cur_page = 1, $displayPerPage = GROUPS_PER_PAGE)
    {
    	if ($cur_page > 1 ) {
    		$prepage_href = common_local_url($action, $args, array_merge($params, array('page' => $cur_page-1)));
    		$this->out->element('a', array('class' => 'prevpage page' , 'href' => $prepage_href), '<<上一页');
    	}
    	if ($cur_page * $displayPerPage < $total) {
    		$nextpage_href = common_local_url($action, $args, array_merge($params, array('page' => $cur_page+1)));
    		$this->out->element('a', array('class' => 'nextpage page', 'href' => $nextpage_href), '下一页>>');
    	}
    }
}