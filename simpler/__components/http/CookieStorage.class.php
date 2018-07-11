<?php
/**
 *
 * Abstract class for storing the cookie files.
 * Part of the Cookie class.
 *
 * @package Simpler
 * @subpackage Http
 * 
 * 
 * ##############################################################################
 * # Other used classes (methods or vars) in this module:						#
 * # @see File{} | Cookie{} | Import{} (extends) | Bootstrap{} (extends)		#
 * # @uses empty(), put(), clean() | trash() | pathExists() | using()			#
 * # @var $path																	#
 * ##############################################################################
 *
 */


	namespace Simpler\Components\Http;
	use Simpler\Bootstrap;
	
	abstract class CookieStorage extends Bootstrap{
		/**
 		 *
		 * Stores the path to the file 
		 * user cookie storage.
 		 *
 		 * @var string
 		 *
 		 */
		private $StorageVisitorsPath;

		/**
 		 *
 		 * Stores paths to storage files.
 		 *
 		 * @var const
 		 *
 		 */
		CONST STORAGE_VISITORS_PATH 	= 'simpler/__storage/visitors';
		CONST STORAGE_ROOT_FILE 		= 'simpler/__storage/cookie.storage';


		/*
		 * 
		 * Assigns visitors cookies to the variable 
		 * path to the storage file.
		 * 
		 */
		public function __construct(){
			$this->StorageVisitorsPath = self::STORAGE_VISITORS_PATH.'/'.$this->using('user')->ip().'.storage';
		}


		//======================================================================
		// CHECK METHOD
		//======================================================================


		/**
		 *
		 * Checks whether the cookie file 
		 * has been saved to storage.
		 *
		 * @return bool
		 *
		 */
		protected function checkInStorage(string $cookie) :bool{
			// Equivalent $_COOKIE for storage.
			$cookies = $this->cookies();

			// Has the cookie been saved?
			foreach($cookies as $name) if($name === $cookie) return true;
			return false;
		}


		//private function 


		//======================================================================
		// GET METHODS
		//======================================================================
	

		/**
		 *
		 * Gets the file and drops its contents into the array, 
		 * then modifies it and creates a new array, 
		 * the key of which is the name of the cookie, 
		 * and the value of its data.
		 *
		 * @return array
		 *
		 */
		private function getDataStorage() :array{
			$this->pathExists($this->StorageVisitorsPath);
			$cookies = array();
			
			$file = @file($this->path, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
			$file = str_replace(array('[', ']'), '', $file); // CLear
			
			// Changes the structure of the file 
			// array and creates a new cookie 
			// name as the key.
			foreach($file as $key => $line){
				$pos = strpos($line, ';');

				$name = substr($line, 0, $pos);
				$content = substr($line, $pos + 1);
		
				$cookies[$name] = $content;
			}

			return $cookies;
		}


		/**
		 *
		 * The equivalent of $_COOKIE array
		 * for cookie storage.
		 *
		 * @return array|bool
		 *
		 */
		protected function cookies(){
			$this->pathExists(self::STORAGE_ROOT_FILE);
			return @file($this->path, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		}


		//======================================================================
		// STORAGE METHODS
		//======================================================================


		/**
		 *
		 * Gets a preview of all active cookies.
		 *
		 * @return string|bool
		 *
		 */
		protected function previewS(){
			$file = $this->using('file');
			return !$file->use($this->StorageVisitorsPath)->isEmpty() ? $file->content() : false;
		}


		/**
		 *
		 * Retrieves temporary data about a specific
		 * cookie file from the file.
		 *
		 * @param string $cookie - Cookie name
		 * @return array|void
		 * 
		 * @uses getDataStorage()
		 *
		 */
		protected function loadS(string $cookie){
			$data = $this->getDataStorage();
			if(array_key_exists($cookie, $data)) return explode(';', $data[$cookie]);

			/* Error: No cookie in storage. */
			else $this->report('Unfortunately, the cookie with the name <i>'.$cookie.'</i> is not in the storage!');
		}


		/**
		 *
		 * Retrieves the contents of the storage file, and then
		 * changes its contents, deleting the particular line.
		 *
		 * @param string|array $cookies - Cookie(s) name(s)
		 * @return void
		 * 
		 * @uses getDataStorage()
		 * 
		 */
		protected function shiftS($cookies) :void{
			$data = $this->getDataStorage();
			$content = '';
			$i = 0;

			/* Call to array $cookies argument. */
			if(is_array($cookies)){
				foreach($cookies as $key => $name){
					if(!array_key_exists($name, $data)) $content .= '['.$name.';'.$data[$name].']'.PHP_EOL;
					$i++;
				}
			}
			
			/* Call to string $cookies argument. */
			else {
				foreach($data as $name => $data){
					if($name !== $cookies) $content .= '['.$name.';'.$data.']'.PHP_EOL;
					$i++;
				}
			}

			// Shift content storage file.
			if($i > 0) $this->using('file')->newLine(false)->put($this->StorageVisitorsPath, $content);
		}


		//======================================================================
		// INSPECTION METHODS
		//======================================================================


		/**
		 *
		 * Checks whether cookies do not exist, if not,
		 * the storage file will be cleared.
		 *
		 * @return bool
		 * @uses cookies()
		 * 
		 */
		private function empties() :bool{
			$file = $this->using('file');
			$cookie = $this->using('cookie');
			
			// Calling the use method.
			$glob = $file->use(self::STORAGE_VISITORS_PATH.DIRECTORY_SEPARATOR.'*.storage');
			$user = $file->use($this->StorageVisitorsPath, true);

			// Equivalent $_COOKIE for storage.
			$storage = $this->cookies();

			// Checks whether the specific visitor's file is empty.
			$empty = $user->isEmpty();
			
			$exists = false;
			$empties = false;

			// When cookies exist
			if($_COOKIE){
				$cookies = $_COOKIE;
				
				// A cookie array without a session.
				if(isset($cookies[session_name()])) unset($cookies[session_name()]);
				if(count($cookies) > 0) $exists = true;

				// When cookies have been deleted.
				if(!$exists){
					if(!$empty) $user->clean();
					$empties = true;
				}

				// When the cookie storage file is empty.
				if(!$storage){
					$data = $this->getDataStorage();

					// Removes all cookie files associated with storage.
					foreach($cookies as $name => $val) if(array_key_exists($name, $data)) $cookie->use($name)->trash();

					// Removal of all visitors storage files.
					$glob->trash();
					$empties = true;
				}

				// When the visitor storage file is empty.
				if($empty && $storage){
					// Removes all cookie files associated with storage.
					foreach($storage as $name) if(array_key_exists($name, $cookies)) $cookie->use($name)->trash();
					$empties = true;
				}
			} else $empties = true;

			return $empties;
		}


		/**
		 *
		 * Checks the storage file to remove
		 * incorrect or non-existent rows (cookies).
		 *
		 * @return void
		 * @uses empties(), getDataStorage(), cookies()
		 * 
		 */
		protected function inspectorS() :void{
			if(!$this->empties()){
				$data = $this->getDataStorage();

				// Equivalent $_COOKIE for storage.
				$cookies = $this->cookies();

				$content = null;
				foreach($cookies as $name){
					if(array_key_exists($name, $data)) $content .= '['.$name.';'.$data[$name].']'.PHP_EOL;
					else $this->using('cookie')->use($name, true)->trash();
				}

				// Shift content storage file.
				if(!empty($content)) $this->using('file')->newLine(false)->put($this->StorageVisitorsPath, $content);
			}
		}


		//======================================================================
		// CLEAN METHOD
		//======================================================================


		/**
		 *
		 * Clears the data file of cookie 
		 * in storage file.
		 *
		 * @return bool
		 * 
		 */
		protected function cleanS() :bool{
			$file = $this->using('file');

			$file->use(self::STORAGE_ROOT_FILE)->clean();
			$file->use(self::STORAGE_VISITORS_PATH.DIRECTORY_SEPARATOR.'*.storage')->trash();

			$this->using('cookie')->trash();
			return $file->isEmpty();
		}
	}
