<?php
function multiPage($uriparams, $totalrow, $pagesize, $shownum, $uriprefix, $kw='', $simple = false) {
        $kw = urlencode($kw);
        $shownum = $shownum - 1;
        if($totalrow > $pagesize) {
            $pagenum = ceil($totalrow/$pagesize);
        } else {
            $pagenum = 1;
        }
        if(isset($uriparams['p'])) {
            $page = $uriparams['p'];
        } else {
            $page = 1;
        }
        $startnum = $page - floor($shownum/2);
        if($startnum < 1) {
            $startnum = 1;
        }
        $endnum = $startnum + $shownum;
        if($endnum > $pagenum) {
            $endnum = $pagenum;
            $startnum = $endnum - $shownum;
        }
        if($startnum < 1) {
            $startnum = 1;
        }
        $list = array();
        for($i=$startnum;$i<=$endnum;$i++) {
            $uriparams['p'] = $i;
            $row['link'] = $uriprefix . encode_seo_parameters($uriparams);
            $row['page'] = $i;
            if($row['page'] == $page) {
                $row['selected'] = 1;
            } else {
                $row['selected'] = 0;
            }
            $list[] = $row;
        }
        $multipage = array("list"=>$list);
        if(!$simple) {
            if($startnum>1) {
                $multipage['firstpage'] = getPage(1, $uriparams,$uriprefix,$kw);
            } else {
                $multipage['firstpage'] = null;
            }
        }
        if($page > 1) {
            $multipage['prexpage'] = getPage($page - 1,$uriparams,$uriprefix,$kw);
        } else {
            $multipage['prexpage'] = null;
        }
        if($page < $pagenum) {
            $multipage['nextpage'] = getPage($page + 1,$uriparams,$uriprefix,$kw);
        } else {
            $multipage['nextpage'] = null;
        }
        if(!$simple) {
            if($endnum < $pagenum) {
                $multipage['endpage'] = getPage($pagenum,$uriparams,$uriprefix,$kw);
            } else {
                $multipage['endpage'] = null;
            }
        }
        if(!$simple) {
            $multipage['totalrow'] = $totalrow;
            $multipage['totalpage'] = $pagenum;
            $multipage['page'] = $page;
        }
        unset($uriparams['p']);
        if(!$simple) {
            $uriparams['p'] = "";
            $multipage['jumplink'] = $uriprefix . encode_seo_parameters($uriparams);
        }
        return $multipage;
    }
function getPage($page , $uriparams ,$uriprefix , $kw) {
    $firstpage = array();
    $firstpage['page'] = $page;
    $uriparams['p'] = $page;
    $firstpage['link'] = $uriprefix . encode_seo_parameters($uriparams);
   	return $firstpage;
}

function getNextPage($page , $uriparams, $uriprefix , $kw) {
    $nextpage = array();
	return $nextpage;
}