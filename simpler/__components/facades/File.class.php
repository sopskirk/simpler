<?php
/**
 *
 * This class manages the file stream, creates, deletes,
 * updates and reads the file from the file.
 *
 * @package Simpler
 * @subpackage Facades
 * 
 * 
 * ##################################################################################
 * # Other used classes (methods or vars) in this module:							#
 * # @see Import{} (extends) | Reportable{} (extends) | Bootstrap{} (extends) 		#
 * # @uses PathExists(), fileExists() | report()									#
 * # @var @static $RegexPattern, @static $regex, $path								#
 * ##################################################################################
 *
 */


	namespace Simpler\Components\Facades;
	use Simpler\Bootstrap;

	class File extends Bootstrap{
		/**
		 *
		 * Stores the file open mode.
		 *
		 * @var string
		 *
		 */
		private $mode;

		/**
		 *
		 * Stores the file's rights.
		 *
		 * @var int
		 *
		 */
		private $access;

		/**
		 *
		 * Stores the stored file(s) name(s).
		 *
		 * @var string|array
		 *
		 */
		private $storage;

		/**
		 *
		 * Stores the decision to add a new line.
		 * Enabled by default.
		 *
		 * @var bool
		 *
		 */
		private $newLine = true;


		/*
		 *
		 * The default pattern (extensions) path for
		 * the RegexIterator class.
		 *
		 */
		public function __construct(){
			self::$RegexPattern = self::$regex['file'];
		}


		//======================================================================
		// CHECK METHODS
		//======================================================================


		/**
		 *
		 * Checks whether the method has not
		 * been called (use).
         * 
		 * @return void
		 *
		 */
		private function checkUseMethod() :void{
			if(empty($this->storage)) 
				$this->report('The <i>use</i> method must be called for proper operation this method!');
		}


		/**
		 *
		 * Checks whether the number of arguments
		 * between methods matches.
         * 
		 * @param array $arr - The arrat from which to count the number of its elements
		 * @return void
		 *
		 */
		private function checkNumArgs($arr) :void{
			if((is_array($arr) && is_array($this->storage)) && count($this->storage) != count($arr)) 
				$this->report('The number of arguments of the method being called must be the same as the number of the <u>use</u> method arguments!');
		}


		/**
		 *
		 * Checks whether the arguments of the method 
		 * are of the same type as the arguments 
		 * of the use method.
         * 
		 * @return void
		 *
		 */
		private function checkTypeArgs($arg) :void{
			if(gettype($arg) !== gettype($this->storage))
				$this->report('Arguments of the function being called are not of the same type as the arguments of the <u>use</u> method!');
		}


		/**
		 *
		 * Checks whether the file is empty.
         * 
		 * @return bool
		 * @uses checkUseMethod()
		 *
		 */
		public function isEmpty() :bool{
			// Has the use method been called?
			$this->checkUseMethod();

			// Is the file empty?
			return empty(trim(@file_get_contents($this->path)));
		}


		/**
		 *
		 * Checks whether the file exists at all.
         * 
		 * @param string $file - Filename or path to file
		 * @return bool
		 *
		 */
		public function isExists(string $file) :bool{
			// The version of the method that returns only bool.
			return $this->fileExists($file, true);
		}


		//======================================================================
		// CREATE METHODS
		//======================================================================
		

		/**
		 *
		 * This is the trait for methods from
		 * the create and update section.
		 *
		 * @param string $path		- Filename or path to file
		 * @param string $append 	- Content added at the beginning of the file
		 * @param bool $put 		- Is the method called in the put() method
		 * 
		 * @return void
		 *
		 */
		private function __traitFileMethods(string $path, string $content, string $append = '', bool $put = false) :void{
			$put ? $this->pathExists($path) : $this->fileExists($path);

			if(empty($append)){
				$content = $this->newLine ? $content.PHP_EOL : $content;
			} else {
				// Append method
				$this->use($path);
				$content = $this->newLine ? $append.PHP_EOL.$this->content(false, false) : $append.$this->content(false, false);
			}

			// Adds the content to the file.
			empty($this->mode) ? @file_put_contents($this->path, $content) : @file_put_contents($this->path, $content, $this->mode);
		}


		/**
		 * 
		 * !#Overloaded function#!
		 * 
		 * Creates a file and overwrites its contents
		 * each time the method is called.
		 *
		 * @param string|array $args
		 * @return void
		 * 
		 * @example for array 	- put(array('file.ext' => 'content', ...))
		 * @example for array	- put(array('file.ext', 'file2.ext' => 'content', ...))
		 * 
		 * @example for string 	- put('file.ext', 'content')
		 * @example for string 	- put('file.ext')
		 * 
		 * @uses __traitFileMethods()
		 *
		 */
		public function put() :void{
			$num = func_num_args();
			$get = func_get_args();

			// Gets an array variable.
			$file = $num == 1 && is_array($get[0]) ? $get[0] : $get[1];

			/* Call to array $args argument. */
			if($num == 1 || $num == 2 && is_array($file)){
				foreach($file as $path => $content){
					// When the content of the file will not be provided.
					$setPath = is_int($path) ? $content : $path;
					$setContent = is_int($path) ? '' : $content;

					// File or own path to file.
					$setPath = is_string($get[0]) ? $get[0].DIRECTORY_SEPARATOR.$setPath : $setPath;

					// Create file
					$this->__traitFileMethods($setPath, $setContent, '', true);
				}
			}

			/* Call to string $args argument. */
			else if(($num == 2 || $num == 1) && is_string($get[0])){
				// When the content of the file will not be provided.
				$content = array_key_exists(1, $get) ? $get[1] : '';

				// Create file
				$this->__traitFileMethods($get[0], $content, '', true);
			}

			/* Error: Diffrent type or number of args(s). */
			else $this->report('Incompatible type ('.gettype($file).') or number ('.$num.') of <u>put(string | array $file, string | void $content)</u> method argument(s)!');
		}


		//======================================================================
		// UPDATE METHODS
		//======================================================================


		/**
		 *
		 * This is the trait for methods from
		 * the update section.
		 *
		 * @param string|array $content
		 * @param bool $append 		- Is the content to be added to the beginning file?
		 * @param string $method 	- Prepend or append method
		 * 
		 * @return void
		 * @uses checkUseMethod(), checkNumArgs(), checkTypeArgs(), __traitFileMethods()
		 *
		 */
		private function __traitUpdateMethods($content, bool $append, string $method) :void{
			$storage = $this->storage;
			
			// Has the use method been called?
			$this->checkUseMethod();

			// Are the arguments of the methods invoked equal?
			$this->checkNumArgs($content);

			// Are the method arguments the same?
			$this->checkTypeArgs($content);

			/* Call to array $storage. */
			if(is_array($storage)){

				/* Call to array $content argument. */
				if(is_array($content)){
					$i = 0;
					
					foreach($content as $add){
						$this->__traitFileMethods($storage[$i], !$append ? $add : '', $append ? $add : '');
						$i++;
					}
				}

				/* Error: Diffrent type $content arguement. */
				else $this->report('Incompatible type <i>'.gettype($content).'</i> of the <u>'.$method.'(string | array $content)</u> method argument!');
			}
			
			/* Call to string $storage and $content argument. */
			elseif(is_string($storage) && is_string($content))  $this->__traitFileMethods($storage, !$append ? $content : '', $append ? $content : '');
			
			/* Error: Diffrent type argument. */
			else $this->report('Incompatible type <i>'.gettype($content).'</i> of the <u>'.$method.'(string | array $content)</u> method argument!');
		}


		/**
		 * 
		 * At the end of the file, add the content provided
		 * as the method argument.
		 *
		 * @param string|array $content
		 * @return void
		 * 
		 * @uses __traitUpdateMethods()
		 * 
		 * @example for array 	- prepend(array('content prepend', ...))
		 * @example for string 	- prepend('content prepend')
		 *
		 */
		public function prepend($content) :void{
			$this->mode = FILE_APPEND;
			$this->__traitUpdateMethods($content, false, 'prepend');
		}


		/**
		 *
		 * At the beginning of the file, add the content provided
		 * as the method argument.
		 *
		 * @param string|array $content
		 * @return void
		 * 
		 * @uses __traitUpdateMethods()
		 * 
		 * @example for array 	- append(array('content prepend', ...))
		 * @example for string 	- append(content string)
		 *
		 */
		public function append($content) :void{
			$this->__traitUpdateMethods($content, true, 'append');
		}


		//======================================================================
		// ACCESS METHODS
		//======================================================================


		/**
		 *
		 * This is the trait for the method
		 * from the access section.
		 * 
		 * @param string $modifier - Modifier for accessing the file
		 * @return void
		 *
		 */
		private function __traitAccessMethod(string $modifier) :void{
			switch($modifier){
				case 'private' 		: $this->access = 0666;break;
				case 'public'		: $this->access = 0755;break;
				case 'protected'	: $this->access = 0750;break;
				default :
					$this->access = 0755;
					$this->report('The given file access modifier <i>'.$modifier.'</i> has not been recognized!');
			}
		}


		/**
		 *
		 * It allows you to change the mode file:
		 * - private (0666),
		 * - public (0755),
		 * - protected (0750)
		 *
		 * @param string|array $modifier - Modifier for accessing the file
		 * @return void
		 * 
		 * @example for string access('public')
		 * @example for array access(array('public', 'private'))
		 * 
		 * @uses __traitAccessMethod(), checkUseMethod(), checkNumArgs()
		 * 
		 */
		public function access($modifier) :void{
			$storage = $this->storage;
			
			// Has the use method been called?
			$this->checkUseMethod();

			// Are the arguments of the methods invoked equal?
			$this->checkNumArgs($modifier);

			/* Call to array $storage. */
			if(is_array($storage)){

				/* Call to array $modifier argument. */
				if(is_array($modifier)){
					$i = 0;
					
					foreach($modifier as $access){
						$this->fileExists($storage[$i]);
						$this->__traitAccessMethod($access);

						chmod($this->path, $this->access);
						$i++;
					}
				}

				/* Call to string $modifier argument. */
				elseif(is_string($modifier)){
					foreach($storage as $file){
						$this->fileExists($file);
						$this->__traitAccessMethod($modifier);

						chmod($this->path, $this->access);
					}
				}

				/* Error: Diffrent type $modifier arguement. */
				else $this->report('Incompatible type <i>'.gettype($modifier).'</i> of the <u>access(string | array $modifier)</u> method argument!');
			}

			/* Call to string $storage. */
			elseif(is_string($storge) && is_string($modifier)){
				$this->__traitAccessMethod($modifier);
				chmod($this->path, $this->access);
			}

			/* Error: Diffrent arguments methods (access/use). */
			else $this->report('Incorrect the <i>access</i> method call relative to the <u>use</u> method!');
		}

		
		//======================================================================
		// CHANGE METHODS
		//======================================================================


		/**
		 *
		 * Performs the operation of moving
		 * or copying the selected file
		 * to a new location.
		 *
		 * @param string $file	- Filename or path to file
		 * @param string $place - Place of file transfer
		 * @param bool $moved 	- Is the file moved?
		 * 
		 * @return bool
		 * @uses content(), mode(), put(), trash(), isExists()
		 *
		 */
		private function __traitChange(string $file, string $place, bool $moved) :bool{
			// New path to file.
			$place 	= str_replace('.', '', $place);
			$path 	= $place.DIRECTORY_SEPARATOR.basename($file);

			// Until the file exists.
			if(!$this->isExists($path) || preg_match('/^(\.|\.\|\.\/)+/', $path)){
				$use = $this->use($file);

				// Get content file
				$content = $moved ? trim($use->content(false, false)) : $use->content(false, false);

				// Get mode file
				$mode = $use->mode(true);

				// Remove old file.
				// Only when the file transfer operation is performed.
				if($moved) $use->trash();

				// Create new file
				$this->put($path, $content);

				// Transfer of file rights.
				chmod($this->path, '0'.$mode);

				// Have you succeeded?
				return $this->isExists($path);
			} else return true;
		}


		/**
		 *
		 * This is the trait for methods 
		 * from the change section.
		 *
		 * @param string|array $place 	- Place of file transfer
		 * @param string $method 		- Method move or copy
		 * @param bool $moved 			- Is the file moved?
		 * 
		 * @return bool
		 * @uses checkUseMethod(), checkNumArgs(), __traitChange()
		 *
		 */
		private function __traitChangeMethods($place, string $method, bool $moved){
			$storage = $this->storage;

			// Stores the value to be returned.
			$returned = false;

			// Has the use method been called?
			$this->checkUseMethod();

			// Are the arguments of the methods invoked equal?
			$this->checkNumArgs($place);
			
			/* Call to array $storage. */
			if(is_array($storage)){

				/* Call to array $place argument. */
				if(is_array($place)){
					$i = 0;
					
					foreach($storage as $file){
						$returned = $this->__traitChange($file, $place[$i], $moved);
						$i++;
					}
				}

				/* Call to string $place argument. */
				elseif(is_string($place)) foreach($storage as $file) $returned = $this->__traitChange($file, $place, $moved);
				
				/* Error: Diffrent type $place argument. */
				else $this->report('Incompatible type <i>'.gettype($place).'</i> of the <u>'.$method.'(string | array $place)</u> method argument!');
			}

			/* Call to string $storage and $place argument. */
			elseif(is_string($storage) && is_string($place)) $returned = $this->__traitChange($storage, $place, $moved);

			/* Error: Diffrent arguments methods (move or copy/use). */
			else $this->report('Incorrect the <i>'.$type.'</i> method call relative to the <u>use</u> method!');

			// Have you succeeded?
			return $returned;
		}


		/**
		 *
		 * Moves the selected file or files to
		 * the specified new path.
		 *
		 * @param string|array $place - place of file transfer
		 * @return bool
		 * 
		 * @example for array 	- move(array('test/test2', 'test2/test3'))
		 * @example for string 	- move('test/test2')
		 * 
		 * @uses __traitChangeMethods()
		 *
		 */
		public function move($place) :bool{
			return $this->__traitChangeMethods($place, 'move', true);
		}


		/**
		 *
		 * Copy the selected file or files to
		 * the specified new path.
		 *
		 * @param string|array $place - place of file transfer
		 * @return bool
		 * 
		 * @example for array 	- copy(array('test/test2', 'test2/test3'))
		 * @example for string 	- copy('test/test2')
		 * 
		 * @uses __traitChangeMethods()
		 *
		 */
		public function copy($place) :bool{
			return $this->__traitChangeMethods($place, 'copy', false);
		}


		/**
		 *
		 * It deletes the entire contents of the file,
		 * but does not delete it.
		 *
		 * @return void
		 *
		 */
		public function clean() :void{
			// Has the use method been called?
			$this->checkUseMethod();

			// Clears the file
			@file_put_contents($this->storage, '');
		}


		/**
		 *
		 * Changes the file name to the new name.
		 *
		 * @param string $filename - New name for the file
		 * @return bool
		 * 
		 * @uses checkUseMethod()
		 *
		 */
		public function rename(string $filename) :bool{
			// Has the use method been called?
			$this->checkUseMethod();

			// Gets the name of the directory.
			$dir = dirname($this->storage);

			// Modify filename
			return @rename($this->storage, $dir.DIRECTORY_SEPARATOR.$filename);
		}


		/**
		 *
		 * Manages the addition of a new line
		 * at the beginning or end of the line.
		 * Default sets true.
		 *
		 * @param bool $new_line - Is there a new line
		 * @return object
		 *
		 */
		public function newLine(bool $new_line = true) :object{
			$this->newLine = $new_line;
			return $this;
		}


		//======================================================================
		// GET METHODS
		//======================================================================


		/**
		 *
		 * Reads the contents of the file given as 
		 * the method argument.
		 *
		 * @param bool $arr		 - Is he to return the boards
		 * @param bool $new_line - Whether to display a new line
		 * 
		 * @return string|array
		 * @uses checkUseMethod()
		 *
		 */
		public function content(bool $arr = false, bool $new_line = true){
			// Has the use method been called?
			$this->checkUseMethod();

			// Is there a new line?
			if(!$arr) return $new_line ? nl2br(@file_get_contents($this->path)) : @file_get_contents($this->path);
			else return @file($this->path, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		}


		/**
		 *
		 * Gets the number of lines from
		 * the file bypassing empty lines.
		 *
		 * @return int
		 * @uses checkUseMethod(), content()
		 *
		 */
		public function length() :int{
			// Has the use method been called?
			$this->checkUseMethod();
			return count($this->content(true, false));
		}


		/**
		 *
		 * Gets the size of the file given as an argument in the get() method.
		 * By default units using KB and MB are enabled.
		 *
		 * @param bool $units - Size display with units
		 * @return string|int
		 * 
		 * @uses checkUseMethod()
		 *
		 */
		public function size(bool $units = true){
			// Has the use method been called?
			$this->checkUseMethod();

			// What size is the file?
			$size = filesize($this->path);
			
			if($units){
				if ($size >= 1048576) 	$size = number_format($size / 1048576, 2).' MB';
				elseif ($size >= 1024) 	$size = number_format($size / 1024, 2).' KB';
				elseif ($size > 1) 		$size = $size.' bytes';
				elseif ($size == 1) 	$size = $size.' byte';
				else 					$size = '0 bytes';
			}

			return $size;
		}


		/**
		 *
		 * Gets the last modified time of the file given.
		 *
		 * @return string
		 * @uses checkUseMethod()
		 *
		 */
		public function lastModified(string $format = 'Y:m:d H:i:s') :string{
			// Has the use method been called?
			$this->checkUseMethod();

			// Returns the full date.
			return date($format, filemtime($this->path));
		}


		/**
		 *
		 * Gets the open/write mode of the file given.
		 *
		 * @param bool $retInt - Is it to return the rights in the number format?
		 * @return string|int
		 * 
		 * @uses checkUseMethod()
		 * @link http://php.net/manual/pl/function.fileperms.php
		 *
		 */
		public function mode(bool $retInt = true){
			// Has the use method been called?
			$this->checkUseMethod();
			
			// What are the file permissions?
			$perms = fileperms($this->path);

			if(!$retInt){
				// Socket
				if (($perms & 0xC000) == 0xC000) $stats = 's';

				// Symbolic link
				elseif (($perms & 0xA000) == 0xA000) $stats = 'l';

				// Simple link
				elseif (($perms & 0xA000) == 0xA000) $stats = 'l';

				// Block device
				elseif (($perms & 0x6000) == 0x6000) $stats = 'b';

				// Catalog
				elseif (($perms & 0x4000) == 0x4000) $stats = 'd';

				// Character device
				elseif (($perms & 0x2000) == 0x2000) $stats = 'c';

				// FIFO
				elseif (($perms & 0x1000) == 0x1000) $stats = 'p';

				// Unknow
				else $stats = 'u';
				
				// Owner
				$stats .= (($perms & 0x0100) ? 'r' : '-');
				$stats .= (($perms & 0x0080) ? 'w' : '-');
				$stats .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
				
				// Group
				$stats .= (($perms & 0x0020) ? 'r' : '-');
				$stats .= (($perms & 0x0010) ? 'w' : '-');
				$stats .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
				
				// Others
				$stats .= (($perms & 0x0004) ? 'r' : '-');
				$stats .= (($perms & 0x0002) ? 'w' : '-');
				$stats .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

				// Returns the rights in the file in 
				// the format -rw-r--r--.
				return $stats.' ('.decoct($perms & 0777).')';
			} else
				// Only number
				return decoct($perms & 0777);
		}


		/**
		 *
		 * Gets the full path to the given file.
		 *
		 * @return string
		 * @uses checkUseMethod()
		 *
		 */
		public function path() :string{
			// Has the use method been called?
			$this->checkUseMethod();

			// Is the file exists?
			$this->fileExists($this->storage);
			
			// Returns the full path to the file.
			return $this->path;
		}


		//======================================================================
		// FIND METHODS
		//======================================================================


		/**
		 *
		 * This trait searches for the phrase 
		 * file given as the method parameter.
		 *
		 * @param string $phrase 	- Search phrase in file
		 * @param bool $multi 		- Should it show all matching?
		 * @param bool $first 		- Should it display the first found?
		 * @param bool $retBool 	- Is he to return the bool?
		 * 
		 * @return string|array|bool|void
		 *
		 */
		public function __traitFindMethods(string $phrase, bool $multi, string $errmsg, bool $first = false, bool $retBool = false){
			// Has the use method been called?
			$this->checkUseMethod();

			/* Call to string $phrase argument. */
			if(is_string($phrase)){
				$exists = false;
				$found = array();

				//$preg = preg_match('')

				// Gets the content of the file.
				$file = $this->content(true, false);

				// Searches the indicated file.
				foreach($file as $line){
					if((strpos($line, $phrase) !== false )){
						$found[] = $line;
						$exists = true;
					}
				}

				// Returns the entire line(s) or error message.
				// Possibly returns boolean.
				if($retBool && !$multi) return $exists;
				else {
					if($exists){
						if(!$multi) return $first ? $found[0] : $found[count($found) - 1];
						else {
							if(!$retBool) return count($found) > 1 ? $found : $found[0];
							else return count($found) > 1 ? true : false;
						}
					} else return 'Not found!';
				}
			}

			/* Error: Diffrent type $phrase arguement. */
			else $this->report('Incompatible type <i>'.gettype($phrase).'</i> of the <u>'.$errmsg.'</u> method argument!');
		}


		/**
		 *
		 * This method returns only the boolean type. 
		 * If the second argument is set, the method 
		 * returns true if it finds more than one phrase.
		 *
		 * @return bool|void
		 * @uses __traitFindMethods()
		 *
		 */
		public function find(string $phrase, bool $multi = false){
			return $this->__traitFindMethods($phrase, $multi, 'find(string $pharse, bool $multi = false)', true, true);
		}


		/**
		 *
		 * Displays the first line in which the searched 
		 * phrase is located.
		 *
		 * @return string|void
		 * @uses __traitFindMethods()
		 *
		 */
		public function findFirst(string $phrase){
			return $this->__traitFindMethods($phrase, false, 'findFirst(string $pharse)', true);
		}
		

		/**
		 *
		 * Displays the last line in which the searched 
		 * phrase is located.
		 *
		 * @return string|void
		 * @uses __traitFindMethods()
		 *
		 */
		public function findLast(string $phrase){
			return $this->__traitFindMethods($phrase, false, 'findLast(string $pharse)', false);
		}


		/**
		 *
		 * Displays all lines in which the searched 
		 * phrase is located.
		 * 
		 * @return string|array|void
		 * @uses __traitFindMethods()
		 *
		 */
		public function findAll(string $phrase){
			return $this->__traitFindMethods($phrase, true, 'findAll(string $pharse, bool $reBool = false)', false);
		}


		//======================================================================
		// DELETE METHODS
		//======================================================================


		/**
		 *
		 * Removes the specified file.
		 *
		 * @param string $path 	- Filename or path to file
		 * @return void
		 *
		 */
		private function unlink(string $path) :void{
			if(is_array($this->storage)) $this->fileExists($path);
			unlink($this->path);
		}


		/**
		 *
		 * Removes the specified file or all file.
		 *
		 * @return void
		 * @uses unlink()
		 *
		 */
		public function trash() :void{
			$storage = $this->storage;
			
			/* Call to array $storage. */
			if(is_array($storage)) foreach($storage as $file) $this->unlink($file);

			/* Call to string $storage. */
			elseif(is_string($storage)){
				
				/* Call to normal structure for $storage. */
				if(strpos($storage, '*') === false) $this->unlink($storage);

				/* Call to glob structure for $storage. */
				else foreach(glob($storage) as $file) $this->unlink($file);
			}
		}


		//======================================================================
		// USE METHOD
		//======================================================================


		/**
		 *
		 * The method used to refer to a specific file 
		 * or a specific group of files. With its help 
		 * you can get the contents of the file, 
		 * update it, delete it, etc.
		 *
		 * @param string|array $path 	- Filename or path to file
		 * @return object|void
		 * 
		 * @example for array 	- use(array('file1.ext', 'file2.ext'))
		 * @example for string 	- use('file.ext')
		 * @example for glob	- use('*.ext')
		 *
		 */
		public function use($path){
			/* Call to array $path argument. */
			if(is_array($path)){
				// Add path to file to storages.
				$this->storage = $path;
				return $this;
			}

			/* Call to string $path argument. */
			elseif(is_string($path)){
				
				/* Call to normal structure for $path argument. */
				if(strpos($path, '*') === false){
					// Checks whether a file exists.
					$this->fileExists($path);

					// Add path to file to storages.
					$this->storage = $path;
					return $this;
				}

				/* Call to glob structure for $path argument. */
				else{
					$this->pathExists($path);
					foreach(glob($this->path) as $file) $this->use(basename($file));

					return $this;
				}
			}

			/* Error: Diffrent type $path arguement. */
			else $this->report('Incompatible type <i>'.gettype($path).'</i> of the <u>use(string | array $path)</u> method argument!');
		}

		public function __destruct(){}
	}