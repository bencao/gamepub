<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/noticelist.php';

class PublicHTMLTemplate extends PublicthreecolumnHTMLTemplate
{
	var $arr = array(
    	'100671' => array('brief' => '冒险岛著名女玩家',
    		'detail' => '冒险岛世界里的著名女玩家，身在七星剑服务器，有着一身华丽的套装，有着无数顶级的装备，有着人人羡慕的满级，平日里以乖巧可爱、心地善良著称，但在冒险岛中绝对是一位冒险的强者。'),
    	'109403' => array('brief' => '穿越火线高玩，外号“狙神”', 
    		'detail' => 'CF游戏里的狙击高手，也是最有影响力的狙击手之一，玩家称其为“狙神”，在CF游戏里拥有众多粉丝和追随者，看鬼狙用狙击枪，那简直是一种享受、一种刺激。'),
    	'100593' => array('brief' => '问道著名玩家',
    		'detail' => '问道游戏里的著名玩家，曾经担任GamePub问道版块的管理员，目前在问道落英缤纷服务器，喜欢分享游戏中有价值的信息和咨询。'),
    	'100570' => array('brief' => '天下贰著名玩家',
    		'detail' => '一个纵横于天下贰游戏里的男人，钟情于天下贰很多年，最爱的就是把搞笑和娱乐带给各位游友，与大家一起分享生活中的快乐。'),
    	'100555' => array('brief' => 'QQ炫舞高级玩家',
    		'detail' => 'QQ炫舞高端玩家，有着一手很棒的炫舞技术，与家族勇敢闯天下，心地善良，一直坚持着自己的炫舞梦想。'),
    	'100552' => array('brief' => '艾泽拉斯最佳博客奖的得主',
			'detail' => '魔兽世界里的著名MM玩家，有着对魔兽世界无比的热爱，善于撰写魔兽世界博客，是艾泽拉斯最佳博客奖的得主。'),
    	'100524' => array('brief' => '魔兽世界著名漫画师', 
    		'detail' => '魔兽世界著名漫画师，魔兽世界忠实的MM玩家，擅长用漫画去讲述魔兽世界中的故事，乐意与大家分享漫画世界中的魔兽世界，在魔兽世界圈内，拥有很多的读者和粉丝。'),
    	'100495' => array('brief' => '魔兽世界高级盗贼',
    		'detail' => '魔兽世界著名盗贼玩家，热爱PVP，热爱背影中的秒杀，曾经担任GamePub魔兽世界版块的管理员。'),
    	'100481' => array('brief' => '魔兽世界插件作者',
			'detail' => '著名的魔兽世界插件制作者，独自制作了著名的魔兽世界插件《渔渔简单爱》，此插件在魔兽世界国服和台服均受到了广大魔兽世界玩家的热爱，有着巨大的粉丝群体。'),
		'100472' => array('brief' => '地下城与勇士高级玩家',
			'detail' => 'DNF高级玩家，5个号，鬼泣、柔道、机械、驱魔、气功，都满级的。目前钟情于柔道和气功，装备不断加强中~')
	);
    		
	function metaKeywords() {
		return '游戏酒馆，GamePub，酒馆动态，游戏，精彩';
	}

	function metaDescription() {
		return '汇集游戏酒馆中，数十款游戏的玩家们发表的精彩消息。按您喜好，可分别对文字、图片、音乐、视频类型的消息进行查看，了解现在正在发生着什么。';
	}

    function title()
    {
        if ($this->cur_page > 1) {
            return sprintf('酒馆动态, 第 %d 页', $this->cur_page);
        } else {
            return common_config('site', 'name') . '酒馆动态';
        }
    }

//    function showEmptyList()
//    {
//        $message = '这是平台最新消息列表， 但是还没有人发表过任何消息。' . ' ';
//
//        if (common_current_user()) {
//            $message .= '快来第一个发言吧！';
//        }
//        else {
//            if (! (common_config('site','closed') || common_config('site','inviteonly'))) {
//                $message .= '赶快来 [注册](%%action.register%%) ， 成为第一个发言的人吧！';
//            }
//		}
//		
//		$this->tu->showEmptyListBlock(common_markup_to_html($message));
//    }
    
    function show($args) {
    	$this->hotword = $args['hotword'];
    	$this->notice = $args['notice'];
    	$this->vipids = $args['vipids'];
    	$this->rand = $args['rand'];
    	parent::show($args);
    }
    
    function map($key) {
		if (array_key_exists($key, $this->arr)) {
    		return $this->arr[$key];
		} else {
			return false;
		}
    }
    
