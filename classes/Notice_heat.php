<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for notice_heat
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Notice_heat extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'notice_heat';      	// table name
    public $notice_id;                       	// int(4)  primary_key not_null
    public $day;									// date not null
    public $heat; 								// int(4)
	
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Notice_heat',$k,$v); }

    
    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function &pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('Notice_heat', $kv);
    }

    
    /*
     	这里只是记录某段时间上升的热度, 在notice表里面保存的是总热度
     	有些是一个小时, 有些是一天, 怎么划分比较, 这里是每天的热度
     	
     	当前查询每天的方法, 只比较字符串前面的几位, 比如2009-12-10-23, 
    	正则表达式day REGEXP '2009-12-10', 可以查找到2009-12-10当天的所有记录, 非常好用
    	
     	为了以后可拓展性, 在保存时间是可以拓展到小时, 比如strftime('%Y-%m-%d-%H', time()), 
    
    	每6个小时更新一次? 00 06 12 18
    */
    function addHeat($notice_id, $heat) {
    	$cur = time();
    	$day =  strftime('%Y-%m-%d', $cur);
    	$hour = strftime('%H', $cur);
    	$int_hour = intval($hour);
    	if($int_hour >= 0 && $int_hour < 6)
    		$day .= ' 00';
    	else if($int_hour >= 6 && $int_hour < 12)
    		$day .= ' 06';
    	else if($int_hour >= 12 && $int_hour < 18)
    		$day .= ' 12';
    	else if($int_hour >= 18 && $int_hour < 24)
    		$day .= ' 18';
    	
    	//$day = '\'' . date('Y-m-d H:i:s', $day) . '\'';
    	$noticeheat = Notice_heat::pkeyGet(array('notice_id' => $notice_id,
                                    'day' => $day));
    	
    	if(empty($noticeheat)) {
    		$noticeheat = new Notice_heat();
			$noticeheat->notice_id = $notice_id;
			$noticeheat->day = $day;
			$noticeheat->heat = $heat;
			
	    	if (!$noticeheat->insert()) {
	            common_log_db_error($noticeheat, 'INSERT', __FILE__);
	            return false;
      		}	
    	} else {
    	    $origHeat = clone($noticeheat);
    		$noticeheat->heat += $heat;
    		$noticeheat->update($origHeat);  		
    	}
    }
   
    //前六个小时的排行榜
   function heatOrderStream($limit=NOTICES_PER_PAGE, $content_type=0)
    {
    	$cur = time();
		$day =  strftime('%Y-%m-%d', $cur);

    	$hour = strftime('%H', $cur);
    	$int_hour = intval($hour);    	
   		$today = strtotime($day);	
   		
   		$yesterday = $today - 3600*24;
   		
    	if($int_hour >= 0 && $int_hour < 6) {
    		$day = date('Y:m:d', time());
    		$day .= ':18';
    		$before = $today-3600*6;
    		$after = $today;
    	} else if($int_hour >= 6 && $int_hour < 12) {
    		$day = date('Y:m:d', time());
    		$day .= ':00';
    		$before = $today;
    		$after = $today+3600*6;
    	} else if($int_hour >= 12 && $int_hour < 18) {
    		$day = date('Y:m:d', time());
    		$day .= ':06';
    		$before = $today+3600*6;
    		$after = $today+3600*12;
    	} else if($int_hour >= 18 && $int_hour < 24) {
    		$day = date('Y:m:d', time());
    		$day .= ':12';
    		$before = $today+3600*12;
    		$after = $today+3600*18;
    	}
    	
    	if($content_type == 0)
        	$ids = Notice::stream(array('Notice_heat', '_heatOrderStreamDirect'),
                              array($yesterday, $today),
                              'noticeheat:heatorder:'.date('Y:m:d', $yesterday),
                              0, $limit, 0, 0, null);
        else 
        	$ids = Notice::stream(array('Notice_heat', '_heatOrderStreamDirect'),
                              array($yesterday, $today),
                              'noticeheat:heatorder:'.date('Y:m:d', $yesterday).':'.$content_type,
                              0, $limit, 0, 0, null, $content_type);
                              
//    	if($content_type == 0)
//        	$ids = Notice::stream(array('Notice_heat', '_heatOrderStreamDirect'),
//                              array($before, $after),
//                              'noticeheat:heatorder:'.$day,
//                              0, $limit, 0, 0, null);
//        else 
//        	$ids = Notice::stream(array('Notice_heat', '_heatOrderStreamDirect'),
//                              array($before, $after),
//                              'noticeheat:heatorder:'.$day.':'.$content_type,
//                              0, $limit, 0, 0, null, $content_type);
  // 修改 by armosee 主要是解决 热门消息页面为空 的问题，当ids为空时，扩大时间范围。
//        if(count($ids) == 0) 
//        {
//       		$day =  strftime('%Y-%m-%d', $cur);
//        	$today = strtotime($day)-3600*24;
//        	$ids = Notice_heat::_heatOrderStreamDirect($today, $after, 0, $limit, 0, 0, null, $content_type);
//        }
        return Notice::getStreamByIdsdirect($ids);
    }
    
    function _heatOrderStreamDirect($before, $after, $offset=0, $limit=20, $since_id=0, 
    			$max_id=0, $since=null, $content_type=0, $area_type=0, $topic_type=0)
    {
    	$noticeheat = new Notice_heat();
		$noticeheat->selectAdd(); // clears it
        $noticeheat->selectAdd('notice_id');

        // comment by bencao 整个算法需要彻底改进，热门度结合时间。这一块类似搜索结果的排序，可以跟云平咨询
//		$noticeheat->whereAdd('day >= \'' . date('Y-m-d H:i:s', $before) . '\'');
//		$noticeheat->whereAdd('day < \'' . date('Y-m-d H:i:s', $after) . '\'');
    	
		if ($since_id != 0) {
            $noticeheat->whereAdd('notice_id > ' . $since_id);
        }

        if ($max_id != 0) {
            $noticeheat->whereAdd('notice_id <= ' . $max_id );
        }
        
		if($content_type  == 0)
			$noticeheat->whereAdd('EXISTS (SELECT * from notice where id = notice_id ' . 
        			'and topic_type != 4 and is_banned=0)');
		else
			$noticeheat->whereAdd('EXISTS (SELECT * from notice where id = notice_id ' . 
        			'and content_type = '. $content_type .' and topic_type != 4 and is_banned = 0)');
        
        $noticeheat->limit(0, $limit);
        $noticeheat->orderBy('heat DESC');
        $ids = array();
        if ($noticeheat->find()) {
        	while ($noticeheat->fetch()) {
        		$ids[] = $noticeheat->notice_id;
        	}
        }
        $noticeheat->free();
        return $ids;
    }
    
    //去掉前6个小时的缓存
    function blowNoticeHeatCache($blowLast=false)
    {    	
    	$notice = Notice::staticGet("id", $this->notice_id);
    	$today = strtotime($this->day);		
		//前一天
		$yesterday = $today - 3600*24;
		$cachestr = date('Y:m:d', $yesterday);
    	
        $cache = common_memcache();
        if (!empty($cache)) {
            $cache->delete(common_cache_key('noticeheat:heatorder:'.$cachestr));
            if ($blowLast) {
                $cache->delete(common_cache_key('noticeheat:heatorder:'.$cachestr.';last'));
            }
        }
    }
    
