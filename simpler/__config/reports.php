<?php
/**
 *
 * Settings regarding error reports.
 *
 * @package Simpler
 * @subpackage Config
 *
 */


    return array(
        /**
         * 
         * Sets the appropriate version of error reports.
         * Versions to choose from:
         * 
         * [private] - All error information is displayed
         * in the report. In addition, the private
         * version is the default version (dev phase),
         * 
         * [protected] - Only the basic information about
         * a specific error (no tables) is displayed
         * in the report (test phase),
         * 
         * [public] - Displays only the Unique Report 
         * Identifier (final phase).
         * 
         * #!Each error goes to the reports.log file
         * irrelevant to the report display versions!#
         * 
         */
       'visibility'     => 'private',


       /**
        * 
        * The time after which the reports.log
        * file will be automatically cleared.
        *
        */
       'auto_clean'     => '12 hours',

       
       /**
        * 
        * The type to use when drawing
        * the Report Identifier:
        *
        * [string]  - only uppercase and lowercase letters,
        * [int]     - only number,
        * [mixed]   - uppercase, lowercase letters and number.
        *
        */
       'rand_range'     => 'mixed'
    );