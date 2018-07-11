<?php
/**
 *
 * This file is a central file that 
 * loads all resources.
 * 
 * @package Simpler
 * 
 */


	// Simpler version
	DEFINE('__SIMPLER_VERSION__', '1.0 beta');

	// Autoload classes
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, dirname(__FILE__).'/__autoload/__autoload.php');
	require $path;

	// Create a Simpler bootstrap object.
	$boot = new Simpler\Bootstrap;

	// Apply of basic settings.
	$boot->setConfig();

	// Alternative Bootstrap class.
	$boot->import('simpler/__bootstrap/Bootstrap.php');

	// Own extension as function
	$boot->import('simpler/__extensions/*.php', '.class.php');
	unset($boot);