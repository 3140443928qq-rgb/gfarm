<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 鍒嗛〉绫?鍖呭惈褰撳墠椤垫暟,姣忛〉鏉℃暟,鎬昏?褰曟暟,褰撳墠椤垫暟鎹?
 * @author 
 *
 */
class Page {
	/**
	 * 褰撳墠椤垫暟
	 * @author 
	 * @access public
	 * @var integer
銆€銆€ */
	public $page		=	0;
	
	/**
	 * 姣忛〉鏉℃暟
	 * @author 
	 * @access public
	 * @var integer
銆€銆€ */
	public $perpage		=	20;
	
	/**
	 * 鎬昏?褰曟暟
	 * @author 
	 * @access public
	 * @var integer
銆€銆€ */
	public $count		=	0;
	
	/**
	 * 褰撳墠鍒嗛〉鏁版嵁
	 * @author 
	 * @access public
	 * @var array
銆€銆€ */
	public $data		=	array();
	

	/**
	 * 
	 * 杩斿洖鍒嗛〉浠ｇ爜
	 * @author
	 * @access public
	 * @param string $link:璺宠浆鐨勮矾寰
	 * @param integer $maxPages: 鍏佽?鏄剧ず鐨勬渶澶ч〉鏁
	 * @param integer $page:鏈€澶氭樉绀哄?灏戦〉鐮
	 * @param boolean $autoGoto:鏈€鍚庝竴椤碉紝鑷?姩璺宠浆
	 * @param boolean $simple:鏄?惁绠€娲佹ā寮忥紙绠€娲佹ā寮忎笉鏄剧ず涓婁竴椤点€佷笅涓€椤靛拰椤电爜璺宠浆锛
	 * @param boolean $jsFunc
	 * @return string :杩斿洖鍒嗛〉浠ｇ爜
	 */
	public function multi($link,$maxPages=0,$page=8,$autoGoto=false,$simple=false,$jsFunc=false){
		$link.='&menuid='.$_GET['menuid'].'&smenuid='.$_GET['smenuid'];
		$paging = helper_page :: multi($this->count, $this->perpage,$this->page, $link, $maxPages, $page, $autoGoto, $simple,$jsFunc);
		return $paging;
	}

	
	
	
	
}