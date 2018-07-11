<?php
 /**
  *
  * Modified and improved function to import any files embedded
  * on the server is a central class to import resources.
  *
  * @package Simpler
  * @subpackage Import
  *
  *
  * #########################################################
  * # Other used classes (methods or vars) in this module:	#
  * # @see Reportable{} (extends)							#
  * # @uses report()										#
  * #########################################################
  *
  */


	namespace Simpler\Components;

	use Simpler\Components\Reports\Reportable;
	use RecursiveIteratorIterator;
	use RecursiveDirectoryIterator;
	use RegexIterator;

	class Import extends Reportable{
		/**
		 *
		 * Stores the absolute path to the file.
		 *
		 * @var string
		 *
		 */
		public $path;
		
		/**
		 *
		 * Stores the pattern determines what files can be
		 * imported or read (pattern in a different class).
		 *
		 * @var string
		 *
		 */
		protected static $RegexPattern;

		/**
		 *
		 * Stores the current setting for
		 * the RegexIterator class pattern
		 *
		 * @var array
		 *
		 */
		protected static $regex = array();

		/**
 		 *
		 * Stores the path to the configuration 
		 * file with patterns.
 		 *
 		 * @var const
 		 *
 		 */
		CONST CONFIG_FILE = 'simpler/__config/regex.php';


		//======================================================================
		// CONFIG METHOD
		//======================================================================


		/**
		 *
         * Gets patterns for the RegexIterator class
		 * from the configuration file.
		 *
		 * @uses import()
		 *
         */
		protected function setConfigImport(){
			self::$regex = $this->import(self::CONFIG_FILE, true);
		}


		//======================================================================
		// PATH METHODS
		//======================================================================


		/**
		 *
		 * Filters the path to the file given as the method argument.
		 *
		 * @param string $path - Filename or path to file
		 * @return string
		 *
		 */
		protected function filterPath(string $path) :string{
			$path = str_replace(array('./', '.\/'), '', $path);
			return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, preg_replace('#(/|\/)+#', DIRECTORY_SEPARATOR, trim($path)));
		}


		/**
		 *
		 * When creating a new file -> class File{}
		 *
		 * @param string $path - Filename or path to file
		 * @return bool
		 * 
		 * @uses filterPath()
		 *
		 */
		private function getPathDir(string $path) :bool{
			$this->path = null; // Bugfix
			$path = $this->filterPath($path);

			// Gets a list of directories and files.
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'.DIRECTORY_SEPARATOR));
			$files = new RegexIterator($iterator, self::$RegexPattern);

			// When the file is not in the main directory.
			if(dirname($path) !== '.'){
				foreach($files as $file){
					if(strpos($file, dirname($path)) !== false){
						$this->path = $file->getRealpath().DIRECTORY_SEPARATOR.basename($path);
						break;
					}
				}
			} 
			
			// When the file is in the main directory.
			else $this->path = $this->filterPath($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.basename($path);

			// Did you find the file?
			return !is_null($this->path);
		}


		/**
		 *
		 * Creates an absolute path to the file and
		 * modifies it accordingly
		 *
		 * @param string $path - Filename or path to file
		 * @return bool
		 * 
		 * @uses filterPath()
		 * @author https://stackoverflow.com/a/49482302/8061968 and sopskirk
		 *
		 */
		private function getPathFile(string $path) :bool{
			$data = array();

			$this->path = null; // Bugfix
			$path = $this->filterPath($path);

			// Gets a list of directories and files.
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'.DIRECTORY_SEPARATOR));
			$files = new RegexIterator($iterator, self::$RegexPattern);

			// Places everything in a new array
			// that will be sorted.
			foreach($files as $file) {
				$depth = $files->getDepth(); // Get depth
				$data[$depth][] = $file->getRealpath(); // Push depth inside another dimension with the file.
			}

			// This is necessary that items within the
			// depth are sorted correctly, first sort
			// by key (the depth) than sort by name.
			ksort($data);
			$data = call_user_func_array('array_merge', $data);

			// Creates an absolute path to the indicated file.
			foreach($data as $file){
				if(strpos($file, $path) !== false){
					$this->path = $file;
					break;
				}
			}

			// Did you find the file?
			return !is_null($this->path);
		}


		//======================================================================
		// EXISTS/CHECK METHODS
		//======================================================================


		/**
		 *
		 * Checks the extension of the imported file(s).
		 *
		 * @param string $path - Filename or path to file
		 * @return true|void
		 *
		 */
		private function extension(string $path){
			// Extends the names of allowed extensions.
			$extensions = str_replace(array('/\.(', '|', ')*$/i'), array('*.', ', *.'), self::$RegexPattern);

			if(preg_match(self::$RegexPattern, $path)) return true;
			else $this->report('Illegal extension file <i>'.basename($path).'</i>! You can only import file: '.$extensions.'');
		}


		/**
		 *
		 * Checks whether a file exists.
		 *
		 * @param string $path - Filename or path to file
		 * @param bool $retBool - Is he to return the bool
		 * 
		 * @return true|void
		 * @uses extension(), getPathFile()
		 *
		 */
		protected function fileExists(string $path, bool $retBool = false){
			$this->extension($path);

			if(!$retBool){
				if($this->getPathFile($path)) return true;
				else $this->report('No such file <i>'.$path.'</i>!');
			} else return $this->getPathFile($path) ? true : false;
		}


		/**
		 *
		 * Checks whether a path exists.
		 * 
		 * @param string $path - Filename or path to file
		 * @return true|void
		 * 
		 * @uses getPathDir()
		 *
		 */
		protected function pathExists(string $path){
			if($this->getPathDir($path)) return true;
			else $this->report('The given path <i>'.dirname($path).'</i> does not exist!');
		}


		//======================================================================
		// INCLUDE METHODS
		//======================================================================


		/**
		 *
		 * Creates an absolute path to the file and
		 * modifies it accordingly.
		 * 
		 * @param string $path 		- Filename or path to file
		 * @param bool $isReturn	- Is there anything to return for?
		 * 
		 * @return string|void
		 * @uses fileExists()
		 *
		 */
		private function include(string $path, bool $isReturn = false){
			if($this->fileExists($path)){
				if($isReturn) return @require $this->path;
				else @require $this->path;
			}
		}


		/**
		 *
		 * Imports a specific file or group of files to the project.
		 * 
		 * @param string|array $path 	- Filename or path to file
		 * @param bool $isReturn	 	- Is there anything to return for?
		 * @param string $omission		- Files that will be omitted in the glob
		 * 
		 * @return string|void
		 * @uses include(), pathExists()
		 * 
		 * @example for array 	- import(array('file_one.ext', 'file_two.ext', ...))
		 * @example for string 	- import('path/file.ext')
		 * @example for glob 	- import('path/*.ext')
		 *
		 */
		public function import($path, bool $isReturn = false, string $omission = ''){
			// Sets pattern for RegexIterator class.
			// Default extension is php.
			self::$RegexPattern = !empty(self::$regex) ? self::$regex['import'] : '/\.(php)*$/i';

			/* Call to array $path argument. */
			if(is_array($path)) foreach($path as $file) $this->include($file);

			/* Call to string $path argument. */
			elseif(is_string($path)){
				
				/* Call to normal structure for $path argument. */
                if(strpos($path, '*') === false){
					if($isReturn) return $this->include($path, $isReturn);
					else $this->include($path);
				}

                /* Call to glob structure for $path argument. */
                else {
                    $this->pathExists($path);
					foreach(glob($this->path) as $file) 
						if(strpos($file, $omission) === false && !empty($omission)) $this->include(basename($file));
                }
            }

			/* Error: Difrent type $path argument. */
			else $this->report('Incompatible type <i>'.gettype($path).'</i> of <u>import(string | array $path, bool $isReturn = false)</u> function param!');
		}
	}
