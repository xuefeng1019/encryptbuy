<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Manager extends MY_Controller
{

    public function __construct()
    {
        parent::__construct(false);
    }


    /**
     * index for all
     *
     */
    public function index() {
        $this->_view('manager/index.tpl');
    }

    public function addGoods() {
        $this->_view('manager/edit_goods.tpl');
    }

    public function getGoodsList() {
        $this->load->model('goods/goods_model', 'g');
        $list = $this->g->getGoodsList();
        if (!$list) {
            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
            $this->ajaxInfo['info'] = 'param error';
            $this->ajaxOutput();
            exit;
        }
        $this->ajaxInfo['code'] = MY_Controller::OK;
        $this->ajaxInfo['info'] = $list;
        $this->ajaxOutput();
        exit;
    }


//    public function upload() {
//        $image = $_FILES['submitimg'];
//        $uid = $_REQUEST['uid'];
//        if (!$uid || !is_numeric($uid)) {
//            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
//            $this->ajaxInfo['info'] = 'param error';
//            $this->ajaxOutput();
//            exit;
//        }
//
//        $image_name = '';
//        $url = '';
//
//        if ($image && $image['error'] == 0) {
//            $type = $_REQUEST['a_type'];
//
//            $this->config->load('upload_file', true);
//            $image_allow_types = $this->config->item('upload_file');
//            $image_allow_types = $image_allow_types['upload_image'];
//            $image_new = getimagesize($image['tmp_name']);
//
//            $image_path = config_item('upload_image_path');
//            if (!is_dir($image_path)) {
//                exec("mkdir " . $image_path);
//            }
//            $image_name = explode(".", $image['name']);
//            $file_name = 'image_'. time() . "." . $image_name[count($image_name) - 1];
//
//            $target_path = $image_path . '/' . $file_name;
//            $image_name = $file_name;
//            move_uploaded_file($image['tmp_name'], $target_path);
//            $url = static_url() . 'upload/' . $image_name;
//
//            $is_avatar = isset($_REQUEST['is_avatar']) ? $_REQUEST['is_avatar'] : false;
//            $this->load->model('user/user_model', 'u');
//            $result = false;
//            if ($type == 'user') {
//                $result = $this->u->insertUserImage($uid, $url, $is_avatar);
//            } else if ($type == 'dialogue') {
//                $to_uid = $_REQUEST['to_uid'];
//                $msg_id = $_REQUEST['msg_id'];
//                $result = $this->u->insertContentImg($uid, $to_uid, $msg_id, $url);
//            } else if ($type == 'froum') {
//                $forum_id = $_REQUEST['forum_id'];
//                //$site_id      = $_REQUEST['site_id'];
//                $this->u->forumImage($uid, $broadcast_id, $site_id, $url);
//                $result = true;
//            }
//            $this->ajaxInfo['code'] = MY_Controller::OK;
//            $this->ajaxInfo['info'] = $result ? array('url' => $url, 'img_id' => $result) : false;
//            $this->ajaxOutput();
//            exit;
//            //同步
//            //synchronous_file($target_path, $image_path);
//        }
//        $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
//        $this->ajaxInfo['info'] = false;
//        $this->ajaxOutput();
//        exit;
//    }
//
//    public function uploadForumImage() {
//        $image_arr = $_FILES;
//        $uid = $_REQUEST['uid'];
//        if (!$uid || !is_numeric($uid)) {
//            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
//            $this->ajaxInfo['info'] = 'param error';
//            $this->ajaxOutput();
//            exit;
//        }
//
//        if ($image_arr) {
//            $this->load->model('user/user_model', 'u');
//            foreach ($image_arr as $key => $image) {
//                $image_name = '';
//                $url = '';
//
//                if ($image && $image['error'] == 0) {
//                    $type = $_REQUEST['a_type'];
//
//                    $this->config->load('upload_file', true);
//                    $image_allow_types = $this->config->item('upload_file');
//                    $image_allow_types = $image_allow_types['upload_image'];
//                    $image_new = getimagesize($image['tmp_name']);
//
//                    $image_path = config_item('upload_image_path');
//                    if (!is_dir($image_path)) {
//                        exec("mkdir " . $image_path);
//                    }
//                    $image_name = explode(".", $image['name']);
//                    $file_name = 'image_'. $key . '_' .time() . "." . $image_name[count($image_name) - 1];
//
//                    $target_path = $image_path . '/' . $file_name;
//                    $image_name = $file_name;
//                    move_uploaded_file($image['tmp_name'], $target_path);
//                    $url = static_url() . 'upload/' . $image_name;
//
//                    $forum_id = $_REQUEST['forum_id'];
//                    //$site_id      = $_REQUEST['site_id'];
//                    $this->u->forumImage($uid, $forum_id, $url);
//                    $result = true;
//                    //同步
//                    //synchronous_file($target_path, $image_path);
//                }
//            }
//            $this->ajaxInfo['code'] = MY_Controller::OK;
//            $this->ajaxInfo['info'] = true;
//            $this->ajaxOutput();
//            exit;
//        }
//
//        $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
//        $this->ajaxInfo['info'] = false;
//        $this->ajaxOutput();
//        exit;
//    }
}