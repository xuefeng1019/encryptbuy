<?php
class Db_base 
{
    private $_charset = 'utf8';
	private $_dbWrite;
	private $_dbRead;
    
	private $_dbName;
	private $_dbUser;
	private $_dbPwd;
	private $_readServers;
	private $_writeServers;
	private $_res;

	protected $_readOnMaster = FALSE;

	const ERR = -1;
	const FETCH_ALL = 0;
	const FETCH_ONE = 1;

	protected function __construct($dbName, $dbUser, $dbPwd, $readServers, $writeServers)
	{
		$this->_dbName = $dbName;
		$this->_dbUser = $dbUser;
		$this->_dbPwd	   = $dbPwd;
		$this->_readServers = $readServers;
		$this->_writeServers = $writeServers;
	}


    public static function & getInstance($dbName, $dbUser, $dbPwd, $readServers, $writeServers)
    {
        $obj = new self($dbName, $dbUser, $dbPwd, $readServers, $writeServers);
        return $obj;
    }


    public function setReadOnMaster($operate = TRUE)
    {
        $this->_readOnMaster = $operate;
    }

    /**
     * 获取SQL查询结果
     *
     * @param string  $sql
     * @param array   $res Out parameter, array to be filled with fetched results
     * @param integer $fetchStyle 获取命名列或是数字索引列，默认为命令列
     * @param integer $fetchMode  获取全部或是一行，默认获取全部
     * @return boolean|integer false on failure, else return count of fetched rows
     */

