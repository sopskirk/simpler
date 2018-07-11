<?php
/**
 *
 * This class manages the folders on the server,
 * creates, deletes, clears their contents.
 *
 * @package Simpler
 * @subpackage Facades
 *
 */


	namespace Simpler\Components\Facades;
	use DirectoryIterator;

	class Dir extends Import{
		/**
		 *
		 * Sets the absolute path to the dir and checks
		 * if the specified dir exists.
		 *
		 * @param $path The relative path under which the folder should be located.
		 * @return true|throw
		 *
		 * @subpackage ExtendsProject, Faulty
		 * @method path(), getFaulty()
		 *
		 */
		private function exists(string $path, bool $create = false){
			$dir = $create ? dirname($this->path($path)) : $this->path($path);
			if(!is_dir($dir)) return $this->getFaulty('dcd77f0efe3c9d1e6d168eae69b815f6', 'Folder does not exist: '.$dir);
		}


		/**
		 *
		 * Creates a new folder under the specified path.
		 *
		 * @param $access Folder access rights
		 * @return bool
		 *
		 * @see mkdir()
		 *
		 */
		public function set(string $path, int $access, int $recursive){
			$this->exists($path, true);
			return !is_dir($this->path) ? mkdir($this->path, $access, $cursive) : false;
		}


		/**
       *
       * Creates folder/s at once.
		 *
		 * @return void
       * @method set()
		 *
       */
		public function create(array $arg) :void{
			if(is_multi_array($arg)){
				foreach($arg as $key => $item){
					$access 		= findKey($item, 1) ? $item[1] : 0600;
					$recursive 	= findKey($item, 2) ? $item[2] : false;

					$this->set($item[0], $access, $recursive);
				}
			} else {
				$access 		= array_key_exists(1, $arg) ? $arg[1] : 0600;
				$recursive	= array_key_exists(2, $arg) ? $arg[2] : false;
				$this->set($arg[0], $access, $recursive);
			}
		}


		/**
		 *
		 * Deletes the folder and all its contents from
		 * the path specified.
		 *
		 * @return delete folder
		 *
		 * @see rmdir()
		 * @method clear()
		 *
		 */
		private function delete(string $path){
			$this->clear($path);
			return rmdir($this->path);
	 	}


		/**
		 *
		 * Removes (cleans) all the contents (files and dir) of
		 * a folder under the specified path.
		 *
		 * @return void
		 * @see unlink()
		 *
		 */
		public function clear(string $path) :void{
			$this->exists($path);

			foreach(new DirectoryIterator($this->path) as $fileinfo){
				if($fileinfo->isFile() || $fileinfo->isLink()) unlink($fileinfo->getPathName());
				elseif(!$fileinfo->isDot() && $fileinfo->isDir()) $this->delete(str_replace('\\', DIRECTORY_SEPARATOR, $path.'/'.basename($fileinfo->getPathName())));
			}
		}
	}
