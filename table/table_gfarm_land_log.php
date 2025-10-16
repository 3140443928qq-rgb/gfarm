<?php
/**
 * 作者：Crazy创意工作室
 * Q Q：25466413
 * 介绍：活动中心中活动专区的设置表。
 *
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_gfarm_land_log extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_land_log';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select * from %t where 1 %i limit %d,%d', array($this->_table,$where,$offset,$limit));
	}
	public function fetch_clname($where=''){
		return DB::fetch_all('select distinct good_id from %t where 1 %i', array($this->_table,$where));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select * from %t where 1 %i', array($this->_table,$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(id) from %t where 1 %i', array($this->_table,$where));
	}
	public function count_num($where=''){
		$str='select count(a.id) cnum from %t a left join %t b on a.good_id=b.id where 1 %i group by uid';
		return DB::result_first($str, array($this->_table,'gfarm_goods',$where));
	}
	public function sum_num($where=''){
		$str='select sum(good_number) snum from %t a left join %t b on a.good_id=b.id where 1 %i group by uid';
		return DB::result_first($str, array($this->_table,'gfarm_goods',$where));
	}
}

?>