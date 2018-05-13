<?php

class Searchcompany {
    
    public function __construct()
    {
        require_once '/home/www/xunsearch/sdk/php/lib/XS.php';
    }
    
    public function getResult() {

		error_reporting(E_ALL ^ E_NOTICE);
		
		//
		// 支持的 GET 参数列表
		// q: 查询语句
		// m: 开启模糊搜索，其值为 yes/no
		// f: 只搜索某个字段，其值为字段名称，要求该字段的索引方式为 self/both
		// s: 排序字段名称及方式，其值形式为：xxx_ASC 或 xxx_DESC
		// p: 显示第几页，每页数量为 XSSearch::PAGE_SIZE 即 10 条
		// ie: 查询语句编码，默认为 UTF-8
		// oe: 输出编码，默认为 UTF-8
		// xml: 是否将搜索结果以 XML 格式输出，其值为 yes/no
		//
		// variables
		$eu = '';
		$__ = array('q', 'm', 'f', 's', 'p', 'ie', 'oe', 'syn', 'xml');
		foreach ($__ as $_)
			$$_ = isset($_GET[$_]) ? $_GET[$_] : '';
		
		// input encoding
		if (!empty($ie) && !empty($q) && strcasecmp($ie, 'UTF-8'))
		{
			$q = XS::convert($q, $cs, $ie);
			$eu .= '&ie=' . $ie;
		}
		
		// output encoding
		if (!empty($oe) && strcasecmp($oe, 'UTF-8'))
		{
		
			function xs_output_encoding($buf)
			{
				return XS::convert($buf, $GLOBALS['oe'], 'UTF-8');
			}
			ob_start('xs_output_encoding');
			$eu .= '&oe=' . $oe;
		}
		else
		{
			$oe = 'UTF-8';
		}
		
		// recheck request parameters
		$q = get_magic_quotes_gpc() ? stripslashes($q) : $q;
		$f = empty($f) ? '_all' : $f;
		${'m_check'} = ($m == 'yes' ? ' checked' : '');
		${'syn_check'} = ($syn == 'yes' ? ' checked' : '');
		${'f_' . $f} = ' checked';
		${'s_' . $s} = ' selected';
		
		// base url
		$bu = $_SERVER['SCRIPT_NAME'] . '?q=' . urlencode($_GET['q']) . '&m=' . $m . '&f=' . $f . '&s=' . $s . $eu;
		
		// other variable maybe used in tpl
		$count = $total = $search_cost = 0;
		$docs = $corrected = $hot = array();
		$error = $pager = '';
		$total_begin = microtime(true);
		
		// perform the search
		try
		{
			$xs = new XS('wlg');
			$search = $xs->search;
			$search->setCharset('UTF-8');
		
			if (empty($q))
			{
				// just show hot query
				$hot = $search->getHotQuery();
			}
			else
			{
				// fuzzy search
				$search->setFuzzy($m === 'yes');
		
				// synonym search
				$search->setAutoSynonyms($syn === 'yes');
				
				// set query
				if (!empty($f) && $f != '_all')
				{
					$search->setQuery($f . ':(' . $q . ')');
				}
				else
				{
					$search->setQuery($q);
				}
		
				// set sort
				if (($pos = strrpos($s, '_')) !== false)
				{
					$sf = substr($s, 0, $pos);
					$st = substr($s, $pos + 1);
					$search->setSort($sf, $st === 'ASC');
				}
		
				// set offset, limit
				$p = max(1, intval($p));
				$n = XSSearch::PAGE_SIZE;
				$search->setLimit($n, ($p - 1) * $n);
		
				// get the result
				$search_begin = microtime(true);
				$docs = $search->search();
				$search_cost = microtime(true) - $search_begin;
		
				// get other result
				$count = $search->getLastCount();
				$total = $search->getDbTotal();
		
				if ($xml !== 'yes')
				{
					// try to corrected, if resul too few
					if ($count < 1 || $count < ceil(0.001 * $total))
						$corrected = $search->getCorrectedQuery();			
					// get related query
				}
		
				// gen pager
				if ($count > $n)
				{
					$pb = max($p - 5, 1);
					$pe = min($pb + 10, ceil($count / $n) + 1);
					$pager = '';
					do
					{
						$pager .= ($pb == $p) ? '<strong>' . $p . '</strong>' : '<a href="' . $bu . '&p=' . $pb . '">[' . $pb . ']</a>';
					}
					while (++$pb < $pe);
				}
			}
		}
		catch (XSException $e)
		{
			$error = strval($e);
		}

		$result = array();
		$result['oe'] = $oe;
		$result['q'] = $q;
		$result['count'] = $count;
		$result['total'] = $total;
		$result['search_cost'] = $search_cost;
		$result['error'] = $error;
		$result['corrected'] = $corrected;
		$result['docs'] = $docs;
		$result['pager'] = $pager;
		//$result['search'] = $search;
		
		return $result;
    }
    
}
