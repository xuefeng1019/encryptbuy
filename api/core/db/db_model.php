<?php
class Db_Model extends Db_base
{
    protected static $_forceReadOnMaster = FALSE;
    
    protected $_table = NULL;
    protected $_dbClusterId = NULL;
    protected $_readOnMaster = FALSE;
    //Used with farm db
    protected $_objectId = NULL;
    
    private $_dbInstance = NULL;
    protected $_sqlHelper = NULL;
    private $_lastSql;
    private $_dbWrite = NULL;
    /**
     * 构造器
     * 
     * @param string $table default NULL, 表名，为NULL则不能使用基类提供的数据库操作方法
     * @param int $clusterId default NULL, 数据库cluster id
     * @param int $objectId default NULL, 对象id，用于分库选取用，单库不需要设置此参数
     */
    public function __construct($table = NULL, $clusterId = NULL, $objectId = NULL)
    {
        $this->_table = $table;
        $this->_dbClusterId = $clusterId;
        $this->_objectId = $objectId;
        $this->_sqlHelper = sqlHelper::getInstance();
    }
    
    //force all operate on master
    public static function setForceReadOnMaster($bool)
    {
        Db_Model::$_forceReadOnMaster = $bool;
    }
    protected function _getDbInstance()
    {
    	
//        if($this->_dbInstance)
//        {
//            return $this->_dbInstance;
//        }

        $this->_dbInstance = Db_Global::getInstance(null, null, $this->_readOnMaster);

        if($this->_dbInstance)
        {
        
            return $this->_dbInstance;
        }
        else
        {
            return NULL;
        }
        /*
        if($this->_dbClusterId !== NULL)
        {
            if($this->_objectId !== NULL)
            {
                //$this->_dbInstance = Db_FarmDb::getInstanceByObjectId($this->_objectId, $this->_dbClusterId);
                //$this->_dbInstance = Db_Global::getInstance($this->_dbClusterId);
            }
            else
            {
                //$this->_dbInstance = Db_GlobalDb::getInstance($this->_dbClusterId);
                $this->_dbInstance = Db_Global::getInstance($this->_dbClusterId);
            }
            //var_dump($this->_dbInstance);exit;
            $this->_dbInstance->setReadOnMaster(Db_Model::$_forceReadOnMaster || $this->_readOnMaster);
            return $this->_dbInstance;
        }
        
        return NULL;
        */
    }

