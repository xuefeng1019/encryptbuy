<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class orderORM  extends Db_Model
{
    const LIMIT = 10;
    public function __construct()
    {
        parent::__construct('eb_order');
    }
}