    public function select($sql, &$res, $fetchStyle = PDO::FETCH_NAMED, $fetchMode = self::FETCH_ALL)
    {
        try
        {
            if($this->_readOnMaster)
            {
                $db = &$this->getDbWrite();
            }
            else
            {
                $db = &$this->getDbRead();
            }

            //log_message("Execute Sql: $sql", LOG_DEBUG);
            if(function_exists('log_message'))
            {
                //log_message('error', "Execute Sql: $sql");
            }
            //file_put_contents('E:\wnmp\www\wolongge\wlg\logs\sqltest.php', $sql."\n", FILE_APPEND);
            //ETS::start(STAT_ET_DB_QUERY);//log message about runtime
            //echo $sql;
            //log_message('debug', "[$sql] ");
            //echo "<br>";
            //$sql = $db->quote($sql);
            $this->_res = $db->query($sql);

            if($this->_res === FALSE)
            {
                //log here
                if(function_exists('log_message'))
                {
                    //log_message('error', "[$sql] " . $db->getReadErrorInfo());
                }
                return FALSE;
            }
            
            //ETS::end(STAT_ET_DB_QUERY, $sql);

            if($fetchMode === self::FETCH_ALL)
            {
                $res = $this->_res->fetchAll($fetchStyle);
                return count($res);
            }
            elseif($fetchMode === self::FETCH_ONE)
            {
                $res = $this->_res->fetch($fetchStyle);
                $res->_res->closeCursor();
                return $res ? 1 : 0;
            }
            else
            {
                return FALSE;
            }
            
        } catch(PDOException $e)
        {
            //log
            //error_report();
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] : " . $e->getMessage());
            }
            if(function_exists('show_error'))
            {
                show_error($e->getMessage());
            }
            //var_dump($e);
            return FALSE;
        }
    }

    /**
     *  获取查询结果下一行
     *  
     *  @param array $res Out parameter, array to be filled with fetched results
     *  @param integer $fetchStyle same as select method
     *  @return boolean false on failure, true on success
     */

    public function fetchNext(&$res, $fetchStyle = PDO::FETCH_NAMED)
    {
        if(!empty($res))
        {
            try{
                $res = $this->_res->fetch($fetchStyle);
            }
            catch(Exception $e){
                //log here
                //error_report
                if(function_exists('log_message'))
                {
                    //log_message('error', $e->getMessage());
                }
                if(function_exists('show_error'))
                {
                    show_error($e->getMessage());
                }
                return FALSE;
            }
            return TRUE;
        }

        return FALSE;
    }

    /**
     * update/delete/insert/replace sql use this method
     *
     * @param string $sql sql语句
     * @param string $mode if is 'a', return affected rows count, else return boolean
     * @return boolean|integer 
     */
    public function mod($sql, $mode = '')
    {
        try{
            
            $db = &$this->getDbWrite();
            
            //log_messge()
            //runtime counter start  
            //$db->exec("SET CHARACTER SET {$this->_charset}");
            $res = $db->exec($sql);
            
            if($res === FALSE)
            {
                //log
                if(function_exists('log_message'))
                {
                    //log
                    if(function_exists('log_message'))
                    {
                        //log_message('error', "[$sql] " . $db->getWriteErrorInfo());
                    }
                }
                return FALSE;
            }
            //runtime counter end
        } catch(PDOException $e){
            //print_r($this->_dbWrite->errorInfo());exit;
            //log
            if(function_exists('log_message'))
            {
                //log_message('error', "[$sql] " . $e->getMessage());
            }
            if(function_exists('show_error'))
            {
                show_error("" . $e->getMessage());
            }
            
            return FALSE;
        }
        
        if($mode == 'a')
        {
            return $res;
        }

        return true;
    }

    public function & getDbWrite()
    {
        $badServerHosts = array();
        if(!$this->_dbWrite)
        {
            $this->_dbWrite = $this->_selectDB($this->_writeServers, $badServerHosts);
        }
        //print_r($this);exit;
        if($this->_dbWrite)
        {
            return $this->_dbWrite;
        }
        
        //log here
        if(function_exists('log_message'))
        {
            //log_message('error', 'DB Write:Connect to host(s) failed:' . implode(',', $badServerHosts));
        }
        return FALSE;
    }

    public function & getDbRead()
    {
        $badServerHosts = array();
        if(!$this->_dbRead)
        {
            $this->_dbRead = $this->_selectDB($this->_readServers, $badServerHosts);
        }

        if($this->_dbRead)
        {
            return $this->_dbRead;
        }

        //log here
        if(function_exists('log_message'))
        {
            //log_message('error', 'DB Read:Connect to host(s) failed:' . implode(',', $badServerHosts));
        }
        //if not return read, use the wirte db
        $this->_dbRead = $this->getDbWrite();

        return $this->_dbRead;
    }

    private function _selectDB($servers, &$badServerHosts = array())
    {
        //is indexed array?
        if(!isset($servers[0]))
        {
            $servers = array($servers);
        }
        
        $activeServers = array();
        $badServerHosts = array();

        foreach($servers as &$server)
        {
            if(!isset($server['weight']))
            {
                $server['weight'] = 1;
            }

            if($this->_isServerOk($server))
            {
                $activeServers[] = $server;
            }
            else
            {
                //log here
                if(function_exists('log_message'))
                {
                    //log_message('error', 'DB Cluster:Bad status:' . $server['host']);
                }
            }
        }
        
        //unset the & 
        unset($server);

        if(count($activeServers) == 0)
        {
            //none server active, try every server
            $activeServers = $servers;
        }

        $weights = 0;
        foreach($activeServers as $server)
        {
            $weights += $server['weight'];
        }

        $dbName = $this->_dbName;
        while($activeServers)
        {
            $ratio = rand(1, $weights);
            $weightLine = 0;
            $selectIndex = -1;
            //log
            foreach($activeServers as $index => $server)
            {
                $weightLine += $server['weight'];
                if($ratio <= $weightLine)
                {
                    $selectIndex = $index;
                    break;
                }
            }

            if($selectIndex == -1)
            {
                $selectIndex = array_rand($activeServers);
            }

            $server = $activeServers[$selectIndex];
            unset($activeServers[$selectIndex]);
            
            //log here
            if(function_exists('log_message'))
            {
                //log_message('debug', "DB CLUSTER: Choose server {$server['host']}:{$server['port']}.");
            }
            $dsn = "mysql:host={$server['host']};port={$server['port']};dbname={$dbName}";
            $pdo = NULL;
            //var_dump(PDO::MYSQL_ATTR_INIT_COMMAND);
            try{
                //runtime start count
                $pdo = new PDO($dsn, $this->_dbUser, $this->_dbPwd, array(
                                                                    //PDO::MYSQL_ATTR_USE_BUFFERED_QUREY => TRUE,
                                                                    PDO::ATTR_EMULATE_PREPARES => TRUE,
                                                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                                                    PDO::ATTR_TIMEOUT => 10,
                ));
                $pdo->exec("SET NAMES " . $this->_charset);
                //runtime end count
                //var_dump($r);exit;

            } catch(PDOEXCEPTION $e){
                //log
                if(function_exists('log_message'))
                {
                    //log_message('error', $e->getMessage());
                }
                if(function_exists('show_error'))
                {
                    show_error($e->getMessage());
                }
                $pdo = NULL;
            }

            $this->_setServerStatus($server, !! $pdo);

            if($pdo)
            {
                return $pdo;
            }
            else
            {
                $badServerHosts[] = $server['host'];
                $weights -= $server['weight'];
            }
        }

        return NULL;
    }

    protected function _isServerOk($server)
    {
        $key = "server_status_{$server['host']}_{$server['port']}";
        if(function_exists('xcache_get'))
        {
            $status = @xcache_get($key);
            if(is_numeric($status))
            {
                return !empty($status);
            }
        }
        
        //memcache get key here

        return TRUE;
    }

    protected function _setServerStatus($server, $status)
    {
        $key = "server_status_{$server['host']}_{$server['port']}";
        $status = $status ? '1' : '0';
        $cacheTime = 60;//1 min

        return '';
    }

    /**
     * 获取上次insert操作时得到的自增id
     *
     * @return integer
     */
    protected function getLastId()
    {
        if($this->_dbWrite)
        {
            return $this->_dbWrite->lastInsertId();//pdo::lastInsertId
        }
        return 0;
    }

    /**
     * 获取sql读取错误信息
     *
     * @return string
     */
    protected function getReadErrorInfo()
    {
        if(!$this->_readOnMaster)
        {
            $db = $this->_dbRead;
        }
        else
        {
            $db = $this->_dbWrite;
        }

        if(!empty($db))
        {
            $err = $db->errorInfo();
            return $err[2];
        }

        return "Db Reader Not initiated\n";
    }

    /**
     * 获取sql写入错误信息
     *
     * @return string
     */
    protected function getWriteErrorInfo()
    {
        if(!empty($this->_dbWrite))
        {
            $err = $this->_dbWrite->errorInfo();
            return $err[2];
        }
        
        return "DB Writer Not initiated\n";

    }

    /**
     * 判断上次错误是否由于重复key引起
     *
     * @return boolean
     */
    public function isDuplicate()
    {
        if(!empty($this->_dbWrite))
        {
            $err = $this->_dbWrite->errorInfo();
            return $err[1] == 1062;
        }
        return FALSE;
    }

    public function close()
    {
        if($this->_dbWrite)
        {
            $this->_dbWrite = NUll;
        }

        if($this->_dbRead)
        {
            $this->_dbRead = NULL;
        }
    }
}
?>