<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GameDealHTMLTemplate extends GametwocolumnHTMLTemplate
{
	//声明所需变量
	var $deals;
	var $total;
	var $game;
	var $gamebigzone;
	var $gameserver;
	var $list_games;
	var $list_bigzones;
	var $list_server;
	var $list_dealtags;
	
	function show($args) { //重写show函数
		//展示页面之前先获取所需变量的值
    	$this->deals = $args['deals'];
    	$this->total = $args['total'];
    	$this->game = $args['game'];
    	$this->gamebigzone = $args['gamebigzone'];
    	$this->gameserver = $args['gameserver'];
    	$this->list_games = $args['list_games'];
    	$this->list_bigzones = $args['list_bigzones'];
    	$this->list_servers = $args['list_servers'];
    	$this->list_dealtags = $args['list_dealtags'];
    	//调用父类的show函数
    	parent::show($args);
    }
	
	function title() {
    	return $this->game->name . '交易';
    }
    
	function showContent() {
		
		$this->showHeadTitle();
		
		$this->showSearchBox();
		
		$this->showWarning();
		
		$this->showNav();
		
		$this->showOption();
		
		$this->showResults();
	}
	
	function showHeadTitle() {
		$this->elementStart('h2');
		$this->text('游戏交易');
		$this->element('span', null, '-- 寻找属于您的神兵利器');
		$this->elementEnd('h2');
	}
	
	function showWarning() {
		$this->elementStart('p', array('class' => 'dealnotice'));
		$this->element('strong', array(), 'GamePub声明：');
		$this->text('本版块仅作为玩家之间交易信息的沟通发布之用，GamePub不会对任何交易进行担保。请您留意风险，谨防上当受骗!');
		$this->elementEnd('p');
	}
	
	function showSearchBox() {
		$this->tu->startFormBlock(array('method' => 'get',
                                          'id' => 'dealsearch',
                                          'class' => 'dealsearch',
                                          'action' => common_local_url('gamedeal',array('gameid' => $this->cur_user->game_id))), '搜索商品');
		$this->elementStart('dl');
		$this->element('dt',array(),'搜索您感兴趣的商品');
		$this->elementStart('dd',array('class'=>'game clearfix'));
		
		$this->elementStart('select',array('class' => 'choosegame', 'name' => 'game'));
		foreach ($this->list_games as $g) {	
			if ($g['id'] == $this->game->id) {
        		$this->element('option', array('value' => $g['id'], 'selected' => 'selected'), $g['name']);
			} else {
				$this->element('option', array('value' => $g['id']), $g['name']);
			}
		}
		$this->elementEnd('select');
		
		$this->elementStart('select', array('class' => 'choosebigzone', 'name' => 'game_big_zone'));
		$this->element('option', array('value' => ''), '不限大区');
		foreach ($this->list_bigzones as $gbz) {	
			if ($this->gamebigzone && $gbz['id'] == $this->gamebigzone->id) {
        		$this->element('option', array('value' => $gbz['id'], 'selected' => 'selected'), $gbz['name']);
			} else {
				$this->element('option', array('value' => $gbz['id']), $gbz['name']);
			}
		}
		$this->elementEnd('select');
		
		$this->elementStart('select', array('class' => 'chooseserver', 'name' => 'game_server'));
		$this->element('option', array('value' => ''), '不限服务器');
		foreach ($this->list_servers as $server) {	
			if ($this->gameserver && $server['id'] == $this->gameserver->id) {
        		$this->element('option', array('value' => $server['id'], 'selected' => 'selected'), $server['name']);
			} else {
				$this->element('option', array('value' => $server['id']), $server['name']);
			}
		}
		$this->elementEnd('select');
		
		$this->elementStart('select', array('class' => 'choosecategory', 'name' => 'deal_tag'));
		$this->element('option', array('value' => ''), '全部分类');
		foreach ($this->list_dealtags as $dt) {	
			if ($dt['id'] == $this->trimmed('deal_tag')) {
        		$this->element('option', array('value' => $dt['id'], 'selected' => 'selected'), $dt['name']);
			} else {
				$this->element('option', array('value' => $dt['id']), $dt['name']);
			}
		}
		$this->elementEnd('select');
		
		$this->element('input', 
			array('type' => 'text',
				'class' => 'text', 
				'name' => 'q', 
				'value' => $this->trimmed('q', '')));

		$this->element('input', array('type' => 'submit', 'class' => 'submit button94 orange94',
						   'value' => '开始搜索'));
		$this->elementEnd('dd');
		$this->elementEnd('dl');
		$this->tu->endFormBlock();
	}
	
	function showNav() {
		$deal_tag = $this->trimmed('deal_tag', 0);
		$this->elementStart('div', array('class' => 'dealnav'));
		
		$this->elementStart('ul', array('class' => 'classfix'));
		$this->elementStart('li');
		if ($deal_tag == 0) {
			$this->elementStart('a', array('href' => '#', 'class' => 'active', 'type' => '0'));
		} else  {
			$this->elementStart('a', array('href' => '#', 'type' => '0'));
		}
		$this->element('span',null,'全部商品');	
		$this->element('b');
		$this->elementEnd('a');
		$this->elementEnd('li');
		foreach($this->list_dealtags as $dt)
		{
			$this->elementStart('li');
			if ($deal_tag == $dt['id']) {
				$this->elementStart('a', array('href' => '#', 'class' => 'active', 'type' => $dt['id']));
			} else {
				$this->elementStart('a', array('href' => '#', 'type' => $dt['id']));
			}
			$this->element('span', null, $dt['name']);
			$this->element('b');
			$this->elementEnd('a');
			$this->elementEnd('li');
		}
		$this->elementEnd('ul');
		
		$this->element('a', array('class' => 'myproduct', 
			'href' => common_local_url('gamedeal',array('gameid' => $this->cur_user->game_id), array('show_self' => true, 'game' => $this->game->id))), '我的商品');
		
		$this->element('a', array('class' => 'tosell', 'href' => common_local_url('gamedealnew'), 'uid' => $this->cur_user->id), '我要出售');
		
		$this->elementEnd('div');
	}
	
	function showOption() {
		$ps = $this->trimmed('deal_per_page', 20);
		$ss = $this->trimmed('sortstyle', 'datedesc');
		$st = $this->trimmed('state', 1);
		
		$this->tu->startFormBlock(array('method' => 'get',
                                          'id' => 'dealoption',
                                          'class' => 'dealoption',
                                          'action' => common_local_url('gamedeal', array('gameid' => $this->cur_user->game_id))), '搜索筛选');
		
		$this->elementStart('span',array('class' => 'disp'));
		$this->text('每页显示：');
		if ($ps == 20) {
			$this->element('a',array('href'=>'#', 'class' => 'active', 'pagesize' => '20'), '20');
		} else {
			$this->element('a',array('href'=>'#', 'pagesize' => '20'), '20');
		}
		if ($ps == 40) {
			$this->element('a',array('href'=>'#', 'class' => 'active', 'pagesize' => '40'), '40');
		} else {
			$this->element('a',array('href'=>'#', 'pagesize' => '40'), '40');
		}
		if ($ps == 80) {
			$this->element('a',array('href'=>'#', 'class' => 'active', 'pagesize' => '80'), '80');
		} else {
			$this->element('a',array('href'=>'#', 'pagesize' => '80'), '80');
		}
		$this->element('input', array('type' => 'hidden', 'name' => 'deal_per_page', 'value' => $ps));
		$this->elementEnd('span');
		
		$this->element('label', null, '排序');
		$this->elementStart('select', array('name' => 'sortstyle'));
		if ($ss == 'dateasc') {
			$this->element('option', array('value' => 'dateasc', 'selected' => 'selected'), '时间从先到后');
		} else {
			$this->element('option', array('value' => 'dateasc'), '时间从先到后');
		}
		if ($ss == 'datedesc') {
			$this->element('option', array('value' => 'datedesc', 'selected' => 'selected'), '时间从后到先');
		} else {
			$this->element('option', array('value' => 'datedesc'), '时间从后到先');
		}
		if ($ss == 'pricedesc') {
			$this->element('option', array('value' => 'pricedesc', 'selected' => 'selected'), '价格由高到低');
		} else {
			$this->element('option', array('value' => 'pricedesc'), '价格由高到低');
		}
		if ($ss == 'priceasc') {
			$this->element('option', array('value' => 'priceasc', 'selected' => 'selected'), '价格由低到高');
		} else {
			$this->element('option', array('value' => 'priceasc'),'价格由低到高');
		}
		$this->elementEnd('select');
		
		$this->element('label', null, '状态：');
		$this->elementStart('select', array('class' => 'state', 'name' => 'state'));
		if ($st == 1) {
			$this->element('option', array('value' => '1', 'selected' => 'selected'), '有效');
		} else {
			$this->element('option', array('value' => '1'), '有效');
		}
		if ($st == 0) {
			$this->element('option',array('value' => '0', 'selected' => 'selected'), '已关闭');
		} else {
			$this->element('option', array('value' => '0'), '已关闭');
		}
		$this->elementEnd('select');
		
		$this->elementStart('label');
		$this->text('价格区间：');
		$this->elementEnd('label');
		$this->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'lowprice', 'value' => $this->trimmed('lowprice', '')));
		$this->text('至');
		$this->element('input', array('class' => 'text', 'type' => 'text', 'name' => 'highprice', 'value' => $this->trimmed('highprice', '')));
		
		$this->element('input', array('type' => 'hidden', 'value' => $this->trimmed('show_self', 0), 'name' => 'show_self'));
		$this->element('input', array('type' => 'hidden', 'name' => 'game_server', 'value' => $this->gameserver ? $this->gameserver->id : ''));
		$this->element('input', array('type' => 'hidden', 'name' => 'game_big_zone', 'value' => $this->gamebigzone ? $this->gamebigzone->id : ''));
		$this->element('input', array('type' => 'hidden', 'name' => 'game', 'value' => $this->game->id));
		$this->element('input', array('type' => 'hidden', 'value' => $this->trimmed('deal_tag', 0), 'name' => 'deal_tag'));
		$this->element('input', array('type' => 'hidden', 'value' => $this->trimmed('q', ''), 'name' => 'q'));
		$this->element('input', array('type' => 'hidden', 'value' => $ps, 'name' => 'deal_per_page'));
		$this->element('input', array('type' => 'hidden', 'value' => $this->cur_page, 'name' => 'page'));
		$this->element('input', array('class' => 'submit', 'type' => 'submit', 'value' => '筛选'));
		
		$this->tu->endFormBlock();
	}
	
	function showResults() {
		$ps = $this->trimmed('deal_per_page', 20);
		
		$this->elementStart('dl', array('class' => 'dealresults'));
		$this->elementStart('dt');
		$this->element('span', array('class' => 'info'), '物品信息');
		$this->element('span', array('class' => 'price'), '价格');
		$this->element('span', array('class' => 'pic'), '图片(点击放大)');
		$this->element('span', array('class' => 'status'), '状态');
		$this->elementEnd('dt');
		if($this->deals->N > 0) {
			$dl = new DealList($this->deals, $this, $ps);
       		$cnt = $dl->show();
       		
       		$this->numpagination($this->total, $ps);
		}
		$this->elementEnd('dl');
		
		if ($this->deals->N == 0) {
			$this->showEmptyList();
		}
	
	}
	
	function showEmptyList()
    {
		$this->tu->showEmptyListBlock('您所查询的商品结果为空， 还没有任何玩家发表过此类出售信息。');
    }
    
 	function numpagination($totalnum, $displayPerPage = NOTICES_PER_PAGE)
    {
    	$have_before = $this->cur_page > 1;
    	$have_after = ($this->cur_page * $displayPerPage) < $totalnum;
    	
    	if($have_before || $have_after) {
	    	$this->elementStart('ol', array('id' => 'pagination'));
	    	
	    	$pages = floor(($totalnum - 1 + $displayPerPage) / $displayPerPage);
	    	
	    	$start = (floor(($this->cur_page-1)/10))*10 + 1;
	    	if ($start > 10) {
	    		$this->elementStart('li');
	    			$this->element('a', array('href' => '#','page' => $start - 10, 
	    				'title' => '前十页', 'rel' => 'nofollow'), '上');
	    		$this->elementEnd('li');
	    	}
	    	
	    	$pp = $start;
	    	do {
	    		if($pp != $this->cur_page){
		    		$this->elementStart('li');	
		    			$this->element('a', array('href' =>'#','page' => $pp, 'rel' => 'nofollow'), $pp);
		    		$this->elementEnd('li');
	    		} else {
	    			$this->elementStart('li', 'active');
	    			$this->element('span', null, $pp);
	    			$this->elementEnd('li');
	    		}
	    		$pp++;
	    	} while ($pp <= $pages && $pp < $start + 10);
	    	
	    	// when page num is more than current displayed, show more...
	    	if ($pages > $start + 9) {
	    		$this->elementStart('li');
	    			$this->element('a', array('title' => '后十页',
		    			'href' => '#','page' => $start + 10, 'rel' => 'nofollow'), '下');
	    		$this->elementEnd('li');
	    	}
	    	
	    	$this->elementEnd('ol');
    	}
    }
	
	function showStylesheets() {
    	parent::showStylesheets();
    	$this->cssLink('css/lightbox.css','default','screen, projection');
    }

    
	function showScripts() {
		parent::showScripts();
		$this->script('js/jquery.lightbox-0.5.min.js');
		$this->script('js/lshai_gamedeal.js');
		$this->script('js/lshai_gameselect.js');
	}
}
?>