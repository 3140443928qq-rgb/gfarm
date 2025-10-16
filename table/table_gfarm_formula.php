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

class table_gfarm_formula extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_formula';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select a.*,b.name goodname,b.img,c.name germname from %t a left join %t b on a.success_id=b.id left join %t c on a.stuff_ids=c.id where 1 %i limit %d,%d', array($this->_table,'gfarm_goods','gfarm_goods',$where,$offset,$limit));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select a.*,b.img,b.small_type,b.name goodname from %t a,%t b where a.success_id=b.id %i', array($this->_table,'gfarm_goods',$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(id) from %t where 1 %i', array($this->_table,$where));
	}
}

?>