    function showContent()
    {   
    	$this->elementStart('h2');
    	$this->text('酒馆动态');
    	$this->element('span', null, '-- 我们为您更新游友们最即时、有趣的消息');
        $this->elementEnd('h2');
        
        $this->elementStart('div', 'daily');
        $this->elementStart('h3');
        $this->element('strong', null, '今日热点：');
        $this->text('大家一起关注');
        // get a good hot topic
        $this->element('a', array('href' => common_local_url('noticesearch', null, array('q' => $this->hotword))), $this->hotword);
        if ($this->cur_user) {
        	$this->element('a', array('href' => common_path('newnotice'), 'class' => 'say', 'topic' => $this->hotword), '我也说两句');
        } else {
        	$this->element('a', array('href' => common_path('newnotice'), 'class' => 'say trylogin', 'topic' => $this->hotword), '我也说两句');
        }
        $this->elementEnd('h3');
        $this->elementStart('div', 'related_notice');
//        $this->elementStart('div', 'card');
//        $this->elementStart('div', 'avatar');
//        $this->elementStart('a', array('href' => '#'));
//        $this->element('img', array('src' => '#', 'alt' => 'nickname'));
//        $this->elementEnd('a');
//        $this->elementEnd('div');
//        $this->elementStart('div', 'op');
//        $this->elementStart('form', array('method' => 'post', 'class' => 'subscribe', 'action' => common_local_url('subscribe')));
//        $this->elementStart('fieldset');
//        $this->element('legend', null, '关注');
//        $this->element('input', array('type' => 'hidden', 'name' => 'token', 'value' => common_session_token()));
//        $this->element('input', array('type' => 'hidden', 'name' => 'subscribeto', 'value' => 'uid'));
//        $this->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => '关注'));
//        $this->elementEnd('fieldset');
//        $this->elementEnd('form');
//        $this->elementEnd('div');
//        $this->elementEnd('div');
//        $this->elementStart('p', 'nickname');
//        $this->element('a', array('href' => '#'), '小丽丽');
//        $this->elementEnd('p');
//        $this->element('p', 'info', '魔兽世界 - 末日行者 粉丝454512');
//        $this->element('p', 'msg', '这里是内容更范围范围范围范围而非哇ifweif为覅为覅为覅ewing非我非为覅为丰富我iefwifeiwefiew覅为发额外覅额为覅');
        $this->elementEnd('div');
        $this->elementEnd('div');
        
        $this->elementStart('dl', 'sugg');
        $this->elementStart('dt');
        $this->text('著名玩家推荐');
        $this->element('a', array('href' => common_path('halloffame')), '更多');
        $this->elementEnd('dt');
        $this->elementStart('dd', 'clearfix');
        $this->element('a', array('class' => 'rollleft', 'href' => '#'));
        $this->element('a', array('class' => 'rollright', 'href' => '#'));
        $this->elementStart('div', 'cont');
        $this->elementStart('ul');
        
        foreach ($this->vipids as $vip) {
        	$vipprofile = Profile::staticGet('id', $vip);
	        
        	$this->elementStart('li');
	        $this->elementStart('div', 'avatar');
	        if ($vipprofile->recommend_words) {
	        	$this->elementStart('a', array('href' => $vipprofile->profileurl, 'title' => $vipprofile->recommend_words));
	        } else {
	        	$this->elementStart('a', array('href' => $vipprofile->profileurl, 'title' => $vipprofile->bio));
	        }
	        $this->element('img', array('src' => $vipprofile->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $vipprofile->nickname, 'width' => '48', 'height' => '48'));
	        $this->elementEnd('a');
	        $this->elementEnd('div');
	        $this->elementStart('p', 'nickname');
        	if ($vipprofile->recommend_words) {
	        	$this->elementStart('a', array('href' => $vipprofile->profileurl, 'title' => $vipprofile->recommend_words));
	        } else {
	        	$this->elementStart('a', array('href' => $vipprofile->profileurl, 'title' => $vipprofile->bio));
	        }
	        $this->text($vipprofile->nickname);
	        $this->elementEnd('a');
	        $this->elementEnd('p');
	        
	        
	        if ($vipprofile->recommend_words) {
	        	$this->element('p', 'intro', $vipprofile->recommend_words);
	        } else {
	        	$this->element('p', 'intro', $vipprofile->bio);
	        }
	        $this->elementEnd('li');
        }
        
        $this->elementEnd('ul');
        $this->elementEnd('div');
        $this->elementEnd('dd');
        $this->elementEnd('dl');
//        
//        $this->elementStart('dl', 'sugg');
//        $this->elementStart('dt');
//        $this->text('今日特别推荐');
//        $this->element('a', array('href' => common_local_url('funnypeople')), '更多');
//        $this->elementEnd('dt');
//        $this->elementStart('dd', 'clearfix');
//        $this->element('a', array('class' => 'rollleft', 'href' => '#'));
//        $this->element('a', array('class' => 'rollright', 'href' => '#'));
//        $this->elementStart('div', 'cont');
//        $this->elementStart('ul');
//    	
//        while ($this->rand->fetch()) {
//        	$vipprofile = $this->rand;
//	        $this->elementStart('li');
//	        $this->elementStart('div', 'avatar');
//	        $this->elementStart('a', array('href' => $vipprofile->profileurl));
//	        $this->element('img', array('src' => $vipprofile->avatarUrl(AVATAR_STREAM_SIZE), 'alt' => $vipprofile->nickname, 'width' => '48', 'height' => '48'));
//	        $this->elementEnd('a');
//	        $this->elementEnd('div');
//	        $this->element('p', 'nickname', $vipprofile->nickname);
//	        $this->element('p', 'intro', $vipprofile->bio);
//	        $this->elementEnd('li');
//        }
//        $this->elementEnd('ul');
//        $this->elementEnd('div');
//        $this->elementEnd('dd');
//        $this->elementEnd('dl');
        
        $this->elementStart('div', 'hot-switch-wrap');
        $this->elementStart('ul', 'hot-switch');
        $this->elementStart('li');
        $this->element('a', array('href' => common_path('ajax/publictimeline?which=discuss'), 'class' => 'active', 'which' => 'discuss'), '最新评论');
        $this->elementEnd('li');
        $this->elementStart('li');
        $this->element('a', array('href' => common_path('ajax/publictimeline?which=retweet'), 'which' => 'retweet'), '最新转发');
        $this->elementEnd('li');
        $this->elementStart('li');
        $this->element('a', array('href' => common_path('ajax/publictimeline?which=latest'), 'which' => 'latest'), '最新消息');
        $this->elementEnd('li');
        $this->elementEnd('ul');
        
        $this->element('a', array('href' => common_path('ajax/publictimeline?which=latest'), 'which' => 'latest', 'class' => 'refresh', 'style' => 'display:none;'), '刷新');
        $this->elementEnd('div');
        
        $cnt = 0;
        
        if ($this->notice->N > 0) {
        	$nl = new ShowgameNoticeList($this->notice, $this);
        	$cnt = $nl->show();
        }
        
    	if ($cnt > NOTICES_PER_PAGE) {
	        $this->element('a', array('href' => common_path('ajax/publictimeline?which=discuss&page=' . ($this->cur_page + 1)),
	            'id' => 'notice_more', 'rel' => 'nofollow'));
        }
//        if (common_current_user()) {
//			$this->tu->showContentFilterBoxBlock($this->cur_user, $this->args['filter_content'], $this->args['tag'], 'public');   	
//        }
//        
//    	$nl = new PublicNoticeList($this->args['notice'], $this);
//        $cnt = $nl->show();
//
//        if($this->args['filter_content_flag'])
//        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
//                     $this->cur_page, 'public', array('filter_content' => $this->args['filter_content']));       		
//       	else if($this->args['tag'])
//        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
//                     $this->cur_page, 'public', array('tag' => $this->args['tag']));       		       	
//        else        	
//        	$this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
//                          $this->cur_page, 'public');    
    }
    
