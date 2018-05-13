<?php
class MY_Controller extends CI_Controller {

    protected $ajaxInfo = array('status' => array('code' => 200, 'message' => ''));
    protected $isAjax   = FALSE;

    const JS_V                = 1.1;
    const CSS_V               = 1.1;

    const OK                  = "200";
    const NO_SC               = "400";
    const SC_ERROR            = "401";
    const PARAM_ERROR         = "402";
    const TOKEN_ERROR         = "403";

    const DATA_EMPTY          = 604;
    const DATA_EXISTS         = -500;
    const AJAX_NOT_LOGINED    = -999;

    const FORM_ERROR          = 444;
    const INSERT_ERROR        = 445;
    const ID_ERROR            = 446;
    const DELETE_ERROR        = 447;
    const USER_BLACK          = 999;
    const IOS_CODE            = 'PFeUifss#.=8n0';

    protected $nonceStr       = 'Wm3WZYTPz0wzccnW';

    public function __construct($check_token = TRUE, $check_sc = TRUE)
    {
        parent::__construct();
        //is a ajax input?
        $this->isAjax();
        //$this->_checkReferer();
        $this->base_url = base_url();
        //$this->static_url = static_url();
        /**
         * add default javascript 
         */
        $get = $this->input->get();
        if ($get && $check_sc) {
            $this->_checkRequestURI();
        }
        if ($check_token) {
            //$this->_checkToken();
        }

        $this->load->model('user/user_model');
        $uid = $this->input->get('uid');
        $token = $this->input->get('token');
        if ($uid) {
            $this->user_model->insertUserLog($uid, $token);
        }
        /**
         * add default css 
         */
        //$this->_addDefaultCss();
    }

    protected function _checkRequestURI() {
        
//        $request_uri = urldecode($_SERVER["REQUEST_URI"]);
//        $parse_url   = parse_url($request_uri);
//        $param       = $this->input->get();
//
//        if (!isset($param['sc']) || !isset($param['type'])) {
//            $this->ajaxInfo['code'] = MY_Controller::NO_SC;
//            $this->ajaxInfo['info'] = 'param error';
//            $this->ajaxOutput();
//            exit;
//        }
//        $request_sc = $param['sc'];
//        unset($param['sc']);
//        //ksort($param);
//
//        $request_str = '';
//        if ($param) {
//            foreach ($param as $key => $value) {
//                $request_str .= $key . '=' . $value;
//                $request_str .= '&';
//            }
//        }
        //echo md5('http://local.didtell.com/auth/register/?login=xuefeng1019@gmail.com&passwd=feng1019&nick=%B7%E7%D0%D0%D5%DF&sex=1&birthday=1986-10-19&type=ios&tc=' . self::IOS_CODE);
        //$tc = ($param['type'] == 'android') ? self::ANDROID_CODE : self::IOS_CODE;
        //$request_str = $this->base_url . ($parse_url['path']) . '?' . $request_str . 'tc=' . $tc;
        //echo $request_str;
        //exit;
        //echo $request_str . '<br />';
        //echo md5($request_str) . '<br />';
        //log_message('error', $request_str);
        //log_message('error', md5($request_str));
        //$this->ajaxInfo['debug'] = array('format_url' => $request_str, 'sc' => md5($request_str));
//        if (md5($request_str) != strtolower($request_sc)) {
//            $this->ajaxInfo['code'] = MY_Controller::SC_ERROR;
//            $this->ajaxInfo['info'] = 'secret error';
//            $this->ajaxOutput();
//            exit;
//        }
    }

//    public function _checkToken() {
//        $token = $this->input->get('token');
//        if (!$token) {
//            $this->ajaxInfo['code'] = MY_Controller::PARAM_ERROR;
//            $this->ajaxInfo['info'] = 'param error';
//            $this->ajaxOutput();
//            exit;
//        }
//        $this->load->model('user/user_model');
//        $check_result = $this->user_model->checkToken($token);
//        if (!$check_result) {
//            $this->ajaxInfo['code'] = MY_Controller::TOKEN_ERROR;
//            $this->ajaxInfo['info'] = 'token error';
//            $this->ajaxOutput();
//            exit;
//        }
//    }

