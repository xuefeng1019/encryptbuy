<?php
function valid_email($address)
{
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}
function valid_password($password) {
	return strlen($password) < 6 || strlen($password) > 12 ? false : true;
}
function filter_s($str)
{
	$str = preg_replace('/\s+/', " ", $str);
	$str = preg_replace('/(<br \/> )+/', "<br />", $str);
	$str = preg_replace('/(<br> )+/', "<br />", $str);
	$str = preg_replace('/(&nbsp; )+/', "&nbsp; ", $str);
	return trim($str, '<br>, <br />, <br/>');
}
function stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                strip_tags($value);
    $value = str_replace("\\\\n", "", $value);
    $value = str_replace("\\n", "", $value);
    $value = str_replace("\\r", "", $value);
    $value = str_replace("<br />n", "<br />", $value);

    $value = str_replace("rn", "", $value);
    if(is_string($value))
    {
    	$value = h($value);
    }

    if(is_string($value) && strlen($value) > 300)
    {
    	$value = cut_paragraphs($value);
    }
    return $value;
}

function feed_copy_stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('feed_copy_stripslashes_deep', $value) :
                strip_tags($value);
    $value = str_replace("\\n", "", $value);
    $value = str_replace("\\r", "", $value);

    return $value;
}

function cut_paragraphs($value)
{
	$value = h($value);
	preg_match('#{title}(.*){/title}#', $value, $m);
	if(isset($m[1]))
	{
		$value = str_replace("{title}$m[1]{/title}", '', $value);
		$value = str_replace("<br />n", "<br />", $value);
		$value = trim($value, "\\n");
	    $value = explode("\\n", $value);//print_r($value);
	    if(isset($value[0]))
	    {
	    	return "{title}$m[1]{/title}" . $value[0];
	    }
	    else
	    {
	    	return "{title}$m[1]{/title}" . $value;
	    }
	}
	else
	{
		$value = str_replace("<br />n", "<br />", $value);
		return $value;
	}
}
function random_code($length = 20)
{
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';    //字符池
	$key = '';
    for($i=0; $i < $length; $i++)
	{
		$key .= $pattern{mt_rand(0,35)};    //生成php随机数
	}
	return $key;
}

function stripbr($str)
{
    return preg_replace("#<br />|<br>|<br/>#iU", "", hd($str));
}

function br($str)
{
    return str_replace("\n", "<br />", $str);
}
function trimBr($str)
{
	if(empty($str)) return '';
	return str_replace("<br/>","",str_replace("<br>", "", str_replace("<br />", "", $str)));
}
//alias htmlspecialchars
function h($str)
{//htmlspecialchars
	if(empty($str)) return '';
    return rtrim(str_replace(array('\n', '	', '<br />'), array('', '', ''), htmlspecialchars_decode($str, ENT_QUOTES)));
}
function hd($str)
{
	return htmlspecialchars_decode($str, ENT_QUOTES);
}
function hs($str)
{
	if(empty($str)) return '';
    return rtrim(htmlspecialchars($str, ENT_QUOTES));
}
//nl2br strip_tags strval
function ns($str)
{
	if(empty($str)) return '';
    return rtrim(htmlspecialchars(nl2br(strip_tags(rawurldecode(strval($str)))), ENT_QUOTES));
}

//strip_tags strval
function ss($str)
{
	if(empty($str)) return '';
    return strip_tags(rawurldecode(strval($str)));
}
/**
 * 格式化时间
 *
 */
function my_date_format($timestamp)
{
    $now = time();
	
    $t = $now - $timestamp;
	$time = date("Y-m-d H:i:s", $timestamp);
    if ($t == 0) 
    {
        $time = "刚才";
    } 
    elseif ($t >0 && $t < 60) 
    {
        $time = $t . "秒前";
    } 
    elseif ($t >= 60 && $t < 3600) 
    {
        $time = floor($t / 60) . "分钟前";
    } 
    elseif ($t >= 3600 && $t < 86400) 
    {
        $time = floor($t / 3600) . "小时前";
    } 
    elseif ($t >= 86400 && $t < 604800) 
    {
        $time = floor($t / 86400) . "天前";
    } 
    elseif ($t >= 604800 && $t < 5184000) 
    {
        $time = floor($t / 604800) . "周前";
    } 
    elseif ($t >= 5184000 && $t < 31536000) 
    {
        $time = floor($t / 2592000) . "个月前";
    } 
    elseif ($t >= 31536000) 
    {
        $time = floor($t / 31536000) . "年前";
    }
    return $time;
}
/**
 * js弹出信息并跳转
 * @param    string    $msg
 * @param    string    $url
 * @return void    
 */
function jsAlert($msg, $url = NULL)
{
    header('Content-Type: text/html; charset=UTF-8');
    $url = $url ? $url : $_SERVER['HTTP_REFERER'];
    $location = "window.location.href = '$url'";
    exit("<script type=\"text/javascript\">alert('{$msg}'); {$location} </script>");
}

/**
 * js弹出信息并关闭
 * @param    string    $msg
 * @param    string    $url
 * @return void    
 */
