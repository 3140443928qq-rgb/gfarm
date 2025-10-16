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

class table_gfarm_member_achieve_task extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_member_achieve_task';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select a.*,a.id myid,b.*,a.create_time ctime from %t a left join %t b on a.task_id=b.id where 1 %i limit %d,%d', array($this->_table,'gfarm_achieve_task',$where,$offset,$limit));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select a.*,a.id myid,a.create_time ctime,b.*,c.way_type,c.way_name from %t a left join %t b on a.task_id=b.id left join %t c on b.task_way=c.id where 1 %i', array($this->_table,'gfarm_achieve_task','gfarm_task_way',$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(id) from %t where 1 %i', array($this->_table,$where));
	}
}

?>