<?php
class MY_Model extends CI_Model {

	var $user_info = array();
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('array');
        //print_r($_SESSION);
        if (isset($_COOKIE['user']) && $_COOKIE['user'] != null && $_COOKIE['user'] != '') 
        {
            $u_cookie = json_decode($_COOKIE['user'], true); 
            if (!isset($u_cookie['active'])) {
                $u_cookie = F::$f->userORM->initUserInfo($u_cookie['uid']);
                setcookie('user', json_encode($u_cookie, true), 0, '/');
            }
            if(!empty($u_cookie) && $u_cookie)
            {
                //$uinfo    = F::$f->userORM->initUserInfo($u_cookie['uid']);
                //if ($uinfo) {
                //    unset($uinfo['password']);
                    $this->user_info = $u_cookie;
                    $this->uid = $u_cookie['uid'];
                //    $this->uid       = $u_cookie['uid'];
                //}
            }
        }
    }
    
    protected function getUname($data)
    {
        $uname = array();
        if(!empty($data))
        {
            $uids = array_get_column($data, 'uid');
            $uids = array_unique($uids);
            $uname = F::$f->userORM->getUnameByUids($uids);
            array_change_key($uname, 'uid');
        }
        
        return $uname;
    }
    

    public function setCookie($key, $value, $time) {
        if ($time) {
            $time = time() + $time;
        } else {
            $time = 0;
        }
        setcookie($key, json_encode($value, true), $time, '/');
    }
}