    private function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
        ) {
            $this->isAjax = TRUE;
        }
    }

    
    protected function _notFound404()
    {
        echo 404;
        exit;
    }
    
    protected function ajaxOutput()
    {
        echo json_encode($this->ajaxInfo, true);
        exit;
    }

    protected function _setAjax($bool)
    {
        $this->isAjax = $bool;
    }

    /**
     * 设置页面标题
     */
    protected function _setPageTitle($title)
    {
        $this->data['title'] = $title;
    }

    /**
     * 设置页面关键字
     */
    protected function _setPageKeywords($keywords)
    {
        $this->data['keyword'] = $keywords;
    }

    /**
     * 设置页面描述
     */
    protected function _setPageDescription($description)
    {
        $this->data['description'] = $description;
    }

    protected function _setSeoByKey($key, $data = array())
    {
        $seo_info = $this->lang->line($key);
        isset($data['title']) ? $seo_info['title']     = str_replace('{key}', $data['title'], $seo_info['title']) : '';
        isset($data['keyword']) ? $seo_info['keyword'] = str_replace('{key}', $data['keyword'], $seo_info['keyword']) : '';
        isset($data['desc']) ? $seo_info['desc']       = str_replace('{key}', $data['desc'], $seo_info['desc']) : '';

        $this->_setPageTitle($seo_info['title']);
        $this->_setPageKeywords($seo_info['keyword']);
        $this->_setPageDescription($seo_info['desc']);
    }


    /**
     * 添加引入css文件或代码
     */
    protected function _addCss($css)
    {
        if(!is_array($css))
        {
            if(preg_match('@\.css@', $css))
            {
                $this->_css['inline'] = $css;
                return ;
            }
            else
            {
                $css = array($css);
            }

        }
        foreach($css as $c)
        {
            if ( ! in_array($c, $this->_css['file']))
            {
                $this->_css['file'][] = $c . ($this->data['is_concat'] ? '' : "?v=". MY_Controller::CSS_V);
            }
        }

    }


    /**
     * 添加引入javascript文件或代码
     */
    protected function _addJs($js)
    {
        if(!is_array($js))
        {
            if(preg_match('@\.js@', $js))
            {
                $this->_javascript['inline'] = $js;
                return ;
            }
            else
            {
                $js = array($js);
            }
        }
        foreach($js as $j)
        {
            if ( ! in_array($j, $this->_javascript['file']))
            {
                $this->_javascript['file'][] = $j . ($this->data['is_concat'] ? '' :  "?v=". MY_Controller::JS_V);
            }
        }
    }

    public function _view($file = 'index', $returnContent = FALSE)
    {
        //$this->lang->load('site', 'english');
        $this->lang->load('site', 'zh_cn');
        $this->_setPageTitle($this->lang->line('site_global_site_name'));
        //load the data
        $data                   = &$this->data;
        $data['base_url']       = base_url();
        $data['static_url']     = static_url();
        $data['resource_url']   = static_url();
        $data['uid']            = 0;

        unset($this->user_info['password'], $this->user_info['user_login_name'], $data['user_info']['user_login_name'], $data['user_info']['password']);
        $data['user_info_json'] = json_encode($this->user_info);
        $data['user_info']      = $this->user_info;

        $data['__js']           = &$this->_javascript;
        $data['__css']          = &$this->_css;
        $data['JS_V']           = MY_Controller::JS_V;
        $data['CSS_V']          = MY_Controller::CSS_V;

        $data['lang_site_login']  = $this->lang->line('site_global_site_login');
        $data['lang_site_market'] = $this->lang->line('site_global_site_market');
        $data['lang_site_about']  = $this->lang->line('site_global_site_about');
        $data['lang_site_qa']     = $this->lang->line('site_global_site_qa');
        $data['lang_site_record'] = $this->lang->line('site_global_site_record');

        //load the page
        $content                = $this->load->view($file, $data, TRUE);
        //var_dump($content);

        $view_path              = APPPATH . 'views/';
        $segments               = explode('/', $file);



//            //$tpl = array_pop($segments);
//            $file_path = $view_path . implode('/', $segments) . '/';
//
//            if(file_exists($file_path . '__base.tpl'))
//            {
//                $__base = implode('/', $segments) . ($segments ? '/' : '') . '__base.tpl';
//                $data['__content'] = $content;
//                $content = $this->load->view($__base, $data, TRUE);
//            }
//            if(file_exists($file_path . '__widget.tpl'))
//            {
//                $__widget = implode('/', $segments) . ($segments ? '/' : '') . '__widget.tpl';
//                $model_widget_content = $this->load->view($__widget, $data, TRUE);
//                $content .= $model_widget_content;
//                $data['__content'] = $content;
//            }
//
//            if(file_exists($file_path . '__inline_js.tpl'))
//            {
//                $__inline_js = implode('/', $segments) . ($segments ? '/' : '') . '__inline_js.tpl';
//                $data['__js']['inline'] .= $this->load->view($__inline_js, $data, TRUE);
//                $data['__js']['inline'] .= "\n\n";
//            }
//
//            if(file_exists($file_path . '__inline_js_footer.tpl'))
//            {
//                $__inline_js_footer = implode('/', $segments) . ($segments ? '/' : '') . '__inline_js_footer.tpl';
//                $data['__js']['inline_footer'] .= $this->load->view($__inline_js_footer, $data, TRUE);
//                $data['__js']['inline_footer'] .= "\n\n";
//            }
//
//            if(file_exists($file_path . '__inline_css.tpl'))
//            {
//                $__inline_css = implode('/', $segments) . ($segments ? '/' : '') . '__inline_css.tpl';
//                $data['__css']['inline'] = $this->load->view($__inline_css, $data, TRUE);
//                $data['__css']['inline'] .= "\n\n";
//            }
//
//
//            if(count($segments) == 0)
//            {
//                //break;
//            }

        $this->output->append_output($content);
    }

    
}
