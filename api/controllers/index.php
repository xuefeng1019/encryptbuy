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

    public function payedOrder() {
        $trade_no    = $this->input->post('trade_no');
        $order_id    = $this->input->post('order_id');
        $pay_account = $this->input->post('account');
        $account     = $_COOKIE['account'];
        if (!$trade_no || !$order_id || !$pay_account || (strtolower($account) != strtolower($pay_account))) {
            $this->ajaxInfo['code']    = MY_Controller::PARAM_ERROR;
            $this->ajaxInfo['message'] = '发生错误了,参数不合法';
            $this->ajaxInfo['info']    = false;
            $this->ajaxOutput();
            exit;
        }
        $this->load->model('user/user_model', 'u');
        $res = $this->u->userPayedOrder($account, $trade_no, $order_id);
        if ($res) {
            $this->ajaxInfo['code']    = MY_Controller::OK;
            $this->ajaxInfo['message'] = '支付成功';
            $this->ajaxInfo['info']    = $order_id;
            $this->ajaxOutput();
            exit;
        }
        $this->ajaxInfo['code']    = MY_Controller::PARAM_ERROR;
        $this->ajaxInfo['message'] = '发生未知错误';
        $this->ajaxInfo['info']    = false;
        $this->ajaxOutput();
        exit;
    }

}