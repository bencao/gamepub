<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

class ProfilesettingsHTMLTemplate extends SettingsHTMLTemplate 
{
	/**
     * Title of the page
     *
     * @return string Title of the page
     */

    function title()
    {
        return '修改个人信息';
    }
    
    function _showNickname() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'nickname', 'class' => 'label60'), '昵称');
    	
    	$this->element('input', array('type' => 'text', 
    					'class' => 'text200',
    					'name' => 'nickname', 
    					'id' => 'nickname',
    					'maxlength' => '12',
    					'value' => ($this->arg('nickname')) ? $this->arg('nickname') : $this->cur_user_profile->nickname));
    	$this->elementEnd('p');
    }
    
    function _showLocation() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'province', 'class' => 'label60'), '居住地');
    	
		$prov = ($this->arg('province')) ? $this->arg('province') : $this->cur_user_profile->province;
		$city = ($this->arg('city')) ? $this->arg('city') : $this->cur_user_profile->city;
		$dist = ($this->arg('district')) ? $this->arg('district') : $this->cur_user_profile->district;
		
		$this->element('input', array('id' => 'province', 'name' => 'province' , 'type' => 'hidden', 
						'value' => $prov));
		$this->element('input', array('id' => 'city','name' => 'city' , 'type' => 'hidden',
						'value' => $city));
		$this->element('input', array('id' => 'district', 'name' => 'district' ,'type' => 'hidden', 
						'value' => $dist));
		
		$this->element('input', array('id' => 'location', 'name' => 'location' ,'type' => 'text', 
						'value' => Profile::location($prov, $city, $dist), 'class' => 'text200'));
		
    	$this->elementEnd('p');
    }
    
    function _showSex() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'sex', 'class' => 'label60'), '性别');
    	
    	$this->elementStart('select', array('name' => 'sex', 'id' => 'sex'));
            
//        $this->option('', '保密', $this->cur_user_profile->sex);
        $this->option('M', '男', $this->cur_user_profile->sex);
        $this->option('F', '女', $this->cur_user_profile->sex);
            
        $this->elementEnd('select');

    	$this->elementEnd('p');
    }
    
    function _showBirthday() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'birthday', 'class' => 'label60'), '生日');
    	
    	$this->element('input', array('id' => 'birthday',
    					'type' => 'text', 
    					'name' => 'birthday', 
    					'class' => 'text200',
    					'value' => ($this->arg('birthday')) ? $this->arg('birthday') : $this->cur_user_profile->birthday));
    	
    	$this->elementEnd('p');
    }
    
    function _showHomepage() {
    	$this->elementStart('p', 'clearfix');
    	
    	$this->element('label', array('for' => 'homepage', 'class' => 'label60'), '博客地址');
    	
    	$this->element('input', array('class' => 'text200',
    					'id' => 'homepage',
    					'type' => 'text', 
    					'name' => 'homepage',
    					'maxlength' => '255', 
    					'value' => ($this->arg('homepage')) ? $this->arg('homepage') : $this->cur_user_profile->homepage));
    	
    	$this->elementEnd('p');
    }
    
    function _showBio() {
    	$this->elementStart('p', array('class' => 'clearfix', 'style' => 'height:80px;'));
    	
    	$this->element('label', array('for' => 'bio', 'class' => 'label60'), '简介');
    	
		$this->elementStart('textarea', array('id' => 'bio', 'name' => 'bio', 'cols' => '28', 'rows' => '3'));
		$this->text($this->arg('bio') ? $this->arg('bio') : $this->cur_user_profile->bio);
		$this->elementEnd('textarea');
		
    	$this->elementEnd('p');
    }
    
    function _showAutosubscribe() {
    	$this->elementStart('p', array('class' => 'clearfix', 'style' => 'padding-left:60px;height:24px;'));
    	
    	$this->checkbox('autosubscribe',
                            '当有人关注我时，自动关注他/她。',
                            ($this->arg('autosubscribe')) ?
                            $this->boolean('autosubscribe') : $this->cur_user_profile->autosubscribe);
                            
    	$this->elementEnd('p');
    }
    
    function _showSchool() {
    	$this->elementStart('p', 'clearfix school');

    	$this->element('label', array('for' => 'school', 'class' => 'label60'), '学校');
    	
    	$this->element('input', array('type' => 'text', 'name' => 'school', 'id' => 'school', 'class' => 'text200',
    				'value' => ($this->arg('school')) ? $this->arg('school') : $this->cur_user_profile->school));
    	
    	$this->elementEnd('p');
    	
    }
    
    function _showOccupation() {
    	$this->elementStart('p', 'clearfix occupation');
    	
    	$this->element('label', array('for' => 'occupation', 'class' => 'label60'), '行业');
    	
    	$this->element('input', array('type' => 'text', 'name' => 'occupation', 'id' => 'occupation', 'class' => 'text200',
    				'value' => ($this->arg('occupation')) ? $this->arg('occupation') : $this->cur_user_profile->occupation));
    	
    	$this->elementEnd('p');
    }
    
    function _showSharefavorites() {
    	$this->elementStart('p', array('class' => 'clearfix', 'style' => 'padding-left:60px;height:24px;'));
    	
    	$this->checkbox('sharefavorites',
                  '公开我的收藏',
                ($this->arg('sharefavorites')) ?
                   $this->boolean('sharefavorites') : $this->cur_user_profile->sharefavorites);
             
    	$this->elementEnd('p');
    }
    
    function _showSubmit() {
    	$this->element('input', array('type' => 'submit', 'class' => 'submit button76 green76', 'value' => '保存'));
    }
    
    function showSettingsTitle() {
    	return '个人设置';
    }
    
    function showSettingsInstruction() {
    	return '为让其他游友更容易了解您，让好友们容易的找到您，请更新真实的个人信息。';
    }
    
    function showSettingsContent() {
        
    	$this->tu->startFormBlock(array('method' => 'post',
                                           'id' => 'form_settings_profile',
                                           'class' => 'settings',
    										'style' => 'border:0;',
                                           'action' => common_path('settings/profile')), '修改个人资料');
    	
    	$this->elementStart('dl');
    	
    	$this->element('dt', null, ' ');
    	
    	$this->elementStart('dd');
    	
    	$this->_showNickname();
    	
//    	$this->_showGame();
    	
    	$this->_showLocation();
    	
    	$this->_showSex();
    	
    	$this->_showBirthday();
    	
    	$this->_showHomepage();
    	
    	$this->_showSchool();
    	
    	$this->_showOccupation();
    	
    	$this->_showBio();
    	
    	$this->_showAutosubscribe();
    	
    	$this->_showSharefavorites();
    	
    	$this->elementEnd('dd');
    	
    	$this->elementEnd('dl');
    	
    	$this->elementStart('div', 'op');
    	
    	$this->_showSubmit();
    	
    	$this->elementEnd('div');
    	
    	$this->tu->endFormBlock();
    	
    }
    
    function showScripts() {
    	parent::showScripts();
    	$this->script('js/jquery.ui.datepicker.min.js');
		$this->script('js/ui.datepicker-zh-CN.js');
		$this->script('js/lshai_cityschooldata.js');
		$this->script('js/lshai_cityschoolselect.js');
		$this->script('js/lshai_occupation.js');
		$this->script('js/jquery.validate.min.js');
		$this->script('js/lshai_profilesettings.js');
    	
    }
}

?>