<?php
/**
 *
 * Settings for creating cookies.
 *
 * @package Simpler
 * @subpackage Config
 *
 */


	return array(
		/**
         * 
         * The path on the server in which the
         * cookie will be available on.
         * 
         */
		'PATH' 		=> '/',


		/**
         * 
         * The (sub)domain that the cookie is
         * available to (recommended).
         * 
         */
		'DOMAIN'	=> '',


		/**
         * 
         * Indicates that the cookie should only be transmitted
         * over a secure HTTPS connection from the client.
         * 
         */
		'SECURE'	=> false,

		
		/**
         * 
         * When TRUE the cookie will be made accessible
         * only through the HTTP protocol.
         * 
         */
		'HTTPONLY'	=> true
	);
