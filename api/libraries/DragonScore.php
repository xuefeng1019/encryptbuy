<?php
/************************************************************
* FILE_NAME : DragonScroe.php
* 龙誉值计算类
*
* @copyright Copyright (c) 2012 wolonge
* @author wolonge
* 
**************************************************************/

class DragonScore {
	var $active_coefficient; //活跃度系数
	var $repeat_coefficient; //重复使用系数
	var $repeat_time; //重复行为的次数
	var $trigger_time; //触发时间段
	var $db;
	var $user_info;
	var $score_id = 0;

	/*常量*/
	//活跃系数常量 coefficient
	const THE_STANDAR_ACTIVE_COEFFICIENT = 1;
	const THE_MAX_ACTIVE_COEFFICIENT     = 2.2;
	const THE_MIN_ACTIVE_COEFFICIENT     = 0.1;

	//重复系数常量
	const THE_STANDAR_REPEAT_COEFFICIENT = 1;
	const THE_MIN_REPEAT_COEFFICIENT     = 0.1;

	//递减递增常量
	const THE_DECREASE_VALUE			 = 0.1;
	const THE_INCREASE_VALUE			 = 0.1;
	

	/*
		注意事项：
		1、	多数行为产生的龙誉=基础行为分*重复使用系数*活跃度系数
		2、	活跃度系数的规则是固定的——
		根据用户连续登陆情况。
		标准情况下是1，最高2.2，最低0.1。
		由标准情况开始，每天连续登陆，每次累加0.1，直至2.2上限为止。如终止连续登陆（即超过24小时未连续登陆），回归至标准情况（即1）。
		有标准情况开始，连续48小时未登录，此后每24小时，以0.1递减，直至0.1下限为止。当用户开始登录时，重新计算48小时未登录状态，再判定是否递减；如果用户连续登陆，则回归至标准情况（即1）。
		3、重复使用系数，根据行为的具体规则，进行递减变化，标准情况为1，以每次行为引发速度为0.1的递减，以0.1为下限。


	    加分类型
		补全资料（每）	1	无	否
		添加头像	2	无	否
		公司留言	1	一小时内连续使用第三次开始递减	是
		吼一吼	1	30分钟内连续使用第五条开始递减	是
		关注	1	一天内，连续关注30人后开始递减	是
		评论	1	10分钟内连续进行第三条后开始递减	是
		被关注	0.5	无	是
		发布文章	2	一天内，连续发布第四条后开始递减	是
		转发	1	规则同评论	是
		收到评论	0.5	无	是
		公司评分	1.5	一天内连续第五家公司后开始递减	是
		邀请好友-成功注册登陆	3	无	无
		提问（被回答）	2	一小时内连续第三个问题后开始递减	是
		回答问题	3	无	是
		回复评论	1	规则同评论	是

	 */
	
	//每种类型的基本分数
	var $score_type  = array('fixProfile' 	   => 1, 
							'addAvatar'        => 2, 
							'ct'               => 1,
							'forward'          => 1,
							'ques'             => 2,
							'answer'           => 3,
							'post'             => 2,
							'standard'         => 1,
							'rock'             => 1,
							'fuck'             => 1,
							'gossip'           => 1,
							'help'             => 1,
							'happy'            => 1,
							'topic'            => 0,
							'join_topic'       => 0,
							'i_rate'           => 1.5,
							's_rate'           => 1.5,
							'reply'            => 1 ,
							'received_reply'   => 0.5,
							'following'        => 1 ,
							'beFollowed'       => 0.5,
							'cancelFollow'     => -1,
							'cancelBeFollowed' => -0.5,
							'reg'              => 6, 
							'recomend'         => 3,
							'beForward'        => 0.5,
							'feedGood'		   => 1,
							'feedBad'		   => -0.3
	);
	

	var $dontRepeatCoefficient = array(//'fixProfile',  补全资料另外处理， 因为补全资料有很多项， 每次补全资料更新一条分数， 取最新的那条作为龙誉值计算
									   'reg', 
									   'addAvatar', 
									   'beFollowed', 
									   'received_reply', 
									   'recomend', 
									   'answer', 
									   'cancelFollow',
									   'beForward',
									   'feedGood',
									   'feedBad'
									   );


