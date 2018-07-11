<?php
/**
 *
 * Central cookie control, delete, create and get.
 *
 * @package Simpler
 * @subpackage Http
 * 
 * 
 * ##################################################################################################################################
 * # Other used classes (methods or vars) in this component:																		#
 * # @see File{} | Import{} (extends) | Bootstrap{} (extends) | Storage{} (extends) | User{} | Valid{}								#
 * # @uses prepend() | import() | using() | shiftS(), loadS(), spyS(), cleanS(), inspectorS(), checkInStorage() | ip() | filter()	#
 * ##################################################################################################################################
 *
 */


	namespace Simpler\Components\Http;

	class Cookie extends CookieStorage{
		/**
		 *
		 * Stores the path on the server in which 
		 * the cookie will be available on.
		 *
		 * @var string
		 *
		 */
		private $pathway;

		/**
		 *
		 * Stores the (sub)domain that the 
		 * cookie is available to.
		 *
		 * @var string
		 *
		 */
		private $domain;

		/**
		 *
		 * Indicates that the cookie should only be transmitted
		 * over a secure HTTPS connection from the client.
		 *
		 * @var bool
		 *
		 */
		private $secure;

		/**
		 *
		 * When TRUE the cookie will be made accessible
		 * only through the HTTP protocol.
		 *
		 * @var bool
		 *
		 */
		private $httponly;

		/**
		 *
		 * Config file for CookieParams.
		 *
		 * @var array
		 *
		 */
		private static $configCookie = array();

		/**
		 *
		 * Storage cookie name
		 *
		 * @var string|array
		 *
		 */
		private $storage;

		/**
		 *
		 * It stores information about the cookie.
		 *
		 * @var array
		 *
		 */
		private static $storageData = array();

		/**
 		 *
		 * Stores the path to a file that has
		 * data to set cookie params.
 		 *
 		 * @var const
 		 *
 		 */
		CONST CONFIG_FILE = 'simpler/__config/cookie.php';

		/**
 		 *
 		 * Stores the path to the cookie storage
 		 *
 		 * @var const
 		 *
 		 */
		CONST STORAGE_FILE = 'simpler/__storage/cookie.storage';


		####################################### CONFIG METHOD #######################################


		/**
		 *
         * Gets the params cookie settings from the configuration file.
		 * 
		 * @return void
		 *
         */
		public function setConfigCookie() :void{
			self::$configCookie = $this->import(self::CONFIG_FILE, true);

			$this->pathway	= self::$configCookie['PATH'];
			$this->domain 	= empty(self::$configCookie['DOMAIN']) ? $_SERVER['HTTP_HOST'] : self::$configCookie['DOMAIN'];
			$this->secure 	= self::$configCookie['SECURE'];
			$this->httponly	= self::$configCookie['HTTPONLY'];

			// If there are no cookies, the storage file is cleared.
			if($_COOKIE) $this->inspectorS();
		}


		####################################### CHECK METHODS #######################################


		/**
		 *
		 * Checks whether the method has not been called (use).
         * 
		 * @return void
		 *
		 */
		private function checkUseMethod() :void{
			if(empty($this->storage)) 
				$this->report('The <i>use</i> method must be called for proper operation!');
		}


		/**
		 *
         * Checks whether a cookie file exists.
		 *
		 * @param string $name	- Cookie name
		 * @param bool $retBool - Is he to return the bool?
         * 
		 * @return bool|void
		 *
         */
		public function has(string $name, bool $retBool = true){
			if($retBool) return isset($_COOKIE[$name]);

			/* Error: Cookie name does not exists. */
			else if(!isset($_COOKIE[$name])) $this->report('The cookie about name <i>'.$name.'</i> does not exist!');
		}


		####################################### VALID METHODS #######################################


		/**
		 *
         * Checks the entered cookie name.
		 *
		 * @param string $name - Cookie name
		 * @return void
		 *
         */
		private function validName(string $name) :void{
			if(!preg_match('/^[a-zA-Z0-9_]+$/', $name) || preg_match('/[=,; \\t\\r\\n\\013\\014]/', $name)) 
				$this->report('Incorrect cookie name: <i>'.$name.'</i>!');
		}


		/**
		 *
         * Checks the entered cookie expire.
		 *
		 * @param string $expire - Verbal time
		 * @return void
		 *
         */
		private function validExpire(string $expire) :void{
			if(!preg_match('/^[0-9 ]*(second|seconds|minute|minutes|hour|hours|day|days|month|months|year|years)$/', $expire)) 
				$this->report('Incorrect cookie expire time: <i>'.$expire.'</i>!');
		}


		####################################### TIMESTAMP METHOD #######################################


		/**
         *
         * Swapping date to timestamp.
		 * 
		 * @param string $expire 		- Verbal time
		 * @param string $custom_date 	- Custom date (other than current)
		 * 
         * @return int
 		 *
         */
		private static function timestamp(string $expire, string $custom_date = '') :int{
			return !empty($custom_date) ? strtotime($custom_date.'+'.$expire) : strtotime(date('Y:m:d H:i:s').'+'.$expire);
		}
		 
		
		####################################### CREATE METHODS #######################################


		/**
		 *
		 * Creates a specific one cookie file.
		 *
		 * @param bool $httponly 		- Custom settings httponly
		 * @param string $custom_date 	- Custom date (other than current)
		 * 
		 * @return void
		 * @uses validName(), validExpire(), timestamp()
		 *
		 */
		private function buildCookie(string $name, string $value, string $expire, bool $httponly = true, string $custom_date = '') :void{
			#Validation cookie data
			$this->validName($name);
			$this->validExpire($expire);

			// Add cookie name to storage.
			$this->storage = $name;

			// Custom settings httponly
			$httponly = $httponly ? $this->httponly : false;

			// Build cookie file
			@setcookie($name, $value, self::timestamp($expire, $custom_date), $this->pathway, $this->domain, $this->secure, $httponly);

			// The date when the cookie was created.
			$created = !empty($custom_date) ? $custom_date : date('Y:m:d H:i:s');

			// Saves information about the cookie file.
			self::$storageData = ['name' => $name, 'value' => $value, 'expire' => $expire, 'created' => $created, 'httponly' => (int)$httponly];
		}


		/**
		 * !#Overloaded function#!
		 * 
		 * Create a new cookie(s).
		 *
		 * @param string|array $args
		 * @return void|object
		 * 
		 * @uses buildCookie, has()
		 * 
		 * @example for array 	- create(array(['name', 'value', 'expire', 'httponly'], ...))
		 * @example for string 	- create($name, $value, $expire, $httponly)
		 *
		 */
		public function create(){
			$num = func_num_args();
			$get = func_get_args();

			// Cookie name or array.
			$cookie = $get[0];

			/* Call to array $cookie argument. */
			if($num == 1 && is_array($cookie)){
				foreach($cookie as $key => $item){
					$httponly = findKey($item, 3) ? $item[3] : true;
					if(!$this->has($item[0])) $this->buildCookie($item[0], $item[1], $item[2], $httponly);
				}

				return $this;
			}

			/* Call to string $cookie argument. */
			elseif($num >= 3 && $num <= 5 && is_string($cookie)){
				$httponly = $num == 4 ? $get[3] : true;
				if(!$this->has($get[0])) $this->buildCookie($get[0], $get[1], $get[2], $httponly);

				return $this;
			}
	
			/* Error: Diffrent type or number of argument(s). */
			else $this->report('Incompatible type ('.gettype($cookie).') or number ('.$num.') of the <u>push(string, string, string, bool = true | array)</u> method argument(s)!');
		}


		####################################### GET METHODS #######################################


		/**
		 *
		 * Trait for methods of getting cookie content.
		 *
		 * @param string $index - Selecting the index whose value will be displayed
		 * @return string|void
		 * 
		 * @uses checkUseMethod(), timestamp()
		 *
		 */
		private function __traitGetMethods(string $index){
			// Has the use method been called?
			$this->checkUseMethod();
			$storage = $this->storage;

			// Whether the cookie file is in storage?
			if($this->checkInStorage($storage)){
				list($value, $expire, $created, $temp) = $this->loadS($storage);

				switch($index){
					case 'value'	: return $value;break;
					case 'expire' 	: return date('Y:m:d H:i:s', self::timestamp($expire, $created));break;
					case 'created'	: return $created;break;
					default 		: $this->report('The given parameter <i>'.$index.'</i> for the <u>get</u> method has not been recognized!');
				}
			} else {
				// Get cookie value.
				if($index === 'value') return $this->using('valid')->filter($_COOKIE[$storage], true);

				/* Error: Illegal parameter */
				elseif(preg_match('/(expire|created)/', $index)) $this->report('The given <i>'.$index.'</i> parameter is reserved only for cookies stored in the storage!');

				/* Error: Incorrect parameter */
				else $this->report('The given parameter <i>'.$index.'</i> for the <u>get</u> method has not been recognized!');
			}
		}


		/**
		 * 
		 * Gets the value of the cookie.
		 * @return string
		 * 
		 */
		public function value() :string{
			return $this->__traitGetMethods('value');
		}


		/**
		 * 
		 * Gets the expire time of the cookie.
		 * @return string
		 * 
		 */
		public function expire() :string{
			return $this->__traitGetMethods('expire');
		}


		/**
		 * 
		 * Gets the creation date of the cookie.
		 * @return string
		 * 
		 */
		public function created() :string{
			return $this->__traitGetMethods('created');
		}


		####################################### UPDATE METHOD #######################################


		/**
		 *
		 * !#Overloaded function#!
		 * 
		 * Updates selected cookie data:
		 * - value,
		 * - expire,
		 * - httponly
		 *
		 * @param array|string $args
		 * @return void
		 * 
		 * @uses buildCookie(), checkUseMethod()
		 * 
		 * @example for array 	- set(array('key' => 'new data', ...))
		 * @example for string 	- set('key', 'new data')
		 *
		 */
		public function update() :void{
			$num = func_num_args();
			$get = func_get_args();

			$this->checkUseMethod();
			$storage = $this->storage;
			
			// Whether the cookie file is in storage?
			if($this->checkInStorage($storage)){
				// Gets data about a specific cookie file from a storage file.
				list($value, $expire, $created, $httponly) = $this->loadS($storage);
				$first = $get[0];

				/* Call to array $first argument. */
				if($num == 1 && is_array($first)){
					$value 		= array_key_exists('value', $first) ? $first['value'] : $value;
					$expire		= array_key_exists('expire', $first) ? $first['expire'] : $expire;
					$httponly 	= array_key_exists('httponly', $first) ? $first['httponly'] : $httponly;

					// Changes the contents of the storage file.
					$this->shiftS($storage);

					// Update data cookie
					$this->buildCookie($storage, $value, $expire, $httponly, $created);
				}

				/* Call to string $first argument. */
				else if($num == 2 && is_string($first)){
					$data = $get[1];

					switch($first){
						case 'value' 	: $value = $data;break;
						case 'expire' 	: $expire = $data;break;
						case 'httponly' : $httponly = $data;break;
						default : $this->report('The given parameter <i>'.$first.'</i> for the <u>update</u> method has not been recognized!');
					}

					// Changes the contents of the storage file.
					$this->shiftS($storage);

					// Update data cookie
					$this->buildCookie($storage, $value, $expire, $httponly, $created);
				}

				/* Error: Diffrent type or number of argument(s). */
				else $this->report('Incompatible type ('.gettype($first).') or number ('.$num.') of the <u>update(string | array $type, string | void $data)</u> method argument(s)!');
			}
			
			/* Error: Illegal use method. */
			else $this->report('The <u>update</u> method called is reserved only for cookies that have been stored in storage!');
		}


		####################################### DELETE METHODS #######################################


		/**
		 *
		 * Deletes a specific cookie.
		 *
		 * @return void
		 * @uses has(), timestamp()
		 *
		 */
		private function delete(string $name) :void{
			// Checks whether a cookie file exists.
			if(is_array($this->storage) || is_null($this->storage)) $this->has($name, false);
			
			// Changes the contents of the storage file.
			$this->shiftS($name);

			// Removal of a cookie.
			@setcookie($name, null, self::timestamp('-1 day'), $this->pathway, $this->domain, $this->secure, $this->httponly);
		}


		/**
		 *
		 * It removes all existing cookies in addition
		 * to the session cookie.
		 *
		 * @return void
		 * @uses delete()
		 *
		 */
		private function deleteAll() :void{
			if($_COOKIE) foreach($_COOKIE as $name => $val) if(session_name() !== $name) $this->delete($name);
		}


		/**
		 *
		 * Deletes a specific cookie or all existing
		 * cookies except session cookies.
		 *
		 * @return void
		 * @uses delete(), deleteAll()
		 *
		 */
		public function trash() :void{
			$storage = $this->storage;

			/* Call to array $storage. */
			if(is_array($storage)) foreach($storage as $name) $this->delete($name);

			/* Call to string $storage. */
			elseif(is_string($storage)) empty($storage) ? $this->deleteAll() : $this->delete($storage);
		}


		####################################### USE METHOD #######################################


		/**
		 *
		 * A method used to refer to a specific or a certain
		 * group of cookies. With its help you can gets cookie
		 * data, update it and delete it.
		 *
		 * @param string|array $cookie - Cookie name
		 * @param bool $retBool - Is he to return the bool?
		 * 
		 * @return object|void
		 * 
		 * @example for array 	- use(array('cookie_name1', 'cookie_name2'))
		 * @example for string 	- use('cookie_name')
		 * 
		 * @uses has()
		 *
		 */
		public function use($cookie, bool $retBool = false){
			/* Call to string or array $cookie argument. */
			if(is_string($cookie) || is_array($cookie)){
				// Checks whether a cookie file exists.
				if(!is_array($cookie)) $this->has($cookie, $retBool);

				// Add cookie name to storages.
				$this->storage = $cookie;
				return $this;
			}

			/* Error: Diffrent type $cookie argument. */
			else $this->report('Incompatible type <i>'.gettype($cookie).'</i> of the <u>use(string | array $cookie, bool | void $retBool)</u> method argument!');
		}


		####################################### STORAGE METHODS #######################################


		/**
		 *
		 * Reference to the Storage class method.
		 * Clears the storage file.
		 *
		 * @return bool
		 *
		 */
		public function cleanStorage() :bool{
			return $this->cleanS();
		}


		/**
		 *
		 * Displays the preview of cookies 
		 * that have been saved to storage.
		 *
		 * @return string
		 *
		 */
		public function preview() :string{
			$content = '';

			// Preview storage
			$preview = $this->previewS();

			// Equivalent $_COOKIE for storage.
			$cookies = $this->cookies();

			// Preview of cookies.
			if($_COOKIE){
				foreach($_COOKIE as $name => $val)
					if(!in_array($name, $cookies)) $content .= $name.' {<br>'.'&emsp;value => '.$val.'<br> &emsp;storage => false<br>}<br></br>';
			}

			// Preview cookies stored in storage.
			if(is_string($preview)){
				// Conversion of the string into array.
				$cookies = str_replace(array('[', ']'), '', $preview);
				$explode = explode("\n", trim($cookies));

				// Creating a preview of cookie 
				// files stored in storage.
				foreach($explode as $line){
					$data = explode(';', $line);
					$content .= $data[0].' {<br>'.'&emsp;value => '.$data[1].'<br> &emsp;expire => '.$data[2].'<br> &emsp;created => '.$data[3].'<br> &emsp;httponly => '.$data[4].' &emsp;storage => true<br>}<br><br>';
				}
			}

			elseif(!$_COOKIE || !$preview) $content = 'Cookies have not been created yet!';
			return $content;
		}


		/**
         *
         * Saves the cookie file in the storage.
		 * 
         * @return void
 		 *
         */
		public function save() :void{
			if(!empty($this->storage)){
				// Information about the cookie file.
				$data = self::$storageData;

				$file = $this->using('file');
				$file->use(self::STORAGE_FILE);

				// Cookie name
				$name = $data['name'];

				// It adds the cookie file name to the store.
				// If it has not been added before.
				if(!$file->find($name)) $file->prepend($name);

				// Gets the cookie information,
				$cookie = '['.$name.';'.$data['value'].';'.$data['expire'].';'.$data['created'].';'.$data['httponly'].']';

				// The path to the guest file.
				$path = 'simpler/__storage/visitors/'.$this->using('user')->ip().'.storage';
				
				// Creates a file or adds cookie 
				// information to the end of it.
				$file->isExists($path) ? $file->use($path)->prepend($cookie) : $file->put($path, $cookie);
			} 
			
			/* Error: No create method called. */
			else $this->report('The <i>create</i> method must be called for proper operation!');
		}
	}