//    SELECT user_id, sum( notice_heat.heat ) AS heatsum
//FROM notice, notice_heat
//WHERE notice.id = notice_heat.notice_id AND day=
//GROUP BY user_id
//ORDER BY heatsum
//LIMIT 0 , 30
//需要使用的时候可以加上缓存, 好像现在没有调用?
//有错误
	function getHotUserId($limit=15) {
		
		$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);		
		//前一天
		$yesterday = $today - 3600*24;
		$cachestr = date('Y-m-d', $yesterday);
		
		$noticeheat = new Notice_heat();
		$query = "SELECT user_id,sum(notice_heat.heat)AS heatsum FROM notice,notice_heat WHERE notice.id=notice_heat.notice_id GROUP BY user_id ORDER BY heatsum DESC LIMIT 0,".$limit;   // AND day=".$cachestr."
		//common_debug($query);
		$noticeheat->query($query);
		$hotusers = array();
		//$notice_select= array();
		while ($noticeheat->fetch()) {
			//$notice_select[]->id=$notice->id;
			$hotusers[] = array('user_id'=>$noticeheat->user_id,'num'=>$noticeheat->heatsum);
		}
		//common_debug($hotusers);
		$noticeheat->free();
		return $hotusers;
		
	}
	
	function getHeatSum($user_id){
		
		$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);		
		//一周
		$weekago = $today - 3600*24*7;
		$cachestr = date('Y-m-d', $weekago);
		
		$noticeheat = new Notice_heat();
		$query = "SELECT sum(notice_heat.heat)AS heatsum FROM notice,notice_heat WHERE notice.id=notice_heat.notice_id and user_id=".$user_id." AND day > "."\"".$cachestr."\"";
		//common_debug($query);
		$noticeheat->query($query);
		
		while ($noticeheat->fetch()) {
			//$notice_select[]->id=$notice->id;
			$weekheatsum=$noticeheat->heatsum;
		}
		//common_debug($hotusers);
		$noticeheat->free();
		return $weekheatsum;
		
	}
    
    //把前几天的缓存清理
}