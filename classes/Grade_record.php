<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Table Definition for grade_record
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';
require_once INSTALLDIR.'/classes/Profile.php';
require_once INSTALLDIR.'/classes/User_grade.php';

class Grade_record extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'grade_record';                    // table name
    public $id;                              // int(11)  not_null primary_key
    public $user_id;                         // int(11)  not_null multiple_key
    public $last_score;                      // mediumint(9)  not_null multiple_key binary
    public $changed;                         // smaillint(6)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Grade_record',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function dailyRecord()
    {
    	$profile = new Profile();
		$profile->query("select id from profile");
		while($profile->fetch()) {
			$graderecord = Grade_record::staticGet('user_id', $profile->id);
			if($graderecord){
				$orig = clone($graderecord);
				$graderecord->last_score = User_grade::getScore($profile->id);
				$graderecord->changed = $graderecord->last_score - $orig->last_score;
				if($graderecord->changed != $orig->changed) {
				    $result = $graderecord->update($orig);
		            if (!$result) {
		                common_log_db_error($graderecord, 'UPDATE', __FILE__);
		                return false;
		            }
				}
			}else {
				$newrecord = new Grade_record();
				$newrecord->user_id = $profile->id;
				$newrecord->last_score = User_grade::getScore($profile->id);
				$newrecord->changed = 0;
				$result = $newrecord->insert();
		        if (!$result) {
		            common_log_db_error($newrecord, 'INSERT', __FILE__);
		            return false;
		        }
			}
		}
		$profile->free();
    }
    
    // get 10 users who has most score increment in last day
    static function getActiveUsers($limit=10, $area='all',$neededid=null)
    {
    	
    	$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);

		$someday = $today - 3600*48;
		$time = date('Y-m-d H:i:s', $someday);
		
    	$gradeRecord = new Grade_record();
    	$qry = 'select user_id, changed from grade_record where user_id != '.
    	    common_config('newuser', 'default_id');
    	$qry .= ' and user_id IN (select distinct user_id from notice where created > "'.$time.'" )';
    	if($neededid)
    	{
    		if($area == 'game')
       			$qry .= ' and user_id IN (select id from user where game_id = '.$neededid.')';
        	else if($area == 'gameserver')
        		$qry .= ' and user_id IN (select id from user where game_server_id = '.$neededid.')';
    	}
    	$qry .=' ORDER BY changed DESC LIMIT 0,'. $limit;
    	$gradeRecord->query($qry);
		$activeusers = array();
		while ($gradeRecord->fetch()) {
			$activeusers[] = array('user_id'=>$gradeRecord->user_id,'changed'=>$gradeRecord->changed);
		}
		$gradeRecord->free();
		return $activeusers;
    }
    
    static function getHotUsers($limit=15,$area='all',$neededid=null)
    {
    	$gradeRecord = new Grade_record();
    	$qry = 'select user_id, last_score, changed from grade_record where user_id != '.
    	    common_config('newuser', 'default_id');
    	if($neededid)
    	{
    		if($area == 'game')
       			$qry .= ' and user_id IN (select id from user where game_id = '.$neededid.')';
        	else if($area == 'gameserver')
        		$qry .= ' and user_id IN (select id from user where game_server_id = '.$neededid.')';
    	}
    	$qry .=' ORDER BY last_score DESC LIMIT 0,'. $limit;
    	$gradeRecord->query($qry);
		$hotusers = array();
		while ($gradeRecord->fetch()) {
			$hotusers[] = array('user_id'=>$gradeRecord->user_id, 
			                    'score'=>$gradeRecord->last_score, 
			                    'changed'=>$gradeRecord->changed);
		}
		$gradeRecord->free();
		return $hotusers;
    }

}