    var $dontActiveCoefficient = array('reg',
    								   'fixProfile',
    								   'addAvatar',
    								   'recomend',
    								   'cancelFollow',
    								   'feedGood',
    								   'feedBad'
    								   );

	public function __construct($user_info = array())
	{
		$this->user_info = $user_info; unset($user_info);
	}

	public function getLastLoginTime()
	{
		$last_drgon_score = F::$f->last_dragon_scoreORM->selectOne(array('uid' => $this->user_info['uid']));

		if(empty($last_drgon_score))
		{
			$this->user_info['last_login_time']  = empty($this->user_info['last_login_time']) ? 0 : $this->user_info['last_login_time']; 
			F::$f->last_dragon_scoreORM->insert(array('uid' => $this->user_info['uid'], 
													  'user_position' => $this->user_info['user_position'], 
													  'trade' => $this->user_info['trade'],
													  'score' => 0,
													  'active_coefficient' => self::THE_STANDAR_ACTIVE_COEFFICIENT,
													  'add_time' => time(),
													  'last_login_time' => $this->user_info['last_login_time']
											    ));
			return $this->user_info['last_login_time'];
		}
		else
		{
			//return strtotime('2012-09-09');
			return $last_drgon_score['last_login_time'];
		}
	}

	public function getLastActiveCofficient()
	{
		$last_drgon_score = F::$f->last_dragon_scoreORM->selectOne(array('uid' => $this->user_info['uid']));
		if(empty($last_drgon_score))
		{
			return 0;
		}
		else
		{
			return floatval($last_drgon_score['active_coefficient']);
		}
	}

	public function run($type)
	{
		$this->computeActiveCoefficient();
		$repeat_score      = $this->computeCoefficient($type);
		$activeCoefficient = $this->_getActiveCoefficient();
		if(in_array($type, $this->dontActiveCoefficient))
		{
			$score = $repeat_score;
		}
		else
		{
			$score = $repeat_score * $activeCoefficient;
		}
		
		$this->saveDragonScoreByUid($this->user_info, array('score' => $score, 'active_coefficient' => $activeCoefficient));
	}

	public function addOneAction($data)
	{
		$this->_setRepeatType($data['type']);
		$this->repeat_time -= 1;
		$score = $this->_getDragonScoreByType($data['type']);
		F::$f->dragon_scoreORM->insert(array('uid' => $data['uid'], 'score_id' => $data['score_id'], 
											 'user_position' => intval($data['user_position']), 'trade' => $data['trade'],
											 'from_table' => $data['from_table'], 'type' => $data['type'],
											 'score' => $score, 'add_time' => time()));
		
	}


	public function saveDragonScoreByUid($user_info, $data)
	{
		$tmp = F::$f->last_dragon_scoreORM->selectOne(array('uid' => $user_info['uid']), array('select' => 'id, score'));
		if(!empty($tmp))
		{
			$score = $data['score']+floatval($tmp['score']);
			$score = empty($score) ? 0 : $score;
			return F::$f->last_dragon_scoreORM->update(array('uid' => $user_info['uid']), array('score' => $score, 
																				   'active_coefficient' => $data['active_coefficient']));
		}
		else
		{
			$data['score'] = empty($data['score']) ? 0 : $data['score'];
			return F::$f->last_dragon_scoreORM->insert(array('uid' => $user_info['uid'], 'score' => $data['score'], 
															 'user_position' => $user_info['user_position'], 'trade' => $user_info['trade'],
															 'active_coefficient' => $data['active_coefficient'], 
															 'add_time' => time()), true);
		}
	}

	public function saveLastLoginTime($user_info, $last_login_time)
	{
		$tmp = F::$f->last_dragon_scoreORM->selectOne(array('uid' => $user_info['uid']), array('select' => 'id, last_login_time'));
		
		if(date("Y-m-d", @$tmp['last_login_time']) == date("Y-m-d", $last_login_time)) 
		{
			//同一天连续登陆不更新了
			return true;	
		}

		if(!empty($tmp))
		{
			return F::$f->last_dragon_scoreORM->update(array('uid' => $user_info['uid']), array('last_login_time' => $last_login_time));
		}
		else
		{
			$t = time();
			return F::$f->last_dragon_scoreORM->insert(array('uid' => $user_info['uid'], 'score' => 0, 'trade' => $user_info['trade'], 'user_position' => $user_info['user_position'],
															 				'active_coefficient' => self::THE_STANDAR_ACTIVE_COEFFICIENT, 
															 				'add_time' => $t, 'last_login_time' => $t), true);
		}
	}


