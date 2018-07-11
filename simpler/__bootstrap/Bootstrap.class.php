<?php
/**
 * 
 * This is the base class that connects all Simpler modules.
 * 
 * @package Simpler
 * @subpackage Bootstrap
 * 
 * 
 * ##########################################################
 * # Other used classes (methods or vars) in this module:	#
 * # @see Import{} (extends) | Reportable{} (extends)		#
 * # @uses import() | report() 								#
 * ##########################################################
 * 
 */


	namespace Simpler;
	use Simpler\Components\{
		Facades\File, Facades\Dir,
		Http\Cookie, Http\Session,
		Put\Assets,
		Randval\Randval,
		Safety\Valid,
		User\User,
		Import
	};

	class Bootstrap extends Import{
		/**
		 *
		 * Sets the new object to the holder as needed.
		 *
		 * @param string $component - The name of the component used
		 * @return object|void
		 *
		 */
		public function using(string $component){
			$component = strtolower($component);

			switch($component){
				case 'dir'		: return new Dir;break;
				case 'file'		: return new File;break;
				case 'cookie' 	: return new Cookie;break;
				case 'session'	: return new Session;break;
				case 'assets'	: return new Assets;break;
				case 'valid'	: return new Valid;break;
				case 'randval'	: return new Randval;break;
				case 'reports'	: return new Reportable;break;
				case 'user'		: return new User;break;
				default : $this->report('The given parameter <i>'.$component.'</i> for the function <u>using</u> has not been recognized!');
			}
		}


		/**
		 *
		 * Sets of basic settings for:
		 * - Import,
		 * - Reportable,
		 * - Sessions,
		 * - Cookies
		 *
		 * @param bool $regenerate_id - Off session_regenerate_id
		 * @return void
		 *
		 */
		public function setConfig(bool $regenerate_id = false) :void{
			$this->setConfigImport();
			$this->setConfigReports();

			$this->using('session')->setConfigSession($regenerate_id);
			$this->using('cookie')->setConfigCookie();
		}
	}
