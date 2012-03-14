<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Table Definition for video
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Video extends Memcached_DataObject
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'video';      	// table name
    public $id;                       		// int(4)  primary_key not_null
    public $notice_id;					// int not null
    public $title; 						// varchar(255)
    public $rendered;         		// text()
	public $source;						//varchar(20)
	public $vid;
	public $picpath;
	public $size;
	public $timelen;
	public $status;
	public $flashsrc;
    
    /* Static get */
    function staticGet($k,$v=null)
    { return Memcached_DataObject::staticGet('Video',$k,$v); }

    
    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function saveNew($title, $rendered, $source, $notice_id=100000, $picpath="", $flashsrc="", $vid="") {
        $video = new Video();
        $video->notice_id = $notice_id;
        $video->rendered = $rendered;
        $video->source = $source;
        $video->title = $title;
        $video->picpath = $picpath;
        $video->flashsrc = $flashsrc;
        $video->vid = $vid;
        $video_id = $video->insert();

        return $video_id;
    }
    
    function getVideo($video_id) {
    	$video = new Video();
        $video->id = $video_id;

         if ($video->find()) {
         	if($video->fetch()) {
         		return $video;
         	}
         }
    }
    
    function getUserVideo($user_id, $offset=0, $limit=20) {
       	$video = new Video();
       	
       	$video->whereAdd('notice_id in (select id from notice where user_id =' . $user_id . ')');
    	$video->orderBy('id desc');
    	
    	if (!is_null($offset)) {
			$video->limit($offset, $limit);
		}
       	$video->find();
    	
    	return $video;
    }
    
    function getVideoFromNotice($notice_id) {
    	$video = new Video();
        $video->notice_id = $notice_id;

         if ($video->find()) {
         	if($video->fetch()) {
         		return $video;
         	}
         }
    }
    
	function getVideoByVid($vid) {
    	$video = new Video();
        $video->vid = $vid;

         if ($video->find()) {
         	if($video->fetch()) {
         		return $video;
         	}
         }
    }
}
