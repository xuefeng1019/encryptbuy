<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model  extends MY_Model
{
    const LIMIT = 10;

    public function __construct()
    {
        parent::__construct();
    }

    public function check_black($uid, $to_uid) {
        if (!$uid || !$to_uid) {
            return false;
        }
        $sql = "select id from app_black where ((uid=" . $to_uid . " and tuid=" . $uid . ") or (uid=" . $uid . " and tuid=" . $to_uid . ")) and is_del=0;";
        //$check = F::$f->blackORM->selectOne(array('tuid' => $uid, 'uid' => $to_uid, 'is_del' => 0), array('select' => 'id'));
        $check = F::$f->blackORM->execute($sql);
        if ($check) {
            return -1;
        }
        return false;
    }

    public function getUserAddress($uid) {
        if (!$uid) {
            return [];
        }
        $address = F::$f->addressORM->select(['user_id' => $uid]);
        if ($address) {
            return $address;
        }
    }

    public function saveAddress($id = 0, $uid, $nickname, $phone, $province, $city, $district, $address, $zip) {
        if (!$id) {
            return F::$f->addressORM->insert(['user_id' => $uid, 'name' => $nickname, 'phone_number' => $phone, 'area' => ($province . "|" . $city . "|" . $district), 'detail_address' => $address, 'zip' => $zip, 'country' => "CN"], true);
        } else {
            $obj = F::$f->addressORM->selectOne(['id' => $id]);
            if (!$obj || $obj['user_id'] != $uid) {
                return false;
            }
            return F::$f->addressORM->update(['id' => $id], ['name' => $nickname, 'phone_number' => $phone, 'area' => ($province . "|" . $city . "|" . $district), 'detail_address' => $address, 'zip' => $zip, 'country' => "CN"]);
        }
    }

    public function createUserOrder($account, $goods_id, $address_id) {
        $obj = F::$f->addressORM->selectOne(['id' => $address_id, 'user_id' => $account]);
        if (!$obj) {
            return false;
        }
        $this->load->model('goods/goods_model', 'g');
        $goods = $this->g->getGoodsDetail($goods_id);
        if (!$goods) {
            return false;
        }
        $order_id = F::$f->orderORM->insert(['user_id' => $account, 'address_id' => $address_id, 'pay_price' => $goods['cn']['eth_price'], 'delivery_price' => 0, 'order_status' => 1], true);

        if (!$order_id) {
            return false;
        }
        $order_goods = F::$f->order_goodsORM->insert(['order_id' => $order_id, 'goods_id' => $goods_id, 'goods_count' => 1]);
        if (!$order_goods) {
            return false;
        }
        return $order_id;
    }

    public function userPayedOrder($account, $trade_no, $order_id) {
        $obj = F::$f->orderORM->selectOne(['id' => $order_id, 'user_id' => $account]);
        if (!$obj) {
            return false;
        }
        if ($obj['out_trade_no'] != '') {
            return false;
        }
        return F::$f->orderORM->update(array('id' => $order_id), array('out_trade_no' => $trade_no, 'pay_time' => date("Y-m-d H:i:s")));
    }
    
    public function doLogin($param) {
        if (!isset($param['login']) && isset($param['passwd'])) {
            return false;
        }
        $result = F::$f->userORM->selectOne(array('login' => $param['login'], 'password' => md5($param['passwd']), 'is_del' => '0', 'status' => '1'), array('select' => 'uid'));
        if ($result) {
            return $result['uid'];
        }
        return false;
    }
    public function checkLogin($param) {
        if (!isset($param['uid']) && isset($param['passwd'])) {
            return false;
        }
        $result = F::$f->userORM->selectOne(array('uid' => $param['uid'], 'password' => md5($param['passwd']), 'is_del' => '0', 'status' => '1'), array('select' => 'uid'));
        if ($result) {
            return $result['uid'];
        }
        return false;
    }

    public function updateClientID($uid, $client_id) {
        if (!$uid || !$client_id) {
            return false;
        }
        return F::$f->userORM->update(array('uid' => $uid), array('client_id' => $client_id));
    }

    public function updateUserToken($uid, $token) {
        if (!$uid || !$token) {
            return false;
        }
        return F::$f->userORM->update(array('uid' => $uid), array('token' => $token, 'last_login_time' => time()));
    }

    public function checkToken($token) {
        $result = F::$f->userORM->selectOne(array('token' => $token, 'is_del' => '0', 'status' => '1'), array('select' => 'uid'));
        if ($result) {
            return true;
        }
        return false;
    }

    public function doRegister($param) {
        if (!isset($param['login']) || !isset($param['passwd']) || !isset($param['sex']) || !isset($param['nick']) || !isset($param['client_id'])) {
            return 0;
        }
        $check_only = F::$f->userORM->selectOne(array('login' => $param['login'], 'is_del' => '0'), array('select' => 'uid'));
        if (count($check_only) > 0) {
            return -1;
        }
        return F::$f->userORM->insert(array('login' => $param['login'], 'password' => md5($param['passwd']), 'client_id' => $param['client_id'], 'user_sex' => $param['sex'], 'nick_name' => $param['nick'], 'add_time' => time()), true);
    }

    public function getUinfos($uids) {
        if (!$uids || !is_array($uids)) {
            return false;
        }
        $result = F::$f->userORM->select(array('uid' => $uids, 'is_del' => 0, 'status' => 1), array('select' => 'uid, nick_name, user_avatar, user_sex, user_birthday, profession, user_company, user_school, user_hobby, user_comment, user_signature'));
        $return = array();

        if ($result) {
            foreach ($result as $tmp) {
                if ($tmp['user_birthday']) {
                    $age = $this->getAge($tmp['user_birthday']);
                    $tmp['user_age'] = $age;
                    $return[] = $tmp;
                }
            }
        }
        return $return;
    }


    public function getUserInfos($uid) {
        if (!$uid) {
            return false;
        }
        $result = array();
        $uinfo = F::$f->userORM->select(array('uid' => $uid, 'is_del' => 0, 'status' => 1), array('select' => 'uid, nick_name, user_avatar, user_sex, user_birthday, profession, user_company, user_school, user_hobby, user_comment, user_signature, from_unixtime(add_time) as add_time, add_time as unix_time, is_hide,client_id'));
        if (!$uinfo) {
            return -1;
        }
        $uimage = F::$f->user_imageORM->select(array('uid' => $uid, 'is_del' => 0), array('select' => 'id, uid, image_url, is_avatar, from_unixtime(add_time) as add_time, add_time as unix_time', 'order_by' => 'sort desc'));
        /*
        $sql = "select t.site_id,t.uid,from_unixtime(t.add_time) as add_time, add_time as unix_time from (SELECT site_id,uid, add_time FROM `app_user_footprint` WHERE uid=" . $uid . " order by add_time desc) t group by t.site_id order by unix_time desc limit 1;";
        $last_footprint = F::$f->user_footprintORM->execute($sql);//(array('uid' => $uid, 'is_del' => 0), array('select' => 'distinct(site_id) as site_id, uid, add_time', 'order_by' => 'add_time desc', 'limit' => '6'));
        $site_id = array_get_column($last_footprint, 'site_id');

        $sql = "select t.site_id from (select count(1) as count,site_id from app_user_footprint where uid='" . $uid . "' and is_del=0 group by site_id) t order by t.count desc limit 3;";
        $always_site = F::$f->user_footprintORM->execute($sql);
        if ($always_site) {
            $always_site_id = array_get_column($always_site, 'site_id');
            if (!$site_id) {
                $site_id = array();
            }
            $site_id = array_merge($site_id, $always_site_id);
        }

        $site_info = F::$f->siteORM->select(array('site_id' => $site_id, 'is_del' => 0), array('select' => 'site_id, sub_id, site_name'));
        */
        $user = array();
        /*/
        if ($uinfo) {
            foreach ($uinfo as $tmp) {
                if ($tmp['user_birthday']) {
                    $age = $this->getAge($tmp['user_birthday']);
                    $tmp['user_age'] = $age;
                    $user[] = $tmp;
                }
            }
        }
        */

        $result['data'] = $uinfo;
        $result['image'] = $uimage;
        //$result['last_footprint'] = $last_footprint ? $last_footprint[0] : (object)array();
        //$result['always_footprint'] = $always_site;

        //$result['site']  = $site_info ? array_change_key($site_info, 'site_id') : (object)array();
        return $result;
    }

    public function getUinfosByUids($uids, $offset = 0, $limit = 0, $keyword = '') {
        if (!$uids) {
            return false;
        }
        $select_array = array('uid' => $uids, 'is_del' => 0, 'status' => 1);
        if ($keyword) {
            $select_array['nick_name'] = array('like' => '%' . $keyword . '%');
        }
        if ($offset) {
            $uinfo = F::$f->userORM->select($select_array, array('select' => 'uid, nick_name, user_avatar, user_sex, user_birthday, profession, user_company, user_school, user_hobby, user_comment, user_signature', 'limit' => $limit . ',' . $offset));
        } else {
            $uinfo = F::$f->userORM->select($select_array, array('select' => 'uid, nick_name, user_avatar, user_sex, user_birthday, profession, user_company, user_school, user_hobby, user_comment, user_signature'));
        }
        if (!$uinfo) {
            return -1;
        }
        //$uimage = F::$f->user_imageORM->select(array('uid' => $uids, 'is_del' => 0), array('select' => 'uid, image_url, is_avatar, add_time', 'order_by' => 'is_avatar, add_time desc'));

        //$result['data'] = $uinfo;
        //$result['image'] = $uimage;

        $return = array();

        if ($uinfo) {
            foreach ($uinfo as $tmp) {
                $sql = "select t.site_id,t.uid,from_unixtime(t.add_time) as add_time, add_time as unix_time from (SELECT site_id,uid, add_time FROM `app_user_footprint` WHERE uid=" . $tmp['uid'] . " order by add_time desc) t group by t.site_id order by unix_time desc limit 1;";
                $last_footprint = F::$f->user_footprintORM->execute($sql);//(array('uid' => $uid, 'is_del' => 0), array('select' => 'distinct(site_id) as site_id, uid, add_time', 'order_by' => 'add_time desc', 'limit' => '6'));
                $site_id = array_get_column($last_footprint, 'site_id');

                $site_info = F::$f->siteORM->select(array('site_id' => $site_id, 'is_del' => 0), array('select' => 'site_id, sub_id, site_name'));

                $tmp['last_footprint'] = $last_footprint ? (object)$last_footprint[0] : (object)array();
                $tmp['site_info'] = $site_info ? (object)array_change_key($site_info, 'site_id') : (object)array();
                if ($tmp['user_birthday']) {
                    $age = $this->getAge($tmp['user_birthday']);
                    $tmp['user_age'] = $age;
                    $return[] = $tmp;
                }
            }
        }
        return $return;
    }

    public function getUinfosByUidsAndSex($uids, $sex = 2, $offset) {
        if (!$uids) {
            return false;
        }

        //$uids = array_slice($uids, $offset, self::LIMIT);

        $select_array = array('uid' => $uids, 'is_del' => 0, 'status' => 1, 'is_hide' => 0);
        if ($sex != 2) {
            if ($sex == 0) {
                $select_array['user_sex'] = 0;
            } else {
                $select_array['user_sex'] = 1;
            }
        }

        $uinfo = F::$f->userORM->select($select_array, array('select' => 'uid, nick_name, user_avatar, user_sex, user_birthday, profession, user_company, user_school, user_hobby, user_comment, user_signature'));//'limit' => $offset . ',' . self::LIMIT));
        if (!$uinfo) {
            return -1;
        }

        $return = array();
        if ($uinfo) {
            foreach ($uinfo as $tmp) {
                if ($tmp['user_birthday']) {
                    $age = $this->getAge($tmp['user_birthday']);
                    $tmp['user_age'] = $age;
                    $return[] = $tmp;
                }
            }
        }
        return $return;
    }

    public function checkLoginName($login) {
        $uinfo = F::$f->userORM->select(array('login' => $login), array('select' => 'uid'));
        if ($uinfo) {
            return false;
        }
        return true;
    }

    public function setUserFootprint($uid, $site_id) {
        $result = F::$f->user_footprintORM->insert(array('uid' => $uid, 'site_id' => $site_id, 'add_time' => time()), true);
        return $result;
    }

    public function updateUser($param) {
        if (!isset($param['uid']) || !is_numeric($param['uid'])) {
            return false;
        }

        return F::$f->userORM->update(array('uid' => $param['uid']), array('user_birthday' => $param['birthday'], 'user_sex' => $param['sex'], 'nick_name' => $param['nick'], 'profession' => $param['profession'], 'user_company' => $param['company'], 'user_school' => $param['school'], 'user_hobby' => $param['hobby'], 'user_comment' => $param['comment'], 'user_signature' => isset($param['u_signature']) ? $param['u_signature'] : ''));
    }

    public function getSiteUsers($site_id, $offset = 0, $sex = 2, $uid = 0) {
        if (!$site_id || !is_numeric($site_id)) {
            return false;
        }
        $offset = 0;
        $black_uid_str = '';
        if ($uid) {
            $black_info = F::$f->blackORM->select(array('uid' => $uid, 'is_del' => 0), array('select' => 'tuid'));
            if ($black_info) {
                $black_uid = array_get_column($black_info, 'tuid');
                $black_uid_str .= " and uid not in (" . implode(',', $black_uid) . ") ";
            }
        }
        if ($uid) {
            $black_info = F::$f->blackORM->select(array('tuid' => $uid, 'is_del' => 0), array('select' => 'uid'));
            if ($black_info) {
                $black_uid = array_get_column($black_info, 'uid');
                $black_uid_str .= " and uid not in (" . implode(',', $black_uid) . ") ";
            }
        }
        $sql = "select t.uid, from_unixtime(t.add_time) as add_time, add_time as unix_time from (select distinct(uid) as uid, add_time from app_user_footprint where site_id='" . $site_id . "'" . $black_uid_str . " order by add_time desc) t group by t.uid order by t.add_time desc;";
        $uids = F::$f->userORM->execute($sql);
        $uids = array_change_key($uids, 'uid');

        $uinfos = $this->getUinfosByUidsAndSex(array_get_column($uids, 'uid'), $sex, $offset);
        if (!$uinfos || $uinfos == -1) {
            return false;
        }
        $uinfos = array_change_key($uinfos, 'uid');
        $result = array();
        foreach ($uids as $uid) {
            if (isset($uinfos[$uid['uid']]) && $uid['uid']) {
                $sql = "select t.site_id,t.uid,from_unixtime(t.add_time) as add_time, add_time as unix_time from (SELECT site_id,uid, add_time FROM `app_user_footprint` WHERE uid=" . $uid['uid'] . " order by add_time desc) t group by t.site_id order by unix_time desc limit 1;";
                $last_footprint = F::$f->user_footprintORM->execute($sql);//(array('uid' => $uid, 'is_del' => 0), array('select' => 'distinct(site_id) as site_id, uid, add_time', 'order_by' => 'add_time desc', 'limit' => '6'));
                $site_id = array_get_column($last_footprint, 'site_id');

                $site_info = F::$f->siteORM->select(array('site_id' => $site_id, 'is_del' => 0), array('select' => 'site_id, sub_id, site_name'));

                $uinfos[$uid['uid']]['last_footprint'] = $last_footprint ? $last_footprint[0] : array();
                $uinfos[$uid['uid']]['site'] = $site_info ? (object)array_change_key($site_info, 'site_id') : (object)array();

                $uinfos[$uid['uid']]['site_time'] = $uid['add_time'];
                $result[] = $uinfos[$uid['uid']];
            }
        }
        return $result;
    }

    public function getSiteUsersForAuth($vid) {
        if (!$vid || !is_numeric($vid)) {
            return false;
        }
        $site_info = F::$f->site_vendingORM->selectOne(array('vending_code' => $vid, 'is_del' => 0), array('select' => 'site_id'));
        if (!$site_info) {
            return false;
        }
        $site_id = $site_info['site_id'];
        $sql = "select t.uid, from_unixtime(t.add_time) as add_time, add_time as unix_time from (select distinct(uid) as uid,add_time from app_user_footprint where site_id='" . $site_id . "'  order by add_time desc) t group by t.uid order by t.add_time desc;";

        $uids = F::$f->userORM->execute($sql);
        $uids = array_change_key($uids, 'uid');

        $uinfos = $this->getUinfosByUids(array_get_column($uids, 'uid'));
        if (!$uinfos || $uinfos == -1) {
            return false;
        }
        $uinfos = array_change_key($uinfos, 'uid');
        $result = array();
        foreach ($uids as $uid) {
            if (isset($uinfos[$uid['uid']]) && $uid['uid']) {
                $new_info = array('Time' => $uid['add_time'], 'IsFemale' => $uinfos[$uid['uid']]['user_sex'] == 0 ? false : true,
                            'Name' => $uinfos[$uid['uid']]['nick_name'], 'Photo' => $uinfos[$uid['uid']]['user_avatar'], 'ID' => $uid['uid']);
                $result[] = $new_info;
            }
        }
        return $result;
    }

    public function getSiteUserCount($site_id) {
        if (!$site_id || !is_numeric($site_id)) {
            return false;
        }
        $sql = "select count(distinct(uid)) as total from app_user_footprint where site_id='" . $site_id . "';";
        $total = F::$f->userORM->execute($sql);
        return $total[0]['total'];
    }

    public function siteBroadcostAction($uid, $broadcast_id, $site_id, $url) {
        if (($broadcast_id && !is_numeric($broadcast_id)) || !is_numeric($uid) || !is_numeric($site_id)) {
            return false;
        }
        if ($broadcast_id) {
            return F::$f->site_broadcastORM->update(array('id' => $broadcast_id), array('image_url' => $url));
        } else {
            return F::$f->site_broadcastORM->insert(array('image_url' => $url, 'site_id' => $site_id, 'uid' => $uid, 'add_time' => time()));
        }
        return false;
    }

    public function followUser($uid, $tuid, $client_id = '') {
        if (!$uid || !is_numeric($uid) || !$tuid || !is_numeric($tuid)) {
            return false;
        }
        if ($this->check_black($uid, $tuid) === -1) {
            return -1;
        }
        $is_follow = F::$f->followORM->selectOne(array('uid' => $uid, 'tuid' => $tuid), array('select' => 'id, is_del'));

        $result = true;
        if ($is_follow) {
            if ($is_follow['is_del']) {
                $result = F::$f->followORM->update(array('id' => $is_follow['id']), array('is_del' => 0));
            } else {
                $result = false;
            }
        } else {
            $tuser_info = F::$f->userORM->selectOne(array('uid' => $tuid, 'is_del' => 0), array('select' => 'client_id'));
            $result = F::$f->followORM->insert(array('uid' => $uid, 'tuid' => $tuid, 'add_time' => time(), 'client_id' => $client_id . ',' . $tuser_info['client_id']), true);
        }

        if ($result) {
            $sql = "select id, uid, from_uid, gift_id, gift_cid, add_time from app_user_gift where from_uid='" . $tuid . "' and uid='" . $uid . "' and is_agree=0 limit 1";
            $gift_info = F::$f->msg_listORM->execute($sql);
            if ($gift_info) {
                $gift = $gift_info[0];
                $time = time();
                if ($time - $gift['add_time'] > (1000 * 60 * 60 * 24)) {
                    $is_agree = -1;
                } else {
                    $is_agree = 1;
                }
                F::$f->user_giftORM->update(array('id' => $gift['id']), array('is_agree' => $is_agree));
            }

            //insert msg_index start
            $this->openDialogue($uid, $tuid, time());

            $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where ((uid='" . $uid. "' and to_uid='" . $tuid . "') or (uid='" . $tuid. "' and to_uid='" . $uid . "')) and is_del=0 limit 1";
            $msg_info = F::$f->msg_listORM->execute($sql);

            $uinfo = $this->getUinfos(array($uid));
            $content = $uinfo[0]['nick_name'] . '刚刚加你为好友了，聊两句吧';
            $this->initMsg($uid, $tuid, $msg_info[0]['msg_id'], true, $content);
            F::$f->msg_contentORM->insert(array('uid' => $uid, 'to_uid' => $tuid, 'msg_id' => $msg_info[0]['msg_id'], 'content' => $content, 'add_time' => time(), 'type' => 'follow'));
            //F::$f->msg_listORM->update(array('msg_id' => $msg_info[0]['msg_id'], 'is_del' => 0), array('last_msg' => $content, 'last_time' => time()));
        }
        return $result;
    }

    public function followUserGift($uid, $tuid, $gift_id, $site_id, $token = '', $client_id = '') {
        if (!$uid || !is_numeric($uid) || !$tuid || !is_numeric($tuid)) {
            return false;
        }
        if ($this->check_black($uid, $tuid) === -1) {
            return -1;
        }
        $user_gift = F::$f->user_giftORM->selectOne(array('uid' => $tuid, 'from_uid' => $uid, 'is_del' => 0));
        $is_follow = F::$f->followORM->selectOne(array('uid' => $uid, 'tuid' => $tuid), array('select' => 'id, is_del'));
        $gift_info = F::$f->gift_siteORM->selectOne(array('gift_id' => $gift_id, 'site_id' => $site_id, 'is_freeze' => 0, 'is_get' => 0));

        $result = true;
        $gift_result = false;
        if ($is_follow) {
            if ($is_follow['is_del']) {
                $result = F::$f->followORM->update(array('id' => $is_follow['id']), array('is_del' => 0, 'is_gift' => $gift_info ? 1 : 0));
                if (!$user_gift && $gift_info) {
                    F::$f->user_giftORM->insert(array('uid' => $tuid, 'from_uid' => $uid, 'gift_cid' => $gift_info['id'], 'gift_id' => $gift_id, 'add_time' => time()));
                    $gift_result = true;
                    F::$f->gift_siteORM->update(array('id' => $gift_info['id']), array('is_freeze' => 1));
                }
            } else {
                $result = false;
            }
        } else {
            $tuser_info = F::$f->userORM->selectOne(array('uid' => $tuid, 'is_del' => 0), array('select' => 'client_id'));
            $result = F::$f->followORM->insert(array('uid' => $uid, 'tuid' => $tuid, 'add_time' => time(), 'is_gift' => $gift_info ? 1 : 0, 'client_id' => $client_id . ',' . $tuser_info['client_id']), true);
            if (!$user_gift && $gift_info) {
                F::$f->user_giftORM->insert(array('uid' => $tuid, 'from_uid' => $uid, 'gift_cid' => $gift_info['id'], 'gift_id' => $gift_id, 'add_time' => time()));
                $gift_result = true;
                F::$f->gift_siteORM->update(array('id' => $gift_info['id']), array('is_freeze' => 1));
            }
        }

        if ($result) {
            //$this->setNewAccost($uid, $tuid, $gift_result);
            $this->updateUserAccost($uid, $tuid);

            $this->openDialogue($uid, $tuid, time());
            $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where ((uid='" . $uid. "' and to_uid='" . $tuid . "') or (uid='" . $tuid. "' and to_uid='" . $uid . "')) and is_del=0 limit 1";
            $msg_info = F::$f->msg_listORM->execute($sql);

            $uinfo = $this->getUinfos($uid);

            $content = $uinfo[0]['nick_name'] . '刚刚加你为好友了，聊两句吧';

            if ($gift_result) {
                F::$f->msg_listORM->update(array('msg_id' => $msg_info[0]['msg_id']), array('is_gift' => 1));
            }
            $this->initMsg($uid, $tuid, $msg_info[0]['msg_id'], true, $content);
                   
        }
        return $result;
    }

    public function unfollowUser($uid, $tuid) {
        if (!$uid || !is_numeric($uid) || !$tuid || !is_numeric($tuid)) {
            return false;
        }
        $is_follow = F::$f->followORM->selectOne(array('uid' => $uid, 'tuid' => $tuid), array('select' => 'id, is_del'));
        $result = false;

        if ($is_follow) {
            if (!$is_follow['is_del']) {
                $result = F::$f->followORM->update(array('id' => $is_follow['id']), array('is_del' => 1));
                $msg_index = F::$f->msg_indexORM->selectOne(array('uid' => $uid, 'to_uid' => $tuid), array('select' => 'msg_id'));
                if ($msg_index) {
                    F::$f->msg_listORM->update(array('msg_id' => $msg_index['msg_id']), array('is_accost' => 1));
                } else {
                    $msg_index = F::$f->msg_indexORM->selectOne(array('to_uid' => $uid, 'uid' => $tuid), array('select' => 'msg_id'));
                    if ($msg_index) {
                        F::$f->msg_listORM->update(array('msg_id' => $msg_index['msg_id']), array('is_accost' => 1));
                    }
                }
            }
        }
        return $result;
    }

    public function setBlackUser($uid, $tuid) {
        if (!$uid || !is_numeric($uid) || !$tuid || !is_numeric($tuid)) {
            return false;
        }
        $is_follow = F::$f->blackORM->selectOne(array('uid' => $uid, 'tuid' => $tuid), array('select' => 'id, is_del'));
        $result = true;
        if ($is_follow) {
            if ($is_follow['is_del']) {
                $result = F::$f->blackORM->update(array('id' => $is_follow['id']), array('is_del' => 0));
            } else {
                $result = false;
            }
        } else {
            $result = F::$f->blackORM->insert(array('uid' => $uid, 'tuid' => $tuid, 'add_time' => time()), true);
        }
        if ($result) {
            F::$f->followORM->update(array('uid' => $uid, 'tuid' => $tuid, 'is_del' => 0), array('is_del' => 1));
            F::$f->followORM->update(array('tuid' => $uid, 'uid' => $tuid, 'is_del' => 0), array('is_del' => 1));
            $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where ((uid='" . $uid. "' and to_uid='" . $tuid . "') or (uid='" . $tuid. "' and to_uid='" . $uid . "')) and is_del=0 limit 1";
            $msg_info = F::$f->msg_listORM->execute($sql);
            $msg_id = @$msg_info[0]['msg_id'];
            if ($msg_id) {
                F::$f->msg_indexORM->update(array('msg_id' => $msg_id, 'is_del' => 0), array('is_del' => 1));
            }
        }
        return $result;
    }

    public function setUnBlackUser($uid, $tuid) {
        if (!$uid || !is_numeric($uid) || !$tuid || !is_numeric($tuid)) {
            return false;
        }
        $is_follow = F::$f->blackORM->selectOne(array('uid' => $uid, 'tuid' => $tuid), array('select' => 'id, is_del'));
        $result = true;
        if ($is_follow) {
            if ($is_follow['is_del'] == 0) {
                $result = F::$f->blackORM->update(array('id' => $is_follow['id']), array('is_del' => 1));
            } else {
                $result = false;
            }
        }
        return $result;
    }

    public function getUserBroadcast($bid) {
        if (!$bid || !is_numeric($bid)) {
            return false;
        }
        $result = F::$f->site_broadcastORM->selectOne(array('is_del' => 0, 'id' => $bid), array('select' => 'id, site_id, uid, broadcast, image_url, from_unixtime(add_time) as add_time, add_time as unix_time, type'));
        if ($result) {
            F::$f->broadcast_likeORM->update(array('bid' => $bid, 'is_del' => 0, 'is_read' => 0), array('is_read' => 1));
            F::$f->broadcast_replyORM->update(array('broad_id' => $bid, 'is_del' => 0, 'is_read' => 0), array('is_read' => 1));
        }
        return $result;
    }

    public function getLikeUser($bid) {
        if (!$bid && (!is_numeric($bid) || !is_array($bid))) {
            return false;
        }

        $result = F::$f->broadcast_likeORM->select(array('is_del' => 0, 'bid' => $bid), array('select' => 'id, uid, bid, from_unixtime(add_time) as add_time, add_time as unix_time'));
        return $result;
    }

    public function getReplayInfo($bid, $offset = 5) {
        if (!$bid && (!is_numeric($bid) || !is_array($bid))) {
            return false;
        }

        $result = F::$f->broadcast_replyORM->select(array('broad_id' => $bid, 'is_del' => 0), array('select' => 'id, broad_id, uid, reply, from_unixtime(add_time) as add_time, add_time as unix_time', 'order_by' => 'add_time desc', 'limit' => $offset));
        return $result;
    }

    public function checkFriend($uid, $my_uid) {
        if (!$uid || !is_numeric($uid) || !$my_uid || !is_numeric($my_uid)) {
            return false;
        }
        if ($uid == $my_uid) {
            return 4;//自己
        }
        $follow = F::$f->followORM->selectOne(array('is_del' => 0, 'uid' => $my_uid, 'tuid' => $uid));
        $fans = F::$f->followORM->selectOne(array('is_del' => 0, 'tuid' => $my_uid, 'uid' => $uid));
        // 4:自己 3:好友  2:我关注的  1:关注我的
        if ($follow && $fans) {
            return 3;
        } else if ($follow) {
            return 2;
        } else if ($fans) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getUserGift($uid, $offset = 0) {
        if (!$uid || !is_numeric($uid)) {
            return false;
        }
        $gift_result = F::$f->user_giftORM->select(array('uid' => $uid, 'is_del' => 0, 'is_agree' => 1), array('select' => 'id, uid, from_uid, gift_cid, gift_id, from_unixtime(add_time) as add_time, add_time as unix_time', 'limit' => $offset . ',' . self::LIMIT));
        if (!$gift_result) {
            return false;
        }
        $result = array();
        //$gift_cid = array_get_column($gift_result, 'gift_cid');
        F::$f->user_giftORM->update(array('id' => array_get_column($gift_result, 'id'), 'is_del' => 0, 'is_read' => 0), array('is_read' => 1));
        $uids = array_get_column($gift_result, 'uid');
        $uids = array_merge($uids, array_get_column($gift_result, 'from_uid'));
        $uinfos = $this->getUinfosByUids($uids);
        //$gift_cinfo = F::$f->gift_siteORM->select(array('id' => $gift_cid, 'is_del' => 0), array('select' => 'gift_id'));

        $gift_id = array_get_column($gift_result, 'gift_id');
        $gift_info = F::$f->giftORM->select(array('id' => $gift_id, 'is_del' => 0), array('select' => 'id, gift_name, gift_image, from_unixtime(add_time) as add_time, add_time as unix_time'));

        $result['users'] = array_change_key($uinfos, 'uid');
        $result['gift']  = $gift_result;
        $result['gift_info']  = array_change_key($gift_info, 'id');
        return $result;
    }

    public function getGiftByCid($gift_cid) {
        if (!$gift_cid || !is_numeric($gift_cid)) {
            return false;
        }
        $gift_result = F::$f->user_giftORM->select(array('gift_cid' => $gift_cid, 'is_del' => 0), array('select' => 'uid, from_uid, gift_cid, gift_id, from_unixtime(add_time) as add_time, add_time as unix_time'));
        if (!$gift_result) {
            return false;
        }
        $result = array();
        //$gift_cid = array_get_column($gift_result, 'gift_cid');
        $uids = array_get_column($gift_result, 'uid');
        $uids = array_merge($uids, array_get_column($gift_result, 'from_uid'));
        $uinfos = $this->getUinfosByUids($uids);

        $gift_cinfo = F::$f->gift_siteORM->select(array('id' => $gift_cid, 'is_del' => 0), array('select' => 'gift_id, site_id, code, release_info'));

        $gift_id = array_get_column($gift_result, 'gift_id');
        $gift_info = F::$f->giftORM->select(array('id' => $gift_id, 'is_del' => 0), array('select' => 'id, gift_name, gift_image, from_unixtime(add_time) as add_time, add_time as unix_time'));


        $gift_info[0]['code'] = $gift_cinfo[0]['code'];
        $gift_info[0]['site_id'] = $gift_cinfo[0]['site_id'];
        $gift_info[0]['release_info'] = $gift_cinfo[0]['release_info'];
        $site_id = $gift_cinfo[0]['site_id'];

        $site_info = F::$f->siteORM->selectOne(array('is_del' => 0, 'site_id' => $site_id), array('select' => 'site_id, sub_id, site_name, longitude, latitude'));

        $sub_id = $site_info['sub_id'];
        $sub_info = $site_info = F::$f->subwayORM->selectOne(array('is_del' => 0, 'sub_id' => $sub_id), array('select' => 'sub_id, city_id, subway_name'));

        $site_info['subway_name'] = $sub_info['subway_name'];
        $site_info['city_id'] = $sub_info['city_id'];
        $result['users'] = array_change_key($uinfos, 'uid');
        $result['gift']  = $gift_result[0];
        $result['gift_info']  = array_change_key($gift_info, 'id');
        $result['site']  = array($site_id => $site_info);
        return $result;
    }

    public function getFriend($uid) {
        if (!is_numeric($uid)) {
            return false;
        }
        $sql = "select tuid from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0') and is_del=0;";

        $tuids = F::$f->followORM->execute($sql);
        //$uids_result = F::$f->followORM->select(array('uid' => $uid), array('select' => 'tuid, is_del'));
        $uids = array_get_column($tuids, 'tuid');
        $result = $this->getUinfosByUids($uids);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function insertBroadcast($param) {
        if (!$param['uid'] || !$param['site_id'] || !$param['broadcast']) {
            return false;
        }
        $content = $this->replce_keyword($param['broadcast']);
        $result = F::$f->site_broadcastORM->insert(array('uid' => $param['uid'], 'site_id' => $param['site_id'], 'broadcast' => $content, 'add_time' => time(), 'type' => isset($param['type']) ? $param['type'] : 'content'), true);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function replce_keyword($content) {
        if (!$content){
            return $content;
        }
        $keyword = F::$f->keywordORM->select(array('is_del' => 0), array('keyword'));
        if (!$keyword) {
            return $content;
        }
        $replace_array = array();
        $to_array = array();
        foreach ($keyword as $tmp) {
            $replace_array[] = $tmp['keyword'];
            $to_array[]      = '**';
        }
        if ($replace_array && $to_array && count($replace_array) == count($to_array)) {
            return str_replace($replace_array, $to_array, $content);
        }
    }

    public function getUserMsgList($uid, $offset = 0, $time_stamp, $token = '') {
        if (!is_numeric($uid)) {
            return false;
        }
        //$userinfo = F::$f->userORM->selectOne(array('uid' => $uid, 'is_del' => 0), array('select' => 'client_id'));
        $show_list = F::$f->msg_indexORM->select(array('uid' => $uid, 'is_del' => '0', 'user_token' => $token), array('select' => 'id, to_uid, msg_id', 'limit' => 10));
        $msg_ids = array_get_column($show_list, 'msg_id');
        if (!$msg_ids) {
            return false;
        }
        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost, is_gift from app_msg_list where msg_id in (" . implode(',', $msg_ids). ") and is_del=0 and is_show=1 and last_msg<>'' order by last_time desc limit " . $offset . ", 10;";
        $all_list = F::$f->msg_listORM->execute($sql);

        if (!$all_list) {
            return false;
        }

        $uids = array_get_column($all_list, 'uid');
        $uids = array_merge($uids, array_get_column($all_list, 'to_uid'));
        $uids = array_unique($uids);

/*
        foreach ($uids as $uid_tmp) {
            $to_uids = array();
            if ($uid_tmp != $uid) {
                $to_uids[] = $uid_tmp;
            }
        }
        $check_result = array();
        foreach ($to_uids as $to_uid_tmp) {
            $check_friend = $this->checkFriend($uid, $to_uid_tmp);
            $check_result[] = array('uid' => $to_uid_tmp, 'check_result' => $check_friend);
        }
        $check_result = array_change_key($check_result, 'uid');
*/
        $uinfos = $this->getUinfosByUids($uids);
/*
        if ($uinfos) {
            foreach ($uinfos as $key=>$info) {
                $uinfos[$key]['check_friend'] = isset($check_result[$info['uid']]['check_result']) ? $check_result[$info['uid']]['check_result'] : '';
            }
        }
*/
        $accost   = array();
        $dialogue = array();
        $accost_unread = 0;
        $all_accost_count = 0;
        $all_msg_unread_count = 0;
        $accost_gift_count = 0;

        if ($all_list) {
            foreach ($all_list as $tmp) {
                $select_uid = '';
                if ($tmp['uid'] == $uid) {
                    $select_uid = $uid;
                } else if ($tmp['to_uid'] == $uid) {
                    $select_uid = $tmp['to_uid'];
                }
                if (!$select_uid) {
                    $select_uid = 0;
                }
                $un_read_count = $this->getUnreadCount($tmp['msg_id'], $time_stamp, $select_uid);
                if ($tmp['is_accost']) {
                    if ($un_read_count) {
                        $accost_unread += $un_read_count;
                    }
                    if (!$accost) {
                        $accost = $tmp;
                    }
                    $accost['unread_count'] = $accost_unread;
                    if ($tmp['is_gift']) {
                        $accost_gift_count += 1;
                    } else {
                        $all_accost_count += 1;
                    }
                } else {
                    if ($un_read_count) {
                        $tmp['unread_count'] = $un_read_count;
                    } else {
                        $tmp['unread_count'] = 0;
                    }
                    $dialogue[] = $tmp;
                }
            }
        }
        $result['accost_list'] = $accost ? $accost : array();
        $result['accost_list']['accost_count'] = $all_accost_count;
        $result['accost_list']['gift_count'] = $accost_gift_count;
        $result['dialogue'] = $dialogue ? $dialogue : array();
        $result['users']    = $uinfos ? array_change_key($uinfos, 'uid') : (object)array();
        return $result;
    }

    public function getUnreadCount($msg_id, $time_stamp, $select_uid) {
        $sql = "select count(1) as count from app_msg_content where msg_id='" . $msg_id . "' and to_uid=" . $select_uid . " and is_del=0 and is_read=0;";
        $un_read_count = F::$f->msg_listORM->execute($sql);
        return isset($un_read_count[0]['count']) ? $un_read_count[0]['count'] : 0;
    }

    public function getAccostList($uid, $offset = 0, $client_id) {
        if (!is_numeric($uid)) {
            return false;
        }
        $show_list = F::$f->msg_indexORM->select(array('uid' => $uid, 'is_del' => '0', 'user_token' => $client_id), array('select' => 'msg_id'));
        $msg_ids = array_get_column($show_list, 'msg_id');
        if (!$msg_ids) {
            return false;
        }

        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost, is_gift from app_msg_list where msg_id in (" . implode(',', $msg_ids). ") and is_del=0 and is_show=1 and is_accost = 1 order by last_time desc limit " . $offset . ", 10;";
        $all_list = F::$f->msg_listORM->execute($sql);
        if (!$all_list) {
            return false;
        }
        $uids = array_get_column($all_list, 'uid');
        $uids = array_merge($uids, array_get_column($all_list, 'to_uid'));
        $uids = array_unique($uids);
        $uinfos = $this->getUinfosByUids($uids);
        $new_result = array();
        foreach ($all_list as $list) {
            $list['unread_count'] = F::$f->msg_contentORM->selectCount(array('msg_id' => $list['msg_id'], 'to_uid' => $uid, 'is_read' => 0));
            $new_result[] = $list;
        }
        $result['msg_list'] = $new_result;
        $result['users']    = array_change_key($uinfos, 'uid');
        return $result;
    }

    public function openDialogue($uid, $to_uid, $l_time) {
        if (!$uid || !is_numeric($uid) || !$to_uid || !is_numeric($to_uid)) {
            return false;
        }
        if ($this->check_black($uid, $to_uid) === -1) {
            return -1;
        }
        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where ((uid='" . $uid. "' and to_uid='" . $to_uid . "') or (uid='" . $to_uid. "' and to_uid='" . $uid . "')) and is_del=0 limit 1";
        $msg_info = F::$f->msg_listORM->execute($sql);
        if (!$msg_info) {
            $msg_id = F::$f->msg_listORM->insert(array('uid' => $uid, 'to_uid' => $to_uid, 'uid_talk' => 1, 'add_time' => time()), true);
        } else {
            $msg_id = $msg_info[0]['msg_id'];

            $update_key = '';
            if ($msg_info[0]['uid'] == $uid) {
                $update_key = 'uid_talk';
            } else if ($msg_info[0]['to_uid'] == $uid) {
                $update_key = 'to_uid_talk';
            } else {
                return false;
            }
            $sql = "update app_msg_list set " . $update_key . "=1 where msg_id=" . $msg_id . ";";
            F::$f->msg_listORM->execute($sql);

        }
        $result = $this->getMsg($msg_id, $uid, 0);

        return $result;
    }

    public function setContentRead($uid, $to_uid, $msg_id) {
        if (!is_numeric($uid) || !is_numeric($to_uid) || !is_numeric($msg_id)) {
            return false;
        }
        return F::$f->msg_contentORM->update(array('to_uid' => $uid, 'uid' => $to_uid, 'msg_id' => $msg_id), array('is_read' => 1));
    }

    public function getMsg($msg_id, $uid, $l_time) {

        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where msg_id='" . $msg_id . "' and is_del=0 limit 1";
        $all_list = F::$f->msg_listORM->execute($sql);

        if (!$all_list) {
            return false;
        }

        $uids = array_get_column($all_list, 'uid');
        $uids = array_merge($uids, array_get_column($all_list, 'to_uid'));
        $uids = array_unique($uids);
        $uinfos = $this->getUinfosByUids($uids);

        $select_param = array('msg_id' => $msg_id, 'to_uid' => $uid, 'is_del' => 0, 'is_read' => 0);
        if ($l_time) {
            $select_param['add_time'] = array('>=' => $l_time);
        }
        $msg_content = F::$f->msg_contentORM->select($select_param, array('select' => 'id, uid, to_uid, msg_id, content, image_url, from_unixtime(add_time) as add_time, add_time as unix_time, type', 'order_by' => 'add_time asc', 'limit' => '10'));

        $result['msg_info'] = array_change_key($all_list, 'msg_id');
        $result['users']    = array_change_key($uinfos, 'uid');
        $result['content']  = $msg_content ? $msg_content : array();

        if ($msg_content) {
            $msg_content_ids = array_get_column($msg_content, 'id');
            F::$f->msg_contentORM->update(array('id' => $msg_content_ids), array('is_read' => 1));
        }
        return $result;
    }

    public function closeDialogue($uid, $to_uid, $msg_id) {

        if (!is_numeric($uid) || !is_numeric($to_uid) || !is_numeric($msg_id)) {
            return false;
        }
        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where msg_id='" . $msg_id . "' and is_del=0 limit 1";

        $msg_info = F::$f->msg_listORM->execute($sql);

        $update_key = '';
        if ($msg_info && $msg_info[0]['uid'] == $uid) {
            $update_key = 'uid_talk';
        } else if ($msg_info && $msg_info[0]['to_uid'] == $uid) {
            $update_key = 'to_uid_talk';
        } else {
            return false;
        }
        $sql = "update app_msg_list set " . $update_key . "=0 where msg_id=" . $msg_id . ";";
        $result = F::$f->msg_listORM->execute($sql);
        return $result;
    }

    /*插入消息*/
    public function insertContent($param) {
        if (!isset($param['uid']) || !is_numeric($param['uid']) || !isset($param['to_uid']) || !is_numeric($param['to_uid']) || !$param['msg_id'] || !is_numeric($param['msg_id'])) {
            return false;
        }
        if ($this->check_black($param['uid'], $param['to_uid']) === -1) {
            return -1;
        }
        $msg_obj = F::$f->msg_listORM->selectOne(array('msg_id' => $param['msg_id'], 'is_del' => 0), array('select' => 'uid, to_uid, uid_talk,to_uid_talk'));
        $is_read = 0;
        if ($msg_obj['uid'] == $param['to_uid']) {
            if ($msg_obj['uid_talk'] == 1) {
                $is_read = 1;
            }
        }

        if ($msg_obj['to_uid'] == $param['to_uid']) {
            if ($msg_obj['to_uid_talk'] == 1) {
                $is_read = 1;
            }
        }

        $result = F::$f->msg_contentORM->insert(array('uid' => $param['uid'], 'to_uid' => $param['to_uid'], 'msg_id' => $param['msg_id'], 'add_time' => time(), 'content' => $param['content'], 'is_read' => $is_read), true);

        if ($result) {
            $this->initMsg($param['uid'], $param['to_uid'], $param['msg_id'], false, $param['content'], @$param['token']);
            
        }
        return $result;
    }

    public function initMsg($uid, $to_uid, $msg_id, $is_follow = false, $content = '', $token = '') {
        //insert msg_index start
        if ($is_follow) {
            $is_friend = $this->checkFriend($uid, $to_uid);
            if ($is_friend == 3) {
                //a关注b 不在a的消息列表里
                $my_index = F::$f->msg_indexORM->selectOne(array('uid' => $uid, 'to_uid' => $to_uid), array('select' => 'id, uid, msg_id, to_uid, is_del'));
                $userinfo = F::$f->userORM->selectOne(array('uid' => $uid, 'is_del' => 0), array('select' => 'client_id'));
                if (!$my_index) {
                    F::$f->msg_indexORM->insert(array('uid' => $uid, 'to_uid' => $to_uid, 'msg_id' => $msg_id, 'add_time' => time(), 'user_token' => $userinfo['client_id']));
                } else if($my_index['is_del']) {
                    F::$f->msg_indexORM->update(array('id' => $my_index['id']), array('is_del' => 0, 'user_token' => $userinfo['client_id']));
                } else {
                    F::$f->msg_indexORM->update(array('id' => $my_index['id']), array('user_token' => $userinfo['client_id']));
                }
            }
        } else {
            $my_index = F::$f->msg_indexORM->selectOne(array('uid' => $uid, 'to_uid' => $to_uid), array('select' => 'id, uid, msg_id, to_uid, is_del'));
            $userinfo = F::$f->userORM->selectOne(array('uid' => $uid, 'is_del' => 0), array('select' => 'client_id'));
            if (!$my_index) {
                F::$f->msg_indexORM->insert(array('uid' => $uid, 'to_uid' => $to_uid, 'msg_id' => $msg_id, 'add_time' => time(), 'user_token' => $userinfo['client_id']));
            } else if($my_index['is_del']) {
                F::$f->msg_indexORM->update(array('id' => $my_index['id']), array('is_del' => 0, 'user_token' => $userinfo['client_id']));
            } else {
                F::$f->msg_indexORM->update(array('id' => $my_index['id']), array('user_token' => $userinfo['client_id']));
            }
        }

        $to_index = F::$f->msg_indexORM->selectOne(array('uid' => $to_uid, 'to_uid' => $uid), array('select' => 'id, uid, msg_id, to_uid, is_del'));
        $tuserinfo = F::$f->userORM->selectOne(array('uid' => $to_uid, 'is_del' => 0), array('select' => 'client_id'));
        if (!$to_index) {
            F::$f->msg_indexORM->insert(array('uid' => $to_uid, 'to_uid' => $uid, 'msg_id' => $msg_id, 'add_time' => time(), 'user_token' => $tuserinfo['client_id']));
        } else {
            F::$f->msg_indexORM->update(array('id' => $to_index['id']), array('user_token' => $tuserinfo['client_id'], 'is_del' => 0));
        }
        //insert msg_index end
        F::$f->msg_listORM->update(array('msg_id' => $msg_id), array('last_msg' => $content, 'last_time' => time()));
    }

    public function insertContentImg($uid, $to_uid, $msg_id, $url) {
        $param['uid'] = $uid;
        $param['to_uid'] = $to_uid;
        $param['msg_id'] = $msg_id;
        $param['content'] = '图片';
        $id = $this->insertContent($param);
        if ($id) {
            return F::$f->msg_contentORM->update(array('id' => $id), array('image_url' => $url));
        }
        return false;

    }

    public function getMsgNewContent($msg_id, $uid, $l_time) {
        $msg_object = F::$f->msg_listORM->selectOne(array('msg_id' => $msg_id, 'is_del' => '0'), array('select' => 'uid, to_uid'));
        $to_uid = 0;
        if (!$msg_object) {
            return false;
        }
        if ($msg_object['uid'] == $uid) {
            $to_uid = $msg_object['to_uid'];
        } else {
            $to_uid = $msg_object['uid'];
        }
        if ($this->check_black($uid, $to_uid) == -1) {
            return -1;
        }
        $sql = "select id, uid, to_uid, content, image_url, from_unixtime(add_time) as add_time, add_time as unix_time, type from app_msg_content where msg_id='" . $msg_id . "' and to_uid=" . $uid . " and is_del=0 and add_time>'" . $l_time . "';";
        $result = F::$f->msg_listORM->execute($sql);
        if ($result) {
            $ids = array_get_column($result, 'id');
            F::$f->msg_contentORM->update(array('id' => $ids), array('is_read' => 1));
        }
        return $result;
    }

    public function setNewAccost($uid, $to_uid, $is_gift = false) {
        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where ((uid='" . $uid. "' and to_uid='" . $to_uid . "') or (uid='" . $to_uid. "' and to_uid='" . $uid . "')) and is_del=0 limit 1";
        $msg_info = F::$f->msg_listORM->execute($sql);

        if (!$msg_info) {
            $msg_id = F::$f->msg_listORM->insert(array('uid' => $uid, 'to_uid' => $to_uid, 'uid_talk' => 1, 'add_time' => time(), 'is_gift' => $is_gift ? 1 : 0), true);

            $my_index = F::$f->msg_indexORM->selectOne(array('uid' => $uid, 'to_uid' => $to_uid), array('select' => 'id, uid, msg_id, to_uid, is_del'));
            $userinfo = F::$f->userORM->selectOne(array('uid' => $uid, 'is_del' => 0), array('select' => 'token'));
            if (!$my_index) {
                F::$f->msg_indexORM->insert(array('uid' => $uid, 'to_uid' => $to_uid, 'msg_id' => $msg_id, 'add_time' => time(), 'user_token' => $userinfo['token']));
            } else if($my_index['is_del']) {
                F::$f->msg_indexORM->update(array('id' => $my_index['id']), array('is_del' => 0, 'msg_id' => $msg_id));
            }
            $tuserinfo = F::$f->userORM->selectOne(array('uid' => $to_uid, 'is_del' => 0), array('select' => 'token'));
            $to_index = F::$f->msg_indexORM->selectOne(array('uid' => $to_uid, 'to_uid' => $uid), array('select' => 'id, uid, msg_id, to_uid, is_del'));
            if (!$to_index) {
                F::$f->msg_indexORM->insert(array('uid' => $to_uid, 'to_uid' => $uid, 'msg_id' => $msg_id, 'add_time' => time(), 'user_token' => $tuserinfo['token']));
            } else {
                F::$f->msg_indexORM->update(array('id' => $to_index['id']), array('msg_id' => $msg_id));
            }
            $uinfo = $this->getUinfos(array($uid));
            if ($uinfo) {
                $user_name = $uinfo[0]['nick_name'];
                $content = $user_name . '刚刚加你为好友了，聊两句吧';
                F::$f->msg_contentORM->insert(array('uid' => $uid, 'to_uid' => $to_uid, 'msg_id' => $msg_id, 'content' => $content, 'add_time' => time(), 'type' => 'follow'));
            }
        }
    }

    public function updateUserAccost($uid, $to_uid) {
        $sql = "select msg_id, uid, to_uid, nead_read_uid, last_msg, last_time, uid_talk, to_uid_talk, uid_read, to_uid_read, from_unixtime(add_time) as add_time, add_time as unix_time, is_accost from app_msg_list where ((uid='" . $uid. "' and to_uid='" . $to_uid . "') or (uid='" . $to_uid. "' and to_uid='" . $uid . "')) and is_del=0 limit 1";
        $msg_info = F::$f->msg_listORM->execute($sql);
        $check_friend = $this->checkFriend($to_uid, $uid);
        if ($check_friend == 3) {
            $msg_id = $msg_info[0]['msg_id'];
            $sql = "update app_msg_list set is_accost=0 where msg_id='" . $msg_id . "';";
            F::$f->msg_listORM->execute($sql);
        }
        return true;
    }

    public function insertUserImage($uid, $image_url, $is_avatar = false) {
        if (!isset($uid) || !is_numeric($uid) || !$image_url) {
            return false;
        }
        $result = F::$f->user_imageORM->insert(array('uid' => $uid, 'image_url' => $image_url, 'is_avatar' => $is_avatar, 'add_time' => time()), true);
        if ($is_avatar) {
            F::$f->userORM->update(array('uid' => $uid), array('user_avatar' => $image_url));
        }
        return $result;
    }

    public function getMyFollow($uid, $offset = 0, $keyword = '') {
        if (!isset($uid) || !is_numeric($uid)) {
            return false;
        }
        $sql = "select tuid from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0') and is_del=0;";

        $tuids = F::$f->followORM->execute($sql);
        $select_uids = array_get_column($tuids, 'tuid');
        if ($select_uids) {
            $sql = "select tuid from app_follow where uid='" . $uid . "' and is_del=0 and tuid not in (" . implode(',', $select_uids) . ");";
            $uids = F::$f->followORM->execute($sql);
        } else {
            $uids = F::$f->followORM->select(array('uid' => $uid, 'is_del' => 0), array('select' => 'tuid'));
        }
        
        if (!$uids) {
            return false;
        }
        $select_uids = array_get_column($uids, 'tuid');
        $uinfos = $this->getUinfosByUids($select_uids, $offset, self::LIMIT, $keyword);
        return $uinfos;
    }

    public function getMyFans($uid, $offset = 0, $keyword = '') {
        if (!isset($uid) || !is_numeric($uid)) {
            return false;
        }
        $sql = "select tuid from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0' and uid<>" . $uid . ") and is_del=0;";

        $tuids = F::$f->followORM->execute($sql);
        $select_uids = array_get_column($tuids, 'tuid');
        if ($select_uids) {
            //$uids = F::$f->followORM->select(array('tuid' => $uid, 'is_del' => 0, 'uid' => array('not in' => '(' . implode(',', $select_uids) . ')')), array('select' => 'uid'));
            $sql = "select uid from app_follow where tuid='" . $uid . "' and uid<>'" . $uid . "' and is_del=0 and uid not in (" . implode(',', $select_uids) . ");";
            $uids = F::$f->followORM->execute($sql);
        } else {
            $uids = F::$f->followORM->select(array('tuid' => $uid, 'is_del' => 0), array('select' => 'uid'));
        }
        
        if (!$uids) {
            return false;
        }
        $select_uids = array_get_column($uids, 'uid');
        $uinfos = $this->getUinfosByUids($select_uids, $offset, self::LIMIT, $keyword);
        return $uinfos;
    }

    public function getMyFriend($uid, $offset = 0, $keyword = '') {
        $sql = "select tuid from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0') and is_del=0;";

        $tuids = F::$f->followORM->execute($sql);
        $select_uids = array_get_column($tuids, 'tuid');

        $uinfos = $this->getUinfosByUids($select_uids, $offset, self::LIMIT, $keyword);
        return $uinfos;
    }

    public function deleteDialogue($uid, $to_uid, $msg_id) {
        if (!$uid || !is_numeric($uid) || !$to_uid || !is_numeric($to_uid) || !$msg_id || !is_numeric($msg_id)) {
            return false;
        }
        $result = F::$f->msg_indexORM->update(array('uid' => $uid, 'to_uid' => $to_uid, 'msg_id' => $msg_id), array('is_del' => 1));
        F::$f->msg_contentORM->update(array('to_uid' => $uid, 'msg_id' => $msg_id, 'is_del' => 0, 'is_read' => 0), array('is_read' => 1));
        return $result;
    }

    public function setLikeBroadcast($uid, $b_id) {
        if (!$uid || !is_numeric($uid) || !$b_id || !is_numeric($b_id)) {
            return false;
        }
        $broad_cast_like = F::$f->broadcast_likeORM->selectOne(array('uid' => $uid, 'bid' => $b_id), array('select' => 'id, is_del'));
        if ($broad_cast_like) {
            if ($broad_cast_like['is_del']) {
                $sql = "update app_site_broadcast set like_count=like_count + 1 where id='" . $b_id . "';";
                F::$f->site_broadcastORM->execute($sql);
                return F::$f->broadcast_likeORM->update(array('id' => $broad_cast_like['id']), array('is_del' => 0));
            }
        } else {
            $sql = "update app_site_broadcast set like_count=like_count + 1 where id='" . $b_id . "';";
            F::$f->site_broadcastORM->execute($sql);
            return F::$f->broadcast_likeORM->insert(array('uid' => $uid, 'bid' => $b_id, 'add_time' => time()), true);
        }
        return false;
    }

    public function setUnLikeBroadcast($uid, $b_id) {
        if (!$uid || !is_numeric($uid) || !$b_id || !is_numeric($b_id)) {
            return false;
        }
        $broad_cast_like = F::$f->broadcast_likeORM->selectOne(array('uid' => $uid, 'bid' => $b_id), array('select' => 'id, is_del'));
        if ($broad_cast_like) {
            if (!$broad_cast_like['is_del']) {
                $sql = "update app_site_broadcast set like_count=like_count - 1 where id='" . $b_id . "';";
                F::$f->site_broadcastORM->execute($sql);
                return F::$f->broadcast_likeORM->update(array('id' => $broad_cast_like['id']), array('is_del' => 1));
            }
        }
        return false;
    }

    public function insertBroadCastReply($uid, $b_id, $content) {
        if (!$uid || !is_numeric($uid) || !$b_id || !is_numeric($b_id) || !$content) {
            return false;
        }

        return F::$f->broadcast_replyORM->insert(array('uid' => $uid, 'broad_id' => $b_id, 'reply' => $content, 'add_time' => time()), true);
    }

    public function getUserBroadcastList($uid) {
        $broad_info = F::$f->site_broadcastORM->select(array('is_del' => 0, 'uid' => $uid), array('select' => 'id, site_id, uid, broadcast, image_url, from_unixtime(add_time) as add_time, add_time as unix_time, type, like_count', 'order_by' => 'add_time desc'));

        if (!$broad_info) {
            return (object)array();
        }
        $users = array($uid);
        $bids = array_get_column($broad_info, 'id');
        $like_user = $this->getLikeUser($bids);

        

        $users = array_merge($users, array_get_column($like_user, 'uid'));
        $reply = array();
        foreach ($bids as $bid) {
            $reply_result = $this->getReplayInfo($bid, 2);
            if ($reply_result) {
                $reply = array_merge($reply, $reply_result);
            }
        }

        $users = array_merge($users, array_get_column($reply, 'uid'));
        $user_info = $this->u->getUinfosByUids($users);

        $new_result = array();

        if ($broad_info) {
            foreach ($broad_info as $content) {
                if ($reply) {
                    foreach ($reply as $tmp) {
                        if ($tmp['broad_id'] == $content['id']) {
                            $content['reply_id'][] = $tmp['id'];
                        }
                    }
                }
                if (isset($content['reply_id'])) {
                    $content['reply_id'] = $content['reply_id'];
                } else {
                    $content['reply_id'] = array();
                }
                $new_result[] = $content;
            } 
        }

        $result['user'] = ($user_info) ? array_change_key($user_info, 'uid') : (object)array();
        $result['broadcast'] = $new_result ? $new_result : array();
        //$result['like'] = $like_user ? array_change_key($like_user, 'id') : (object)array();
        $result['reply'] = $reply ? array_change_key($reply, 'id') : (object)array();
        return $result;
    }

    public function checkMsg($uid, $to_uid, $client_id) {
        if (!$uid || !is_numeric($uid) || !$to_uid || !is_numeric($to_uid)) {
            return false;
        }
        if ($uid == $to_uid) {
            return 4;//自己
        }
        $follow = F::$f->followORM->selectOne(array('is_del' => 0, 'uid' => $uid, 'tuid' => $to_uid));
        $fans = F::$f->followORM->selectOne(array('is_del' => 0, 'uid' => $to_uid, 'tuid' => $uid));
        $result = array('me' => (object)array(), 'o_side' => (object)array());

        if (substr_count($follow['client_id'], $client_id) < 1 && substr_count($fans['client_id'], $client_id) < 1) {
            return $result;
        }

        $users = array($uid, $to_uid);
        $user_infos = $this->getUinfosByUids($users);
        $user_infos = array_change_key($user_infos, 'uid');
        

        $sql = "select uid, from_uid, gift_id, is_agree from app_user_gift where ((from_uid='" . $uid . "' and uid='" . $to_uid . "') or (from_uid='" . $to_uid . "' and uid='" . $uid . "')) limit 1";
        $gift_info = F::$f->msg_listORM->execute($sql);

        // 4:自己 3:好友  2:我关注的  1:关注我的
        if ($follow && $fans) {
            $result['me'] = array('follow_data' => $follow, 'msg' => $user_infos[$to_uid]['nick_name'] . '关注了你，你们已经是好友，聊聊吧');
            if ($gift_info && $gift_info[0]['uid'] == $uid) {
                if ($gift_info[0]['is_agree'] == 1) {
                    $result['me']['msg'] .= '--请在“我的”页面中查收礼物';
                } else {
                    $result['me']['msg'] .= '--可惜已过领取时间，抱歉~';
                }
                $gift_id = $gift_info[0]['gift_id'];
                $gift_info = F::$f->giftORM->select(array('id' => $gift_id));
                $result['me']['gift_id'] = $gift_id;
                $result['me']['gift_info'] = (object)$gift_info[0];
            }
            //$result['o_side'] = array('follow_data' => $fans, 'msg' => $user_infos[$uid]['nick_name'] . '关注了你，你们已经是好友，聊聊吧');
        } else if ($follow) {
            //$result['me'] = array('follow_data' => $follow, 'msg' => '关注了你，你们已经是好友，聊聊吧');
        } else if ($fans) {
            $result['me'] = array('follow_data' => $fans, 'msg' => $user_infos[$to_uid]['nick_name'] . '刚刚关注了你，聊聊吧');
            if ($gift_info && $gift_info[0]['from_uid'] == $to_uid) {
                $gift_id = $gift_info[0]['gift_id'];
                $gift_info = F::$f->giftORM->select(array('id' => $gift_id));
                $result['me']['msg'] = $user_infos[$to_uid]['nick_name'] . '刚刚关注了你，回加关注后即可获得他送你小礼物';
                $result['me']['gift_id'] = $gift_id;
                $result['me']['gift_info'] = (object)$gift_info[0];
            }
        }
        return $result;
    }

    public function setReadUserMsg($uid) {
        if (!is_numeric($uid)) {
            return false;
        }
        $show_list = F::$f->msg_indexORM->select(array('uid' => $uid, 'is_del' => '0'), array('select' => 'id, to_uid, msg_id'));
        $msg_ids = array_get_column($show_list, 'msg_id');
        if (!$msg_ids) {
            return false;
        }
        return F::$f->msg_contentORM->update(array('to_uid' => $uid, 'msg_id' => $msg_ids, 'is_del' => 0, 'is_read' => 0), array('is_read' => 1));
    }

    public function deleteAlldialogue($uid) {
        if (!is_numeric($uid)) {
            return false;
        }
        return F::$f->msg_indexORM->update(array('uid' => $uid, 'is_del' => 0), array('is_del' => 1));
    }

    public function deleteAllAccsot($uid) {
        if (!is_numeric($uid)) {
            return false;
        }
        $sql = "select msg_id from app_msg_list where (uid='" . $uid. "' or to_uid='" . $uid . "') and is_del=0 and is_accost=1";
        $accost_list = F::$f->msg_listORM->execute($sql);
        if (!$accost_list) {
            return false;
        }
        $msg_id = array_get_column($accost_list, 'msg_id');
        return F::$f->msg_indexORM->update(array('uid' => $uid, 'msg_id' => $msg_id, 'is_del' => 0), array('is_del' => 1));
    }

    public function getBroadcastReply($broad_id, $last_id = 0) {
        if (!$broad_id || !is_numeric($broad_id)) {
            return false;
        }
        $select_array = array('is_del' => 0, 'broad_id' => $broad_id);
        if ($last_id) {
            $select_array['id'] = array('<' => $last_id);
        }

        $reply_result = F::$f->broadcast_replyORM->select($select_array, array('select' => 'id, uid, reply, broad_id, from_unixtime(add_time), add_time as unix_time', 'limit' => self::LIMIT, 'order_by' => 'id desc'));
        if (!$reply_result) {
            return false;
        }
        $uids = array_get_column($reply_result, 'uid');
        $uinfos = $this->getUinfosByUids($uids);

        return array('reply' => $reply_result, 'user' => $uinfos, 'last_id' => @$reply_result[count($reply_result) - 1]['id']);

    }

    public function deleteUserImg($uid, $iid) {
        if (!$uid || !is_numeric($uid) || !$iid || !is_numeric($iid)) {
            return false;
        }
        return F::$f->user_imageORM->update(array('uid' => $uid, 'id' => $iid, 'is_avatar' => 0), array('is_del' => 1));
    }

    public function updateUserAvatar($uid, $iid) {
        if (!$uid || !is_numeric($uid) || !$iid || !is_numeric($iid)) {
            return false;
        }
        $img_obj = F::$f->user_imageORM->selectOne(array('id' => $iid, 'is_del' => 0));
        if ($img_obj) {
            F::$f->user_imageORM->update(array('uid' => $uid, 'is_del' => 0, 'is_avatar' => '1'), array('is_avatar' => 0, 'sort' => 10));
            //F::$f->user_imageORM->update(array('uid' => $uid, 'is_del' => 0), array('is_avatar' => 0));
            F::$f->user_imageORM->update(array('id' => $iid, 'is_del' => 0), array('is_avatar' => 1, 'sort' => 99));
            return F::$f->userORM->update(array('uid' => $uid, 'is_del' => 0), array('user_avatar' => $img_obj['image_url']));
        }
        return false;
    }

    public function updateUserSign($uid, $signature) {
        if (!$uid || !is_numeric($uid)) {
            return false;
        }
        return F::$f->userORM->update(array('uid' => $uid, 'is_del' => 0), array('user_signature' => $signature));
    }

    public function getAvalibleGift($site_id) {
        $sql = "select t.gift_id from (select count(1) as count,gift_id from app_gift_site where is_del=0 and is_freeze=0 and site_id='" . $site_id . "' group by gift_id) t where t.count > 0;";
        $gift_result = F::$f->giftORM->execute($sql);
        if (!$gift_result) {
            return false;
        }

        $gift_ids = array_get_column($gift_result, 'gift_id');
        return F::$f->giftORM->select(array('is_del' => 0, 'id' => $gift_ids));
    }

    public function getUserGiftCount($uid) {
        $all_count = F::$f->user_giftORM->selectCount(array('uid' => $uid, 'is_del' => 0, 'is_agree' => 1));
        $new_count = F::$f->user_giftORM->selectCount(array('uid' => $uid, 'is_del' => 0, 'is_agree' => 1, 'is_read' => 0));
        return array('gift_count' => $all_count, 'new_count' => $new_count);
    }

    public function getUserFriendCount($uid) {
        if (!$uid || !is_numeric($uid)) {
            return false;
        }
        $sql = "select tuid from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0') and is_del=0;";

        $tuids = F::$f->followORM->execute($sql);
        $select_uids_array = array_get_column($tuids, 'tuid');
        $select_uids = count($select_uids_array) > 0 ? implode(',', $select_uids_array) : 0;

        $sql = "select count(1) as count from app_follow where uid=" . $uid . " and is_del=0 and tuid not in (" . $select_uids . ");";
        $follow_count = F::$f->followORM->execute($sql);//(array('uid' => $uid, 'is_del' => 0));

        $sql = "select count(tuid) as count from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0' and uid<>" . $uid . ") and is_del=0;";
        //$sql = "select count(1) as count from app_follow where tuid=" . $uid . " and is_del=0 and uid not in (" . $select_uids . ");";
        $fans_count = F::$f->followORM->execute($sql);//(array('tuid' => $uid, 'is_del' => 0));
        //$sql = "select count(1) as count from app_follow where uid ='" . $uid . "' and tuid in (select uid from app_follow where tuid='" . $uid . "' and is_del='0') and is_del=0;";
        //$friend_arr = F::$f->followORM->execute($sql);
        return array('follow' => $follow_count[0]['count'], 'fans' => $fans_count[0]['count'], 'friend' => count($select_uids_array));
    }

    public function canSendGift($uid) {
        if (!$uid || !is_numeric($uid)) {
            return false;
        }
        $last_info = F::$f->user_giftORM->selectOne(array('from_uid' => $uid, 'is_del' => 0), array('select' => 'substring(from_unixtime(add_time), 1, 10) as add_time', 'limit' => '1'));

        if (!$last_info) {
            return true;
        }
        //24小时不能送礼物
        if ($last_info['add_time'] != date("Y-m-d")) {
            return true;
        }
        return false;
    }

    public function checkSendGiftUser($site_users, $uid) {
        if (!$site_users) {
            return false;
        }
        $famale_uid_tmp = array();
        foreach ($site_users as $tmp) {
            //性别
            if ($tmp['user_sex'] == 1 && $tmp['uid'] != $uid) {
                $famale_uid_tmp[] = $tmp['uid'];
            }
        }
        if (!$famale_uid_tmp) {
            return false;
        }
        $rand = array_rand($famale_uid_tmp, 1);
        return $famale_uid_tmp[$rand];
/*
        $famale_uid = array();
        //是否关注过
        $sql = "select uid,tuid from app_follow where ((uid in (" . implode(',', $famale_uid_tmp). ") and tuid=" . $uid . ") or (tuid in (" . implode(',', $famale_uid_tmp). ") and uid=" . $uid . "))";
        $follow_info = F::$f->followORM->execute($sql);
        if ($follow_info) {
            foreach ($follow_info as $follow) {
                foreach ($famale_uid_tmp as $tmp) {
                    if ($follow['uid'] != $tmp && $follow['tuid'] != $tmp) {
                        $famale_uid[] = $tmp;
                    }
                }
            }
        }
        if ($famale_uid) {
            $rand = array_rand($famale_uid, 1);
            return $famale_uid[$rand];
        }
        return false;
        */
    }

    public function changePassword($uid, $new_password) {
        if (!$uid || !is_numeric($uid) || !$new_password) {
            return false;
        }
        return F::$f->userORM->update(array('uid' => $uid), array('password' => md5($new_password)));
    }

    public function setAvatarSort($uid, $sort) {
        if (!$uid || !is_numeric($uid) || !$sort) {
            return false;
        }
        $sort_array = explode(',', $sort);
        if (!$sort_array) {
            return false;
        }
        $result = false;
        foreach ($sort_array as $tmp) {
            $format_data = explode(':', $tmp);
            F::$f->user_imageORM->update(array('uid' => $uid, 'id' => $format_data[0], 'is_avatar' => 0), array('sort' => $format_data[1]));
            $result = true;
        }
        return $result;

    }

    public function getUserNotice($uid, $client_id) {
        if (!is_numeric($uid)) {
            return false;
        }
        $all_accost_count = 0;
        $all_msg_unread_count = 0;
        $result = array();

        $show_list = F::$f->msg_indexORM->select(array('uid' => $uid, 'is_del' => '0', 'user_token' => $client_id), array('select' => 'msg_id'));
        $msg_ids = array_get_column($show_list, 'msg_id');
        if (!$msg_ids) {
            $bid = F::$f->site_broadcastORM->select(array('uid' => $uid, 'is_del' => 0), array('select' => 'id'));
            $bids = array_get_column($bid, 'id');

            $like_count = F::$f->broadcast_likeORM->selectCount(array('bid' => $bids, 'is_del' => 0));
            $reply_count = F::$f->broadcast_replyORM->selectCount(array('broad_id' => $bids, 'is_del' => 0));

            $result['accost_count'] = $all_accost_count;
            $result['msg_count'] = $all_msg_unread_count;
            $result['like_count'] = $like_count;
            $result['reply_count'] = $reply_count;
        } else {
            $sql = "select msg_id,is_accost from app_msg_list where msg_id in (" . implode(',', $msg_ids). ") and is_del=0 and is_show=1 and last_msg<>''";
            $all_list = F::$f->msg_listORM->execute($sql);

            if ($all_list) {
                foreach ($all_list as $tmp) {
                    $un_read_count = $this->getUnreadCount($tmp['msg_id'], '', $uid);
                    if ($tmp['is_accost']) {
                        if ($un_read_count) {
                            $all_accost_count += 1;
                        }
                    } else {
                        $all_msg_unread_count += $un_read_count;
                    }
                }
            }


            $bid = F::$f->site_broadcastORM->select(array('uid' => $uid, 'is_del' => 0), array('select' => 'id'));
            $bids = array_get_column($bid, 'id');

            $like_count = F::$f->broadcast_likeORM->selectCount(array('bid' => $bids, 'is_del' => 0));
            $reply_count = F::$f->broadcast_replyORM->selectCount(array('broad_id' => $bids, 'is_del' => 0));

            $result['accost_count'] = $all_accost_count;
            $result['msg_count'] = $all_msg_unread_count;
            $result['like_count'] = $like_count;
            $result['reply_count'] = $reply_count;
        }
        return $result;
    }

    public function setUserHide($uid) {
        if (!is_numeric($uid)) {
            return false;
        }
        $user_info = $uinfo = F::$f->userORM->selectOne(array('uid' => $uid, 'is_del' => 0), array('select' => 'uid, is_hide'));
        if ($user_info) {
            return F::$f->userORM->update(array('uid' => $uid), array('is_hide' => $user_info['is_hide'] ? 0 : 1));
        }
        return false;
    }

    public function getUserBlackList($uid, $offset = 0) {
        if (!$uid || !is_numeric($uid)) {
            return false;
        }
        $uids = F::$f->blackORM->select(array('uid' => $uid, 'is_del' => 0), array('select' => 'tuid', 'limit' => $offset . ',' . self::LIMIT));
        $uids = array_change_key($uids, 'tuid');

        $uinfos = $this->getUinfosByUidsAndSex(array_get_column($uids, 'tuid'), 2, $offset);
        if (!$uinfos || $uinfos == -1) {
            return false;
        }
        $uinfos = array_change_key($uinfos, 'uid');

        $result = array();
        foreach ($uids as $uid) {
            if (isset($uinfos[$uid['tuid']]) && $uid['tuid']) {
                $sql = "select t.site_id,t.uid,from_unixtime(t.add_time) as add_time, add_time as unix_time from (SELECT site_id,uid, add_time FROM `app_user_footprint` WHERE uid=" . $uid['tuid'] . " order by add_time desc) t group by t.site_id order by unix_time desc limit 1;";
                $last_footprint = F::$f->user_footprintORM->execute($sql);//(array('uid' => $uid, 'is_del' => 0), array('select' => 'distinct(site_id) as site_id, uid, add_time', 'order_by' => 'add_time desc', 'limit' => '6'));
                $site_id = array_get_column($last_footprint, 'site_id');

                $site_info = F::$f->siteORM->select(array('site_id' => $site_id, 'is_del' => 0), array('select' => 'site_id, sub_id, site_name'));

                $uinfos[$uid['tuid']]['last_footprint'] = $last_footprint ? $last_footprint[0] : array();
                $uinfos[$uid['tuid']]['site'] = $site_info ? (object)array_change_key($site_info, 'site_id') : (object)array();

                //$uinfos[$uid['tuid']]['site_time'] = $uid['add_time'];
                $result[] = $uinfos[$uid['tuid']];
            }
        }
        return $result;
    }

    public function insertUserLog($uid, $token = '') {
        $sql = "select id from app_request_log where substring(from_unixtime(add_time), 1, 10)='" . date('Y-m-d') . "' and is_del='0' and uid='" . $uid . "';";
        $check = F::$f->request_logORM->execute($sql);
        if (!$check) {
            F::$f->request_logORM->insert(array('uid' => $uid, 'token' => $token, 'add_time' => time()));
        }
    }

    public function forumImage($uid, $forum_id, $url) {
        F::$f->forum_imageORM->insert(array('uid' => $uid, 'forum_id' => $forum_id, 'image_url' => $url, 'add_time' => time()));

    }
}
