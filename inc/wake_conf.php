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

require_once( dirname(__FILE__) . '/common_help_wake_up.php');
require_once( dirname(__FILE__) . '/monitor_class.php');

/*
$fileneed = array('common_help_wake_up.php','monitor_class.php');

foreach ($fileneed as &$file) {  
    $f = dirname(__FILE__) .'/'. $file;
    try {
        if (!file_exists ($f))
             throw new Exception ($f.' does not exist');
        else
        require_once($f); 
     } /// end try inlude file ...
     
            catch (Exception $ex) {
            echo "Message : " . $ex->getMessage();
            echo "Code : " . $ex->getCode();
    }
}  
*/


///  ABSPATH root from wordpress possibel == $_SERVER['DOCUMENT_ROOT']
//// define('WAKEFILETMP',"log_action.txt");
define('PLUGIN_W_NAME','Wake_UP');
define('WAKEUP_TMPDIR',get_tmp_dir());
define('WAKEUP_LOGFILE_ERROR',WAKEUP_TMPDIR.'wake_error.log');
define('WAKEUP_LOGFILE_NOTICE',WAKEUP_TMPDIR.'wake_notice.log');
define('WAKEUP_LOG_DEBUG',false); //// only to file not db
//// define('CAPTURE_POST_ARRAY',array('_wpcf7_version','morgana','fatima'));
define('PLUGIN_WORK_MODUS',1);  /// 1/2 todo future sqlite3 db ... 
define('CURRENT_CORE_ACTION',current_action_fly()); /// post or get lowercase 

