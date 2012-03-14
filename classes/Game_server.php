<?php
/**
 * Table Definition for game_server
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Game_server extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'game_server';                     // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  multiple_key
    public $game_big_zone_id;                // int(11)  not_null
	public $notice_num;                      // int(11)
	public $gamers_num;
	
    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Game_server',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function listAllServerIds() {
    	return common_stream('gameserver:allserverids', array("Game_server", "_listAllServerIds"), null, 24 * 3600);
    }
    
    function _listAllServerIds() {
    	$server = new Game_server();
    	$server->find();
    	
    	$servers = array();
    	while ($server->fetch()) {
    		$servers[] = $server->id;
    	}
    	$server->free();
    	return $servers;
    }
    
    function getActiveTop50($server_id) {
    	return common_stream('gameserver:activetop50:' . $server_id, array("Game_server", "_getActiveTop50"), array($server_id), 24 * 3600);
    }
    
    function _getActiveTop50($server_id) {
    	$queryString = "SELECT profile.id as id FROM profile, grade_record WHERE profile.id = grade_record.user_id";
    	$queryString .= " AND profile.game_server_id = " . $server_id . " AND profile.is_banned = 0";
    	$queryString .= " ORDER BY grade_record.changed DESC, profile.is_vip DESC";
    	$queryString .= " LIMIT 0,50";
    	
    	$user = new Profile();
    	$user->query($queryString);
    	
    	$pops = array();
    	while ($user->fetch()) {
    		$pops[] = $user->id;
    	}
    	$user->free();
    	
    	return $pops;
    }
    
	function getPopularTop($server_id, $offset = 0, $limit = 50, $sex = '') {
    	return common_stream('gameserver:populartop:' . $server_id . ':' . $offset . ':' . $limit, array("Game_server", "_getPopularTop"), array($server_id, $offset, $limit, $sex), 24 * 3600);
    }
    
    function _getPopularTop($server_id, $offset = 0, $limit = 50, $sex = '') {
    	$queryString = "SELECT user.id FROM user, profile WHERE user.id = profile.id";
    	$queryString .= " AND user.game_server_id = " . $server_id . " AND profile.is_banned = 0";
    	if ($sex == 'M' || $sex == 'F') {
    		$queryString .= " AND profile.sex = '" . $sex . "'";
    	}
    	$queryString .= " ORDER BY profile.followers DESC";
    	$queryString .= " LIMIT " . $offset . ", " . $limit;
    	
    	$user = new User();
    	$user->query($queryString);
    	
    	$pops = array();
    	while ($user->fetch()) {
    		$pops[] = $user->id;
    	}
    	$user->free();
    	
    	return $pops;
    }
    
    function getPopularTop50($server_id, $sex = '') {
    	return Game_server::getPopularTop($server_id, 0, 50, $sex);
    }
    
	function getRandom100($server_id, $sex = '', $province = null, $city = null) {
    	
    	
    	$memcachedKey = 'gameserver:random100:' . $server_id;
    	if ($sex != '') {
    		$memcachedKey .= ':' . $sex;
    	}
    	if ($province != null
    		&& $city != null) {
    		$memcachedKey .= ':' . hash(HASH_ALGO, $province . '-' . $city);
    	}
    	
    	return common_stream($memcachedKey,
    		array("Game_server", "_getRandom100"), array($server_id, $sex, $province, $city), 24 * 3600);
    }
    
	function _getRandom100($server_id, $sex = '' , $province = null, $city = null) {
    	$queryString = "SELECT profile.id FROM profile";
    	$queryString .= " WHERE profile.game_server_id = " . $server_id . " AND profile.is_banned = 0";
		
    	if ($sex == 'M' || $sex == 'F') {
    		$queryString .= " AND profile.sex = '" . $sex . "'";
    	}
		if($province  && $city )
		{	
			if($province != '北京' && $province != '天津' && $province != '上海' && $province != '重庆')
				$queryString .= " and city = '". $city ."'";
			$queryString .= " and province = '". $province ."'";	
		}
		$queryString .= " order by followers desc limit 0,100";
    	$profile = new Profile();
    	$profile->query($queryString);
    	//common_debug($queryString);
    	$pops = array();
    	while ($profile->fetch()) {
    		$pops[] = $profile->id;
    	}
    	$profile->free();
    	
    	return $pops;
    }
    
	function getVideoTop20($server_id) {
    	return common_stream('gameserver:videotop20:' . $server_id, array("Game_server", "_getVideoTop20"), array($server_id), 24 * 3600);
    }
    
	function _getVideoTop20($game_server_id)
    {
 		$query = "SELECT user_id,count(*)AS num FROM notice,user WHERE notice.user_id=user.id AND content_type=3 AND topic_type != 4 AND notice.is_banned = 0";
 		if($game_server_id)
 			$query .= "AND user.game_id='".$game_server_id."'";
 		$query .= "GROUP BY user_id ORDER BY num DESC LIMIT 0,20";
 		
 		$notice = new Notice();
    	$notice->query($query);
    	
    	$pops = array();
    	while ($notice->fetch()) {
    		$pops[] = $notice->user_id;
    	}
    	$notice->free();
    	
    	$pops = common_random_fetch($pops,100);
    	
    	return $pops;
    }
    
	function getPicTop20($server_id) {
    	return common_stream('gameserver:pictop20:' . $server_id, array("Game_server", "_getPicTop20"), array($server_id), 24 * 3600);
    }
    
	function _getPicTop20($game_server_id)
    {
 		$query = "SELECT user_id,count(*)AS num FROM notice,user WHERE notice.user_id=user.id AND content_type=4 AND topic_type != 4 AND notice.is_banned = 0";
 		if($game_server_id)
 			$query .= "AND user.game_id='".$game_server_id."'";
 		$query .= "GROUP BY user_id ORDER BY num DESC LIMIT 0,20";
		
 		$notice = new Notice();
    	$notice->query($query);
    	
    	$pops = array();
    	while ($notice->fetch()) {
    		$pops[] = $notice->user_id;
    	}
    	$notice->free();
    	
    	$pops = common_random_fetch($pops,100);
    	
    	return $pops;
    }
    
	function getTextTop20($server_id) {
    	return common_stream('gameserver:texttop50:' . $server_id, array("Game_server", "_getTextTop20"), array($server_id), 24 * 3600);
    }
    
	function _getTextTop20($game_server_id)
    {
 		$query = "SELECT user_id,count(*)AS num FROM notice,user WHERE notice.user_id=user.id AND content_type=1 AND topic_type != 4 AND notice.is_banned = 0";
 		if($game_server_id)
 			$query .= "AND user.game_id='".$game_server_id."'";
 		$query .= "GROUP BY user_id ORDER BY num DESC LIMIT 0,20";
 		$notice = new Notice();
    	$notice->query($query);
    	
    	$pops = array();
    	while ($notice->fetch()) {
    		$pops[] = $notice->user_id;
    	}
    	$notice->free();
    	
    	$pops = common_random_fetch($pops,100);
    	
    	return $pops;
    }
    
   	function gameServerUserCount() {
    	$c = common_memcache();

        if (!empty($c)) {
            $cnt = $c->get(common_cache_key('user:game_server_count:'.$this->id));
            if (is_integer($cnt)) {
                return (int) $cnt;
            }
        }

        $user = new User();
        $user->game_server_id = $this->id;
        $cnt = (int) $user->count('distinct id');

        if (!empty($c)) {
            $c->set(common_cache_key('user:game_server_count:'.$this->id), $cnt);
        }

        return $cnt;
    }
    
    function blowGameServerUserCount() {
    	$c = common_memcache();
        if (!empty($c)) {
            $c->delete(common_cache_key('user:game_server_count:'.$this->id));
        }
    }
    
    function getGameBigZone() {
    	return Game_big_zone::staticGet('id', $this->game_big_zone_id);
    }
    
    static function getGameBigZoneId($gameserver_id)
    {
    	//注：服务器id格式为 xxxyyzzz, 从左至右分别为三位游戏码, 2位大区码, 3位服务器码
    	//也可查数据库得
    	return (int)($gameserver_id / 1000);
    } 
}
