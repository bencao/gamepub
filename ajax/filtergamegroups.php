<?php 
if (!defined('SHAISHAI')) { exit(1); }

class FiltergamegroupsAction extends ShaiAction
{
	var $sameserver;
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->sameserver = $this->trimmed('sameserver');
		return true;
	}
    
    function handle($args)
    {
    	parent::handle($args);
    	
    	//多取一条判断是否有下一页
    	$offset = ($this->cur_page - 1) * GROUPS_PER_PAGE_GAME;
        $limit =  GROUPS_PER_PAGE_GAME;
    	
    	if ($this->sameserver) {
    		$total = User_group::getGameGroupsByServerCount($this->cur_user->game_server_id);
        	$groups_game = User_group::getGameGroupsByServer($this->cur_user->game_server_id, $offset, $limit);
        } else {
        	$total = User_group::getGameGroupsByGameCount($this->cur_user->game_id);
        	$groups_game = User_group::getGameGroupsByGame($this->cur_user->game_id, $offset, $limit);
        }
        
    	$stringer = new XMLStringer();
    	if ($groups_game->N) {
	    	$gl = new GroupsList($groups_game, $stringer);
            $gl->show();
	    	if ($this->cur_page > 1) {
	        	$prepage_href = common_path('ajax/filtergamegroups?page=' . $this->cur_page - 1);
	        	$stringer->element('a', array('class' => 'prevpage page' , 'href' => $prepage_href), '<<上一页');
	        } 
	    	if ($this->cur_page * GROUPS_PER_PAGE_GAME < $total) {
	    		$nextpage_href = common_path('ajax/filtergamegroups?page=' . $this->cur_page + 1);
	    		$stringer->element('a', array('class' => 'nextpage page', 'href' => $nextpage_href), '下一页>>');
	    	} 
	    } else {
	    	if ($this->sameserver) {
	    		$title = $this->cur_user->getGameServer()->name;
	    	} else {
	    		$title = $this->cur_user->getGame()->name;
	    	}
	    	$stringer->elementStart('div', 'instruction guide');
			$stringer->elementStart('p');
			$stringer->text($title . '现在还没有任何游戏' . GROUP_NAME() . '，还不快抢创'.$title .'第一游戏' . GROUP_NAME() . '？');
			$stringer->elementEnd('p');
			$stringer->elementEnd('div');
		}
    	
    	$this->showJsonResult(array('result' => 'true', 'groups' => $stringer->getString()));
    }
}

?>