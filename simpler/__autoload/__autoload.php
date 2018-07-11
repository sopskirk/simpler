<?php
/**
 *
 * This function is used to load components classes
 * located under a given namespace prefix.
 *
 * @package Simpler
 * @subpackage Autoload
 *
 */


	spl_autoload_register(function(string $class){
		// What prefixes should be recognized?
		$prefixes = [
			'Simpler\Components\Facades' 	=> [dirname(__FILE__).'/../__components/facades'],
			'Simpler\Components\Http' 		=> [dirname(__FILE__).'/../__components/http'],
			'Simpler\Components\Init' 		=> [dirname(__FILE__).'/../__components/init'],
			'Simpler\Components\Put' 		=> [dirname(__FILE__).'/../__components/put'],
			'Simpler\Components\Randval'	=> [dirname(__FILE__).'/../__components/randval'],
			'Simpler\Components\Reports' 	=> [dirname(__FILE__).'/../__components/reports'],
			'Simpler\Components\Safety' 	=> [dirname(__FILE__).'/../__components/safety'],
			'Simpler\Components\User' 		=> [dirname(__FILE__).'/../__components/user'],
			'Simpler\Components' 			=> [dirname(__FILE__).'/../__components'],
			'Simpler\Extensions'			=> [dirname(__FILE__).'/../__extensions'],
			'Simpler' 						=> [dirname(__FILE__).'/../__bootstrap']
		];

		// Go through the prefixes.
		foreach($prefixes as $prefix => $dirs){
			// Does the requested class match the namespace prefix?
			$prefix_len = strlen($prefix);
			if(substr($class, 0, $prefix_len) !== $prefix) continue;

			// Strip the prefix off the class.
			$class = substr($class, $prefix_len);

			// A partial filename.
			$part = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, preg_replace('#/+#', '/', trim($class))).'.class.php';

			// Go through the directories to find classes.
			foreach($dirs as $dir){
				$dir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, preg_replace('#/+#', '/', trim($dir)));
				$file = $dir.DIRECTORY_SEPARATOR.$part;

				if(is_readable($file)){
					require $file;
					return;
				}
			}
		}
	});
