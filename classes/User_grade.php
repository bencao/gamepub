<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Table Definition for user_grade
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class User_grade extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'user_grade';                      // table name
    public $id;                              // int(11)  not_null primary_key
    public $user_id;                         // int(11)  not null
    public $grade;                           // int(4)  not_null
    public $score;                           // int(9)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('User_grade',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    // Add new record for new user
    static function newScore($userid) {
    	$user_grade = new User_grade();
        $user_grade->user_id = $userid;
        $user_grade->grade = 1;
        $user_grade->score = 10;
	    $result = $user_grade->insert();
	    if (!$result) {
	            common_log_db_error($user_grade, 'INSERT', __FILE__);
	            return false;
	    }
    	return true;
    }
    
    // Add score of a user
    static function addScore($userid, $score) {
    	$followers = Profile::staticGet('id', $userid)->followers;
    	$user_grade = User_grade::staticGet('user_id', $userid);
    	$orig = clone($user_grade);
    	$user_grade->score += $score;
        $user_grade->grade = User_grade::checkGrade($followers, $user_grade->score);
        $result = $user_grade->update($orig);
	    if (!$result) {
	        common_log_db_error($user_grade, 'UPDATE', __FILE__);
	        return false;
	    }
	    
	    if ($user_grade->grade > $orig->grade) {
	    	// if user get promoted, send him a system message
	    	// XXX: haven't decide the format of message
	    }
    	return true;
    }

    // Deduct score of a user
    static function deductScore($userid, $score) {
    	$followers = Profile::staticGet('id', $userid)->followers;
    	$user_grade = User_grade::staticGet('user_id', $userid);
    	$orig = clone($user_grade);
    	// make sure the score won't smaller than 0
    	if($user_grade->score - $score >0) {
    	    $user_grade->score -= $score;
    	}else {
    		$user_grade->score = 0;
    	}
        $user_grade->grade = User_grade::checkGrade($followers, $user_grade->score);
        
        if ($user_grade->grade != $orig->grade 
        	|| $user_grade->score != $orig->score) {
	        $result = $user_grade->update($orig);
		    if (!$result) {
		        common_log_db_error($user_grade, 'UPDATE', __FILE__);
		        return false;
		    }
        }
    	return true;
    }
    
    // Get the user scroe by userid
    static function getScore($userid) {
    	$user_grade = User_grade::staticGet('user_id', $userid);
    	return $user_grade->score;
    }
    
    // Get the user grade by userid
    static function getGrade($userid) {
    	$user_grade = User_grade::staticGet('user_id', $userid);
    	return $user_grade->grade;
    }
    
    // Get the user upgrade percentage
    static function getUpgradeInfo($userid) {
    	$user_grade = User_grade::staticGet('user_id', $userid);
    	$grade = $user_grade->grade;
    	$currentScore = $user_grade->score;
    	$thisScore = User_grade::getGradeScore($grade);
    	$nextScore = User_grade::getGradeScore($grade+1);
    	// when user reach the top level, this code causes warning because of .
    	if($nextScore-$thisScore > 0) {
    		$percent = ($currentScore-$thisScore)*100/($nextScore-$thisScore);
    	}else {
    		$percent = 0;
    	}
    	$gradeinfo = array();
    	$gradeinfo['grade'] = $grade;
    	$gradeinfo['score'] = $currentScore;
    	$gradeinfo['nextScore'] = $nextScore;
    	$gradeinfo['nextfollowers'] = User_grade::getGradeFollowers($grade+1);
    	$gradeinfo['percent'] = $percent>100 ? 100:$percent;
    	return $gradeinfo;
    }

    // Get the user grade by followers and score
    static function checkGrade($followers, $score) {
    	$follgrade = 1;
//    	if($followers<10) {
//    		$follgrade = 1; 
//    	} else 
		if($followers<20) {
    		$follgrade = 2;
    	} else if($followers<40) {
    		$follgrade = 3;
    	} else if($followers<100) {
    		$follgrade = 4;
    	} else if($followers<200) {
    		$follgrade = 5;
    	} else if($followers<500) {
    		$follgrade = 6;
    	} else if($followers<2000) {
    		$follgrade = 7;
    	} else if($followers<5000) {
    		$follgrade = 8;
    	} else {
    		$follgrade = 9;
    	}
    	$scoregrade = 1;
    	if($score<200) {
    		$scoregrade = 1;
    	} else if($score<500) {
    		$scoregrade = 2;
    	} else if($score<1000) {
    		$scoregrade = 3;
    	} else if($score<2500) {
    		$scoregrade = 4;
    	} else if($score<5000) {
    		$scoregrade = 5;
    	} else if($score<10000) {
    		$scoregrade = 6;
    	} else if($score<20000) {
    		$scoregrade = 7;
    	} else if($score<40000) {
    		$scoregrade = 8;
    	} else {
    		$scoregrade = 9;
    	}
    	return $follgrade<$scoregrade? $follgrade : $scoregrade;
    }
    
    // XXX: We don't count login currently, the algorithm need to be re-thinked
    //static function weeklydeduction($userID, $noticeNum, $loginNum) {
    static function weeklyDeduction($userID, $noticeNum) {
    	// 100% active = $noticeNum>10 and $loginNum >5
    	//if ($noticeNum >=10 && $loginNum>=5){
    	if ($noticeNum >=10) {
    		return true;
    	}
    	$userGrade = User_grade::staticGet('user_id', $userID);
    	$grade = $userGrade->grade;
    	$currentScore = $userGrade->score;
    	$nextScore = User_grade::getGradeScore($grade+1);
    	$thisScore = User_grade::getGradeScore($grade);
    	//$todeduct = floor(((5-$loginNum)/100 + (10-$noticeNum)/200)*($currentScore-$toScore));
    	
    	// 100 means 10*5*2 user's score will be deduct to the beginning of this grade in 5 times
    	$todeduct = floor(((10-$noticeNum)/100)*($nextScore-$thisScore));
    	
    	// If the score of the user is nearly used out in this grade, score won't deduct anymore
    	if ($todeduct<=0 || ($currentScore - $thisScore)<$todeduct) {
    		return true;
    	} else {
    		User_grade::deductScore($userID, $todeduct);
    	}
    }
    
    // Get the level score by grade
    static function getGradeScore($grade) {
    	switch($grade) {
    		case 0: return 0;
    		case 1: return 0;
    		case 2: return 200;
    		case 3: return 500;
    		case 4: return 1000;
    		case 5: return 2500;
    		case 6: return 5000;
    		case 7: return 10000;
    		case 8: return 20000;
    		case 9: return 40000;
    		default: return 0;
    	}
    }
    
    // Get the level followers by grade
    static function getGradeFollowers($grade) {
    	switch($grade) {
    		case 0: return 0;
    		case 1: return 0;
    		case 2: return 10;
    		case 3: return 20;
    		case 4: return 40;
    		case 5: return 100;
    		case 6: return 200;
    		case 7: return 500;
    		case 8: return 2000;
    		case 9: return 5000;
    		default: return 0;
    	}
    }
    
    // This is weekly called from a daemon service to deduct users' score
    static function weeklyAdjust() {
    	$fromdate = date("Y-m-d", strtotime("-1 Week Saturday -6 day")) ." 00:00:00";
    	$todate = date("Y-m-d", strtotime("-1 Week Saturday")) ." 23:59:59";
    	$profile = new Profile();
		$profile->query("select id from profile");
		while($profile->fetch()) {
			$noticeNum = User::getNoticesNum($profile->id, $fromdate, $todate);
			User_grade::weeklyDeduction($profile->id, $noticeNum);
		}
		$profile->free();
    }
    
    // This is daily called from a daemon service to give excellent user award everyday
    // follower > 1000 && msgnum/day > 4 , add 35 scores
    // follower > 100 && msgnum/day > 4 , add 20 scores
    // follower > 50 && msgnum/day > 4 , add 10 scores
    static function dailyAward() {
    	$fromdate = date("Y-m-d", strtotime("-1 day")) ." 00:00:00";
    	$todate = date("Y-m-d", strtotime("-1 day")) ." 23:59:59";
    	$profile = new Profile();
    	$profile->query('BEGIN');
		$profile->query("select id, followers from profile");
		while($profile->fetch()) {
			if($profile->followers>999) {
				if(User::getNoticesNum($profile->id, $fromdate, $todate)>4){
					User_grade::addScore($profile->id, 50);
					// Send a system message to the user who got award
                    $content = '您的跟随者达到了1000人以上，昨日发消息数超过5条，'. common_config('site', 'name') . '为您增加了5个铜G币作为奖励。';
                    System_message::saveNew(array($profile->id), $content, $content, 0);
				}
			}else if($profile->followers>99){
			    if(User::getNoticesNum($profile->id, $fromdate, $todate)>4){
					User_grade::addScore($profile->id, 20);
					$content = '您的跟随者达到了100人以上，昨日发消息数超过5条，' . common_config('site', 'name') . '为您增加了2个铜G币作为奖励。';
                    System_message::saveNew(array($profile->id), $content, $content, 0);
				}
			}else if($profile->followers>49){
			    if(User::getNoticesNum($profile->id, $fromdate, $todate)>4){
					User_grade::addScore($profile->id, 10);
					$content = '您的跟随者达到了50人以上，昨日发消息数超过5条，'. common_config('site', 'name') . '为您增加了1个铜G币作为奖励。';
                    System_message::saveNew(array($profile->id), $content, $content, 0);
				}
			}
		}
		$profile->query('COMMIT');
		$profile->free();
    }
    
    static function awardForNewbieMission($profile) {
    	User_grade::addScore($profile->id, 200);
    }

}