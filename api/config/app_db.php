<?php
//mysefl config for pdo
//ex: array('host' => 'localhost', 'port' => 3306, 'weight' => 1)
$config['db']['dev']['host'] = '127.0.0.1';
$config['db']['dev']['port'] = '3306';
$config['db']['dev']['weight'] = '1';
$config['db']['dev']['dbname'] = 'encryptbuy';
//$db['db']['dev']['dbprefix'] = 'eb_'; //沒有封裝
$config['db']['dev']['username'] = 'root';
$config['db']['dev']['password'] = 'feng1019';
$config['db']['dev']['read'] = array('host' => '127.0.0.1', 'port' => 3306, 'weight' => 1, 'username' => 'root', 'password' => 'feng1019');
$config['db']['dev']['write'] = array('host' => '127.0.0.1', 'port' => 3306, 'weight' => 1, 'username' => 'root', 'password' => 'feng1019');