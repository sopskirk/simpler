<?php
/**
 *
 * Gets information about the user 
 * who visits the page.
 *
 * @package Simpler
 * @subpackage User
 *
 */


    namespace Simpler\Components\User;

    class User{
        /**
		 *
		 * It stores information from 
         * the user agent.
		 *
		 * @var array
		 *
		 */
        private $useragent;


        /*
         * 
         * Assigns current information from 
         * the user agent to the variable.
         * 
         */
        public function __construct(){
            $this->useragent = $_SERVER['HTTP_USER_AGENT'];
        }


		//======================================================================
		// GET METHODS
		//======================================================================


        /**
         * 
         * Gets the guest IP address.
         * Converts the IP address to the int type.
         * 
         * @return int
         * 
         */
        public function ip() :int{
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];

            // Gets the IP address.
            if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
            elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
            else $ip = $remote;

            // When you are on localhost.
            if($ip == '::1') $ip = '127.0.0.1';

            // Converts IP to int.
            return ip2long($ip);
        }


        /**
         * 
         * Gets the guest device.
         * 
         * @return string
         * 
         */
        public function device() :string{
            if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle
            |lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket
            |psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $this->useragent)
            
            ||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)
            |amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)
            |br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw
            |da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)
            |esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit
            |hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro
            |idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)
            |lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)
            |mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)
            |n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg
            (13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)
            |qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)
            |sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6
            (00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)
            |utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|
            w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($this->useragent, 0, 4))) return 'mobile';

            else return 'pc';
        }


        /**
         * 
         * Gets the guest lang browser.
         * 
         * @return string
         * 
         */
        public function lang() :string{
            // Extracts the language short code.
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            return $lang;
        }


        /**
         * 
         * Gets the guest operating system.
         * The system name and its version are returned.
         * Example: Windows NT 10.0
         * 
         * @return string
         * 
         */
        public function system() :string{
            // Positions
            $first 	= stripos($this->useragent, '(');
            $last 	= stripos($this->useragent, ')');

            // Extracts the system name 
            // and other information.
            $system = str_replace('(', '',substr($this->useragent, $first, $last - $first));

            // Getting rid of unnecessary information.
            $place 	= stripos($system, ';');

            // Returns the name of the operating system.
            return substr($system, 0, $place);
        }


        /**
         * 
         * Gets the guest browser name.
         * 
         * @return string
         * 
         */
        public function browser() :string{
            // Searches for the guest browser name.
            if(strpos($this->useragent, 'Opera') !== false || strpos($this->useragent, 'OPR/') !== false) return 'Opera';
            elseif(strpos($this->useragent, 'Edge') !== false) return 'Edge';
            elseif(strpos($this->useragent, 'Chrome') !== false) return 'Chrome';
            elseif(strpos($this->useragent, 'Safari') !== false) return 'Safari';
            elseif(strpos($this->useragent, 'Firefox') !== false) return 'Firefox';
            elseif(strpos($this->useragent, 'MSIE') !== false || strpos($this->useragent, 'Trident/7') !== false) return 'Internet Explorer';

            // Unlikely
            return 'Unknow';
        }
    }