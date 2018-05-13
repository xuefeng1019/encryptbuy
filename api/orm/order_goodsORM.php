<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class order_goodsORM  extends Db_Model
{
    const LIMIT = 10;
    public function __construct()
    {
        parent::__construct('eb_order_goods');
    }
}
