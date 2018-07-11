<?php
/**
 * 
 * The functions in this file are instances 
 * of the class methods that will 
 * be used most often.
 * 
 * @package Simpler
 * @subpackage Bootstrap
 * 
 */


    /**
     * 
     * Instance of the using method.
     * 
     * @param string $component - The name of the component used
     * @return object
     * 
     */
    function using(string $component) :object{
        $boot = new Simpler\Bootstrap;
        return $boot->using($component);
    }


    /**
     * 
     * Instance of the import method.
     * 
     * @param string|array $path    - Filename or path to file
     * @param bool $isReturn	    - Is there anything to return for?
     * @param string $omission		- Files that will be omitted in the glob
     * 
     * @return object
     * 
     */
    function import($path, bool $isReturn = false, string $omission = ''){
        $file = new Simpler\Components\Import;
        return $file->import($path, $isReturn, $omission);
    }


    /**
     * 
     * Instance of the report method.
     * 
     * @param string|object $err - Error message or object
     * @return object
     * 
     */
    function report($err){
        $exc = new Simpler\Components\Reports\Reportable;
        return $exc->report($err);
    }