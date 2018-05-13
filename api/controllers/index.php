<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Index extends MY_Controller
{
	
    public function __construct()
    {
        parent::__construct(false);
    }

    
    /**
     * index for all
     *
     */
    public function index()
    {
        //phpinfo();
        $this->_view('index/building.tpl');
        //exit;
    }

    public function faq() {
        $this->_view('index/faq.tpl');
    }

    public function help() {
        $this->_view('index/help.tpl');
    }

    public function about() {
        $this->_view('index/about.tpl');
    }

    public function tech() {
        $this->_view('index/tech.tpl');
    }

    public function signIn() {
        $this->data['uri'] = 'sign-in';
        $this->_view('index/login.tpl');
    }

    public function orders() {
        $this->data['need_login'] = true;
        $this->data['uri']        = 'orders';
        $this->_view('index/building.tpl');
    }

    public function market($page = 1) {
        $limit    = 12;
        $this->load->model('goods/goods_model', 'g');

        $all_count = $this->g->getGoodsListCount();
        $all_page  = ceil($all_count / $limit);


        $list = $this->g->getGoodsList(($page - 1) * $limit);

        $this->data['goods']    = $list;
        $this->data['all_page'] = $all_page;
        $this->data['page']     = $page;
        $this->_view('index/market.tpl');
    }

    public function goods($goods_id) {
        $this->load->model('goods/goods_model', 'g');
        $goods_id               = intval($goods_id);
        $data                   = $this->g->getGoodsDetail($goods_id);
        $this->data['goods']    = $data;
        $this->data['buy']      = true;
        $this->data['goods_id'] = $goods_id;
        $this->_view('index/goods.tpl');
    }

    public function buyConfirm($goods_id) {
        $this->load->model('goods/goods_model', 'g');
        $this->load->model('user/user_model', 'u');
        $goods_id               = intval($goods_id);
        $data                   = $this->g->getGoodsDetail($goods_id);
        //echo $_COOKIE['account'];
        $address                  = $this->u->getUserAddress($_COOKIE['account']);
        $this->data['goods']      = $data;
        $this->data['address']    = $address;
        $this->data['need_login'] = 1;
        $this->_view('index/buy-confirm.tpl');
    }

    public function createOrder() {
        $account    = $this->input->post('account');
        $goods_id   = $this->input->post('goods_id');
        $address_id = $this->input->post('address_id');
        $error    = true;
        $order_id = 0;
        if (!$account || !$goods_id || !$address_id || !isset($_COOKIE['account']) || $_COOKIE['account'] != $account) {
            $error = true;
        } else {
            $this->load->model('user/user_model', 'u');
            $order_id = $this->u->createUserOrder($account, $goods_id, $address_id);
            if ($order_id) {
                $error = false;
            }
        }
        if ($error) {
            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
            $this->ajaxInfo['info'] = false;
            $this->ajaxOutput();
            exit;
        }
        $this->ajaxInfo['code'] = MY_Controller::OK;
        $this->ajaxInfo['info'] = $order_id;
        $this->ajaxOutput();
    }

    public function saveAddress()
    {
        $nickname = $this->input->post('nickname');
        $phone    = $this->input->post('phone');
        $province = $this->input->post('province');
        $city     = $this->input->post('city');
        $district = $this->input->post('district');
        $address  = $this->input->post('address');
        $zip      = $this->input->post('zip');
        $account  = $_COOKIE['account'];
        if (!$nickname || !$phone || !$province || !$city || !$district || !$address || !$zip || !$account) {
            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
            $this->ajaxInfo['info'] = false;
            $this->ajaxOutput();
            exit;
        }
        $this->load->model('user/user_model', 'u');
        $saved = $this->u->saveAddress(0, $account, $nickname, $phone, $province, $city, $district, $address, $zip);
        if ($saved) {
            $this->ajaxInfo['code'] = MY_Controller::OK;
            $this->ajaxInfo['info'] = $saved;
            $this->ajaxOutput();
        }
        $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
        $this->ajaxInfo['info'] = false;
        $this->ajaxOutput();
        exit;
    }

    public function privacyPolicy() {
        $this->_view('index/privacy.tpl');
    }

    public function termsOfUse() {
        $this->_view('index/terms_of_use.tpl');
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