	public function getDragonScoreByUid($uid)
	{
		$tmp       = F::$f->last_dragon_scoreORM->select(array('uid' => $uid));
		$user_info = F::$f->userORM->getUnameByUids($uid, 
                                              		'uid, username', 
                                              		'*');
		$user_info = $user_info[$uid];
		$score     = empty($tmp) ? 0 : $tmp[0]['score'];
		$i         = 0;
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
		//print_r($this->user_info);
	}

	public function getDragonScoreByUids($uids)
	{
		$dragon_score   = F::$f->last_dragon_scoreORM->select(array('uid' => $uids));
		if(!empty($dragon_score))
		{
			array_change_key($dragon_score, 'uid');
		}

		$score     =  array();
		$i         = 0;
		$user_info = F::$f->userORM->getUnameByUids($uids, '*', '*');

		foreach(array('user_full_name', 'user_nick_name', 'user_sex', 'user_position', 'trade', 'functions', 
					  'office', 'company', 'working_years', 'salary', 'school', 'professional', 'start_year',
					  'end_year', 'user_birthday', 'user_comment') as $item)
		{
			foreach($user_info as $u)
			{
				if(!isset($score[$u['uid']]))
				{
					$score[$u['uid']] = 0;
				}

				$score[$u['uid']] = empty($u[$item]) ? $score[$u['uid']] : $score[$u['uid']]+1;
			}
		}

		return array('dragon_score' => $dragon_score, 'basic_score' => $score);
	}

	//计算活跃系数
	public function computeActiveCoefficient()
	{
		//获取最后一次登录时间和最后一次登录的活跃值
		$last_login_time              = $this->getLastLoginTime();
		$last_login_active_cofficient = $this->getLastActiveCofficient();
		
		//获取本次登录时间
		$cur_login_time               = intval($this->user_info['last_login_time']);
		
		$period                       = $cur_login_time - $last_login_time;
		$active_time                  = 86400;//24*60*60 = 86400 ， 每天登录
		$extreme_time                 = 259200;//24*60*60*3 , 72小时
		if(date('Y-m-d', $cur_login_time) == date('Y-m-d', $last_login_time))
		{	//当天多次登陆的活跃值，大于标准值则不往上加，小于标准值就重置为标准值
			$cur_login_active_cofficient = $last_login_active_cofficient >= self::THE_STANDAR_ACTIVE_COEFFICIENT ?
										   $last_login_active_cofficient : self::THE_STANDAR_ACTIVE_COEFFICIENT;
		}
		else if($period <= $active_time && $last_login_active_cofficient >= self::THE_STANDAR_ACTIVE_COEFFICIENT)
		{
			$cur_login_active_cofficient = $last_login_active_cofficient != self::THE_MAX_ACTIVE_COEFFICIENT ?
										   $last_login_active_cofficient + 0.1 :
										   self::THE_MAX_ACTIVE_COEFFICIENT;
		}

		else if($period <= $active_time && $last_login_active_cofficient < self::THE_STANDAR_ACTIVE_COEFFICIENT)
		{
			$cur_login_active_cofficient = self::THE_STANDAR_ACTIVE_COEFFICIENT;
		}

		else if($period > $active_time && $period <= $extreme_time)
		{   //大于24小时但是小于48小时
			//cur_login_active_cofficient不变
			
			if($last_login_active_cofficient >= self::THE_STANDAR_ACTIVE_COEFFICIENT)
			{
				$cur_login_active_cofficient = self::THE_STANDAR_ACTIVE_COEFFICIENT;
			}
			else if($last_login_active_cofficient < self::THE_STANDAR_ACTIVE_COEFFICIENT)
			{
				$cur_login_active_cofficient = $last_login_active_cofficient == self::THE_MIN_ACTIVE_COEFFICIENT ?
											   self::THE_MIN_ACTIVE_COEFFICIENT :
											   $last_login_active_cofficient - self::THE_DECREASE_VALUE;
			}
		}
		else if( $period > $extreme_time)
		{
			//24小时未登录， 重置为标准分数
			$period                      -= 86400;
			if($last_login_active_cofficient >= self::THE_STANDAR_ACTIVE_COEFFICIENT)
			{
				$cur_login_active_cofficient  = self::THE_STANDAR_ACTIVE_COEFFICIENT;
			}
			else
			{
				$cur_login_active_cofficient  = $last_login_active_cofficient;
			}

			//连续48小时未登录， 48小时候后每天递减0.1
			$period                      -= 172800;
			$tmp                          = ceil($period / 86400) * self::THE_DECREASE_VALUE;
			$cur_login_active_cofficient -= $tmp;
			$cur_login_active_cofficient  = $cur_login_active_cofficient <= self::THE_MIN_ACTIVE_COEFFICIENT ?
											self::THE_MIN_ACTIVE_COEFFICIENT :
											$cur_login_active_cofficient ;
		}

		$this->_setActiveCoefficient($cur_login_active_cofficient);
		return $cur_login_active_cofficient;
		//$this->cur_login_active_cofficient = $cur_login_active_cofficient;
	}

