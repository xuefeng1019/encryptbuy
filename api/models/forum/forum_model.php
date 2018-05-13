<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Forum_model  extends MY_Model
{
    const LIMIT = 10;

    public function __construct()
    {
        parent::__construct();
    }

    public function getForumList($offset = 10) {
        $show_list = F::$f->forumORM->select(array('is_del' => '0'), array('select' => 'id as forum_id, uid, forum_title, forum_content, nice_count, reply_count, from_unixtime(add_time) as add_time, from_unixtime(update_time) as update_time', 'order_by' => 'update_time desc', 'limit' => $offset . ', 10'));
        //$show_list = array_change_key($show_list, 'id');
        //echo F::$f->forumORM->getLastSql();
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

    public function insertForum($param) {
        if ($param['uid'] && $param['forum']) {
            return F::$f->forumORM->insert(array('uid' => $param['uid'], 'forum_title' => $param['title'], 'forum_content' => $param['forum'], 'add_time' => time(), 'update_time' => time()), true);
        }
        return false;
    }
}
