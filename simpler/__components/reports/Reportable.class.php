<?php
/**
 *
 * Supports reporting of returned errors/exceptions.
 *
 * @package Simpler
 * @subpackage Reports
 * 
 * 
 * ##################################################################################################
 * # Other used classes (methods or vars) in this module:	                                        #
 * # @see Import{} | File{} | CustomReportMessage{} (extends)                                       #
 * # @uses import() | empty(), content(), prepend(), append(), clean() | customReportMessage()      #
 * ##################################################################################################
 *
 */


    namespace Simpler\Components\Reports;
    use Simpler\Components\{
        Facades\File,
        Randval\Randval,
        Import
    };

    class Reportable extends CustomReportMessage{
        /**
         * 
         * It stores the unique identifier of the report.
         * 
         * @var string|int
         * 
         */
        private $errid;

        /**
         * 
         * Stores versions of report visibility.
         * 
         * @var string
         * 
         */
        private $visibility;

        /**
		 *
		 * Stores the table of settings from
         * the report configuration file.
		 *
		 * @var array
		 *
		 */
        private static $configReports = array();
        
        /**
 		 *
 		 * Stores the path to the report configuration file.
 		 *
 		 * @var const
 		 *
 		 */
		CONST CONFIG_FILE = 'simpler/__config/reports.php';

        /**
 		 *
 		 * Stores the path to the report log file.
 		 *
 		 * @var const
 		 *
 		 */
        CONST LOG_FILE = 'simpler/__log/reports.log';


        ####################################### CONFIG METHOD #######################################


        /**
		 *
         * Gets the params cookie settings from the configuration file.
		 * 
		 * @return void
		 *
         */
		public function setConfigReports() :void{
            $file = new Import;
            self::$configReports = $file->import(self::CONFIG_FILE, true);
        }


        ####################################### VISIBILITY METHOD #######################################


        /**
         * 
         * Checks whether the given version of report
         * visibility is correct.
         * 
         * @return void
         * 
         */
        private function visibilityReports() :void{
            $version = self::$configReports['visibility'];
            $this->visibility = preg_match('/^(private|protected|public)$/', $version) ? $version : 'private';
        }


        ####################################### LOG METHODS #######################################


        /**
		 *
         * Gets a specific report from the log file.
		 * 
         * @param string $phrase - A phrase that is included in the search query.
		 * @return void|array
		 *
         */
        public function getReport(string $phrase){
            $file = new File;
            $file = $file->use(self::LOG_FILE);

            if($file->find($id)){
                $find = $file->findFirst($id);

                // Creates an array of the search report.
                $explode = str_replace(array('[REPORT ID:', 'DATE:', 'MSG:', 'FILE:', 'LINE:', 'FUNCTION:', 'CLASS:', '[', ']'), '', $find);
                $explode = explode('  ', $explode);
                $explode = array_map('trim', $explode);
                
                // Report
                $report = array(
                    'ID'        => $explode[0], 
                    'DATE'      => $explode[1],
                    'MSG'       => $explode[2], 
                    'FILE'      => $explode[3], 
                    'LINE'      => $explode[4], 
                    'FUNCTION'  => $explode[5], 
                    'CLASS'     => $explode[6]
                );

                return $report;
            }
        }


        /**
         * 
         * Saves information from the error report 
         * in the log file.
         * 
         * @param string $errfunc   - Function in which an exception occurred
         * @param string $errclass  - Element of the class with which the problem occurred
         *
         * @return void
         * @uses getReport()
         * 
         */
        private function ReportLog(string $errstr, string $errfile, int $errline, string $errfunc = '', string $errclass = '') :void{
            $file = new File;
            $file->use(self::LOG_FILE);

            // Get current date.
            $current_date = date('Y:m:d H:i:s');

            // Strip HTML tags from a error message.
            $errstr = strip_tags($errstr);

            // Found report
            $report = $this->getReport($file->content(true, false)[$file->length() - 1]);

            // Preventing duplication of reports.
            if($errstr !== $report['MSG'] && $errfile !== $report['FILE'] && $errfunc !== $report['FUNCTION'] && $errclass !== $report['CLASS']){
                // Get forward date.
                $forward_date = date('Y:m:d H:i:s', strtotime($current_date.'+'.self::$configReports['auto_clean']));

                // Gets the first line from the log 
                // file - expiry date of the logs.
                $expiry = !$file->isEmpty() ? $file->content(true)[0] : '';

                // Extracts the expiration date of logs.
                $extract_date = str_replace(array('[', ']'), '', $expiry);

                if(
                    // Checks whether the ruler with the
                    // creation date of the log file is correct.
                    // If it is not, then clean the file and
                    // add the correct line.
                    preg_match('/\[[0-9]{4}:[0-9]{2}:[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\]/', $expiry) != 1 ||

                    //Cleans the file every 12 hours.
                    strtotime($extract_date) <= strtotime($current_date))
                {
                    
                    if(!$file->isEmpty()) $file->clean();
                    $file->append('['.$forward_date.']');
                }

                // Unique Report Identifier
                $randval = new Randval;
                $this->errid = $randval->generate(15, self::$configReports['rand_range']);

                // Adds a new liner except for the log file.
                $func = !empty($errfunc) ? ' [FUNCTION: '.$errfunc.'] [CLASS: '.$errclass.']' : '';
                $file->prepend('[REPORT ID: '.$this->errid.'] [DATE: '.$current_date.'] [MSG: '.$errstr.'] [FILE: '.$errfile.'] [LINE: '.$errline.']'.$func);
            } 
            
            // Places the same error in 
            // errid Unique Report Identifier.
            else $this->errid = $report['ID'];
        }


        ####################################### MESSAGE METHOD #######################################


        /**
         * 
         * The appearance of the error report message.
         * 
         * @param string $errfunc   - function in which an exception occurred
         * @param string $errclass  - Element of the class with which the problem occurred
         * 
         * @return string
         * 
         */
        private function ReportMessage(string $errstr, int $errline, string $errfile, string $errfunc, string $errclass) :string{
            // Displays additional report information
            // in the form of a table.
            $table_report = $this->visibility == 'private' ? '<table><tr><th>FILE</th><th>'.$errfile.'</th></tr><tr><th>LINE</th><th>'.$errline.'</th></tr><tr><th>FUNCTION</th><th>'.$errfunc.'</th></tr><tr><th>ID</th><th>'.$this->errid.'</th></tr><tr><th>CLASS</th><th>'.$errclass.'</th></tr></table>' : '';


            // The displayed Report message depending on
            // the visibility version of the report.
            $errstr = $this->visibility == 'public' ? '<p>Unique Report Identifier: <i>'.$this->errid.'</i></p>' : '<p>'.$errstr.'</p>'.$table_report;


            // Returns the complete sections of the report.
            return '<!-- SIMPLER REPORT --><script>window.onload=function(){for(var e=document.querySelectorAll("link[rel=stylesheet]"),l=0;l<e.length;l++)e[l].parentNode.removeChild(e[l]);var t=document.querySelectorAll("style");for(l=0;l<t.length;l++)"simpler_style"!==t[l].getAttribute("id")&&t[l].parentNode.removeChild(t[l])};</script><style id="simpler_style">h1, p, th, a, u{font:normal 13px Basic, sans-serif;color:#999}ul, li, p, body, html, h1{margin:0;padding:0}li{list-style:none}a{text-decoration:none;margin-top:10px;color:#386bee}a:hover, i{text-decoration:underline}section{height:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;text-align:center;width:100%;height:100%;background:#fafafa;position:fixed;top:0;left:0;right:0;z-index:999999999999}h1{font-size:14px;font-weight:bold}p{margin:5px 15px;max-width:600px}table{border-collapse:collapse;border-spacing:0;margin-top:15px}table th, table tr{padding:10px;border:1px solid #e5e5e5;background:#fff}i{font-style:normal;color:#FF5F5F}footer p{color:#cfcfcf;font-size:12px}</style><section class="sr" id="simpler_report"><img src="https://i.imgur.com/PwAmfWm.png" alt="Simpler"><h1>The Simpler engine reported the following exception:</h1>'.$errstr.'<a href="/" target="__blank" title="Simpler documentation">Documentation</a><footer><p>Simpler version '.__SIMPLER_VERSION__.'<p></footer></section><!-- END SIMPLER REPORT -->';
        }


        ####################################### THROW METHOD #######################################


        /**
         * 
         * Displays the appropriate report and saves 
         * the error information to the log file.
         * 
         * @param string|object $err - Error message or object
         * @return void
         * 
         * @uses ReportLog(), ReportMessage()
         * 
         */
        public function report($err) :void{
            // Checks the visibility versions of the report.
            $this->visibilityReports();

            /* Call to string $err argument. */
            if(is_string($err)){
                $exists = false;
                $debug = debug_backtrace();

                // Gets the index of the first 
                // instance of the require function.
                $i = 0;
                foreach($debug as $error){
                    if($error['function'] === 'require'){
                        $exists = true;
                        break;
                    }

                    $i++;
                }
                
                // Retrieves data from the last call
                // to the report method.
                // Bugfix
                $debug = $exists ? $debug[$i - 1] : $debug[count($debug) - 1];

                // Displays the error report and places
                // it in the log file.
                $this->ReportLog($err, $debug['file'], $debug['line'], $debug['function'], $debug['class']);
                die($this->ReportMessage($err, $debug['line'], $debug['file'], $debug['function'], $debug['class']));
            }

            /* Call to object $err argument. */
            elseif(is_object($err)){
                // Displays the error report and places
                // it in the log file.
                $this->ReportLog($err->getMessage(), $err->getFile(), $err->getLine());
                die($this->customReportMessage($this->visibility, $err->getMessage(), $err->getFile(), $err->getLine(), $this->errid));
            }

            /* Error: Diffrent type $err arguement. */
			else $this->report('Incompatible type <i>'.gettype($err).'</i> of <u>report(string | object $err)</u> function param!');
        }
    }