    public function insertReplace($insArr, $replaceArr = NUll)
    {
        if($this->_table === NULL)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', __CLASS__ . " property : table is empty");
            }
            return FALSE;
        }
        
        $db = $this->_getDbInstance();
        if(!$db)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', "getDbInstance fail");
            }
            return FALSE;
        }
        
        $this->beforeInsertReplace($insArr, $replaceArr);
        
        $sql = "INSERT `" . $this->_table . "`" . $this->_sqlHelper->replace($insArr, $replaceArr);
        
        $ret = $db->mod($sql);
        $this->_lastSql = $sql;
        
        if($ret === FALSE)
        {
            //log here
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getWriteErrorInfo());
            }
            return FALSE;
        }
        
        $this->afterInsertReplace($insArr, $replaceArr);
        
        return $ret;
    }
    
    
    
    
    public function insert($insArr, $returnLastId = FALSE)
    {
        if($this->_table === NULL)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', __CLASS__ . " property : table is empty");
            }
            return FALSE;
        }
        $this->_readOnMaster = true;
        $db = $this->_getDbInstance();
        
        if(!$db)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', "getDbInstance fail");
            }
            return FALSE;
        }
        
        $this->beforeInsert($insArr);
        $sql = "INSERT `". $this->_table . "`" . $this->_sqlHelper->insert($insArr);

        $ret = $db->mod($sql);
        $this->_lastSql = $sql;
        if($ret === FALSE)
        {
            //log here
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getWriteErrorInfo());
            }
            return FALSE;
        }
        if($returnLastId)
        {
            return $db->getLastId();
        }
        
        $this->afterInsert($insArr);
        
        return $ret;
    }
    
    public function update($where, $upArr, $mode = '')
    {
    	$this->_readOnMaster = TRUE;
        if($this->_table === FALSE)
        {
            return FALSE;
        }
        
        $db = $this->_getDbInstance();
        if(!$db)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', "getDbInstance fail");
            }
            return FALSE;
        }
        
        $this->beforeUpdate($where, $upArr);
        
        $sql = "UPDATE `" . $this->_table . "`" . $this->_sqlHelper->update($upArr) . $this->_sqlHelper->where($where);
        //echo $sql;
        $ret = $db->mod($sql, $mode);
        $this->_lastSql = $sql;
        
        if($ret === FALSE)
        {
            //log here
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getWriteErrorInfo());
            }
            return FALSE;
        }
        
        $this->afterUpdate($where, $upArr);
        return $ret;
    }
    
    
    public function delete()
    {
        if($this->_table === FALSE)
        {
            return FALSE;
        }
        
        $db = $this->_getDbInstance();
        if(!$db)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', "getDbInstance fail");
            }
            return FALSE;
        }
        
        $this->beforeDelete($where);
        
        $sql = "DELETE FROM `" . $this->_table . "`" . $this->_sqlHelper->where($where);
        
        $ret = $db->mod($sql);
        $this->_lastSql = $sql;
        
        if($ret === FALSE)
        {
            //log here
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getWriteErrorInfo());
            }
            return FALSE;
        }
        
        $this->afterDelete($where);
        
        return $ret;
    }
    
    
    public function select($where = array(), $attrs = array())
    {
    	
        if($this->_table === FALSE)
        {
            return FALSE;
        }
        $this->_readOnMaster = FALSE;
        
        $db = $this->_getDbInstance();

        if(!$db)
        {
            return FALSE;
        }
        if(is_callable(array($this, 'beforeSelect', TRUE)))
        {
            $this->beforeSelect($where, $attrs);
        }
        
        $selectFields = isset($attrs['select']) ? $attrs['select'] : '*';
        
        $sql = "SELECT {$selectFields} FROM `" . $this->_table . "`" . $this->_sqlHelper->where($where, $attrs);
		//echo $sql."<br />";
        $res = NULL;
        $this->_lastSql = $sql;
        if($db->select($sql, $res) === FALSE)
        {
            //log here
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getReadErrorInfo());
            }
            
            return FALSE;
        }
        if(is_callable(array($this, 'afterSelect'), TRUE))
        {
            $this->afterSelect($where, $attrs);
        }
        return $res;
    }
    
    public function selectOne($where = array(), $attrs = array())
    {

        $attrs['limit'] = 1;
        $attrs['offset'] = 0;
        
        $res = $this->select($where, $attrs);
        if($res === FALSE)
        {
            //log here
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getReadErrorInfo());
            }
            return FALSE;
        }
        if(empty($res))
        {
            return NULL;
        }
        return $res[0];
    }
    
    
    public function selectCount($where = array(), $attrs = array())
    {
        if(!isset($attrs['select']))
        {
            $attrs['select'] = 'COUNT(0)';
        }
        $attrs['select'] .= ' AS `total`';
        
        $res = $this->selectOne($where, $attrs);
        if($res === FALSE)
        {
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $db->getReadErrorInfo());
            }
            return FALSE;
        }
        return intval($res['total']);
    }
    
    //exceute 拼错了....  防止程序出错， 把正确和错误的都兼容进去....
    public function execute($sql)
    {
        return $this->exceute($sql);
    }
    
    /**
     * Execute sql statement:
     * For select statement, return the rows;
     * For non-select statement, return rows affected;
     * When error, return false
     * 
     * @param string $sql
     */
    public function exceute($sql)
    {
        $method = @strtoupper(array_shift(explode(' ', trim($sql))));
        //file_put_contents('sqldebug.txt', $sql . "\r\n", FILE_APPEND);
        $db = $this->_getDbInstance();
        if(!$db)
        {
            return FALSE;
        }
        
        if(in_array($method, array('SELECT', 'SHOW', 'DESC')))
        {
            $res = NULL;
            if($db->select($sql, $res) === FALSE)
            {
                //log here
                if(function_exists('log_message'))
                {
                    //log_message('error', "[$sql] " . $db->getReadErrorInfo());
                }
                return FALSE;
            }
            $this->_lastSql = $sql;
            return $res;
        }
        else
        {
            $ret = $db->mod($sql, 'a');
            $this->_lastSql = $sql;
            
            if($ret === FALSE)
            {
                //log here
                if(function_exists('log_message'))
                {
                    //log_message('error', "[$sql] " . $db->getWriteErrorInfo());
                }
                return FALSE;
            }
            
            return $ret;
        }
    }
    
    /**
     * Magic函数 
     * 用于实现 get_by_xxx/getByXxx方法 
     */
    
    public function __call($name, $args)
    {
        if(strpos($name, "get_by_") === 0)
        {
            $key = substr($name, 7);
            $value = $args[0];
            return $this->selectOne(array($key => $value));
        }
        
        
    }

    public function setReadOnMaster($bool = TRUE)
    {
        $this->_readOnMaster = $bool;
        if($this->_dbInstance)
        {
            $this->_dbInstance->setReadOnMaster($bool);
        }
    }
    
    public function getTable()
    {
        return $this->_table;
    }
    
    public function table($table = NULL)
    {
        if(empty($table))
        {
            return $this->_table;
        }
        
        $this->_table = $table;
    }
    
    public function getLastSql()
    {
        return $this->_lastSql;
    }
    
/*
    public function __destruct()
    {
        if(isset($this->_dbInstance))
        {
             $this->_dbInstance->close();
             $this->_dbInstance = NULL;
        }
        $this->_sqlHelper = NULL;
    }
    */
    protected function beforeInsert ()
    {}
    protected function afterInsert ()
    {}
    protected function beforeUpdate ()
    {}
    protected function afterUpdate ()
    {}
    protected function beforeInsertReplace ()
    {}
    protected function afterInsertReplace ()
    {}
    protected function beforeDelete ()
    {}
    protected function afterDelete ()
    {}
     
}
