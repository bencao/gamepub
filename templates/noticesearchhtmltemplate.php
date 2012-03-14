<?php
/**
 * Notice search template class.
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class NoticesearchHTMLTemplate extends SearchHTMLTemplate
{
	var $ct;
	var $srid;
	
    function title()
    {
        return $this->q == '' ? '搜索消息' : ('含有“' . $this->q . '”的消息');
    }
    
    function show($args)
    {
    	$this->ct = $args['notice_ct'];
    	$this->srid = array_key_exists('srid', $args) ? $args['srid'] : 0;
    	parent::show($args);
    }
    
    function showSearchResults() {
//    	common_debug($this->resultset);
    	if (count($this->resultset) > 0) {
    		$terms = preg_split('/[\s,]+/', $this->q);
	        $nl = new SearchNoticeList($this->resultset, $this, $terms, $this->srid);
	        $this->cnt = $nl->show();
//	        $this->resultset->free();
    	} else {
	        $this->showEmptyList();
    	}
    }
    
    function showPagination() {
//		$this->numpagination($this->cur_page > 1, $this->cnt > NOTICES_PER_PAGE,
//                          $this->cur_page, 'noticesearch', array('q' => $this->q, 'ct' => $this->ct, $this->total));              
        $this->numpagination($this->total_count, 'noticesearch', array(), 
				array('q' => $this->q, 'ct' => $this->ct), NOTICES_PER_PAGE);
    }
    
	function searchAction() {
		return common_local_url('noticesearch');
	}
    
	function showMoreSearchItems() {
//    	$this->elementStart('li');
//    	$this->element('a', array('class' => 'search', 'href' => common_local_url('subjectsearch')), '跟“' . $this->q . '”有关的话题');
//    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('wendasearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的问答');
    	$this->elementEnd('li');
		$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('peoplesearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的玩家');
    	$this->elementEnd('li');
    	$this->elementStart('li');
    	$this->element('a', array('class' => 'search', 'href' => common_local_url('groupsearch', null, array('q' => $this->q))), '跟“' . $this->q . '”有关的' . GROUP_NAME());
    	$this->elementEnd('li');
    	
    }
    
	function showSearchOptions() {
		$this->elementStart('p', 'options');
	    	
    	if ($this->ct == '0') {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '0', 'id' => 'allct', 'checked' => 'checked', 'class' => 'radio'));
        } else {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '0', 'id' => 'allct', 'class' => 'radio'));
        }
        $this->element('label', array('for' => 'allct'), '全部');
    	if ($this->ct == '2') {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '2', 'id' => 'sndct', 'checked' => 'checked', 'class' => 'radio'));
        } else {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '2', 'id' => 'sndct', 'class' => 'radio'));
        }
        $this->element('label', array('for' => 'sndct'), '音乐');
    	if ($this->ct == '3') {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '3', 'id' => 'vdoct', 'checked' => 'checked', 'class' => 'radio'));
        } else {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '3', 'id' => 'vdoct', 'class' => 'radio'));
        }
        $this->element('label', array('for' => 'vdoct'), '视频');
    	if ($this->ct == '1') {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '1', 'id' => 'txtct', 'checked' => 'checked', 'class' => 'radio'));
        } else {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '1', 'id' => 'txtct', 'class' => 'radio'));
        }
        
        $this->element('label', array('for' => 'txtct'), '文字');
    	if ($this->ct == '4') {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '4', 'id' => 'picct', 'checked' => 'checked', 'class' => 'radio'));
        } else {
        	$this->element('input', array('type' => 'radio', 'name' => 'ct', 'value' => '4', 'id' => 'picct', 'class' => 'radio'));
        }
        $this->element('label', array('for' => 'picct'), '图片');
		$this->elementEnd('p');
	}
}

class SearchNoticeList extends NoticeList {
    function __construct($notice, $out=null, $terms, $srid)
    {
        parent::__construct($notice, $out);
        $this->terms = $terms;
        $this->srid = $srid;
    }
    
	function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices'));
        $cnt = 0;
		$count = count($this->notice);
		
        for ($i = 0; $i < $count; $i ++) {
            $cnt++;
           
            if ($cnt > NOTICES_PER_PAGE) {
                break;
            }
            $item = $this->newListItem($this->notice[$i]);
            $item->show();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }

    function newListItem($notice)
    {
        return new SearchNoticeListItem($notice, $this->out, $this->terms, $this->srid);
    }
}

class SearchNoticeListItem extends NoticeListItem {
    function __construct($notice, $out=null, $terms, $srid)
    {
        parent::__construct($notice, $out);
        $this->terms = $terms;
        $this->srid = $srid;
    }
    
	function showImage() {
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	
    	$avatar = $this->profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
        // 添加srid和notice_id，以便跟踪
    	$attrs = array('href' => common_local_url('showstream', array('uname' => $this->profile->uname), 
    		array('s' => $this->srid, 'n' => $this->notice->id)));
        if (!empty($this->profile->nickname)) {
            $attrs['title'] = $this->profile->nickname . ' (' . $this->profile->uname . ') ';
        }
    	$this->out->elementStart('a', $attrs);
    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $this->profile->id, $this->profile->sex),
                                         'alt' => $this->profile->uname));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('div');
    }
    
	function show()
    {
        $this->showStart();
        
        $this->showImage();
               
        $this->out->elementStart('h3'); 
        $this->out->element('a', array('href' => common_local_url('showstream', array('uname' => $this->profile->uname), array('s' => $this->srid, 'n' => $this->notice->id)),
        			'class' => 'name'), $this->profile->nickname);
        if ($this->profile->is_vip) {
        	$this->out->element('strong', null, 'V');
        }
        $this->out->elementEnd('h3');
        
        $this->showNoticeInfo();
        $this->showRoot();
        $this->showNoticeBar();
		$this->showContext();
		
        $this->showEnd();
    }
    
	function showNoticeInfo()
	{
    	$this->out->elementStart('p', array('class' => 'content'));
    	preg_match('/^<span>(?<desc>.*)<\/span>(?<html>.*)$/siU', $this->notice->rendered, $matches);
    	$this->out->raw('<span>' . $this->domHighlight($matches['desc']) . '</span>' . $matches['html']);
//		$this->out->raw($this->notice->rendered);
    	$this->out->elementEnd('p');                                	
    }

    /**
     * Highlist query terms
     *
     * @param string $text  notice text
     * @param array  $terms terms to highlight
     *
     * @return void
     */
    function highlight($text)
    {
        /* Highligh search terms */
    	
    	$text = htmlspecialchars($text);
    	
        $options = implode('|', array_map('preg_quote', array_map('htmlspecialchars', $this->terms),
                                                            array_fill(0, sizeof($this->terms), '/')));
        $pattern = "/($options)/i";
        $result  = preg_replace($pattern, '<strong style="font-weight:bold;color:#6E7F02;">\\1</strong>', $text);
        
        /* Remove highlighting from inside links, loop incase multiple highlights in links */
//        $pattern = '/((href|src|link|title|class|target|rel|style)="[^"]*)<em style="font-weight:bold;font-style:normal;color:#00f;">('.$options.')<\/em>([^"]*")/iU';
//        do {
//            $result = preg_replace($pattern, '\\1\\3\\4', $result, -1, $count);
//        } while ($count);
        return $result;
    }
    
    var $textNodes;
    
    function domHighlight($xml) {
    	
    	$this->textNodes = array();

		$document = new DOMDocument();
		$document->loadXML('<html>' . $xml . '</html>');
		
    	
		$this->recurAdd($document->documentElement);

		foreach ($this->textNodes as $e) {
			$f = $e->ownerDocument->createDocumentFragment();
			$f->appendXML($this->highlight($e->nodeValue));
			$e->parentNode->replaceChild($f, $e);
		}
		$html = $document->saveXML();
		if(preg_match('/.*<html>(?<cont>.*)<\/html>.*/si', $html, $m))
			return $m['cont'];
		else
			return null;
    }
    
	function recurAdd($e) {
		if (! $e) {
			return;
		}
		if ($e->nodeName == '#text') {
			$this->textNodes[] = $e;
		}
		if ($e->hasChildNodes()) {
			foreach ($e->childNodes as $ec) {
				$this->recurAdd($ec);
			}
		}
	}
}