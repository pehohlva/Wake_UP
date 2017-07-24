<?php

/**
 * Plugin Name: Wake_UP
 * Plugin URI:  http://www.google.com/
 * Text Domain: wake_up
 * Description: Register Global Post & Register or send wake_up as action-hooks. Wordpress Plugin Help Objects
 * Version:     1.1.0-dev
 * License:     GPL-3+
 * Author:      Peter H.
 * Author URI:  http://www.google.com/
 *
 * @version 2017-07-23
 * @package Wake_UP
 */

/* 
 * check the file for error or notice wake_error.log wake_notice.log in linux /tmp or mac /private/var/tmp
 * dir that php can write by default as upload dir or other session action 
define('WAKEUP_TMPDIR',get_tmp_dir());
define('WAKEUP_LOGFILE_ERROR',WAKEUP_TMPDIR.'wake_error.log');
define('WAKEUP_LOGFILE_NOTICE',WAKEUP_TMPDIR.'wake_notice.log');
*/

ini_set( 'max_execution_time', 10 );
//// date_default_timezone_set('UTC');
ini_set('html_errors', false);
        
ob_start();
require_once( dirname(__FILE__) . '/inc/wake_conf.php'); /// as index include all file 
require_once( dirname(__FILE__) . '/inc/common_help_wake_up.php');
require_once( dirname(__FILE__) . '/inc/monitor_class.php');

/// capture contact form 7 post data:
if ( isset($_POST["_wpcf7_locale"]) ) {
    $uri =  $_SERVER["REQUEST_URI"]; /// if contact form 7 url request? 
    $find_uri = strpos($uri,'contact-form-7'); /// is this exact from _wpcf7_version contact form?
    $fromref = $_SERVER["HTTP_REFERER"];
    $xkernel = capture_post_as_json();
    $find_post = strpos($xkernel,'_wpcf7_');
    $postid = url_to_postid( $fromref );
    //// not inject code from outside!
    if ($postid > 0 && $find_post > 0 && $find_uri >0 ) {
    $post_title = get_the_title( $postid );
    $post_url = get_permalink( $postid );
    $message = "New request:\n"; 
    $message .= "Time:".date('h:i:s')."\n";
    $message .= "Uri:".$uri."\n";
    $message .= "Referer:".$fromref."\n";
    $message .= "A post has been updated on your website:\n\n";
    $message .= $post_title . ": " . $post_url;
    $message .= "\n\n";
    $admin_email = get_option('admin_email');
    $message .= "Mail to:".$admin_email." \n\n";
    /// $message .= "Referrer fii :".$find_uri."\n";  
    /// $message .= "Referrer fipo :".$find_post."\n"; 
    $message .= "Arg json:".$xkernel."\n"; /// flat array of post
    $argumentsall = redecode_back_json($xkernel);
    if (is_array($argumentsall)) {
        foreach($argumentsall as $key => $value) {
           $message .= $key."-> (".htmlspecialchars($value).")   \n";
        }
    }
    /// wp_mail($admin_email,"New post on Page ".$post_title,$message); /// send mail ... uncomment here to send mail
    register_error_lambda($message,__FILE__,__LINE__); /// log on tmp file
    $obj = new Monitor_Wake_Ups();   /// insert to db here 
    $obj->action_log($message,'contact-form-7'); /// insert to db here as type contact-form-7
    }
}

function capture_post_wakeup( $post_id = 0 ) {
    if ( wp_is_post_revision( $post_id ) )
		return;
    
    $xkernel = capture_post_as_json();
    
    $post_title = get_the_title( $post_id );
    $post_url = get_permalink( $post_id );
    
    $message = "A post has been updated on your website:\n\n";
    $message .= $post_title . ": " . $post_url;
    $message .= "\n\n";
    $message .= $xkernel; /// reformat 
    register_error_lambda($message,__FILE__,__LINE__);
	// Send email to admin.
    /// wp_mail( 'admin@example.com', $subject, $message );
    
}

function _v110_install_wakeup() {
    $obj = new Monitor_Wake_Ups();  
    $obj->install();
}

function _v110_deactivation_wakeup() {
    $obj = new Monitor_Wake_Ups();  
    $obj->shutdown_deactivation();
}

function _v110_uninstall_wakeup() {
    $obj = new Monitor_Wake_Ups();  
    $obj->shutdown_remove();
}
           
 register_activation_hook( __FILE__,'_v110_install_wakeup');
 register_deactivation_hook( __FILE__,'_v110_deactivation_wakeup'); //// remove table 
 register_uninstall_hook( __FILE__, '_v110_uninstall_wakeup' ); //// remove log file  
            //// add_action( 'save_post', 'capture_post_wakeup' );
  
 
 
 
 
 
 

$autodebug = ob_get_clean();
if ( strlen($autodebug) > 1 ) {
    register_error_lambda($autodebug,__FILE__,__LINE__);
}


 /// end            