	//计算重复系数*基本分值后的龙誉， 自动剔除不需要计算重复系数的值
	public function computeCoefficient($type)
	{
		$this->_setRepeatType($type);
		$score = $this->_getDragonScoreByType($type);
		return $score;
	}

	private function _getDragonScoreByType($type)
	{
		if(in_array($type, $this->dontRepeatCoefficient))
		{
			return $this->score_type[$type];
		}
		else if($type == 'fixProfile')
		{
			//当是补全资料的类型时候， 不计算， 等到最终结果出来， 直接通过user_info计算有多少资料是补全的再加上去。
		}
		else
		{
			if(in_array($type, array('standard','rock', 'fuck', 'gossip', 'help', 'happy')))
			{
				$in_type = '"standard", "rock", "fuck", "gossip", "help", "happy"';
			}
			elseif(in_array($type, array('i_rate', 's_rate')))
			{
				$in_type = '"i_rate", "s_rate"';
			}
			else
			{
				$in_type = "\"{$type}\"";
			}

			$sql  = "SELECT * FROM `wlg_dragon_score` WHERE `uid`={$this->user_info['uid']} AND type IN ({$in_type}) AND is_del=0 LIMIT ".($this->repeat_time-1).", 10";
			
			
			
			$last = F::$f->dragon_scoreORM->execute($sql);

			if(!empty($last))
			{
				$cur_login_time = intval($this->user_info['last_login_time']);
				$i              = 0;
				foreach ($last as $v) 
				{
					$tmp_time = $cur_login_time - strtotime($v['last_update_time']);
					if($tmp_time < $this->trigger_time)
					{
						$i++;
					}
				}

				$repeat_coefficient = self::THE_STANDAR_REPEAT_COEFFICIENT;
				$repeat_coefficient -= $i*self::THE_DECREASE_VALUE;
				$repeat_coefficient = $repeat_coefficient <= self::THE_MIN_REPEAT_COEFFICIENT ?
									  self::THE_MIN_REPEAT_COEFFICIENT :
									  $repeat_coefficient; 

				$this->_setRepeatCoefficient($repeat_coefficient);
				return $repeat_coefficient*$this->score_type[$type];
			}
			else
			{
				return $this->score_type[$type];
			}
		}
	}

