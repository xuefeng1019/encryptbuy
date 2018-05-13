<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lottery_model  extends MY_Model
{
    const LIMIT = 10;

    public function __construct()
    {
        parent::__construct();
    }

    public function insertForum($param) {
        //if ($param['uid'] && $param['forum']) {
        return F::$f->lotteryORM->insert(array('red_1' => (int)$param[0][0], 'red_2' => (int)$param[0][1], 'red_3' => (int)$param[0][2], 'red_4' => (int)$param[0][3], 'red_5' => (int)$param[0][4], 'red_6' => (int)$param[0][5], 'blue_1' => (int)$param[1]), true);
        //}
    }

    public function getNext($param) {
        if ($param[0] && $param[1]) {
            $array    = array();
            $all      = F::$f->lotteryORM->select(array('is_del' => '0', ), array('select' => "*", 'order_by' => 'id DESC'));
            $result   = array();
            $result[] = $this->checkNum($all, 1, $param[0][0], $result);

            for ($i = 1; $i <= 6; $i++) {
                $result[] = $this->checkNumByFirst($all, $i, $result[count($result) - 1], $result);
            }
            $result[6] = $this->checkBlue($all, 1, $param[1]);
            print_r($result);
            exit;
        }
    }
    public function checkNum($all, $num, $kindle, $result) {

        $data = array();
        if ($all) {
            for ($i = 0; $i < count($all); $i++) {
                if ($all[$i]['red_' . $num] == $kindle) {
                    $data[$all[$i + 1]['red_' . $num]] += 1;
                }
            }
        }

        if ($data) {
            arsort($data);
            foreach ($data as $key => $value) {
                if (!in_array($key, $result)) {
                    return $key;
                }
            }
        }
        return 0;
    }

    public function checkNumByFirst($all, $num, $kindle, $result) {

        $data = array();
        if ($all) {
            for ($i = 0; $i < count($all); $i++) {
                if ($all[$i]['red_' . $num] == $kindle) {
                    $data[$all[$i]['red_' . ($num + 1)]] += 1;
                }
            }
        }

        if ($data) {
            arsort($data);
            //print_r($data);
            foreach ($data as $key => $value) {
                if (!in_array($key, $result)) {
                    return $key;
                }
            }
        }
        return 0;
    }

    public function checkBlue($all, $num, $kindle) {

        $data = array();
        if ($all) {
            for ($i = 0; $i < count($all); $i++) {
                if ($all[$i]['blue_' . $num] == $kindle) {
                    $data[$all[$i + 1]['blue_' . $num]] += 1;
                }
            }
        }

        if ($data) {
            arsort($data);
            foreach ($data as $key => $value) {
                if ($key <= 15) {
                    return $key;
                }
            }
        }
        return 0;
    }
}