	function showRightside() {
		if ($this->cur_user) {
			$page_owner_profile = $this->cur_user->getProfile();
    	
    		$this->tu->showOwnerInfoWidget($page_owner_profile);
    		$this->tu->showSubInfoWidget($page_owner_profile, true);	
    		
		} else {
			$this->showSearchFormWidget();
		}
		$this->_showSofa($this->args['sofanotice_id']);
    	
		$this->_showGamePubEvents();
		
		
//    	$users = common_stream('userids:mosttalk', array("Notice", "getMosttalkUsers"), array(20), 3600 * 24);
//    	$users = common_random_fetch($users,5);
//    	$this->showUserListWidget($users, '游戏酒馆草根达人',common_local_url('rank',array('type' => 'user')));
    	
    	$users = common_stream('userids:mostvisit', array("Profile", "getMostvisitUsers"), array(20), 3600 * 24);
    	$users = common_random_fetch($users,5);
    	if($users)
    		$this->showUserListWidget($users, '游戏酒馆人气之星',common_path('rank/user'));
    	
    	
		$subs = common_stream('userids:active', array("Grade_record", "getActiveUsers"), array(20), 3600 * 24);
    	$subs = common_random_fetch($subs,5);
    	if($subs)
    		$this->showUserListWidget($subs, '今日活跃用户',common_path('rank/user'));
    	
	}
	
