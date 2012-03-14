<?php
/**
 * Table Definition for game
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Game extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'game';                            // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $name;                            // string(255)  multiple_key
    public $design_id;                       // int(11)  
    public $game_job_name;                   // string(255)  
    public $game_group_name;                 // string(32)  
    public $gamers_num;                      // int(11)  
    public $hot;
    public $category;
    public $notice_num;                      // int(11)
    public $game_introduction;				//string(255)



    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Game',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function listAll() {
    	return common_stream('game:listall', array("Game", "_listAll"), null, 3600 * 24);
    }
    
    function _listAll() {
    	$game = new Game();
    	$game->orderBy('id');
    	$game->find();
    	
    	$games = array();
    	
    	while ($game->fetch()) {
    		$games[] = array('id' => $game->id, 'name' => $game->name);
    	}
    	
    	$game->free();
    	
    	return $games;
    }
    
	function listHots() {
    	return common_stream('game:listhots', array("Game", "_listHots"), null, 3600 * 24);
    }
    
    function _listHots() {
    	$game = new Game();
    	$game->whereAdd("hot = 1");
    	$game->orderBy('gamers_num DESC');
    	$game->find();
    	
    	$games = array();
    	
    	while ($game->fetch()) {
    		$games[] = array('id' => $game->id, 'name' => $game->name, 'hot' => $game->hot);
    	}
    	
    	$game->free();
    	
    	return $games;
    }
    
    function listByCategory($categoryName) {
    	return common_stream('game:listbycategory:' . $categoryName, array("Game", "_listByCategory"), array($categoryName), 3600 * 24);
    }
    
    function _listByCategory($categoryName) {
    	$game = new Game();
    	$game->whereAdd("category = '" . $categoryName . "'");
    	$game->orderBy('CONVERT(name USING GBK) ASC');
    	$game->find();
    	
    	$games = array();
    	
    	while ($game->fetch()) {
    		$games[] = array('id' => $game->id, 'name' => $game->name, 'hot' => $game->hot);
    	}
    	
    	$game->free();
    	
    	return $games;
    }
    
    function listAllGameIds() {
    	return common_stream('game:listallgameids', array("Game", "_listAllGameIds"), null, 3600 * 24);
    }
    
    function _listAllGameIds() {
    	$game = new Game();
    	$game->find();
    	
    	$games = array();
    	
    	while ($game->fetch()) {
    		$games[] = $game->id;
    	}
    	
    	$game->free();
    	
    	return $games;
    }
    
    function getBigZones() {
    	return common_stream('game:getbigzones:' . $this->id, array($this, "_getBigZones"), null, 3600 * 24);
    }
    
    function _getBigZones() {
    	$gbz = new Game_big_zone();
    	$gbz->whereAdd('game_id = ' . $this->id);
//    	$gbz->orderBy('id');
		$gbz->orderBy('CONVERT(name USING GBK) ASC');
    	$gbz->find();
    	
    	$gbzs = array();
    	
    	while ($gbz->fetch()) {
    		$gbzs[] = array('id' => $gbz->id, 'name' => $gbz->name);
    	}
    	$gbz->free();
    	
    	return $gbzs;
    }
    
	function getBigZoneIds() {
    	return common_stream('game:getbigzoneids:' . $this->id, array($this, "_getBigZoneIds"), null, 3600 * 24);
    }
    
	function _getBigZoneIds() {
    	$gbz = new Game_big_zone();
    	$gbz->whereAdd('game_id = ' . $this->id);
//    	$gbz->orderBy('id');
		$gbz->orderBy('CONVERT(name USING GBK) ASC');
    	$gbz->find();
    	
    	$gbzs = array();
    	
    	while ($gbz->fetch()) {
    		$gbzs[] = $gbz->id;
    	}
    	$gbz->free();
    	
    	return $gbzs;
    }
    
    function getServers($big_zone_id) {
    	return common_stream('game:getservers:' . $big_zone_id, array("Game", "_getServers"), array($big_zone_id), 3600 * 24);
    }
    
    function _getServers($big_zone_id) {
    	$gs = new Game_server();
    	$gs->whereAdd('game_big_zone_id = ' . $big_zone_id);
    	$gs->orderBy('CONVERT(name USING GBK) ASC');
    	$gs->find();
    	
    	$gss = array();
    	while ($gs->fetch()) {
    		$gss[] = array('id' => $gs->id, 'name' => $gs->name);
    	}
    	$gs->free();
    	
    	return $gss;
    }
    
	function getServerIds($big_zone_id) {
    	return common_stream('game:getserverids:' . $big_zone_id, array("Game", "_getServerIds"), array($big_zone_id), 3600 * 24);
    }
    
	function _getServerIds($big_zone_id) {
    	$gs = new Game_server();
    	$gs->whereAdd('game_big_zone_id = ' . $big_zone_id);
    	$gs->orderBy('CONVERT(name USING GBK) ASC');
    	$gs->find();
    	
    	$gss = array();
    	while ($gs->fetch()) {
    		$gss[] = $gs->id;
    	}
    	$gs->free();
    	
    	return $gss;
    }
    
    function getJobs() {
    	return common_stream('game:jobs:' . $this->id, array($this, "_getJobs"), null, 3600 * 24 * 7);
    }
    
    function _getJobs() {
    	$st = new Second_tag();
    	$st->whereAdd('first_tag_id = ' . $this->id . '2');
    	$st->whereAdd('game_id = ' . $this->id);
    	$st->find();
    	
    	$arr = array();
    	while ($st->fetch()) {
    		$arr[] = $st->name;
    	}
    	
    	return $arr;
    }
    
    function isValidServer($game_id, $game_zone_id, $game_server_id) {
    	return common_stream('game:isvalidserver:' . $game_id . ':' . $game_zone_id . ':' . $game_server_id, array("Game", "_isValidServer"), array($game_id, $game_zone_id, $game_server_id), 3600 * 24);
    }
    
    function _isValidServer($game_id, $game_zone_id, $game_server_id) {
    	$game = Game::staticGet('id', $game_id);
    	
    	if (empty($game)) {
    		return false;
    	}
    	
    	if (! in_array($game_zone_id, $game->getBigZoneIds())) {
    		return false;
    	}
    	
    	if (! in_array($game_server_id, Game::getServerIds($game_zone_id))) {
    		return false;
    	}
    	return true;
    }
    
    function isValidJob($game_id, $job) {
    	return common_stream('game:isvalidjob:' . $game_id . ':' . $job, array("Game", "_isValidJob"), array($game_id, $job), 3600 * 24 * 7);
    }
    
    function _isValidJob($game_id, $job) {
    	$game = Game::staticGet('id', $game_id);
    	
    	if (empty($game)) {
    		return false;
    	}
    	
    	foreach ($game->getJobs() as $gj) {
    		if (trim($gj) == $job) {
    			return true;
    		}
    	}
    	return false;
    }
    
    function getActiveTop50($game_id) {
    	return common_stream('game:activetop50:' . $game_id, array("Game", "_getActiveTop50"), array($game_id), 24 * 3600);
    }
    
	function _getActiveTop50($game_id) {
    	$queryString = "SELECT profile.id as id FROM profile, grade_record WHERE profile.id = grade_record.user_id";
    	$queryString .= " AND profile.game_id = " . $game_id . " AND profile.is_banned = 0";
    	$queryString .= " ORDER BY grade_record.changed DESC";
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
    
    function getRandom100($game_id, $sex = '', $province = null, $city = null, $school = null) {
    	
    	
    	$memcachedKey = 'game:random100:' . $game_id;
    	if ($sex != '') {
    		$memcachedKey .= ':' . $sex;
    	}
    	if ($province != null
    		&& $city != null) {
    		$memcachedKey .= ':' . hash(HASH_ALGO, $province . '-' . $city);
    	}
    	
   		if ($school != '') {
    		$memcachedKey .= ':' . $school;
    	}
    	
    	return common_stream($memcachedKey,
    		array("Game", "_getRandom100"), array($game_id, $sex, $province, $city, $school), 24 * 3600);
    }
    
	function _getRandom100($game_id, $sex = '', $province = null, $city = null, $school = null) {
    	$queryString = "SELECT id FROM profile";
    	$queryString .= " WHERE game_id = " . $game_id;
    	
    	if($sex == 'F' || $sex == 'M')
    	$queryString .= " AND profile.sex = '" . $sex . "'";
    	if($province  && $city )
		{	if($province != '北京' && $province != '天津' && $province != '上海' && $province != '重庆')
				$queryString .= " and city = '". $city ."'";
			$queryString .= " and province = '". $province ."'";
		}
		if($school)
    		$queryString .= " AND profile.school = '" . $school . "'";
		$queryString .= " order by followers desc limit 1,100";
    	$profile = new Profile();
    	$profile->query($queryString);
 //      common_debug($queryString);
    	$pops = array();
    	while ($profile->fetch()) {
    		$pops[] = $profile->id;
    	}
    	$profile->free();
//    	$pops = common_random_fetch($pops,100);
    	
    	return $pops;
    }
    
	function getRecents50($game_id) {
    	return common_stream('game:recents50:' . $game_id, array("Game", "_getRecents50"), array($game_id), 300);
    }
    
    function _getRecents50($game_id) {
    	$profile = Profile::getGameRecentRegisteredPeople($game_id, 0, 50);
    	
    	$pops = array();
    	while ($profile->fetch()) {
    		$pops[] = $profile->id;
    	}
    	$profile->free();
    	
    	return $pops;
    }
    
	function getVideoTop20($game_id) {
    	return common_stream('game:videotop20:' . $game_id, array("Game", "_getVideoTop20"), array($game_id), 24 * 3600);
    }
    
	function _getVideoTop20($game_id)
    {
 		$query = "SELECT user_id,count(*) AS num FROM notice,user WHERE notice.user_id=user.id AND content_type=3 AND topic_type != 4 AND notice.is_banned = 0";
 		if($game_id)
 			$query .= "AND user.game_id='".$game_id."'";
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
    
	function getPicTop20($game_id) {
    	return common_stream('game:pictop20:' . $game_id, array("Game", "_getPicTop20"), array($game_id), 24 * 3600);
    }
    
	function _getPicTop20($game_id)
    {
 		$query = "SELECT user_id,count(*) AS num FROM notice,user WHERE notice.user_id=user.id AND content_type=4 AND topic_type != 4 AND notice.is_banned = 0";
 		if($game_id)
 			$query .= "AND user.game_id='".$game_id."'";
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
    
	function getTextTop20($game_id) {
    	return common_stream('game:texttop20:' . $game_id, array("Game", "_getTextTop20"), array($game_id), 24 * 3600);
    }
    
	function _getTextTop20($game_id)
    {
 		$query = "SELECT user_id,count(*) AS num FROM notice,user WHERE notice.user_id=user.id AND content_type=1 AND topic_type != 4 AND notice.is_banned = 0";
 		if($game_id)
 			$query .= "AND user.game_id='".$game_id."'";
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
    
	function getVips($game_id) {
    	return common_stream('game:vips:' . $game_id, array("Game", "_getVips"), array($game_id), 24 * 3600);
    }
    
	function _getVips($game_id) {
    	$queryString = "SELECT profile.id as id FROM profile WHERE";
    	$queryString .= " profile.is_vip = 1 AND profile.game_id = " . $game_id;
    	$queryString .= " ORDER BY profile.id DESC";
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
}
