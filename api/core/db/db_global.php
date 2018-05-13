<?php
class Db_Global extends Db_Base
{
    protected static $_instances;
    
/*
    protected function __construct(
        $dbName, $dbUser, $dbPwd, $readServers, $writeServers
    )
    {
        parent::__construct(
            $dbName, $dbUser, $dbPwd, $readServers, $writeServers 
        );
    }
    */
    /**
     * 获取global db 对象
     *
     * @param integer $clusterId
     * @param boolean $singleton 是否使用单例模式
     * @return object Db_GlobalDb
     */
    public static function & getInstance($clusterId = 'dev', $singleton = TRUE, $isMaster = false)
    {
    	if ($clusterId == null || $clusterId == '' || $clusterId == false) {
    		$clusterId = 'dev';
    	}
        // Is the config file in the environment folder?
		if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = CONFIG_PATH.'/'.ENVIRONMENT.'/app_db.php'))
		{
			if ( ! file_exists($file_path = CONFIG_PATH.'/app_db.php'))
			{
                if(function_exists('show_error'))
                {
                    show_error('The configuration file app_db.php does not exist.');
                }
			}
		}
        //include($file_path); //load the db config
        $CI = &get_instance();
        $db = $CI->config->item('db');
        $db_config = $db[$clusterId];
        if (!$isMaster) {
        	return parent::getInstance($db_config['dbname'], $db_config['read']['username'], $db_config['read']['password'], 
                    $db_config['read'], 
                    $db_config['write']);
        } else {
        	return parent::getInstance($db_config['dbname'], $db_config['write']['username'], $db_config['write']['password'], 
                    $db_config['read'], 
                    $db_config['write']);
        }
    }
}
