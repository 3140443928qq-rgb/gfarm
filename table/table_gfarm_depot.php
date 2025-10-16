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

class table_gfarm_depot extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_depot';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select a.*,b.big_type,b.use_level,b.use_benevolence,b.small_type,b.name,b.sale_price,b.money_types,c.separate_name,b.description,b.img from %t a,%t b,%t c where a.goods_id=b.id and b.big_type=c.id %i limit %d,%d', array($this->_table,'gfarm_goods','gfarm_goods_separate',$where,$offset,$limit));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select a.*,b.name,b.img,b.sale_price,b.money_types from %t a,%t b where a.goods_id=b.id %i', array($this->_table,'gfarm_goods',$where));
	}
	public function fetch_sum_data($where=''){
		return DB::result_first('select sum(number) from %t a,%t b where a.goods_id=b.id %i', array($this->_table,'gfarm_goods',$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(a.id) from %t a,%t b where a.goods_id=b.id %i', array($this->_table,'gfarm_goods',$where));
	}
}

?>