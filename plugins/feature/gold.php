<?php

require_once INSTALLDIR . '/plugins/feature/ifeature.php';

class Feature_Gold implements IFeature {
	var $clazz = 'gold';
	
	public function showHTML($out) {
		$out->raw('<div id="feature_new" cz="' . $this->clazz . '"><a href="#" class="close"></a><a href="/doc/help/usergrade#future_use" target="_blank" class="follow">更多用途</a></div>');
	}
	
	public function showStylesheets($out) {
		$out->cssLink('plugins/feature/css/gd.css');
	}
	
	public function showScripts($out) {
	}
}