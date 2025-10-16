<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class Component{
	/**
	 * 娣囶喗鏁
	 * @access public
	 * @author wangt
	 * @param Array $data:閺佺増宓
	 * @param Integer $id:缂傛牕褰
	 * @return -1:鐠佹澘缍嶆稉宥呯摠閸︼拷   1:娣囶喗鏁奸幋鎰??

	 */
	public function add($data) {
		$model = M('gfarm_plugin');
		$model->add($data);
		return 1;
	}

	/**
	 * 娣囶喗鏁
	 * @access public
	 * @author wangt
	 * @param Array $data:閺佺増宓
	 * @param Integer $id:缂傛牕褰
	 * @return -1:鐠佹澘缍嶆稉宥呯摠閸︼拷   1:娣囶喗鏁奸幋鎰??

	 */
	public function update($data,$id) {
		$model = M('gfarm_plugin');
		$where['id'] = $id;
		$componentInfo =$this->getInfo($id);
		if(empty($componentInfo)) {
			return -1;
		}
		$model->save($data,$where);
		return 1;
	}
	/**
	 * 閸掔娀娅
	 * @access public
	 * @author wangt
	 * @param Integer $id:缂傛牕褰
	 * @return -1:鐠佹澘缍嶆稉宥呯摠閸︼拷 1:閸掔娀娅庨幋鎰??
	 */
	public function delete($id) {
		$model = M('gfarm_plugin');
		$where['id'] = $id;
		$componentInfo =$this->getInfo($id);
		if(empty($componentInfo)) {
			return -1;
		}
		$model->delete($where);
		unlink($componentInfo['img']);
		return 1;

	}
	/**
	 * 閼惧嘲褰囬崡鏇熸蒋娣団剝浼
	 * @access public
	 * @author wangt
	 * @param Integer $id:缂傛牕褰
	 * @return 鏉╂柨娲栭崡鏇熸蒋鐠佹澘缍
	 */
	public function getInfo($id) {
		$model = M('gfarm_plugin');
		$where['id'] = $id;
		$componentInfo = $model->find($where);
		return $componentInfo;
	}
	/**
	 * 閼惧嘲褰囬崡鏇熸蒋娣団剝浼
	 * @access public
	 * @author wangt
	 * @param $mod:濡?€虫健閸氾拷
	 * @return 鏉╂柨娲栭崡鏇熸蒋鐠佹澘缍
	 */
	public function find($mod) {
		$model = M('gfarm_plugin');
		$where['model'] = $mod;
		$componentInfo = $model->find($where);
		return $componentInfo;
	}
	/**
	 * 閼惧嘲褰囬梿鍡楁値
	 * @access public
	 * @author wangt
	 * @param array $where:閺屻儴顕楅弶鈥叉?
	 * @param array|string $order:閹烘帒绨?弶鈥叉?
	 * @return array 鏉╂柨娲栭弻銉?嚄閸掓壆娈憇ql闂嗗棗鎮
	 */
	public function getList($where,$order,$limit=''){
		$model=M('gfarm_plugin');
		if($limit){
			$result=$model->where($where)->order($order)->limit($limit)->select();
		}else{
			$result=$model->where($where)->order($order)->select();
		}
		return $result;
	}

	/**
	 * 閸掑棝銆夐懢宄板絿闂嗗棗鎮
	 * @access public
	 * @author wangt
	 * @param Page page:閸掑棝銆夌€电?钖
	 * @param array $where:閺屻儴顕楅弶鈥叉?
	 * @param array|string $order:閹烘帒绨?弶鈥叉?
	 * @return Page page鐎电?钖
	 */
	public function getPage(Page $page,$where,$order){
		$model=M('gfarm_plugin');
		$limit['start']=($page->page-1)*$page->perpage;
		$limit['limit']=$page->perpage;
		$page->data=$model->limit($limit)->order($order)->select($where);
		$page->count=$model->where($where)->count();
		return $page;
	}

}

?>