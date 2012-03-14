<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

class SearchEngine
{
    protected $target;
    protected $table;

    function __construct($target, $table)
    {
        $this->target = $target;
        $this->table = $table;
    }

    function query($q)
    {
    }

    function limit($offset, $count, $rss = false)
    {
        return $this->target->limit($offset, $count);
    }

    function set_sort_mode($mode)
    {
        if ('chron' === $mode)
            return $this->target->orderBy('created desc');
    }
}

class SphinxSearch extends SearchEngine
{
    private $sphinx;
    private $connected;

    function __construct($target, $table, $content_type)
    {
//        $fp = @fsockopen(common_config('sphinx', 'server'), common_config('sphinx', 'port'));
//        if (!$fp) {
//            $this->connected = false;
//            return;
//        }
//        fclose($fp);
        parent::__construct($target, $table);
        require_once 'sphinxapi.php';
        $this->sphinx = new SphinxClient;
        $this->sphinx->setServer(common_config('sphinx', 'server'), common_config('sphinx', 'port'));
        $this->connected = true;
        $this->content_type=$content_type;
    }

    function is_connected()
    {
        return $this->connected;
    }

    function limit($offset, $count, $rss = false)
    {
        //FIXME without LARGEST_POSSIBLE, the most recent results aren't returned
        //      this probably has a large impact on performance
        $LARGEST_POSSIBLE = 1e6;

        if ($rss) {
            $this->sphinx->setLimits($offset, $count, $count, $LARGEST_POSSIBLE);
        }
        else {
            // return at most 50 pages of results
            $this->sphinx->setLimits($offset, $count, 50 * ($count - 1), $LARGEST_POSSIBLE);
        }

        return $this->target->limit(0, $count);
    }

    function query($q)
    {
    	// 分词处理
    	$keywords = common_tokenize($q);
    	
        if ('leshai_people' === $this->table) {
            $subQry = array();
			foreach($keywords as $v){
				if (preg_match('/^\d+$/', $v)) {
					$this->sphinx->setFilter('qq', array($v));
				} else {
					$subQry[] = $v;
				}
			}
			$qry = implode(' ', $subQry);
			$result = $this->sphinx->query($qry, 'profile');
        } else if ('leshai_notices' === $this->table) {
        	if (in_array($this->content_type, array(2, 3, 4))) {
        		$this->sphinx->setFilter('content_type', array($this->content_type));
        	}
			$qry = implode(' ', $keywords);
			$result = $this->sphinx->query($qry, 'notice');
        } else if ('leshai_group' == $this->table) {
			$qry = implode(' ', $keywords);
			$result = $this->sphinx->query($qry, 'group');
        } else if ('leshai_userinterest' == $this->table) {
			$qry = implode(' ', $keywords);
			$result = $this->sphinx->query($qry, 'userinterest');        	
        } else {
            throw new ServerException('Unknown table: ' . $this->table);
        }
        
        if (!isset($result['matches'])) return false;
        $id_set = join(', ', array_keys($result['matches']));
        $this->target->whereAdd("id in ($id_set)");
        return true;
    }

    function set_sort_mode($mode)
    {
        if ('chron' === $mode) {
            $this->sphinx->SetSortMode(SPH_SORT_ATTR_DESC, 'created_ts');
            return $this->target->orderBy('created desc');
        }
    }
}

class MySQLLikeSearch extends SearchEngine
{
	private $content_type;
	function __construct($target,$table,$content_type)
	{
		parent::__construct($target,$table);
		$this->content_type=$content_type;
	}
	
    function query($q)
    {
    	// 分词处理
    	$keywords = common_tokenize($q);
    	
        if ('leshai_people' === $this->table) {
            $subQry = array();
			foreach($keywords as $v){
//				if (preg_match('/^\d+$/', $v)) {
//					array_push($subQry, '(qq = ' . $v . ')');
//				} else {
					array_push($subQry, sprintf('(nickname LIKE "%%%1$s%%" OR '.
	                           ' location LIKE "%%%1$s%%" OR '.
							   ' game_org LIKE "%%%1$s%%" OR '.
	                           ' bio      LIKE "%%%1$s%%")'
							, addslashes($v)));
//				}
			}
			$qry = implode(' AND ', $subQry);
        } else if ('leshai_notices' === $this->table) {
        	if ($this->content_type == 2) {//搜索音乐
	            $str="content_type=2 and topic_type<>4 and (";	
				foreach($keywords as $v){
					$str.=sprintf('content LIKE "%%%1$s%%" OR ', addslashes($v));
				}
				$str.="1=0)";
        	} elseif ($this->content_type==3) {//搜索视频
	            $str="content_type=3 and topic_type<>4 and (";	//不搜索' . GROUP_NAME() . '消息,HUANGBIN,20090923		
				foreach($keywords as $v){
					$str.=sprintf('content LIKE "%%%1$s%%" OR ', addslashes($v));
				}
				$str.="1=0)";
        	} elseif ($this->content_type==4) {//搜索图片
	            $str="content_type=4 and topic_type<>4 and (";	//不搜索' . GROUP_NAME() . '消息,HUANGBIN,20090923		
				foreach($keywords as $v){
					$str.=sprintf('content LIKE "%%%1$s%%" OR ', addslashes($v));
				}
				$str.="1=0)";
        	} else {
	            $str="topic_type<>4 and (";	//不搜索' . GROUP_NAME() . '消息,HUANGBIN,20090923		
				foreach($keywords as $v){
					$str.=sprintf('content LIKE "%%%1$s%%" OR ', addslashes($v));
				}
				$str.="1=0)";
        	}
			$qry=$str;
        } else if ('leshai_group' == $this->table) {
        	$wheres = array('uname', 'nickname', 'homepage', 
                        'description', 'location', 'catalog', 'category');
	        $subQry = array();
	        foreach($keywords as $v){
	        	foreach ($wheres as $where) {
	        		$subQry[] = $where . sprintf(' LIKE "%%%1$s%%" ', addslashes($v));
	        	}
			}
			$qry = '(' . implode(' OR ', $subQry) . ')';
			$qry .= ' AND (validity <> 0)';
        } else if ('leshai_userinterest' == $this->table) {
        	foreach($keywords as $v){
        		$this->target->whereAdd(sprintf('interest like "%%%1$s%%"', addslashes($v)), "OR");
        	}
        	return true;
        } else {
            throw new ServerException('Unknown table: ' . $this->table);
        }

        $this->target->whereAdd($qry);

        return true;
    }
}

