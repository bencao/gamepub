<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GameexperiencesHTMLTemplate extends GamethreecolumnHTMLTemplate
{
	function title()
    {
    	return $this->cur_game->name . '经验';
    }
	
	function showContent() {
		$this->elementStart('h2');
    	$this->text('游戏经验');
    	$this->elementStart('span');
    	$this->text('-- ' . $this->cur_game->name . '经验交流消息');
    	$this->elementEnd('span');
    	$this->elementEnd('h2');
    	
		$this->showExpelist();
	}
	
	function showExpelist() {
		$notice = $this->args['notice'];
		if ($notice->N > 0) {
			$nl = new ShowgameNoticeList($notice, $this);
	        $cnt = $nl->show();
	        $this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE, $this->cur_page, 
	                     'gameexperiences', array('gameid' => $this->cur_game->id)); 
		} else {
			$this->showEmptyList();
		}
	}
    
 	function showEmptyList()
 	{
 		$message = '这是' . $this->cur_game->name . '经验交流消息列表， 但是还没有人发表过任何消息。';
 		$this->tu->showEmptyListBlock(common_markup_to_html($message));
    }
    
	function showPageNotices($args) {
        $this->args = $args;
        $this->cur_game = $args['cur_game'];
    		
        $view = TemplateFactory::get('JsonTemplate');
        $view->init_document();
        
        $notice = $this->args['notice'];
        if ($notice->N > 0) {
        	$xs = new XMLStringer();
        	$nl = new ShowgameNoticeList($notice, $xs);
	        $cnt = $nl->show();
        	$xs1 = new XMLStringer();
       		if ($cnt > NOTICES_PER_PAGE) {
	        	$xs1->element('a', array('href' => common_local_url('gameexperiences', array('gameid' => $this->cur_game->id), array('page' => $this->args['page'] + 1)),
	                                   'id' => 'notice_more', 'rel' => 'nofollow'));
        	}
        	       	
        	$resultArray = array('result' => 'true', 'notices' => $xs->getString(), 'pg' => $xs1->getString()); 	
        } else {
        	$resultArray = array('result' => 'false');
        }

	    $view->show_json_objects($resultArray);
        $view->end_document();
    }
}
?>