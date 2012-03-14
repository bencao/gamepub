<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for Report
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

/* We keep the first three 20-Report pages, plus one for pagination check,
 * in the memcached cache. */

define('Report_CACHE_WINDOW', 61);

define('Report_LOCAL_PUBLIC', 1);
//define('Report_REMOTE_OMB', 0);
define('Report_LOCAL_NONPUBLIC', -1);
define('Report_GATEWAY', -2);

if (! defined('MAX_BOXCARS')) {
	define('MAX_BOXCARS', 128);
}

class Report extends Memcached_DataObject
{
	###START_AUTOCODE
	/* the code below is auto generated do not remove the above tag */

	public $__table = 'Report';                          // table name
	public $id;                              // int(4)  primary_key not_null
	public $content;                         // varchar(140)
	public $rendered;                        // text()
	public $url;                             // varchar(255)
	public $created;                         // datetime()   not_null
	public $modified;                        // timestamp()   not_null default_CURRENT_TIMESTAMP
	public $topic_type;						 // smallint
	public $beyongto;					 //smallint
	public $click_num;				//int(4)
	public $discussion_num;
	public $server_id;
	public $reply_only;

	/* Static get */
	function staticGet($k,$v=NULL) {
		return Memcached_DataObject::staticGet('Report',$k,$v);
	}

	/* the code above is auto generated do not remove the tag below */
	###END_AUTOCODE

	function getProfile()
	{
		return Profile::staticGet('id', $this->user_id);
	}
	
	function getUser()
	{
		return User::staticGet('id', $this->user_id);
	}

	//add by frederica for send Report to link_user
	//get article Report only
	function getReport($beyongto,$start_time,$end_time)
	{
		$report = new Report();
		$query = "SELECT id FROM Report WHERE game_id='$beyongto' " .
		"AND (modified BETWEEN '$start_time' AND DATE_ADD('$end_time',INTERVAL 1 DAY))"; //AND is_delete=0 
//		common_debug($query);
		$report->query($query);
		$ids = array();
		while ($report->fetch()) {
			$ids[] = $report->id;
		}
		$report->free();
		return $ids;
	}

	//这个可以这样写, $report = Report::staticGet('id', $id); $report->content等等
	function getContent($id)
	{
		$report = new Report();
		$query = "SELECT content,modified FROM report WHERE id='$id'"; //AND is_delete=0 
//		common_debug($query);
		$report->query($query);
		//$notice_select= array();
		while ($report->fetch()) {
			//$notice_select[]->id=$report->id;
			$notice_select->content = $report->content;
			$notice_select->modified=$report->modified;
		}
		$report->free();
		return $notice_select;
	}

	function delete()
	{
		$deleted = new Deleted_notice();

        $deleted->id         = $this->id;
        $deleted->game_id = $this->game_id;
        $deleted->url        = $this->url;
        $deleted->content = $this->content;
        $deleted->rendered        = $this->rendered;
        $deleted->created    = $this->created;
        $deleted->deleted    = common_sql_now();
		
		$deleted->insert();
		
		$report = new Report();
		$report->query('DELETE FROM ' . strtolower($report) . ' WHERE id = ' . $this->id);
		$report->free();
	
	}
	function SaveNew($id, $url, $belongto,$topic_type=null)
	{
		
	}
}     