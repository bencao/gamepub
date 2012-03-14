<?php
if (!defined('SHAISHAI')) { exit(1); }
/**
 * Table Definition for discussion
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Discussion extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'discussion';                      // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $notice_id;                       // int(11)  not_null multiple_key
    public $user_id;                      // int(11)  not_null multiple_key
    public $content;                         // string(200)  multiple_key binary
    public $rendered;                        // blob(65535)  blob binary
    public $created;                        // date
    public $source;							// where the discussion is from

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Discussion',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    static function saveNewDis($notice_id,$user_id,$content,$add_rendered=null, $source='web', $created=null)
    {
    
    	$profile = Profile::staticGet('id',$user_id);
    	//$content = (!$rendered)?$content:$rendered;
    	$rendered = $add_rendered.$content;// $rendered 需要进一步处理
    	
    	
    	if (mb_strlen($content) > 280) {
			common_log(LOG_INFO, 'Rejecting notice that is too long.');
			return '消息太长.';
		}

		if (!$profile) {
			common_log(LOG_ERR, 'Problem saving notice. Unknown user.');
			return '未知用户.';
		}
		
		$discussion = New Discussion();
		$discussion->notice_id = $notice_id;
		$discussion->user_id = $user_id;
		$discussion->content = $content;
		$discussion->rendered = Notice::renderContent($rendered, $discussion);
		$discussion->source = $source;
		if($created)
			$discussion->created = $created;
		else
			$discussion->created = common_sql_now();
		
		$id = $discussion->insert();
    	if (!$id) {
			common_log_db_error($discussion, 'INSERT', __FILE__);
			return '保存评论产生问题.';
		}
		Notice_heat::addHeat($notice_id,2);
		return $discussion;
    }

    function disListStream($notice_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $max_id=0, $since=null)
	{
		$ids = Discussion::_streamDirect($notice_id, $offset, $limit, $since_id, $max_id, $since);
		
		return Discussion::_getStreamByIds($ids);
	}
    
	
    function _streamDirect($notice_id, $offset=0, $limit=NOTICES_PER_PAGE, $since_id=0, $max_id=0, $since=null)
    {
        $discussion = new Discussion();
        $discussion->notice_id = $notice_id;

        if ($since_id != 0) {
            $discussion->whereAdd('id > ' . $since_id);
        }

        if ($max_id != 0) {
            $discussion->whereAdd('id < ' . $max_id);
        }

        if (!is_null($since)) {
            $discussion->whereAdd('created > \'' . date('Y-m-d H:i:s', $since) . '\'');
        }

        $discussion->whereAdd("exists (select * from discussion where notice_id=".$discussion->notice_id.")"); //is_delete=0 and 

        $discussion->orderBy('id DESC');

        if (!is_null($offset)) {
            $discussion->limit($offset, $limit);
        }

        $ids = array();

        if ($discussion->find()) {
            while ($discussion->fetch()) {
                $ids[] = $discussion->id;
            }
        }
        return $ids;
    }
    
    function _getStreamByIds($ids)
	{
		$cache = common_memcache();

		if (!empty($cache)) {
			$notices = array();
			foreach ($ids as $id) {
				$n = Discussion::staticGet('id', $id);
				if (!empty($n)) {
					$notices[] = $n;
				}
			}
			return new ArrayWrapper($notices);
		} else {
			$discussion = new Discussion();
			if (empty($ids)) {
                return $discussion;
            }
            $discussion->whereAdd('id in (' . implode(', ', $ids) . ')');
            $discussion->orderBy('id DESC');
            $discussion->find();

            $temp = array();

            while ($discussion->fetch()) {
                $temp[$discussion->id] = clone($discussion);
            }

            $wrapped = array();

            foreach ($ids as $id) {
                if (array_key_exists($id, $temp)) {
                    $wrapped[] = $temp[$id];
                }
            }

            return new ArrayWrapper($wrapped);
		}
	}
    
	function delete()
	{
		$discussdelete = $this->query(sprintf("DELETE FROM discussion where id = %d", $this->id));
		if(!$discussdelete) {
			common_log_db_error($this, 'delete', __FILE__);
			return '删除评论产生问题.';
		}
		
		return true;
	}
}
