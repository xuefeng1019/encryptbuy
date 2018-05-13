<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lottery extends MY_Controller
{
    const USER_ERROR = 404;
    const USER_EXTISTS = 500;
    const USER_FAIL = 499;
    const USER_EMAIL_ERROR = '请输入正确的邮箱';
    public function __construct()
    {
        parent::__construct(false, false);
    }

    
    /**
     * index for all
     *
     */
    public function index()
    {
        //phpinfo();
        //exit;
    }

    public function initData() {
        $data = file_get_contents("./lottery.txt");
        $line = explode("\n", $data);
        if ($line) {
            $this->load->model('lottery/lottery_model', 'l');
            foreach ($line as $tmp) {
                $exp_1 = explode("|", $tmp);
                $exp_2 = explode(",", $exp_1[0]);
                $new_param[0] = $exp_2;
                $new_param[1] = (int)$exp_1[1];
                $result = $this->l->insertForum($new_param);
            }
        }
        echo "OK";
    }
    public function getNext() {
        $param = $this->input->get();
        if (!isset($param['num'])) {
            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
            $this->ajaxInfo['info'] = 'param error';
            $this->ajaxOutput();
            exit;
        }
        $this->load->model('lottery/lottery_model', 'l');
        $exp_1 = explode("|", $param['num']);
        $exp_2 = explode(",", $exp_1[0]);
        $new_param[0] = $exp_2;
        $new_param[1] = (int)$exp_1[1];
        //$this->l->insertForum($new_param);
        $result = $this->l->getNext($new_param);
        $this->ajaxInfo['info'] = $result;
        $this->ajaxOutput();
    }
}