	function _showSofa($notice_id) {
    	$this->elementStart('div', 'sofa');
    	$this->element('a', array('href' => common_path('discussionlist/' . $notice_id), 'title' => '找个还没有被评论的消息抢个沙发！'), '逛累了？找个沙发坐坐');
    	$this->elementEnd('div');
    }
	
	function _showGamePubEvents() {
		$this->elementStart('dl', 'widget hot_board');
		$this->element('dt', null, 'GamePub新闻');
		$this->elementStart('dd');
		$this->elementStart('ol');
		
		$this->elementStart('li');
    	$this->text('1月17日，Firefox扩展');
    	$this->element('a', array('href' => '/clients/fxext'), 'XGamePub V1.0');
    	$this->text('发布！');
    	$this->elementEnd('li');
    	
		$this->elementStart('li');
    	$this->text('12月2日，Chrome扩展');
    	$this->element('a', array('href' => '/clients/chromeext'), 'GamePub Button V1.0');
    	$this->text('发布，快试试吧！');
    	$this->elementEnd('li');
    	
		$this->elementStart('li');
		$this->text('11月28日，Feed自动导入功能上线，');
		$this->element('a', array('href' => common_path('settings/feed')), '尝鲜试用');
		$this->elementEnd('li');
		
		$this->elementStart('li');
		$this->text('9月16日，呼游桌面端更新V1.2版，');
		$this->element('a', array('href' => common_path('clients/hooyou')), '点此下载');
		$this->elementEnd('li');
		
		$this->elementStart('li');
		$this->text('8月22日，可以看视频了解GamePub了，');
		$this->element('a', array('href' => common_path('showtime')), '点此观看');
		$this->elementEnd('li');
		
		$this->elementEnd('ol');
		$this->elementEnd('dd');
		$this->elementEnd('dl');
	}
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	$this->script('js/lshai_public.js');
    	$this->script('js/lshai_say.js');
    	if (! $this->cur_user) {
    		$this->script('js/lshai_search.js');
    	}
    }
}

class RankNoticeList extends ShowgameNoticeList
{
	var $type = 'retweet'; 	
	function __construct($notice, $out=null, $type='retweet')
   	{
       	 parent::__construct($notice, $out);
        $this->type = $type;
     }
    function newListItem($notice)
    {
        return new RankNoticeListItem($notice, $this->out, $this->type);
    }
}

class RankNoticeListItem extends ShowgameNoticeListItem
{
	var $type = 'retweet';
	function __construct($notice, $out=null, $type='retweet')
    {
        parent::__construct($notice, $out);
        $this->profile = $notice->getProfile();
        $this->profileUser = $this->profile->getUser();
        $this->user = common_current_user();
        $this->type = $type;
    }
	function showNoticeInfo()
    {
    	$this->out->elementStart('div', array('class' => 'content'));
    	$game = Game::staticGet('id', $this->profileUser->game_id);
    	$server = Game_server::staticGet('id', $this->profileUser->game_server_id);
    	$text = '[<span class="tag"><a rel="tag" target="_blank" href="' . 
    			common_path('game/' . $this->profileUser->game_id) .
    			'">' . $game->name . '</a></span>][<span class="tag"><a rel="tag" target="_blank" href="' . 
    			common_path('gameserver/' .$this->profileUser->game_server_id) .
    			'">' . $server->name . '</a></span>]';
    			
    	$this->out->raw($text);
    	$this->out->raw($this->notice->rendered);
    	$this->out->elementEnd('div');
    	}
    
	function showImage() {
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	
    	if($this->type == 'discuss')
    	{
    		$this->out->element('p',array('class' => 'retweet_cnt'),$this->notice->discussion_num?$this->notice->discussion_num:0);
    		$this->out->element('p',null,'被评论');
    	} else {
    		$this->out->element('p',array('class' => 'retweet_cnt'),$this->notice->retweet_num?$this->notice->retweet_num:0);
    		$this->out->element('p',null,'被转');
    	}
    	$this->out->elementEnd('div');
    }
    
}
?>