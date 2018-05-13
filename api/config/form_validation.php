<?php
$config = array(
    'company/submitCt' => array(
                            'field' => 'comment_content',
                            'label' => '评论内容',
                            'rules' => 'required' 
                          ),
                          array(
                            'field' => 'company_id',
                            'label' => '公司ID',
                            'rules' => 'required|integer' 
                          ),
                          array(
                            'field' => 'feeling',
                            'label' => '好差评',
                            'rules' => 'required|integer' 
                          ),
                          array(
                            'field' => 'ctype',
                            'label' => '评论类型',
                            'rules' => 'required|integer' 
                          ),
                          //array(
                          //  'field' => 'from',
                          //  'label' => '内容来源',
                          //  'rules' => 'required|integer' 
                          //),
                          //array(
                          //  'field' => 'transpon_id',
                          //  'label' => '转发id',
                          //  'rules' => 'integer' 
                          //),
    
);