function jsClose($msg, $url = NULL)
{
    header('Content-Type: text/html; charset=UTF-8');
    $action = 'window.close();';
    exit("<script type=\"text/javascript\">alert('{$msg}'); {$action} </script>");
}
/**
 * 301跳转
 * @param    string    $url
 * @return void    
 */
function header301($url)
{
	header("HTTP/1.1 301");
	header("Location: $url");
	exit;
}

function pag($total, $per_page = 10, $url = '')
{
    $CI = &get_instance();
    $CI->load->library('pagination');
    //$config['base_url'] = $url;
    $config['total_rows'] = $total;
    $config['per_page'] = $per_page; 

    $CI->pagination->initialize($config); 

    return $CI->pagination->create_links();
}


/**
 * 检测是否含有@某人功能
 * 
 */
function isAt($content)
{
    preg_match_all("#@([^\s]+)#", $content, $m);
    return $m[1];
}
/**
 * 滤掉话题两端的空白
 * 
 */
function stripBlank($str)
{
    $str = preg_replace("/#[\s]*([^#^\s][^#]*[^#^\s])[\s]*#/is",'#'.trim("\${1}").'#',$str);
    return $str;
}

function stripTopic($str)
{
	return preg_replace('/^(#.*#)/U', '', $str);
}
/**
 * 自动添加a连接给内容中的URL
 * 
 */
if(!function_exists('autoHref'))
{
	function autoHref($content)
	{
		$content = preg_replace("#(http|https)://([\w-]+\.)+[\w-]+(/[\w- ./?%&\#!=,;]*)?#i", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $content);
		preg_match_all("/(<([a]+)[^>]*>.*?<\/\\2>)/i", $content, $matches, PREG_SET_ORDER);
		foreach ($matches as $key => $match)
		{
			$matches[$key] = array_unique($match);
			array_pop($matches[$key]);
			$matches[$key] = $matches[$key][0];
		}
		//new content array
		$new_content = array();
		$j = 0;
		$i = 0;
		$len = array();
		foreach ($matches as $k => $m)
		{
			$len[$k] = strlen($m);
			
			$i = strpos($content, $m);
			
			if($j != 0) 
			{
				$new_content[] = substr($content, $j + $len[$k-1], $i - $j - $len[$k-1]);
			}
			else 
			{
				$new_content[] = substr($content, $j, $i);
			}
			$new_content[] = substr($content, $i, $len[$k]);
			$j = $i;
		}
		$new_content[] = substr($content, $i + strlen(array_pop($matches)) );
		return $new_content;
	}
}

/*
 * 截取字符串
 */
 
function cutStr($string, $length = 140, $dot = ' ...') {
	$string = trim($string); //echo mb_strlen($string, 'utf-8');
	$string = (mb_strlen($string, 'utf-8') < $length) ? $string : mb_strcut($string, 0, $length * 3 - 3, 'utf-8') . '...';
	return h($string);
	/*
	$charset = 'utf-8';
	$string = strip_tags($string);
	if(strlen($string) <= $length) {
		return checkAt($string);
	}
	$length -= 6;
	
	

	$strcut = '';
	if(strtolower($charset) == 'utf-8') {
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	//$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return checkAt($strcut).$dot;
	 */
}


function mySubStr($string, $start = 0, $end = 0) {
	$charset = 'utf-8';
    if($end == 0)
    {
        return mb_substr($string, 0, $start, $charset);
    }
    
    return mb_substr($string, $start, $end, $charset);
}
if(!function_exists('mb_substr'))
{
	function mb_substr($str, $start = 0, $length = 0, $encode = 'utf-8') {
		//该编码每个非英文字符的字节长
		$encode_len = $encode == 'utf-8' ? 3 : 2;
		//计算开始字节
		for($byteStart = $i = 0; $i < $start; ++$i) {
			$byteStart += ord($str{$byteStart}) < 128 ? 1 : $encode_len;
			// 当起始坐标超出字符串，则返回空值
			if( $str{$byteStart} == '' ) return '';
		}
		// 计算字节长度
		for($i = 0, $byteLen = $byteStart; $i < $length; ++$i)
			$byteLen += ord($str{$byteLen}) < 128 ? 1 : $encode_len;
	 
		return substr( $str, $byteStart, $byteLen-$byteStart );
	}
}


function setRefer() {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$refer = $_SERVER['HTTP_REFERER'];
		if ($refer) {
			setcookie('l_referer', $refer, 0);
		}
	}
}

function addslashes_array(&$array){
	if(is_array($array)){
		foreach($array as $key=>$val){
			$array[$key]=addslashes_array($val);
		}
	}
	else if(is_string($array)){
		preg_match_all("/(<([a]+)[^>]*>.*?<\/\\2>)/i", $array, $matches, PREG_SET_ORDER);
		if(empty($matches))
			$array = addslashes($array);
	}
	return $array;
}


