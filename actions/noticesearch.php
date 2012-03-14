<?php
/**
 * Notice search action class.
 *
 * PHP version 5
 *
 * @category Action
 * @package  ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Notice search action class.
 *
 * @category Action
 * @package  ShaiShai
 */
class NoticesearchAction extends SearchAction
{
	var $ct;
	
	var $memcached;
	
	var $memcachedKey;
	
	var $orderedIdList;
	
	function prepare($args)
	{
		if (! parent::prepare($args)) {return false;}
		$this->ct = $this->trimmed('ct', '0');
		if (! is_numeric($this->ct)) {
			$this->clientError('无效的查询参数ct');
			return false;
		}
    	$this->addPassVariable('notice_ct', $this->ct);
		return true;
	}
	
	function doSearch($args) {
	 	$this->memcached = common_memcache();
	
		if (! empty($this->memcached)) {
			$this->memcachedKey = hash(HASH_ALGO, $this->q) . ':' . $this->ct;
			
			$totalkey = common_cache_key('noticesearch:total:' . $this->memcachedKey);
			$totalResult = @$this->memcached->get($totalkey);
	
			if (empty($totalResult)) {
				$this->_doSearch($args);
			} else {
				// 缓存命中，直接用缓存输出
				$this->total = $totalResult;
				
				$orderedListKey = common_cache_key('noticesearch:ol:' . $this->memcachedKey);
				$this->orderedIdList = @$this->memcached->get($orderedListKey);
				
				$offset = ($this->cur_page - 1) * NOTICES_PER_PAGE;
					
				$resultNotices = array();
				for ($i = $offset; $i <= $offset + NOTICES_PER_PAGE && $i < $this->total; $i ++) {
					$resultNotices[] = Notice::staticGet('id', $this->orderedIdList[$i]);
				}
		      	
		      	$this->resultset = $resultNotices;
			}
		} else {
			$this->_doSearch($args);
		}
		
		// 保留搜索日志
		$this->srid = Search_request::saveNew(0, $this->q, $this->cur_user ? $this->cur_user->id : null);
		
		$this->addPassVariable('srid', $this->srid);
		
		return true;
	}
	
    function _doSearch($args)
    {
    	if (common_config('site', 'yunpingsearch')) {
	    	require_once INSTALLDIR . '/lib/ShaiNoticeSearchEngine.php';
	        $engine = new ShaiNoticeSearchEngine();
	        // 缓存版
	        $ids = $engine->search($this->q);
    	} else {
    		$ids = false;
    	}
        
        $offset = ($this->cur_page - 1) * NOTICES_PER_PAGE;
        
        if (! $ids) {
        	// fail connect to yunping search, use database search
        	// 连接失败
        	$queryString = '(';
        	$keywords = common_tokenize($this->q);
        	foreach($keywords as $v){
				$queryString .= sprintf('rendered LIKE "%%%1$s%%" OR ', addslashes($v));
			}
			$queryString .= ' 1=0) AND topic_type <> 4';
			if ($this->ct != '0') {
				$queryString .= ' AND content_type = ' . $this->ct;
			}
			$notice = new Notice();
			$notice->whereAdd($queryString);
			$notice->whereAdd('is_banned = 0');
			$notice->orderBy('created desc');
			$notice->limit($offset, NOTICES_PER_PAGE + 1);
			$notice->find();
			
			$this->total = $notice->count();
			
			$resultNotices = array();
			
			$fetchedCount = 0;
			while ($notice->fetch()) {
				$fetchedCount ++;
				if ($fetchedCount > NOTICES_PER_PAGE + 1) {
					break;
				}
				$resultNotices[] = clone($notice);
			}
	      	
	      	$this->resultset = $resultNotices;
        } else {
        	// success connect to yunping search
	    	$allIdArray = explode(' ', trim($ids));
	    	
	    	if ($this->ct == '0') {
	    		// 不限制内容，直接用云平搜索的返回id进行排序
	    		
	    		// 跳过内容筛选，直接返回
	    		$this->total = count($allIdArray);
	    		$this->orderedIdList = $allIdArray;
	    		
	    		// 根据页号取前面的
				$resultNotices = array();
				for ($i = $offset; $i <= $offset + NOTICES_PER_PAGE && $i < $this->total; $i ++) {
					$aNotice = Notice::staticGet('id', $this->orderedIdList[$i]);
					if ($aNotice) {
						$resultNotices[] = $aNotice;
					}
				}
		      	
		      	$this->resultset = $resultNotices;
	    	} else {
		    	// 根据内容进行筛选
		    	$notice = new Notice();
		    	$notice->selectAdd();
		    	$notice->selectAdd('id');
		    	$notice->whereAdd('id in (' . implode(',',  $allIdArray). ')');
		    	$notice->whereAdd('content_type = ' . $this->ct);
		    	$notice->whereAdd('is_banned = 0');
		    	
		    	$this->total = $notice->count();
		    	
		    	$notice->find();
		    	
		    	// 数据库返回的id序列是不符合搜索相关度和时间等方面的排序要求的
		    	$unorderedIdList = array();
		    	while ($notice->fetch()) {
		    		$unorderedIdList[] = $notice->id;
		    	}
		    	
		    	// 根据云平搜索的结果，将无序的id列表有序化
		    	$this->orderedIdList = array();
		    	for ($i = 0, $t = count($allIdArray); $i < $t; $i ++) {
		    		if (in_array($allIdArray[$i], $unorderedIdList)) {
		    			$this->orderedIdList[] = $allIdArray[$i];
		    		}
		    	}
		    	
		    	// 根据页号取前面的
				$resultNotices = array();
				for ($i = $offset; $i <= $offset + NOTICES_PER_PAGE && $i < $this->total; $i ++) {
					$resultNotices[] = Notice::staticGet('id', $this->orderedIdList[$i]);
				}
		      	
		      	$this->resultset = $resultNotices;
	        }
	        
	        // 保存缓存，有效时间120秒，翻页时可以直接从memcached中取出排好序的列表展示
	        if (! empty($this->memcached)) {
	        	$this->memcached->set('noticesearch:total:' . $this->memcachedKey, $this->total, 0, 120);
		    	$this->memcached->set('noticesearch:ol:' . $this->memcachedKey, $this->orderedIdList, 0, 120);
	        }
	    }
    }
    
    function getViewName() {
    	return 'NoticesearchHTMLTemplate';
    }

}

?>