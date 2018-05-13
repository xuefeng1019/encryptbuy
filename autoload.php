<?php
/**
 * load the PDO lib first
 */
include_once APPPATH."core/db/db_sql.php";
include_once APPPATH."core/db/db_base.php";
include_once APPPATH."core/db/db_model.php";
include_once APPPATH."core/db/db_global.php";
//spl_autoload_register('wz_autoload');
include_once FCPATH.'Factory'.EXT;

if(!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}
if(!defined('MODEL'))
{
    define('MODEL', 'orm'.DS);
}
if(!defined('PHP_LIB'))
{
    define('PHP_LIB', APPPATH.MODEL);
}

function autoload($class, $type = 'ORM')
{  
    static $lookUpPath = array();
    
    if(empty($lookUpPath))
    {
        $lookUpPath = array(PHP_LIB);
    }

    $filename = $class;
    $filename .= EXT;  

    foreach($lookUpPath as $libPath)
    { 
        if(file_exists($libPath.$filename))
        {
            require_once $libPath.$filename;
            return ;
        }
    }
}

