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

class table_gfarm_sale extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_sale';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select a.good_price,a.depot_id,b.uid,c.use_level,c.name,b.number,c.img,c.big_type,c.small_type from %t a,%t b,%t c where a.depot_id=b.id and b.goods_id=c.id %i limit %d,%d', array($this->_table,'gfarm_depot','gfarm_goods',$where,$offset,$limit));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select * from %t where 1 %i', array($this->_table,$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(id) from %t where 1 %i', array($this->_table,$where));
	}
}

?>