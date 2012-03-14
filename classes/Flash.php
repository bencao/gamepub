<?php
/**
 * Table Definition for flash
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Flash extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'flash';                           // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $notice_id;                       // int(11)  not_null
    public $user_id;                         // int(11)  not_null
    public $rendered;                        // blob(65535)  blob
    public $title;                           // string(40)  
    public $type;                         	 // int(4)  
    public $introduction;                    // string(280)  
    public $detail;                          // blob(65535)  blob
    public $path;                         	 // string(255)  
    public $picpath;                         // string(255)  
    public $click_count;                     // int(11)  not_null
    public $is_valid;                        // int(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Flash',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function saveNew($user_id, $rendered, $title, $type, $introduction, $detail, $path, $picpath="", $notice_id=100000) 
    {
        $flash = new self();
        $flash->user_id = $user_id;
        $flash->rendered = $rendered;
        $flash->title = $title;
        $flash->type = $type;
        $flash->introduction = $introduction;
        $flash->detail = $detail;
        $flash->path = $path;
        $flash->picpath = $picpath;
        $flash->notice_id = $notice_id;
        $flash_id = $flash->insert();

        return $flash;
    }
    
    function updateNoticeId($notice_id) {
    	$f = clone($this);
    	$this->notice_id = $notice_id;
    	$this->update($f);
    }
    
	function increaseClick()
    {
    	$orig = clone($this);
    	$this->click_count++;
    	$this->update($orig);
    }
    
	function getUserFlash($type, $offset, $limit, $user_id) 
	{
		return self::getLatestFlash($type, $offset, $limit, $user_id);
    }
    
	function getLatestFlash($type=0, $offset=0, $limit=20, $user_id= false) 
	{
       	$flash = new self();
       	
		if ($type > 0) {
			//type == 0 means all
       		$flash->whereAdd('type = ' . $type);
       	}
       	if ($user_id) {
       		$flash->whereAdd('user_id = ' . $user_id);
       	}
    	$flash->whereAdd('is_valid = 1');
    	$flash->orderBy('id desc');
    	$flash->limit($offset, $limit);
    	
		$flash->find();
    	return $flash;
    }
    
    function getFlashTotal($type, $user_id=false)
    {
    	$flash = new self();
        if ($type > 0) {
       		//type == 0 means all
       		$flash->whereAdd('type = ' . $type);
       	}
    	if ($user_id) {
       		$flash->whereAdd('user_id = ' . $user_id);
       	}
    	$flash->whereAdd('is_valid = 1');
        return $flash->count();
    }
    
	function getHottestFlash($type, $offset=0, $limit=20) 
	{
		//目前单纯按点击数来排热度，免去关联大数据量notice表
		// XXX:  若实际中发现质量不佳再加  转载数和评论数 衡量指标
       	$flash = new self();
       	
       	if ($type > 0) {
       		//type == 0 means all
       		$flash->whereAdd('type = ' . $type);
       	}
       	$flash->whereAdd('is_valid = 1');
    	$flash->orderBy('click_count desc');
    	$flash->limit($offset, $limit);
		$flash->find();
		
    	return $flash;
    }
    
    /**
     * 点击数是5的几次方就几颗星，最多5颗星，用户量变大后再增大基数
     * @param $click_count
     * @return int
     */
    function getRating() {
    	$rating = $this->click_count * 100 / ($this->click_count + 50);
    	return (int)$rating;
    }
    
    function getUploader() {
    	return Profile::staticGet($this->user_id);
    }
}