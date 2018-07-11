<?php
/**
 *
 * It supports its custom report messages.
 *
 * @package Simpler
 * @subpackage Reports
 * 
 */


    namespace Simpler\Components\Reports;

    abstract class CustomReportMessage{
        /**
         * 
         * The structure of the custom error message.
         * 
         * @param string $visibility    - version of report visibility
         * @param string $errstr        - Report message
         * @param string $errfile       - The file in which the error occurre
         * @param int $errline          - The line number in which the error occurred
         * @param int $errid            - Unique Report Identifier
         * 
         * @return string
         * 
         */
        public function customReportMessage(string $visibility, string $errstr, string $errfile, int $errline, string $errid) :string{
            //Displays additional report information
            //in the form of a table.
            $table_report = $visibility == 'private' ? '
            <table>
                <tr><th>FILE</th><th>'.$errfile.'</th></tr>
                <tr><th>LINE</th><th>'.$errline.'</th></tr>
                <tr><th>ID</th><th>'.$errid.'</th></tr>
            </table>' : '';


            //The displayed Report message depending on
            //the visibility version of the report.
            $errstr = $visibility == 'public' ? '<p>Unique Report Identifier: <i>'.$errid.'</i></p>' : '<p>'.$errstr.'</p>'.$table_report;


            //The complete sections of the report.
            $custom_msg = '
            <!-- SIMPLER REPORT -->
            <style>
                h1, span, p, th, a{font:normal 13px Basic, sans-serif;color:#999}ul, li, p, body, html, 
                h1{margin:0;padding:0}li{list-style:none}a{text-decoration:none;margin-top:10px;color:#386bee}a:hover, i{text-decoration:underline}
                section{
                    height:100%;display:flex;justify-content:center;align-items:center;flex-direction:column;
                    text-align:center;width:100%;height:100%;background:#fafafa;position:fixed;top:0;left:0;right:0;z-index:999999999999
                }
                h1{font-size:14px;font-weight:bold}p{margin:5px 15px;max-width:600px}table{border-collapse:collapse;border-spacing:0;margin-top:15px}
                table th, table tr{padding:10px;border:1px solid #e5e5e5;background:#fff}i{font-style:normal;color:#FF5F5F}
            </style>
            
            <section class="sr" id="simpler_report">
                <!-- SIMPLER LOGO --><img src="https://i.imgur.com/PwAmfWm.png" alt="Simpler"><!-- # -->
                <h1>The Simpler engine reported the following exception:</h1>
                <!-- REPORT MESSAGE -->'.$errstr.'<!-- # -->
            </section>
            <!-- END SIMPLER REPORT -->';

            return $custom_msg;
        }
    }