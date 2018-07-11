<?php
/**
 *
 * Pattern settings for RegexIterator class.
 *
 * @package Simpler
 * @subpackage Config
 *
 */


    /**
     * 
     * The keys of the array are individual names
     * of modules (classes).
     * 
     * #!THE BASE FORM MUST BE THE SAME!#
     * 
     * @return array
     * 
     */
    return array(
        /**
         * 
         * The default pattern (extensions) for the
         * RegexIterator class to import files.
         * 
         */
        'import'    => '/\.(html|php|phtml)*$/i',


        /**
         * 
         * The default pattern (extensions) for the RegexIterator
         * class for loading and creating files.
         * 
         * #!THE STORAGE AND LOG EXTENSION MUST BE IN THE PATTERN!!#
         * 
         */
        'file'      => '/\.(storage|log|txt|ini)*$/i',


        /**
         * 
         * #Default pattern (extensions) for the RegexIterator
         * class for loading resources (photos, css and js).
         * 
         */
        'assets'    => '/\.(jpg|jpeg|svg|png|bmp|gif|css|js)*$/i'
    );