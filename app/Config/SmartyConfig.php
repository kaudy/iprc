<?php
namespace Config;

class SmartyConfig {
	public static $configDirs = [
		'templateDir' 	=> APPPATH . 'Views',
		'compileDir' 	=> APPPATH . 'Templates_c',
		'cacheDir'		=> APPPATH . 'Cache',
		'configDir'		=> APPPATH . 'Config',
		'pluginsDir'	=> APPPATH . 'ThirdParty/smarty/libs/plugins/'
	];

	public static $fileExtension = 'tpl';
}