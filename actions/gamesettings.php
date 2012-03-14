<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/validatetool.php';

/**
 * Change profile settings
 *
 * @category Settings
 * @package  LShai
 */

class GamesettingsAction extends SettingsAction
{
    function getViewName() {
    	return 'GamesettingsHTMLTemplate';
    }
	
	function _validate() {
		$this->game_id = $this->arg('game');
        if (! in_array($this->game_id, Game::listAllGameIds())) {
        	$this->errorMessage = '不存在该游戏';
        	return false;
        }
        
		$this->game_big_zone = $this->trimmed('game_big_zone');
		$this->game_server = $this->trimmed('game_server');
		
		if (empty($this->game_big_zone) 
			|| ! isNum($this->game_big_zone)
			|| empty($this->game_server)
			|| ! isNum($this->game_server)
			|| ! Game::isValidServer($this->game_id, $this->game_big_zone, $this->game_server)) {
			$this->errorMessage = '选择的服务器不存在';
			return false;
		}
		
		$this->game_job = $this->trimmed('game_job');
		if (empty($this->game_job)
			|| ! Game::isValidJob($this->game_id, $this->game_job)) {
			$this->errorMessage = '选择的' . JOB_NAME() . '不存在';
			return false;
		}
		
		$this->game_org = $this->trimmed('game_org');
		
		return true;
	}
    
    function _updateUser() {
    	if ($this->cur_user->game_id == $this->game_id
    		&& $this->cur_user->game_server_id == $this->game_server
    		&& $this->cur_user->game_job == $this->game_job
    		&& $this->cur_user->game_org == $this->game_org) {
    		return true;
    	}
    	$original = clone($this->cur_user);
    	$this->cur_user->game_id = $this->game_id;
        $this->cur_user->game_server_id = $this->game_server;
        $this->cur_user->game_job = $this->game_job;
        $this->cur_user->game_org = $this->game_org;
        $game_server = $this->cur_user->getGameServer();
       	$game_server->blowGameServerUserCount();
        return $this->cur_user->update($original);
    }
    
    function _updateProfile() {
    	$orig_profile = clone($this->cur_user_profile);       
        $this->cur_user_profile->game_id = $this->cur_user->game_id;
        $this->cur_user_profile->game_server_id = $this->cur_user->game_server_id;
        $this->cur_user_profile->game_job = $this->cur_user->game_job;
        $this->cur_user_profile->game_org = $this->cur_user->game_org;         
        return $this->cur_user_profile->update($orig_profile);
    }
    
    function handlePost()
    {
        if (Event::handle('StartGameProfileSaveForm', array($this))) {
            if (! $this->_validate()) {
            	$this->showForm($this->errorMessage);
            	return;
            }

            // start transaction
            $this->cur_user->query('BEGIN');

            // try update
            if (! $this->_updateUser()
            	|| ! $this->_updateProfile()) {
            	$this->showForm('抱歉，因技术问题暂时无法更新');
            	return;
            }
            
            $this->cur_user->updateCompleteness();
            
            // TODO: 清理缓存
            
            // end transaction
            $this->cur_user->query('COMMIT');

            $this->showForm('设置已保存', true);
            
            Event::handle('EndGameProfileSaveForm', array($this));
        }
    }
    
    function showForm($msg=null, $success=false) {
    	if ($this->trimmed('game')) {
    		$this->game = Game::staticGet('id', $this->trimmed('game'));
    		$this->game_big_zone = Game_big_zone::staticGet('id', $this->trimmed('game_big_zone'));
    		$this->game_server = Game_server::staticGet('id', $this->trimmed('game_server'));
    		$this->game_job = $this->trimmed('game_job');
    		$this->game_org = $this->trimmed('game_org');
    	} else {
    		$this->game = $this->owner_game;
    		$this->game_big_zone = Game_big_zone::staticGet('id', $this->owner_game_server->game_big_zone_id);
    		$this->game_server = $this->owner_game_server;
    		$this->game_job = $this->owner->game_job;
    		$this->game_org = $this->owner->game_org;
    	}
    	
    	$this->game_big_zones = $this->game ? $this->game->getBigZones() : null;
    	$this->game_servers = $this->game && $this->game_big_zone ? $this->game->getServers($this->game_big_zone->id) : null;
		$this->game_jobs = $this->game ? $this->game->getJobs() : null;
		
		$this->addPassVariable('game', $this->game);
		$this->addPassVariable('game_big_zone', $this->game_big_zone);
		$this->addPassVariable('game_server', $this->game_server);
		$this->addPassVariable('game_job', $this->game_job);
		$this->addPassVariable('game_org', $this->game_org);
		$this->addPassVariable('game_big_zones', $this->game_big_zones);
		$this->addPassVariable('game_servers', $this->game_servers);
		$this->addPassVariable('game_jobs', $this->game_jobs);
		$this->addPassVariable('hotgames', Game::listHots());
		parent::showForm($msg, $success);
    }
}

?>