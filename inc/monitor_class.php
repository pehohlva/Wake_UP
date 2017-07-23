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

require_once( dirname(__FILE__) . '/wake_conf.php'); /// as index include all file 

/**
 * Class to write as Base to extend Wordpress Plugin
 * Here is only install table sql and sample install action or to deactivate plugin
 *
 * @author Peter H. <pehohlva@gmail.com>
 * @package none 
 */

class Monitor_Wake_Ups {
    
    private static $instance;
    private static $table_index_name;  /* main table name + prefix  */
    private static $table_index_is_install;  /* if main table exist in this process   */ 

   /**
     * Description: Auto load __construct ,  so if possibel to having all parameter database on standby 
     *
     * @param null
     * @return null
     */
    
   function  __construct() {
         self::$instance = 0; 
         $this->initialize();
     }
     
    /**
     * Description: Init the class static so if possibel to having all parameter database on standby 
     *
     * @param null
     * @return null
     */
     
    function initialize() {
        ini_set( 'max_execution_time', 10 );
        date_default_timezone_set('UTC');
        ini_set('html_errors', false);
        self::$table_index_name = self::get_table_index();
        self::$table_index_is_install = self::isdb_run();
        @file_put_contents(WAKEUP_LOGFILE_NOTICE,".NEW initialize NEW.".date('h:i:s')."\n",FILE_APPEND);
        if (self::$table_index_is_install) {
            self::action_log("DB@ ok ******* Beginn zycle Call  ... ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
        } else {
            self::action_log("File@ ok ******* Beginn zycle Call  ... ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug"); 
        }
        
     }
     
      /**
     * Description: 
     *
     * @param null
     *
     * @param null
     *
     * @return null
     */
     
     function isdb_run() {
        $t = self::get_table_index();
        return self::generic_table_exist( $t );
     }
     /*   
     public function getInstance()  {
            if (!isset(self::$instance))
            {
                $class = __CLASS__;
                self::$instance = new $class();
                self::$instance->initialize();
            }
            return self::$instance;
     }
     */
    
     /**
     * Description: 
     *
     * @param null
     * @return null
     */
    
     public function on_save_post() {
         if (strtolower($_SERVER["REQUEST_METHOD"]) == 'post') {
            $data = capture_post_as_json(); /// fast very fast now check if exist name x inside data
            self::action_log($data,"post");
         }
         self::action_log("Call ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug"); 
     }
     
      /**
     * Description: 
     *
     * @param null
     * @return null
     */
     
     public function shutdown_deactivation() {
         //// truncate table here 
         self::action_log("Call ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
         global $wpdb;
         $remsql = "DROP TABLE IF EXISTS ".self::$table_index_name;
         $idx = $wpdb->query( $remsql );
          if ($idx == false) {
              wp_die(wp_sprintf('<strong>Sorry wrong query DROP TABLE IF EXISTS  sql... ' . __FILE__.':'.__LINE__ . ' <strong/>'));  
          } else {
              self::$table_index_is_install = false;
              self::action_log("Call OK.. ".$remsql." ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
          }
         
     }
      /**
     * Description: 
     *
     * @param null
     * @return null
     */
     
     public function shutdown_remove() {
         //// remove tmp file 
         self::action_log("Call ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
         @unlink(WAKEUP_LOGFILE_NOTICE); /// remove debug file 
         @unlink(WAKEUP_LOGFILE_ERROR); /// remove debug file 
     }
     
     /**
     * Description: Install table if not exist in db wordpress
     *
     * @param null
     * @return null
     */
     public function install() {
            // do not generate any output here
            $t = self::get_table_index();
            self::action_log("Call ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
            if (self::generic_table_exist( $t ) ) {
                self::action_log("Table is ready?? ","debug");
            } else {
                //// create table 
              ///  = false;
              self::action_log("Call static go function create table install()","debug");
              global $wpdb;
                        $charset_collate = $wpdb->get_charset_collate();
			$idx = $wpdb->query(
				"CREATE TABLE ".$t." (
				id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                myda_tetime datetime,
				type varchar(22) DEFAULT NULL,
				coreline text ) ".
			        $charset_collate );
                           if ($idx == false) {
                              wp_die(wp_sprintf('<strong>Sorry wrong query sql... ' . __FILE__.':'.__LINE__ . ' <strong/>'));  
                           }
                           $idx = $wpdb->query("ALTER TABLE ".$t." ADD FULLTEXT KEY core_search (coreline)");
                           if ($idx == false) {
                              wp_die(wp_sprintf('<strong>Sorry wrong query sql... ' . __FILE__.':'.__LINE__ . ' <strong/>'));  
                           }
                           ob_start();
                           $wpdb->query("COMMIT"); 
                           $loglast = ob_get_clean();
                           if (strlen($loglast) > 9 ) {
                               self::action_log('Error: '.$loglast,'error');
                            } else {
                                
                                self::action_log("Table is ok and run ","debug");
                            }
                //// end table build         
            }   
            
            self::action_log("Call ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
     }
     /**
     * Description: 
     * @param string on table name to chck if exist.
     * @method bool test if table $table_check name xx exist
     * 
     * @return bool return true/false
     */  
     function generic_table_exist($table_check) {
                    self::action_log("Call ".__FUNCTION__ .":".__FILE__.":".__LINE__.".","debug");
                    if ($table_check == self::$table_index_name) {
                        /// table friend exist 
                        self::$table_index_is_install = true;
                    }
                    global $wpdb;
                    if($wpdb->get_var("SHOW TABLES LIKE '$table_check'") != $table_check) {
                        self::action_log('Debug: FALSE no table found generic_table_exist: '.$table_check,'debug');
                        return false;
                    } else {
                        return true;
                    }
       }
       /**
     * Description: get table name 
     *
     *
     * @return string table name + wordpress prefix 
     */    
       function get_table_index() {
                    /// self::action_log("Call static function install()","debug");
                    global $wpdb;
                    $t = $GLOBALS[ 'wpdb' ]->base_prefix . "wake_up_monitor";
                    return $t;
     }
 

     /**
     * Description: log on file action e debug or go silent at end!
     *
     * @param $rec text to log 
     *
     * @param $type debug error post or other to search in db
     *
     * @return null
     */   
       function action_log( $rec = 'NotGoing' , $type = 'error' ) {
                    ///// register debug action or not?
                    if (WAKEUP_LOG_DEBUG == false && $type == 'debug') {
                        return;
                    }
                    $date = date_create();
                    $timerun = date_timestamp_get($date);
                    $osrun = strtoupper(PHP_OS); //// ".php_uname()."|
                    $textsum = $timerun."|".$osrun."|end firstline.\n".$rec;
                    $queryinsertlog ="";
                    global $wpdb;
                    if ($type == 'postform') {
                       $textsum = $rec; 
                    }
                    
                    if (self::$table_index_is_install && $type!='debug' ) {
                        /// enable to write on database table self::$table_index_name
                        //// $textsum .="\nDatabase is free and can use .. :-)..";
                        $queryinsertlog .="INSERT INTO ".self::get_table_index()." (id, myda_tetime, type, coreline) VALUES (NULL,now(),  '".$type."','".addslashes ($textsum)."')";
                        $wpdb->insert( 
                                    self::get_table_index(), 
                                    array(  'id' => "NULL",
                                            'myda_tetime' => current_time( 'mysql' ), 
                                            'type' => $type, 
                                            'coreline' => addslashes ($textsum),)
                                     );
                    } else {
                        $textsum .=$queryinsertlog ."\n";
                         if (is_writable(WAKEUP_LOGFILE_NOTICE)) {
                            $kbi = @file_put_contents(WAKEUP_LOGFILE_NOTICE,$textsum . "\n",FILE_APPEND);
                            if ($kbi == false ) {
                               wp_die(wp_sprintf('<strong>Sorry, This plugin <' . PLUGIN_W_NAME . '> is unable to write on ' .WAKEUP_LOGFILE_NOTICE. ' </strong>')); 
                            }
                          }
                    }      
                              
      }
}

