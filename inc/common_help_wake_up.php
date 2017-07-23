<?php

/**
 * Plugin Name: Wake_UP
 * Plugin URI:  http://www.google.com/
 * Text Domain: wake_up
 * Description: Register Global Post & Register or send and action-hooks. Wordpress Help Objects
 * Version:     1.1.0-dev
 * License:     GPL-3+
 * Author:      Peter H.
 * Author URI:  http://www.google.com/
 *
 * @version 2017-07-23
 * @package Wake_UP
 */

if ( !defined('ABSPATH') ) {
     echo "Hi there! ABSPATH is not defined! ";
     exit;
}
//// function from wordpress to activate....
$function_need_here = array('add_action','register_activation_hook',
                            'register_deactivation_hook','register_uninstall_hook');
//// function from wordpress to activate....
foreach ($function_need_here as &$fu) {  
    if (!function_exists($fu)) {
        ///echo "<!-- add_action function not exist out of box -->";
        throw new Exception ('Function '.$fu.' Not exist!');
    }
}  

require_once( dirname(__FILE__) . '/wake_conf.php'); /// as index include all file 

/// helper function 

     /**
     * @method string 
     * @todo test on other system ... 
     * @return string full dir path to can write
     */
     function get_tmp_dir() {
         $osrun = strtoupper(PHP_OS);
         $tmpfname = tempnam( sys_get_temp_dir(), 'WAKEsummer');
         @file_put_contents($tmpfname,"\n");
         $tmp_dir = "/unknow_____";
          if (strpos($osrun,"LINUX")  ) {
                       @unlink($tmpfname);
                       return '/tmp/';
           }
                        if (file_exists($tmpfname)) {
                            $tmp_dir = dirname($tmpfname) .'/';
                            @unlink($tmpfname);
                        if (   strpos($osrun,"DARWI")  ) {
                            $tmp_dir = dirname($tmpfname) .'/tmp/'; /// mac special each php version??!
                        }
                         return $tmp_dir;
                       } else {
                          return ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir(); 
                       }
         
      }
     

function register_error_lambda($error = 'none' , $file =null,$line=null) {
    /// write on file WAKEUP_LOGFILE_ERROR 
    $text ="Error found:\n";
    $text .="File:".$file.":".$line." \n";
    $text .="TXT:".$error." \n";
    $kbi = @file_put_contents(WAKEUP_LOGFILE_ERROR,$text,FILE_APPEND);
    if ($kbi == false ) {
        throw new Exception ('Unable to write log in function register_error_lambda.');
    }
}

function current_action_fly() {
   return strtolower($_SERVER["REQUEST_METHOD"]); 
}

function is_ajax_request() {
    $header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;
    return ($header === 'XMLHttpRequest');
}

function redecode_back_json($m = 'from_capture_post_as_json') {
            $mo = json_decode($m,true);
            if (!is_array($mo)) {
                return array();
            }
            $xdata =array();
            foreach($mo as $key => $value) {
                if ( is_array($value) ) {
                    $bar = each($value);
                    $xdata[$bar['key']]=htmlspecialchars_decode($bar['value']); /// make native back!
                }
            }
            return $xdata;
}

function capture_post_as_json() {
    $set =(int)0;
    $valuenow ='';
    $method='post';
    ob_start();
    if (current_action_fly() == 'post') {
        if (is_ajax_request()) {
          $method=strtolower('post_XMLHttpRequest');  
        }
        $x = $_POST;
        $array[0]['method']=$method;
        if (is_array($x)) {
                 foreach($x as $key => $value) {
                        $set++;
                        $array[$set][$key]= htmlspecialchars($value);
                     }
                    //// fast way better as php serialize....
                    $valuenow = json_encode($array,JSON_FORCE_OBJECT);
        }
    }
    $errors = strip_html(ob_get_clean());
    if (strlen($errors) > 0 ) {
       @register_error_lambda($errors,__FILE__,__LINE__); 
    }
    //// JSON_FORCE_OBJECT as multiple array!
    return $valuenow;
}



function strip_html($text, $tags = '', $invert = FALSE) { 
    /// check if tidy exist or dom load html
    return strip_tags($text);
} 



