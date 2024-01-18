<?php
namespace Config;

class SmartyConfig {
	public static $configDirs = [
		'templateDir' 	=> APPPATH . 'Views',
		'compileDir' 	=> APPPATH . 'Templates_c',
		'cacheDir'		=> APPPATH . 'Cache',
		'configDir'		=> APPPATH . 'Config'
	];

	public static $fileExtension = 'tpl';
}