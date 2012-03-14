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

class ProfilesettingsAction extends SettingsAction
{
    function getViewName() {
    	return 'ProfilesettingsHTMLTemplate';
    }
	
    function _validateForm() {
    	
        $this->homepage = $this->trimmed('homepage');
        if (! empty($this->homepage) 
        	&& ! isValidURL($this->homepage)) {
            $this->errorMessage = '主页URL无效。';
            return false;
        }
        
        $this->nickname = $this->trimmed('nickname');
        if (! isValidLength($this->nickname, 1, 12)) {
        	$this->errorMessage = '昵称长度应在1~12个字之间。';
            return false;
        }
        
        $this->bio = $this->trimmed('bio');
        if (! empty($this->bio) 
        	&& ! isValidLength($this->bio, 0, 140)) {
        	$this->errorMessage = '简介长度应在0~140个字之间。';
            return false;
        }
        
        $this->province = $this->trimmed('province');
        if (! empty($this->province) 
        	&& ! isValidLength($this->province, 1, 8)) {
        	$this->errorMessage = '省份长度应在1~8个字之间。';
            return false;
        }

        $this->city = $this->trimmed('city');
        if (! empty($this->city) 
        	&& ! isValidLength($this->city, 1, 12)) {
        	$this->errorMessage = '城市长度应在1~12个字之间。';
            return false;
        }
        
        $this->district = $this->trimmed('district');
        if (! empty($this->district) 
        	&&! isValidLength($this->district, 1, 12)) {
            $this->errorMessage = '区域的长度应在1~12个字之间';
            return false;
        }
        
        $this->sex = $this->trimmed('sex');
        if (! isValidSex($this->sex)) {
            $this->errorMessage = '性别不正确';
            return false;
        }
        
        $this->birthday = $this->trimmed('birthday');
        if (! empty($this->birthday) 
        	&& ! isValidDate($this->birthday)) {
            $this->errorMessage = '生日格式不正确，请参考以下格式：1989-01-08';
            return false;
        }
        
        $this->school = $this->arg('school');
        // XXX : school check
        
        $this->occupation = $this->arg('occupation');
        // XXX : occupation check
        
        $this->autosubscribe = $this->boolean('autosubscribe');
        $this->sharefavorites = $this->boolean('sharefavorites');
        
        return true;
    }
    
    function _updateUser() {
    	if ($this->cur_user->nickname == $this->nickname) {
//    		&& $this->cur_user->game_id == $this->game_id) {
    		return true;
    	}
    	$original = clone($this->cur_user);
        $this->cur_user->nickname = $this->nickname;
//        if ($this->cur_user->game_id != $this->game_id) {
//        	$newgame = Game::staticGet('id', $this->game_id);
//        	$bigzones = $newgame->getBigZoneIds();
//        	$serversInFirstBigZone = $newgame->getServerIds($bigzones[0]);
//        	$jobs = $newgame->getJobs();
//        	// if change game, reset game server
//        	$this->cur_user->game_server_id = $serversInFirstBigZone[0];
//        	$this->cur_user->game_job = $jobs[0];
//        	$this->cur_user->game_org = '';
//        }
//        $this->cur_user->game_id = $this->game_id;
        return $this->cur_user->update($original);
    }
    
    function _updateProfile() {
        $orig_profile = clone($this->cur_user_profile);
            
        $this->cur_user_profile->nickname = $this->nickname;
        $this->cur_user_profile->homepage = $this->homepage;
        $this->cur_user_profile->bio = $this->bio;
        $this->cur_user_profile->province = $this->province;
        $this->cur_user_profile->city = $this->city;
        $this->cur_user_profile->district = $this->district;
        $this->cur_user_profile->location = Profile::location($this->province, $this->city, $this->district);
        $this->cur_user_profile->sex = $this->sex;
        $this->cur_user_profile->birthday = $this->birthday;
        $this->cur_user_profile->school = $this->school;
        $this->cur_user_profile->occupation = $this->occupation;
        
        if ($this->cur_user_profile->sharefavorites != $this->sharefavorites) {
        	$this->cur_user_profile->sharefavorites = $this->sharefavorites;
        }
        if ($this->cur_user_profile->autosubscribe != $this->autosubscribe) {
        	$this->cur_user_profile->autosubscribe = $this->autosubscribe;
        }
        
//        $this->cur_user_profile->game_id = $this->cur_user->game_id;
//        $this->cur_user_profile->game_server_id = $this->cur_user->game_server_id;
//        $this->cur_user_profile->game_job = $this->cur_user->game_job;
//        $this->cur_user_profile->game_org = $this->cur_user->game_org;
            
        return $this->cur_user_profile->update($orig_profile);
    }
    
    function handlePost()
    {
        if (Event::handle('StartProfileSaveForm', array($this))) {
            if (! $this->_validateForm()) {
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
            
            Event::handle('EndProfileSaveForm', array($this));
        }
    }
    
//    function showForm($msg=null, $success=false) {
//    	$this->addPassVariable('allgames', Game::listAll());
//    	parent::showForm($msg, $success);
//    }
}
