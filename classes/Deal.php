<?php
/**
 * Table Definition for deal
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Deal extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'deal';                            // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $user_id;                         // int(11)  not_null
    public $game_id;                         // int(11)  not_null
    public $game_big_zone_id;                // int(11)  not_null
    public $game_server_id;                  // int(11)  not_null
    public $deal_tag;                        // int(11)  not_null
    public $price;                           // int(11)  not_null
    public $deal_type;                       // int(4)  not_null
    public $description;                     // string(280)  
    public $state;                           // int(4)  not_null
    public $expire_time;                     // datetime  not_null
    public $created;                         // datetime(19)  not_null binary
    public $modified;                        // timestamp(19)  not_null unsigned zerofill binary timestamp

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Deal',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
 	function saveNew($user_id, $game_id, $game_big_zone_id, $game_server_id, $deal_tag, $options=array())
    {
    	$default = array('deal_type'=>1,'state'=>1,'expire_time'=>30);
    	if(!empty($options))
    	{
    		$options = $options + $default;
    		extract($options);
    	} else {
    		extract($default);
    	}
    	
    	$deal = new Deal();
    	$deal->user_id = $user_id;
    	$deal->game_id = $game_id;
    	$deal->game_big_zone_id = $game_big_zone_id;
    	$deal->game_server_id = $game_server_id;
    	$deal->deal_tag = $deal_tag;
    	$deal->price = $price;
    	$deal->deal_type = $deal_type;
    	$deal->description = $description;
    	$deal->state = $state;
    	$deal->expire_time = common_sql_date(time() + $expire_time*24*3600);
    	
    	if (!empty($created)) {
			$deal->created = $created;
		} else {
			$deal->created = common_sql_now();
		}
    		
		$deal->insert();
			
		return $deal->id;
    }
    
    function getDealImages()
    {
    	return deal_images::getImagesBydealId($this->id);
    }
    
    function getDealsNum($game, $game_big_zone, $game_server, $deal_tag = 0, $keyword = '', $options=array())
    {
    	$default = array('lowprice'=>null,'highprice'=>null,'state'=>1);
    	if(!empty($options))
    	{
    		$options = $options + $default;
    		extract($options);
    	} else {
    		extract($default);
    	}
    	
    	$deal = new Deal();
    	$deal->selectAdd('count(*) as num');
    	$deal->whereAdd('game_id='.$game);
    	$deal->whereAdd('game_big_zone_id='.$game_big_zone);
    	$deal->whereAdd('game_server_id='.$game_server);
    	$deal->whereAdd('state='.$state);
    	if($deal_tag != 0) $deal->whereAdd('deal_tag='.$deal_tag);
    	if($lowprice || $highprice) $deal->whereAdd('price between '.$lowprice.' and '.$highprice);
    	if($keyword != '') $deal->whereAdd('description like "%'.$keyword.'%"');
    	return $deal->count();
    } 
    
     function getDealsNumofUser($user_id,$lowprice,$highprice,$state)
    {
    	$deal = new Deal();
    	$deal->selectAdd('count(*) as num');
    	$deal->whereAdd('user_id='.$user_id);
    	$deal->whereAdd('state='.$state);
    	if($lowprice && $highprice) $deal->whereAdd('price between '.$lowprice.' and '.$highprice);
    	return $deal->count();
    } 
    
    
    function getDeals($game, $game_big_zone, $game_server, $deal_tag = 0, $keyword = '', $options=array())
    {
    	$default = array('sortstyle' => 'dateasc','lowprice'=>null,'highprice'=>null,'offset'=>0,'limit'=>20, 'state'=>1);
    	if(!empty($options))
    	{
    		$options = $options + $default;
    		extract($options);
    	} else {
    		extract($default);
    	}
    	
    	$deal = new Deal();
    	$deal->whereAdd('game_id='.$game);
    	if ($game_big_zone) {
    		$deal->whereAdd('game_big_zone_id='.$game_big_zone);
    	}
    	if ($game_server) {
    		$deal->whereAdd('game_server_id='.$game_server);
    	}
    	$deal->whereAdd('state='.$state);
    	if($deal_tag != 0) $deal->whereAdd('deal_tag='.$deal_tag);
    	if($lowprice || $highprice) $deal->whereAdd('price between '.$lowprice.' and '.$highprice);
    	if($keyword != '') $deal->whereAdd('description like "%'.$keyword.'%"');
    	switch($sortstyle)
    	{
    		case 'datedesc':
    			$deal->orderBy('id desc');
    			break;
    		case 'priceasc':
    			$deal->orderBy('price asc');
    			break;
    		case 'pricedesc':
    			$deal->orderBy('price desc');
    			break;
    		case 'dateasc':
    		default:
    			$deal->orderBy('id ASC');
    			break;
    	}
    	$deal->limit($offset,$limit);
    	$deal->find();
    	
    	$wrapped = array();
    	while($deal->fetch())
    	{
   // 		$temp[$deal->id] = clone($deal);
    		$wrapped[] = clone($deal);
    	}
    	$deal->free();

    	return new ArrayWrapper($wrapped); //arrayWrapper 封装了->N 和fetch()方法。
    	
    } 
    
     function getDealsbyUser($user_id,$lowprice,$highprice,$sortstyle,$offset,$limit,$state)
    {	
    	$deal = new Deal();
    	$deal->whereAdd('user_id='.$user_id);
    	$deal->whereAdd('state='.$state);
    	if($lowprice && $highprice) $deal->whereAdd('price between '.$lowprice.' and '.$highprice);
    	switch($sortstyle)
    	{
    		case 'datedesc':
    			$deal->orderBy('id desc');
    			break;
    		case 'priceasc':
    			$deal->orderBy('price asc');
    			break;
    		case 'pricedesc':
    			$deal->orderBy('price desc');
    			break;
    		case 'dateasc':
    		default:
    			$deal->orderBy('id ASC');
    			break;
    	}
    	
    	$deal->limit($offset,$limit);
    	$deal->find();
    	
    	$wrapped = array();
    	while($deal->fetch())
    	{
    		$wrapped[] = clone($deal);
    	}
    	$deal->free();

    	return new ArrayWrapper($wrapped); //arrayWrapper 封装了->N 和fetch()方法。
    	
    } 
    
    function close($id)
    {
    	$deal = Deal::staticGet('id',$id);
    	$tmp = clone($deal);
    	$deal->state = 0;
    	$deal->update($tmp);
    	
    }
    
	function getProfile()
	{
		return Profile::staticGet('id', $this->user_id);
	}
	
	function closeExpireDeals()
	{
		$deal = new Deal();
		$deal->whereAdd("state = 1");
		$deal->whereAdd("expire_time < '".common_sql_date(time())."'");
		$deal->find();
		
		while ($deal->fetch())
		{
			$tmp = clone($deal);
			$deal->state = 0;
			$deal->update($tmp);
		}
	}
	
}