function init_robot($uid = 0)
{
	if($uid == 0) return false;
	$group_id = get_robot_group_id();
	$fans_uids = get_robot_users($group_id);
	$sql = 'INSERT INTO `wlg_follow_user` (`ctime`, `uid`, `fuid`) VALUES ';
	$v = array();
	foreach($fans_uids as $u)
	{
		$v[] = '(' . time() . ', ' . $u . ', ' . $uid . ')';
	}
	$sql .= implode(',', $v);
	// echo F::$f->userORM->getLastSql();
	// echo $sql;exit;
	F::$f->userORM->execute($sql);
	F::$f->need_robot_userORM->insert(array('uid' => $uid, 'group_id' => $group_id, 'add_time' => time()));
	$insert_group_id = F::$f->need_robot_userORM->getLastSql();
	log_message('info', 'robot insert sql[$sql]' . "\n" . "insert group_id sql[$insert_group_id]");
}

function get_robot_group_id()
{
	return rand(1, 5);
}

function get_robot_users($gid, $offset = 0, $limit = 10)
{
	$uid   = $gid * 200 + 800;
	$robot = F::$f->userORM->select(array('uid' => array('>=' => $uid, '<' => $uid + 200, '__logic' => 'AND')), 
									array('select' => 'uid', 'order_by' => 'uid ASC', 'offset' => $offset, 'limit' => $limit));
	return array_get_column($robot, 'uid');
}


function preFormatFate($rate)
{
	@list($y, $x) = explode('.', $rate);
	$x = intval($x);
	$y = intval($y);
	if($x < 3 ) {
		if($y == 0 && $x == 0) {
			return intval($y) . "0";
		}
		else if($y == 0) {
			return intval($y) . 'h'; 
		} else { 
			return intval($y) . "0";
		}
	} else if($x >= 3 && $x <= 7) {
		return intval($y) . 'h';
	} else if( $x > 5 && $x <= 9 ) {
		return intval($y) . "1";
	}
}

function getInterviewRateHtml($data)
{
	$i = substr($data, 0, -1);
	$k = substr($data, -1);
	$str = '';
	for($m = $n = 0 ; $m < 5; $m++, $n++)
	{
		if($n < $i)
		{
			$str .= '<span class="rank rank_f"></span>&nbsp;';
		}
		else if($n == $i)
		{
			if($k === "0")
			{
				$str .= '<span class="rank "></span>&nbsp;';
			}
			else if($k === "h")
			{
				$str .= '<span class="rank rank_h"></span>&nbsp;';
			}
			else if($k === "1")
			{
				$str .= '<span class="rank rank_f"></span>&nbsp;';
			}
		}
		else
		{
			$str .= '<span class="rank rank_e"></span>&nbsp;';
		}
	}

	return $str;
}

function getInterviewRateHtml2($data)
{
	$str = '';
	for($m = $n = 0 ; $m < 5; $m++, $n++)
	{
		if($n < $data) 
		{
			$str .= '<span class="rank rank_f"></span>&nbsp;';
		}
		else
		{
			$str .= '<span class="rank rank_e"></span>&nbsp;';
		}
	}
	return $str;
}


function getCompanyScore($re, $de, $cul, $le, $in) {
	return ($re + $de + $cul + $le + $in);//四舍五入
}

function get_company_score($company_score) {
	if (!$company_score || !is_array($company_score)) {
		return array('re' => 0, 'de' => 0, 'cul' => 0, 'le' => 0, 'in' => 0, 'all' => 0);
	}
	$re = $de = $cul = $le = $in = $all = 0;
	$i = 0;
	foreach ($company_score as $score) {
		$re  += $score['remuneration'];
		$de  += $score['developing'];
		$cul += $score['culture'];
		$le  += $score['leadership'];
		$in  += $score['integrity'];
		$all += $score['all_fraction'];
		$i++;
	}
	return array('re' => ceil($re / $i), 'de' => ceil($de / $i), 'cul' => ceil($cul / $i), 'le' => ceil($le / $i), 'in' => ceil($in / $i), 'all' => ceil($all / $i));
}
function get_user_ip(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
	  $cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
	  $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif(!empty($_SERVER["REMOTE_ADDR"])){
	  $cip = $_SERVER["REMOTE_ADDR"];
	}
	else{
	  $cip = "unknow";
	}
	return $cip;
}

function getBasicDragonScore($user_info)
{
	$score = $i = 0;
	foreach(array('user_full_name', 'user_nick_name', 'user_sex', 'user_position', 'trade', 'functions', 
					  'office', 'company', 'working_years', 'salary', 'school', 'professional', 'start_year',
					  'end_year', 'user_birthday', 'user_comment') as $item)
	{
		if(!empty($user_info[$item]))
		{
			$i++;
		}
	}
	return $score+$i;
}

function closetags($html) {
	preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	$openedtags = $result[1];
	preg_match_all('#</([a-z]+)>#iU', $html, $result);
	$closedtags = $result[1];
	$len_opened = count($openedtags);
	$len_closed = count($closedtags);
	if ($len_closed == $len_opened) {
		return $html;
	}
	$openedtags = array_reverse($openedtags);
	for ($i=0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)) {
			$html .= '</'.$openedtags[$i].'>';
		} else {
			unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
	}
	return $html;
}