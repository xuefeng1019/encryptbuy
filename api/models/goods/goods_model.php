<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Goods_model  extends MY_Model
{
    const LIMIT = 10;

    public function __construct()
    {
        parent::__construct();
    }

    public function getGoodsListCount() {
        return F::$f->goodsORM->selectCount(array('is_show' => '1'));
    }

    public function getGoodsList($offset = 0) {
        $res = [];
        $show_list = F::$f->goodsORM->select(array('is_show' => '1'), array('select' => '*', 'order_by' => 'updated_at desc', 'limit' => $offset . ', 12'));
        if ($show_list) {
            foreach ($show_list as $value) {
                $main_titles = explode("||", $value['main_title']);
                $sub_titles  = explode("||", $value['sub_title']);
                $value['main_img'] = json_decode($value['main_img'], true);
                $en_list = $cn_list = $value;
                $cn_list["main_title"] = $main_titles[0];
                $en_list["main_title"] = $main_titles[1];
                $cn_list["sub_title"]  = $sub_titles[0];
                $en_list["sub_title"]  = $sub_titles[1];
                $res[] = ['cn' => $cn_list, 'en' => $en_list];
            }
        }
        return $res;
    }

    public function getGoodsDetail($goods_id) {
        if (!$goods_id) {
            return [];
        }
        $data = F::$f->goodsORM->selectOne(array('is_show' => '1', 'id' => $goods_id), array('select' => '*'));
        if ($data) {
            //foreach ($show_list as $value) {
                $main_titles           = explode("||", $data['main_title']);
                $sub_titles            = explode("||", $data['sub_title']);
                $data['main_img']      = json_decode($data['main_img'], true);

                $en_list = $cn_list = $data;

                $cn_list["main_title"] = $main_titles[0];
                $en_list["main_title"] = $main_titles[1];
                $cn_list["sub_title"]  = $sub_titles[0];
                $en_list["sub_title"]  = $sub_titles[1];
                $res = ['cn' => $cn_list, 'en' => $en_list];
            //}
        }
        return $res;
    }

    public function getForumList($offset = 10) {
        $show_list = F::$f->forumORM->select(array('is_del' => '0'), array('select' => 'id as forum_id, uid, forum_title, forum_content, nice_count, reply_count, from_unixtime(add_time) as add_time, from_unixtime(update_time) as update_time', 'order_by' => 'update_time desc', 'limit' => $offset . ', 10'));

        $new_result = array();
        if ($show_list) {
            $ids    = array_get_column($show_list, 'forum_id');
            $images = F::$f->forum_imageORM->select(array('forum_id' => $ids, 'is_del' => 0), array('order_by' => 'add_time desc'));
            if ($images) {
                foreach ($images as $image) {
                    foreach ($show_list as $tmp) {
                        if ($tmp['forum_id'] != $image['forum_id']) {
                            $tmp['image'] = $image;
                        }
                        $new_result[$tmp['forum_id']] = $tmp;
                    }
                }
            } else {
                $new_result = $show_list;
            }
        } else {
            $new_result = $show_list;
        }
        $t_result = array();
        if ($new_result) {
            foreach ($new_result as $tmp) {
                $t_result[] = $tmp;
            }
        }
        $result['forum_list'] = $t_result;
        return $result;
    }

//    public function insertForum($param) {
//        if ($param['uid'] && $param['forum']) {
//            return F::$f->forumORM->insert(array('uid' => $param['uid'], 'forum_title' => $param['title'], 'forum_content' => $param['forum'], 'add_time' => time(), 'update_time' => time()), true);
//        }
//        return false;
//    }
}
