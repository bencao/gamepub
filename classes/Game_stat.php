<?php
/**
 * Table Definition for game_stat
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Game_stat extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'game_stat';                       // table name
    public $game_id;                         // int(11)  not_null primary_key
    public $video_num;                       // int(11)  
    public $music_num;                       // int(11)  
    public $pic_num;                         // int(11)  
    public $text_num;                        // int(11)  
    public $user_num;                        // int(11)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Game_stat',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function dailyrecord()
    {
    	
    	$all_game_ids = Game::listAllGameIds();
    	
    	$cur = time();
		$dt = strftime('%Y-%m-%d', $cur);
		//当天0:0:0的总秒数
		$today = strtotime($dt);		
		//一周
		$dayago = $today - 3600*24;
		$cachestr = date('Y-m-d', $dayago);
    	
    	foreach ($all_game_ids as $agi) {
   		    
    		if(!Game_stat::staticGet('game_id',$agi)) {
 //   			echo $agi."\n";
    			$newrecord = new Game_stat();
//				$newrecord->game_id = $agi;
//				echo $newrecord->game_id."aa   \n";
//				$newrecord->video_num = 0;
//				$newrecord->music_num = 0;
//				$newrecord->pic_num = 0;
//				$newrecord->text_num = 0;
//				$newrecord->user_num = 0;
//				$result = $newrecord->insert();
				//insert()存在问题，会产生game_stat_seq表，而且插入的数据不正确。
				$query = "INSERT INTO game_stat(`game_id`, `video_num`, `music_num`, `pic_num`, `text_num`, `user_num`)  values(" .$agi. ",0,0,0,0,0)";
//				echo $query;
				$result = $newrecord -> query($query);
//		        if (!$result) {
//		            common_log_db_error($newrecord, 'INSERT', __FILE__);
//		            return false;
//		        }
    		}
    		$game_stat = Game_stat::staticGet('game_id', $agi);
    		if ($game_stat && !Game_stat_history::stathisGetbyidtime($agi,$cachestr)) {
    			$hisrecord = new Game_stat_history();
				$hisrecord->game_id = $agi;
				$hisrecord->video_num = $game_stat->video_num;
				$hisrecord->music_num = $game_stat->music_num;
				$hisrecord->pic_num = $game_stat->pic_num;
				$hisrecord->text_num = $game_stat->text_num;
				$hisrecord->user_num = $game_stat->user_num;
				$hisrecord->his_date = $cachestr;
				$result = $hisrecord->insert();
		        if (!$result) {
		            common_log_db_error($hisrecord, 'INSERT', __FILE__);
		            return false;
		        }
    		
	    		$game_stat->user_num = User::getUsernumbyGame($agi);
	    		$game_stat->video_num = Notice::getNoticenumbyGameandctype(3 ,$agi);
	    		$game_stat->music_num = Notice::getNoticenumbyGameandctype(2 ,$agi);
	    		$game_stat->pic_num = Notice::getNoticenumbyGameandctype(4 ,$agi);
	    		$game_stat->text_num = Notice::getNoticenumbyGameandctype(1 ,$agi);
	    		$game_stat->update();
	    		
	    		
    		}
    		
    		if ($game_stat) {
    			$game = Game::staticGet('id', $agi);
	    		$gameorig = clone($game);
	    		$game->gamers_num = $game_stat->user_num;
	    		$game->notice_num = $game_stat->video_num + $game_stat->music_num + $game_stat->pic_num + $game_stat->text_num;
	    		$game->update($gameorig);
    		}
    	}
    	
    	   	
    }
    
    function getTopGameidbyntype($limit = 15, $type = 1)
    {
    	$game_stat = new Game_stat();
    	$game_stat->selectAdd('game_id');
    	switch($type)
    	{
    		case 4: $game_stat->orderBy('pic_num DESC');
    				break;
    		case 3: $game_stat->orderBy('video_num DESC');
    				break;
    		case 2: $game_stat->orderBy('music_num DESC');
    				break;
    		default: $game_stat->orderBy('text_num DESC');
    				break;
    	}
    	
    	$game_stat->limit(0,$limit);
    	
    	$gameids = array();
    	$game_stat->find();
    	while ($game_stat->fetch()) {
    		$gameids[] = $game_stat->game_id;
    	}
    	
    	$game_stat->free();
    	
//    	common_debug('id'.$gameids[0]);
    	
    	return $gameids;
    }
}
