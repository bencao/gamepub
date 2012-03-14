<?php

if (!defined('SHAISHAI')) {
    exit(1);
}
require_once(INSTALLDIR.'/lib/netspider.php');

class RecentnewsHTMLTemplate extends GamethreecolumnHTMLTemplate
{ 		
	function metaKeywords() {
		return '游戏酒馆，GamePub，游戏动态，游戏，精彩';
	}

	function metaDescription() {
		return '汇集游戏酒馆中，数十款游戏的玩家们发表的精彩消息。按您喜好，可分别对文字、图片、音乐、视频类型的消息进行查看，了解现在正在发生着什么。';
	}

    function title()
    {
        if ($this->cur_page > 1) {
            return sprintf('%s动态, 第 %d 页', $this->cur_game->name, $this->cur_page);
        } else {
            return $this->cur_game->name . '动态';
        }
    }

    function show($args) {
    	$this->notice = $args['notice'];
    	parent::show($args);
    }
    
    function showContent()
    {   
    	
    	$this->elementStart('h2');
    	$this->text('最新动态');
    	$this->element('span', null, '-- '.$this->cur_game->name.'的最新动态');
        $this->elementEnd('h2');
        
        $this->elementStart('dl', 'news');
        $this->elementStart('dt');
        $this->text('最新热点');
        $this->element('a', array('href' => '#', 'class' => 'next'), '后一页');
        $this->element('a', array('href' => '#', 'class' => 'prev'), '前一页');
	    $this->elementEnd('dt');
	    
        $this->elementStart('dd');
        $this->elementStart('div', 'cont');
        $this->elementStart('ul');
        
        $newslist = net_getgamenews($this->cur_game->id);
        
        $count = 0;
		foreach ($newslist as $news)
        {
			$this->elementStart('li', array('class' => 'clearfix'));
			$this->elementStart('span', array('class' => 'content'));
     	    $this->element('a', array('href' => $news->url, 'target' => '_blank'), $news->content);
			$this->elementEnd('span');
    		$this->element('span', array('class'=>'date'), date('Y-m-d'));
			$this->elementEnd('li');
			//$count++;
			//if ($count >= 10)
			//	break;
        }
        $this->elementEnd('ul');

        $this->elementEnd('div');
        
	    $this->elementEnd('dd');
        $this->elementEnd('dl');

        
        ///////////////////////////////////////////////////////////////////////////////////////////
        $this->elementStart('dl', 'videos');
        $this->elementStart('dt');
        $this->text($this->cur_game->name.'玩家分享最新视频');
        $this->element('a', array('href' => common_local_url('hotnotice',array('type'=>'video'))), '更多');
        $this->elementEnd('dt');
        $this->elementStart('dd');
        $this->element('a', array('class' => 'prev', 'href' => '#'),'前');
        $this->elementStart('div', 'cont');
        $this->elementStart('ol', 'clearfix');

        $videoslist = Notice::getLatestVideos(0, 6, $this->cur_game->id);
        foreach ($videoslist as $id)
        {
        	$video = Notice::staticGet($id);
        	$user = Profile::staticGet($video->user_id);
			$this->elementStart('li');
			$this->elementStart('p', array('class' => 'nickname'));
     	    $this->element('a', array('href' => $user->profileurl), $user->nickname);
			$this->elementEnd('p');
			$this->elementStart('div', array('class' => 'content'));
     	    $this->raw($video->rendered);
			$this->elementEnd('div');
			$this->elementEnd('li');
        }

        $this->elementEnd('ol');
        $this->elementEnd('div');
        $this->element('a', array('class' => 'next', 'href' => '#'),'后');
        $this->elementEnd('dd');
        $this->elementEnd('dl');
        
        ///////////////////////////////////////////////////////////////////////////////////////////
        $this->elementStart('dl', 'photos');
        $this->elementStart('dt');
        $this->text($this->cur_game->name.'玩家分享最新图片');
        $this->element('a', array('href' => common_local_url('hotnotice',array('type'=>'photo'))), '更多');
        $this->elementEnd('dt');
        $this->elementStart('dd');
        $this->element('a', array('class' => 'prev', 'href' => '#'),'前');
        $this->elementStart('div', 'cont');
        $this->elementStart('ol', 'clearfix');
        
        $pictureslist = Notice::getLatestPictures(0, 6, $this->cur_game->id);
        foreach ($pictureslist as $id)
        {
        	$picture = Notice::staticGet($id);
        	$user = Profile::staticGet($picture->user_id);
			$this->elementStart('li');
			$this->elementStart('p', array('class' => 'nickname'));
     	    $this->element('a', array('href' => $user->profileurl), $user->nickname);
			$this->elementEnd('p');
			$this->elementStart('div', array('class' => 'content'));
			$this->raw($this->_replaceWithPrimitivePic($picture->rendered));
			$this->elementEnd('div');
			$this->elementEnd('li');
        }
        $this->elementEnd('ol');
        $this->elementEnd('div');
        $this->element('a', array('class' => 'next', 'href' => '#'),'后');
        $this->elementEnd('dd');
        $this->elementEnd('dl');
        
        
        ///////////////////////////////////////////////////////////////////////////////////////////
        $this->elementStart('dl', 'musics');
        $this->elementStart('dt');
        $this->text($this->cur_game->name.'玩家分享最新音乐');
        $this->element('a', array('href' => common_local_url('hotnotice',array('type'=>'music'))), '更多');
        $this->elementEnd('dt');
        $this->elementStart('dd');
        $this->element('a', array('class' => 'prev', 'href' => '#'),'前');
        $this->elementStart('div', 'cont');
        $this->elementStart('ol', 'clearfix');
        
        $musicslist = Notice::getLatestMusics(0, 6, $this->cur_game->id);
        foreach ($musicslist as $id)
        {
        	$music = Notice::staticGet($id);
        	$user = Profile::staticGet($music->user_id);	
			$this->elementStart('li', array('class' => 'noticeitem', 'nid' => $id));
			$this->elementStart('p', array('class' => 'nickname'));
     	    $this->element('a', array('href' => $user->profileurl), $user->nickname);
			$this->elementEnd('p');
			$this->elementStart('div', array('class' => 'content'));
			$this->raw($music->rendered);
			$this->elementEnd('div');
			$this->elementEnd('li');
        }
        
        $this->elementEnd('ol');
        $this->elementEnd('div');
        $this->element('a', array('class' => 'next', 'href' => '#'),'后');
        $this->elementEnd('dd');
        $this->elementEnd('dl');
        
        ///////////////////////////////////////////////////////////////////////////////////////////
        
        

        $this->elementStart('ul', 'hot-switch');
        $this->elementStart('li');
        $this->element('a', array('href' => common_local_url('recentnewstimeline', null, array('which' => 'discuss',"gameid" => $this->cur_game->id)), 'class' => 'active', 'which' => 'discuss'), '最新评论');
        $this->elementEnd('li');
        $this->elementStart('li');
        $this->element('a', array('href' => common_local_url('recentnewstimeline', null, array('which' => 'retweet',"gameid" => $this->cur_game->id)), 'which' => 'retweet'), '最新转发');
        $this->elementEnd('li');
        $this->elementStart('li');
        $this->element('a', array('href' => common_local_url('recentnewstimeline', null, array('which' => 'latest',"gameid" => $this->cur_game->id)), 'which' => 'latest'), '最新消息');
        $this->elementEnd('li');
        
        $this->elementEnd('ul');
        
        $cnt = 0;
        
        if ($this->notice->N > 0) {
        	
        	$nl = new ShowgameNoticeList($this->notice, $this);
        	$cnt = $nl->show();
        }
        
    	if ($cnt > NOTICES_PER_PAGE) {
	        $this->element('a', array('href' => common_local_url('recentnewstimeline', null, array('page' => $this->cur_page + 1, 'which' => 'discuss', 'gameid' => $this->cur_game->id)),
	            'id' => 'notice_more', 'rel' => 'nofollow'));
        }
    }
    
    function _replaceWithPrimitivePic($rendered) 
    {
    	//replace 'href=javascript:void(0);' of rendered text with primitive picture url
    	$offset = strpos($rendered, 'a class="primitivepicture"');	
    	$start = strpos($rendered,  'href="', $offset) + 6;
    	$end = strpos($rendered,  '" target="_blank"', $offset);
    	$primitivepic = substr($rendered, $start, $end-$start);
    	return str_replace('javascript:void(0);', $primitivepic, $rendered);
    }
    
	function showStylesheets() {
    	parent::showStylesheets();
    	$this->cssLink('css/lightbox.css','default','screen, projection');
    }
    
    function showShaishaiScripts() {
    	parent::showShaishaiScripts();
    	$this->script('js/lshai_recentnews.js');
    	$this->script('js/lshai_say.js');
    	if (! $this->cur_user) {
    		$this->script('js/lshai_search.js');
    	}
    	$this->script('js/jquery.lightbox-0.5.min.js');
    }
}

?>