	private function _setRepeatType($type)
	{
		switch ($type) {
			case 'fixProfile':
				$this->_setRepeatTime(1);
				$this->_setRepeatTriggerTime(1);
				break;
			case 'addAvatar':
				$this->_setRepeatTime(1);
				$this->_setRepeatTriggerTime(1);
				break;
			case 'ct':
				$this->_setRepeatTime(3);
				$this->_setRepeatTriggerTime(3600);//60*60 1小时
				break;
			case 'i_rate':
			case 's_rate':
				$this->_setRepeatTime(5);
				$this->_setRepeatTriggerTime(86400);//60*60*24 1小时
				break;
			case 'reply':
			case 'forward':
				$this->_setRepeatTime(3);
				$this->_setRepeatTriggerTime(600);//60*10
				break;
			case 'ques':
				$this->_setRepeatTime(3);
				$this->_setRepeatTriggerTime(3600);//60*60
				break;
			case 'answer':
				$this->_setRepeatTime(1);
				$this->_setRepeatTime(1);
				break;
			case 'post':
				$this->_setRepeatTime(4);
				$this->_setRepeatTriggerTime(86400);//60*60*24
				break;
			case 'standard':
			case 'rock':
			case 'fuck':
			case 'gossip':
			case 'help':
			case 'happy':
				$this->_setRepeatTime(5);
				$this->_setRepeatTriggerTime(1800);//60*30
				break;
			case 'following':
				$this->_setRepeatTime(30);// default 30
				$this->_setRepeatTriggerTime(86400);//60*60*24
				break;
			case 'cancelFollow':
				$this->_setRepeatTime(1);
				$this->_setRepeatTriggerTime(1);
				break;
			case 'beFollowed':
				$this->_setRepeatTime(1);
				$this->_setRepeatTriggerTime(1);
				break;
			case 'reg':
				$this->_setRepeatTime(1);
				$this->_setRepeatTriggerTime(1);//60*60*24
			case 'topic':
				break;
			case 'join_topic':
				break;
		}
	}

	public function setUserInfo($user_info)
	{
		$this->user_info = $user_info;
	}

	//重复次数
	private function _setRepeatTime($time)
	{
		$this->repeat_time = $time;
	}

	//活跃系数
	private function _setActiveCoefficient($val)
	{
		$this->active_coefficient = $val;
	}

	//重复系数
	private function _setRepeatCoefficient($val)
	{
		$this->repeat_coefficient = $val;
	}

	//在多少时间段内会触发事件
	private function _setRepeatTriggerTime($val)
	{
		$this->trigger_time = $val;
	}

	public function setScoreId($id)
	{
		$this->score_id = $id;
	}
	private function _getRepeatCoefficient()
	{
		return $this->repeat_coefficient;
	}

	private function  _getActiveCoefficient()
	{
		return $this->active_coefficient;
	}

	public function getUserLastLoginTime($uid)
	{
		$tmp = F::$f->last_dragon_scoreORM->selectOne(array('uid' => $uid));
		if(empty($tmp))
		{
			$ext             = F::$f->user_extORM->selectOne(array('uid' => $uid), array('select' => 'user_position, trade'));
			$last_login_time = F::$f->userORM->selectOne(array('uid' => $uid), array('select' => 'last_login_time'));
			return array_merge($ext, $last_login_time);
		}
		else
		{
			return $tmp;
		} 
	}

	public function getGrouthByUids($uids)
	{
		if(empty($uids)) return array();
		$tmp = F::$f->userORM->select(array('uid' => $uids), array('select' => 'uid, last_login_time'));
		if(!empty($tmp)) {
			array_change_key($tmp, 'uid');
			$period = 86400;//24小时
			$grouth_score = array(); 
			foreach ($uids as $uid) {
				$time = $tmp[$uid]['last_login_time'] - $period;
				$grouth_score[$uid] = F::$f->dragon_scoreORM->selectOne(array('uid' => $uid, 'is_del' => 0, 'add_time' => array('>' => $time)), 
											   				   		   	  array('select' => 'uid, SUM(score) AS grouth_score'));
			}
			return $grouth_score ;
		}
	}

	public function getMyDragenPosition($uid)
    {
    	//array(242,107,101,86,105,112,92,88,102,109)
        //$res = F::$f->last_dragon_scoreORM->select(array('uid' => ''), array('order_by' => 'score DESC'));
        $sql = "SELECT * FROM wlg_last_dragon_score WHERE uid != 242 AND uid != 107 AND uid != 101 
        											 AND uid != 86 AND uid != 105 AND uid != 112 
        											 ORDER BY score DESC;";
        $res = F::$f->last_dragon_scoreORM->execute($sql);
        if(!empty($res))
        {
			$uids  = array_get_column($res, 'uid');
			$index = array_search($uid, $uids);
            return $index !== false ? $index+1 : false;
        }

        return false;
    }
}