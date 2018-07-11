<?php
/**
 * 
 * The proposed structure of the project 
 * for your website. Place the entire 
 * page in the 'website' directory and import 
 * its structure into the 'index.php' file. 
 * All errors/exceptions are also implemented here. 
 * In addition, when calling this page, you can 
 * reference the Simpler function without re-importing it.
 * 
 * !#The file name and the folder name 
 * can also be your own.#!
 * 
 * @author Kamil Staniec (sopskirk)
 * @copyright Simpler 2018 All rights reserved, made in Poland
 * @version 1.0 beta
 * @link https://github.com/sopskirk/simpler
 * @license MIT License (https://opensource.org/licenses/MIT)
 *
 * @package Simpler
 * 
 */


	// Thoroughly checks the type of function argument.
	declare(strict_types = 1);

	// Load Simpler
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, dirname(__FILE__).'/simpler/simpler.php');
	require $path;

	// Own error/exceptions handling.
	try{
		// Imports your webiste into the project.
		import('website/index.php');
	} catch(\Throwable $t){
		// Handling all errors/exceptions.
		report($t);
	}