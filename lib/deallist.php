<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class DealList extends Widget
{
    /** the current stream of notices being displayed. */

    var $deals = null;
	var $ps = null;
    /**
     * constructor
     *
     * @param Notice $notice stream of notices from DB_DataObject
     */

    function __construct($deals, $out=null, $ps)
    {
        parent::__construct($out);
        $this->deals = $deals;
        $this->ps = $ps;
    }

    /**
     * show the list of notices
     *
     * "Uses up" the stream by looping through it. So, probably can't
     * be called twice on the same list.
     *
     * @return int count of notices listed.
     */

    function show()
    {
    	$this->out->elementStart('dd');
    	$this->out->elementStart('ol', array('id' => 'deals'));
        $cnt = 0;
		
        while ($this->deals != null 
        	&& $this->deals->fetch()) {
            $cnt++;
            
            if ($cnt > $this->ps) {
                break;
            }
                        
            $item = $this->newListItem($this->deals);
            $item->show();
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        $this->out->elementEnd('dd');
        
        return $cnt;
    }

    /**
     * returns a new list item for the current notice
     *
     * Recipe (factory?) method; overridden by sub-classes to give
     * a different list item class.
     *
     * @param Notice $notice the current notice
     *
     * @return NoticeListItem a list item for displaying the notice
     */

    function newListItem($deal)
    {
        return new dealsListItem($deal, $this->out);
    }
}

/**
 * widget for displaying a single notice
 *
 * This widget has the core smarts for showing a single notice: what to display,
 * where, and under which circumstances. Its key method is show(); this is a recipe
 * that calls all the other show*() methods to build up a single notice. The
 * ProfileNoticeListItem subclass, for example, overrides showAuthor() to skip
 * author info (since that's implicit by the data in the page).
 *
 * @category UI
 * @package  LShai
 * @see      NoticeList
 * @see      ProfileNoticeListItem
 */

class dealsListItem extends Widget
{
    /** The notice this item will show. */

    var $deal = null;

    var $deal_tag = null;
    
    var $game = null;
    
    var $game_server = null;
    
    var $game_big_zone = null;
    
    var $profile = null;
    
    var $notice_award = null;
    
    var $profileUser = null;
    
    var $user = null;
    
    var $deal_image_url = null;
    
   
    
    /**
     * constructor
     *
     * Also initializes the profile attribute.
     *
     * @param Notice $notice The notice we'll display
     */

    function __construct($deal, $out=null)
    {
        parent::__construct($out);
        $this->deal  = $deal;
        $this->deal_tag = Deal_tag::staticGet('id',$this->deal->deal_tag);
        $this->profile = $deal->getProfile();
  		$this->game = Game::staticGet('id',$this->deal->game_id);
  		$this->game_big_zone = Game_big_zone::staticGet('id',$this->deal->game_big_zone_id);
  		$this->game_server = Game_server::staticGet('id',$this->deal->game_server_id);
        $this->deal_image_url = Deal_images::getImagesByDealId($this->deal->id);
        $this->user = common_current_user();
    }

    /**
     * recipe function for displaying a single notice.
     *
     * This uses all the other methods to correctly display a notice. Override
     * it or one of the others to fine-tune the output.
     *
     * @return void
     */

    function show()
    {
    	$this->out->elementStart('li',array('class'=>'clearfix', 'did' => $this->deal->id));
    	
    	$this->out->elementStart('div',array('class'=>'info'));
    	$this->out->element('a',array('class'=>'title','href'=>'#'),$this->deal->description);
    	$this->out->element('p',null,'商品类型：'.$this->deal_tag->name);
    	$this->out->element('p',null,$this->game->name.'/'.$this->game_big_zone->name.'/'.$this->game_server->name);
    	$this->out->elementStart('p');
    	$this->out->text($this->deal->deal_type ? '买家:' : '卖家:');
    	$this->out->element('a',array('href'=>$this->profile->profileurl),$this->profile->nickname);
    	$user = common_current_user();
    	if ($user && $user->id != $this->profile->id) {
    		$this->out->element('a',array('class'=>'totalk','href'=>common_path('message/new'),'nickname'=>$this->profile->nickname,'to'=>$this->profile->id),'(给他发信)');
    	}
    	$this->out->elementEnd('p');
    	$this->out->elementEnd('div');
    	
    	$this->out->element('div',array('class'=>'price'),$this->deal->price);
    	
    	$this->out->elementStart('div',array('class' => 'pic'));
    	if($this->deal_image_url)
    	{	$this->out->elementStart('a',array('href' => $this->deal_image_url));
    		$this->out->element('img',array('src' => $this->deal_image_url,'class'=>'lightbox'));
    		$this->out->elementEnd('a');
    	}
    	else $this->out->text('暂无图片');
    	$this->out->elementEnd('div');
    	
    	$this->out->elementStart('div',array('class'=>'status'));
    	$this->out->text($this->deal->state?'有效':'已关闭');
    	if($this->deal->state && $this->deal->user_id == $this->user->id) {
    		$this->out->element('a',array('href' => '#','class' => 'close_deal', 'deal_id'=>$this->deal->id),'关闭该交易');
    	}
    	$this->out->elementEnd('div');
    	$this->out->elementEnd('li');
  
    }
    
  
}

