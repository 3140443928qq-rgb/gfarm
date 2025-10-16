<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
function M($tableName){
	$model=new Model($tableName);
	return $model;
}
/**
 * 鏈?被涓哄揩鎹稵ableModel绫?鎵€鏈夋潯浠剁敓鎴愬彧鏀?寔and杩炴帴涓嶆敮鎸乷r杩炴帴
 * 姝ょ被鍩轰簬DB绫诲疄鐜扮殑
 * 褰撲紶閫抯tring鏃惰?鍋氬ソ杞?崲鍏堣繘琛宒addslashes($str)濡俰d=1璇峰啓鎴恑d='1',username=admin鍐欐垚username='admin'
 * 澶氳〃鏌ヨ?鑷?繁鍋氬ソdadslases($str)
 * 鏈?被涓嶆彁渚涘?鏉傜殑鏌ヨ?璇?彞,澶嶆潅鐨勮?浣跨敤query($sql,$args)鏂规硶
 * @author Administrator
 *
 */
class Model{
	// 涓婚敭鍚嶇О
	protected $pk               =   'id';
	// 鏁版嵁琛ㄥ墠缂€
	protected $tablePrefix      =   '';
	// 鏁版嵁琛ㄥ悕锛堜笉鍖呭惈琛ㄥ墠缂€锛
	protected $tableName        =   '';
	// 瀹為檯鏁版嵁琛ㄥ悕锛堝寘鍚?〃鍓嶇紑锛
	protected $trueTableName    =   '';
	// 鏈€杩戦敊璇?俊鎭
	public $error           	=   '';
	//閿欒?瀛楁?
	public $errorField			=	'';
	// 瀛楁?淇℃伅
	protected $fields           =   '';
	// 鏁版嵁淇℃伅
	protected $data             =   array();
	// 鏁版嵁鏉′欢瀛楁?
	protected $where            =   array();
	// 鏁版嵁瀛楁?
	protected $field            =   '';
	// 鏌ヨ?缁撴灉鏁伴噺闄愬埗
	protected $limit            =   array();
	// 杩炴帴鏌ヨ?鏀?寔
	protected $join             =   '';
	// 杩炴帴鏌ヨ?鍙傛暟鏀?寔
	protected $joinParams		=	array();
	// 鍘婚噸澶嶆敮鎸
	protected $distinct         =   '';
	// 鎺掑簭鏀?寔
	protected $order            =   array();
	// 鍒嗙粍鏀?寔
	protected $group            =   '';
	// having鏉′欢杩囨护
	protected $having           =   '';
	// 鑱斿悎鏌ヨ?
	protected $union            =   '';
	// 澶氳〃鏌ヨ?鏀?寔
	protected $tables			=	'';
	//澶氳〃鏌ヨ?鍙傛暟鏀?寔
	protected $tablesParams 	=	array();

	// 鍙傛暟
	protected $options          =   array();

	// 瀛楁?鏄犲皠
	protected $_map				=	array();

	// 鑷?姩濉?厖
	protected $_filled			=	array();

	// 鑷?姩楠岃瘉
	protected $_validate		=	array();


	// 鏌ヨ?琛ㄨ揪寮
	//  protected $selectSql  = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%COMMENT%';


	public function __construct($tableName='',$tablePrefix='',$trueTableName='') {
		$this->tableName=$tableName;
		$this->tablePrefix=$tablePrefix;
		$this->trueTableName=$trueTableName;
		$this->options[]=$this->tableName;
		$this->fields=$this->getFields($tableName);
	}


	/**
	 * 鏂板?璁板綍(鏁版嵁浼樺厛绾э紝鑷?姩鏄犲皠璁惧€稽$model->data($data);<add($data))
	 * @access public
	 * @param array $data  鏂板?鏁版嵁
	 * @return integer:鏂板?id insert_id
	 */
	public function add($data){
		$udata=array();
		if(!empty($data)&&is_array($data)){
			$udata=$data;
		}
		if(!empty($this->data)&&is_array($this->data)){
			$udata=array_merge($this->data,$udata);
		}
		if(empty($udata)|!is_array($udata)){
			return false;
		}
		$this->resetOption();
		return DB::insert($this->tableName, $udata,true);
	}
	/**
	 * 鍒犻櫎璁板綍
	 * @access public
	 * @param array $where  鍒犻櫎鏉′欢
	 * @param boolea $all  鏃犳潯浠跺苟涓旇?鍒犻櫎鍏ㄩ儴閬垮厤寮€鍙戣€呰?鐢ㄦ病浼犲弬鏁板垹闄ゅ叏閮ㄦ暟鎹
	 * @return integer:褰卞搷琛屾暟
	 */
	public function delete($where){
		$uwhere=array();
		if(!empty($where)&&is_array($where)){
			$uwhere=$where;
		}
		if(!empty($this->where)&&is_array($this->where)){
			$uwhere=array_merge($uwhere,$this->where);
		}
		$this->resetOption();
		return DB::delete($this->tableName, $uwhere);
	}
	/**
	 * 鍒犻櫎鎵€鏈夋暟鎹?delete from %t
	 * @access public
	 */
	public function deleteAll(){
		$sql='delete from %t';
		return DB::query($sql, $this->tableName);
	}

	/**
	 * 瀵笵B::fetch_all鏀?寔鐩存帴璋冪敤DB::fetch_all
	 * @param string $sql:瑕佹墽琛岀殑sql
	 * @param array $arg:瑕佷紶閫掔殑鍙傛暟
	 */
	public function fetch_all($sql,$arg){
		return DB::fetch_all($sql, $arg);
	}
	/**
	 * 鏇存柊璁板綍(鏁版嵁浼樺厛绾э紝鑷?姩鏄犲皠璁惧€稽$model->data($data);<add($data))
	 * @access public
	 * @param array $data  鏇存柊鏁版嵁
	 * @param array|string $where  鏉′欢
	 * @return integer:褰卞搷琛屾暟
	 */
	public function save($data,$where=''){
		$udata=array();
		if(!empty($data)&&is_array($data)){
			$udata=$data;
		}
		if(!empty($this->data)&&is_array($this->data)){
			$udata=array_merge($this->data,$udata);
		}
		if(empty($udata)|!is_array($udata)){
			return false;
		}
		$condition=array();
		if(!empty($where)&&is_array($where)){
			$condition=$where;
		}
		if(!empty($this->where)&&is_array($this->where)){
			$condition=array_merge($this->where,$condition);
		}
		if(empty($condition)){
			if(empty($udata)||!is_array($udata)){
				return false;
			}
			if(empty($udata[$this->pk])){
				return false;
			}
			$condition[$this->pk]=$udata[$this->pk];
		}
		$this->resetOption();
		return DB::update($this->tableName, $udata, $condition);
	}
	/**
	 * 鏌ヨ?鏁版嵁
	 * @access public
	 * @param array|string $where  鏌ヨ?鏉′欢
	 * @return array :璁板綍闆
	 */
	public function select($where){
		$sql=' select '.$this->distinct.' '.$this->parseField().' from %t '.$this->parseTables().$this->parseJoin().' where 1 ';
		$sql.=$this->parseWhere($where).$this->parseWhere($this->where).' ';
		$sql.=$this->parseGroup().' '.$this->parseHaving().' '.$this->parseOrder().' ';
		$sql.=$this->parseLimit().' '.$this->parseUnion();
		$this->where=null;
		$options=$this->options;
		$this->resetOption();
		
		return DB::fetch_all($sql, $options);
	}

	/**
	 * 鏌ヨ?鍗曟潯鏁版嵁
	 * @access public
	 * @param array|string $where  鏌ヨ?鏉′欢
	 * @return array :鍗曟潯
	 */
	public function find($where){
		$sql=' select '.$this->distinct.' '.$this->parseField().' from %t '.$this->parseTables().$this->parseJoin().' where 1 ';
		$sql.=$this->parseWhere($where).$this->parseWhere($this->where).' ';
		$sql.=$this->parseGroup().' '.$this->parseHaving().' '.$this->parseOrder().' ';
		$sql.=$this->parseLimit().' '.$this->parseUnion();
		$options=$this->options;
		$this->resetOption();
		return DB::fetch_first($sql, $options);
	}

	/**
	 * 瀛楁?鍊煎?闀?鐩稿綋浜巙pdate %t set $field=$field+$step where...
	 * @access public
	 * @param array|string $field  瀛楁?鍚
	 * @param integer $step  澧為暱鍊
	 * @return boolean
	 */
	public function setInc($field,$step=1) {
		return $this->setIncDec($field,'inc',$step);
	}

	/**
	 * 杞?崲瀛楁?鍙樻垚string渚夸簬瀵瑰瓧娈靛?鍔犳垨鑰呭噺灏戠壒瀹氬€兼柟娉曡皟鐢?紝鏀?寔澶氬瓧娈
	 * @access public
	 * @param array|string $field:瀛楁?鍚
	 * @param string $type:鍙樺寲绫诲瀷
	 * @param integer $step:鍙樺寲鍊
	 * return string
	 */
	private function parseFieldString($field,$type='inc',$step=1){
		if(empty($field)&&empty($this->field)){//鍒ゆ柇鍙?敤瀛楁?鍙橀噺鏄?惁涓虹┖
			return false;
		}
		$fields=array();
		$fieldString='';

		if(!empty($field)&&is_string($field)){
			$fields=explode(',', $field);
		}
		if(!empty($field)&&is_array($field)){
			$fields=$field;
		}
		if(!empty($this->field)){
			$fields=array_merge($fields,explode(',',$this->field));
		}
		$count=count($fields);
		for($i=0;$i<$count;$i++){
			$operate=strtolower($type)=='inc'?' + ':' - ';
			$fieldString.=$fields[$i].' = '.$fields[$i].$operate.' %d ';
			if($i<($count-1)){
				$fieldString.=',';
			}
			$this->options[]=$step;
		}

		return $fieldString;
	}
	/**
	 * 瀛楁?鍊煎噺灏?鐩稿綋浜巙pdate %t set $field=$field-$step where...
	 * @access public
	 * @param array|string $field  瀛楁?鍚
	 * @param integer $step  鍑忓皯鍊
	 * @return boolean
	 */
	public function setDec($field,$step=1) {
		return $this->setIncDec($field,'dec',$step);
	}
	/**
	 * 渚挎嵎瀵圭壒瀹氭煇浜涘瓧娈佃繘琛屽?鍔犳垨鑰呭噺灏
	 * @access public
	 * @param array|string $field:瀛楁?鍚
	 * @param string $type:鍙樺寲绫诲瀷
	 * @param integer $step:鍙樺寲鍊
	 * return
	 */
	public function setIncDec($field,$type='inc',$step=1){
		$fieldString=$this->parseFieldString($field,$type,$step);
		if(!$fieldString||empty($fieldString)){
			return false;
		}
		$sql='update %t set '.$fieldString.' where 1 '.$this->parseWhere($this->where);
		$options=$this->options;
		$this->resetOption();
		return DB::query($sql, $options);
	}
	/**
	 * 鎵ц?sql鐩存帴璋冪敤DB::query($sql,$args);
	 * @access public
	 * @param string $sql :鎵ц?鐨剆ql
	 * @param array $args :鍙傛暟
	 * @return
	 */
	public function query($sql,$args=null){
		return DB::query($sql, $args);
	}
	/**
	 * 鍒涘缓瀵硅薄鏁版嵁骞惰繘琛岄獙璇
	 * @param array $data:鎵嬪姩璁剧疆鏁版嵁
	 * @param string $method:瑕佹搷浣滅殑鏂规硶
	 * @param string $type:鑾峰彇鏁版嵁鐨勬柟寮
	 * @return boolean
	 */
	public function create($method='add',$data='',$type='get'){
		//	global $_G;
		$this->autoMap($method,$data,$type)->autoFilled($method);
		return $this->autoValidate($method);
	}
	/**
	 * 鍒涘缓瀵硅薄($data),绠€鍗曠殑鑷?姩瀛楁?鏄犲皠
	 * @access private
	 * @param array $data:鎵嬪姩璁剧疆鏁版嵁
	 * @param string $method:鍒涘缓鏂规硶
	 * @param string $type:鑾峰彇鏁版嵁鏂瑰紡
	 * @return Model
	 */
	private function autoMap($method='add',$data='',$type='get'){
		$this->data=array();
		if(!empty($this->_map)&&is_array($this->_map)){//绠€鍗曞瓧娈垫槧灏勫姛鑳?_map['username(鏁版嵁搴撳瓧娈?']=array('name琛ㄥ崟瀛楁?鍚?,'add');
			foreach ($this->_map as $key => $val){
				$methods=explode('|', $val[1]);
				if(!is_array($methods)||!in_array($method, $methods)){//涓嶅湪瑕佹槧灏勬柟娉曞唴
					continue;
				}
				$value='';
				if(strtolower($type)=='gpc'){
					$value=getgpc($val[0]);//鍖呭惈cookie鐨勫€
				}
				if(strtolower($type)=='get'){
					$value=$_GET[$val[0]];
				}
				if(strtolower($type)=='post'){
					$value=$_POST[$val[0]];
				}
				if(strtolower($type)=='request'){
					$value=$_REQUEST[$val[0]];
				}
				$this->data[$key]=$value;
			}
		}else{
			foreach ($this->fields as $key => $val){
				$value='';
				if(strtolower($type)=='gpc'){
					$value=getgpc(strtolower($val['Field']));
				}
				if(strtolower($type)=='get'){
					$value=$_GET[strtolower($val['Field'])];
				}
				if(strtolower($type)=='post'){
					$value=$_POST[strtolower($val['Field'])];
				}
				if(strtolower($type)=='request'){
					$value=$_REQUEST[strtolower($val['Field'])];
				}
				if(!empty($value)&&!is_array($value)){
					$this->data[$val['Field']]=$value;
				}
			}
		}
		if(!empty($data)&&is_array($data)){
			$this->data=array_merge($this->data,$data);
		}
		unset($this->data['id']);//create鏁版嵁閬垮厤涓㈠け涓婚敭
		return $this;
	}
	/**
	 * 鑷?姩楠岃瘉鏀?寔,鍙?敮鎸佹?鍒欒〃杈惧紡楠岃瘉,鍞?竴楠岃瘉鍜岃嚜瀹氫箟鍑芥暟
	 * @access private
	 * @param $method:瑕侀獙璇佺殑鏂规硶
	 * @return boolean
	 */
	private function autoValidate($method='add'){
		if(!empty($this->_validate)&&is_array($this->_validate)){//鑷?姩楠岃瘉array('0'=>array('name','require','鐢ㄦ埛鍚嶅繀椤?,'add|save','function');
			foreach ($this->_validate as $key => $val){
				$methods=array();
				$methods=explode('|', $val[3]);
				if(!in_array($method, $methods)){continue;}
				if(strtolower($val[4])=='function'){//鑷?畾涔夋柟娉曢獙璇
					if(!$this->$val[1]()){
						$this->error=$val[2];//璁剧疆閿欒?淇℃伅
						$this->errorField=$val[0];
						return false;
					}
					continue;
				}
				if(strtolower($val[1])=='unique'){//瀛楁?鍞?竴鍊煎垽鏂
					if($this->find(array($this->pk=>$this->data[$val[0]]))){
						$this->error=$val[2];//璁剧疆閿欒?淇℃伅
						$this->errorField=$val[0];
						return false;
					}
					continue;
				}
				if(!$this->regex($this->data[$val[0]], $val[1])){//榛樿?姝ｅ垯琛ㄨ揪寮忔柟寮忛獙璇
					$this->error=$val[2];//璁剧疆閿欒?淇℃伅
					$this->errorField=$val[0];
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * 鑷?姩濉?厖鏀?寔鍙?敮鎸乢timestamp,_ip,_md5鍜岃嚜瀹氫箟鍑芥暟濉?厖
	 * @access private
	 * @param $method :瑕佸～鍏呮柟娉
	 * @return Model
	 */
	private function autoFilled($method='add'){
		if(!empty($this->_filled)&&is_array($this->_filled)){//绠€鍗曡嚜鍔ㄥ～鍏呭姛鑳?_auto['create_time']=array('_timestamp','add','function');
			foreach ($this->_filled as $key => $val){
				$methods=explode('|', $val[1]);
				if(!in_array($method, $methods)){continue;}
				if(!is_array($val)){continue;}
				$value=$val[0];
				if(strtolower($val[2])=='function'){//鑷?畾涔夋柟娉曞～鍏呭€?鍏朵粬榛樿?鐢ㄦ彁渚涚殑鍑犻」濉?厖
					$this->data[$key]=$this->$val[0];
					continue;
				}
				if(strtolower($val[0])=='_timestamp'){
					$value=mktime();
				}
				if(strtolower($val[0])=='_ip'){
					if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
						$onlineip = getenv('HTTP_CLIENT_IP');
					} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
						$onlineip = getenv('HTTP_X_FORWARDED_FOR');
					} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
						$onlineip = getenv('REMOTE_ADDR');
					} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
						$onlineip = $_SERVER['REMOTE_ADDR'];
					}
					$value=$onlineip;
				}
				if(strtolower($val[0])=='_md5'&&!empty($this->data[$key])){
					$value=md5($this->data[$key]);
				}
				$this->data[$key]=$value;
			}
		}
		return $this;
	}
	/**
	 * 缁熻?鏉℃暟
	 * @access public
	 * @param string $field :闇€瑕佷綔涓虹粺璁＄殑瀛楁?;
	 * @return integer
	 */
	public function count($field){
		if(!empty($field)){
			$this->field($field);
		}
		$sql=' select '.$this->distinct.' '.' count('.$this->parseField().') count from %t '.$this->parseTables().$this->parseJoin().' where 1 ';
		$sql.=$this->parseWhere($data).$this->parseWhere($this->where).' ';
		$sql.=$this->parseGroup().' '.$this->parseHaving().' '.$this->parseOrder().' ';
		$sql.=$this->parseLimit().' '.$this->parseUnion();
		$options=$this->options;
		$this->resetOption();
		$count=DB::fetch_first($sql, $options);
		return $count['count'];
	}

	/**
	 * 姹傚拰
	 * @access public
	 * @param string $field :闇€瑕佷綔涓烘眰鍜岀殑瀛楁?;
	 * @return integer
	 */
	public function sum($field){
		if(!empty($field)){
			$this->field($field);
		}
		$sum=$this->parseSumField();

		$sql=' select '.$this->distinct.$sum.'  from %t '.$this->parseTables().$this->parseJoin().' where 1 ';
		$sql.=$this->parseWhere($data).$this->parseWhere($this->where).' ';
		$sql.=$this->parseGroup().' '.$this->parseHaving().' '.$this->parseOrder().' ';
		$sql.=$this->parseLimit().' '.$this->parseUnion();
		$options=$this->options;
		$this->resetOption();
		$sum=DB::fetch_first($sql, $options);

		return $sum;
	}

	protected function parseOptions(){

	}

	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseField(){
		$ufield='*';
		if(!empty($this->field))$ufield=$this->field;
		$this->field='';
		return $ufield;
	}
	/**
	 * 杞?崲姹傚拰鐩稿簲鐨剆ql鍙傛暟
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	private function parseSumField(){
		$sumFields=explode(',', $this->field);
		if(is_array($sumFields)){
			$sum=' ';
			$count=count($sumFields);
			$index=0;
			foreach ($sumFields as $val){
				$sum.='sum('.$val.') '.$val;
				if($index<$count-1){
					$sum.=' , ';
				}
				$index++;
			}
			return $sum;
		}else{
			$sum='sum('.$this->field.') '.$this->field;
			return $sum;
		}
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseTables(){
		$utables=' ';
		if(!empty($this->tables))$utables=','.$this->tables;
		$this->tables='';
		if(!empty($this->tablesParams)&&is_string($this->tablesParams)){
			$this->options[]=$this->tablesParams;
		}
		if(!empty($this->tablesParams)&&is_array($this->tablesParams)&&$this->arrayLevel($this->tablesParams)==1){
			foreach ($this->tablesParams as $val){
				$this->options[]=$val;
			}
		}
		return $utables;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseJoin(){
		$ujoin='';
		if(!empty($this->join))$ujoin=$this->join;
		$this->join='';
		if(!empty($this->joinParams)&&is_string($this->joinParams)){
			$this->options[]=$this->joinParams;
		}
		if(!empty($this->joinParams)&&is_array($this->joinParams)&&$this->arrayLevel($this->joinParams)==1){
			foreach ($this->joinParams as $val){
				$this->options[]=$val;
			}
		}
		return $ujoin;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseWhere($where=''){
		$uwhere='';
		$map=array(//鎺ㄨ崘鐢╡q绛
		'eq'=>'=',
		'neq'=>'!=',
		'gt'=>'>',
		'lt'=>'<',
		'egt'=>'>=',
		'elt'=>'<=',
		'<'=>'<',
		'>'=>'>',
		'>='=>'>=',
		'<='=>'<=',
		'='=>'=',
		'!='=>'!=',
		);
		$smap=array(
		'like'=>'like',
		'between'=>'between',
		'not between'=>'not between',
		'in'=>'=',
		'not in'=>'not in',
		'_exp'=>'_exp',//鑷?畾涔夊瓧绗︿覆鏉′欢鏀?寔
		);

		if(empty($where)){
			return $uwhere;
		}
		if(!empty($where)&&is_string($where)){
			$uwhere.=' and ( %i )';
			$this->options[]=$where;
		}

		if(is_array($where)){
			foreach ($where as $key=>$val){
				if(isset($val)&&!is_array($val)){
					$uwhere.=" and ( $key = ".$this->formatColumn($key).' )';
					$this->options[]=$val;
				}
				if(is_array($val)){
					$level=0;
					$level=$this->arrayLevel($val);
					if($level==1){
						foreach ($val as $kk=>$vv){
							if($kk=='_exp'){
								$uwhere.=' and '.$vv;
							}else{
								$uwhere.=" and ( $kk = ".$this->formatColumn($kk).' )';
								$this->options[]=$vv;
							}

						}
					}
					if($level==2){//涓€涓?瓧娈靛?涓?潯浠?瀵筼r鏀?寔array('name'=>array('like','%test%','or'))
						foreach ($val as $k => $v) {
							$op=strtolower($v[2])=='or'?' or ':' and ';
							if(!empty($map[strtolower($v[0])])){
								$uwhere.=$op.'( '.$k.' '.$map[strtolower($v[0])].' '.$this->formatColumn($k).' )';
								$this->options[]=$v[1];
							}
							if(!empty($smap[strtolower($v[0])])){
								if(strtolower($v[0])=='_exp'){
									$uwhere.=$op.$v[1];
								}else{
									$uwhere.=$op.'( '.$k.' '.$smap[strtolower($v[0])].' %s )';
									$this->options[]=$v[1];
								}
							}
						}
					}
				}
			}
		}
		return $uwhere;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseLimit(){
		$limit=' ';
		if(!empty($this->limit)&&count($this->limit)==2){
			$limit.=' limit %d , %d ';
			if(isset($this->limit['start'])&&$this->regex($this->limit['start'], 'number')&&!empty($this->limit['limit'])&&$this->regex($this->limit['limit'], 'number')){
				$this->options[]=$this->limit['start'];
				$this->options[]=$this->limit['limit'];
			}
			if(isset($this->limit[0])&&$this->regex($this->limit['0'], 'number')&&!empty($this->limit[1])&&$this->regex($this->limit['1'], 'number')){
				$this->options[]=$this->limit[0];
				$this->options[]=$this->limit[1];

			}
		}
		$this->limit=null;
		return $limit;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseOrder(){
		$order=' ';

		if(!empty($this->order)&&is_array($this->order)&&$this->arrayLevel($this->order)==1){
			$order.=' order by ';
			$count=count($this->order);
			$index=0;
			foreach ($this->order as $key => $val){
				$val=strtolower($val);
				$val=$val=='asc'?' asc ':' desc ';
				$order.=$key .' '.$val;
				$index++;
				if($index!=$count){
					$order.=',';
				}
			}
		}
		if(!empty($this->order)&&is_string($this->order)){
			$order.=' order by '.$this->order;
			$this->order=$order;
		}
		$this->order=null;
		return $order;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseGroup(){
		$group=' ';
		if(!empty($this->group)){
			$group=' group by '.$this->group;
		}
		$this->group=null;
		return $group;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseHaving(){
		$having=' ';
		$having=$this->having;
		$this->having=null;
		return $having;
	}
	/**
	 * 杞?崲鐩稿簲鐨勫弬鏁
	 * @access public
	 * @return string 鐩稿簲鐨剆ql
	 */
	protected function parseUnion(){
		$union=' ';
		$union=$this->union;
		$this->union=null;
		return $having;
	}

	/**
	 * 鏌ヨ?瀵瑰?琛ㄧ殑鏀?寔eg:$model->tables(',%t,%t',array('member','bag'))->where($where)->select()
	 * @access public
	 * @param string|array $order
	 * @param string|array $params//(涓嶆帹鑽?鐗瑰埆娉ㄦ剰褰撹?鐢ㄨ繖涓?弬鏁扮殑鏃跺€欏墠闈㈢敤%t瑕佹斁鍦ㄦ墍鏈夎繛璐?柟娉曞墠闈
	 * @return Model
	 */
	public function tables($tables,$params=''){
		if(!empty($tables)&&is_array($tables)){
			$this->tables=implode(',', $tables);
		}
		if(!empty($tables)&&is_string($tables)){
			$this->tables=$tables;
		}
		$this->tablesParams=$params;

		return $this;
	}

	/**
	 * 鏌ヨ?|鏇存柊|鍒犻櫎鏉′欢璁剧疆
	 * Enter description here ...
	 * @param string|array $where:鏉′欢(瀛楃?涓瞸涓€缁存暟缁剕浜岀淮鏁扮粍|涓夌淮鏁扮粍)
	 * @return Model
	 */
	public function where($where=''){
		if(!empty($where)&&is_array($where)){
			$this->where=$where;
		}
		if(!empty($where)&&is_string($where)){
			$this->where=$where;
		}
		return $this;
	}
	/**
	 * 琛ㄨ繛鎺ユ搷浣?闇€瑕佽嚜宸卞啓LEFT JOIN杩樻槸RIGHT浠ュ強ON鏉′欢)鏍煎紡left join xxx琛ㄥ叏鍚 on 鏉′欢
	 * 褰撴湁璁剧疆琛ㄧ殑鍒?悕鏃舵敞鎰廸ield鐨勮?缃?? c left join aaa a on c.id=a.cid涓璮ield璁剧疆c.*,a.img绛
	 * @param string $join :杩炴帴璇?彞
	 * @param array|string $params :鍙傛暟姣斿?table//鐗瑰埆娉ㄦ剰浣跨敤姝ゅ弬鏁扮殑鏃跺€檍oin鏂规硶瑕佸湪tables()鏂规硶涔嬪悗鍦ㄥ叾浠栨柟娉曚箣鍓
	 * @return Model
	 */
	public function join($join,$params=''){
		if(!empty($this->join)&&is_string($join)){
			$this->join.=$join;
			return $this;
		}
		$this->joinParams=$params;
		$this->join=$join;
		return $this;
	}
	/**
	 * 璁剧疆limit鍙傛暟
	 * @access public
	 * @param string|array $limit锛氳?缃?潯鏁伴檺鍒跺弬鏁板?锛?,5鎴栬€?limit=array(1,5);
	 * @return Model
	 */
	public function limit($limit){
		if(!empty($limit)&&is_array($limit)){
			$this->limit=$limit;
		}
		if(!empty($limit)&&is_string($limit)){

			$this->limit=explode(',',$limit);
		}
		return $this;
	}
	/**
	 * 鏌ヨ?瀵筼rder鐨勬敮鎸
	 * @access public
	 * @param string|array $order
	 * @return Model
	 */
	public function order($order){
		if(!empty($order)&&is_array($order)){
			$this->order=$order;
		}
		if(!empty($order)&&is_string($order)){
			$this->order=$order;
		}
		return $this;
	}
	/**
	 * 璁剧疆鏌ヨ?|缁熻?鐨勫瓧娈靛悕
	 * @access public
	 * @param string|array $data
	 * @return Model
	 */
	public function field($field){
		if(!empty($field)&&is_array($field)){
			$this->field=implode(',',$field);
		}
		if(!empty($field)&&is_string($field)){
			$this->field=$field;
		}
		return $this;
	}
	/**
	 * 璁剧疆鏇存柊鎴栨彃鍏ョ殑鏁版嵁
	 * @access public
	 * @param array $data
	 * @return Model
	 */
	public function data($data){
		if(!empty($data)&&is_array($data)){
			$this->data=$data;
		}
		return $this;
	}
	/**
	 * 鏌ヨ?瀵筪istinct鐨勬敮鎸
	 * @access public
	 * @param boolean $distinct
	 * @return Model
	 */
	public function distinct($distinct){
		if($distinct==true){
			$this->distinct='distinct';
		}
		return $this;
	}
	/**
	 * 鏌ヨ?瀵筭roup鐨勬敮鎸
	 * @access public
	 * @param string $group
	 * @return Model
	 */
	public function group($group){
		if(!empty($group)&&is_array($group)){
			$this->group=implode(',',$group);
		}
		if(!empty($group)&&is_string($group)){
			$this->group=$group;
		}
		return $this;
	}
	/**
	 * 鏌ヨ?瀵筯aving鐨勬敮鎸
	 * @access public
	 * @param string $having
	 * @return Model
	 */
	public function having($having){
		if(!empty($having)&&is_string($having)){
			$this->having=$having;
		}
		return $this;
	}
	/**
	 * 鏌ヨ?瀵箄nion鐨勬敮鎸
	 * @access public
	 * @param string $union
	 * @return Model
	 */
	public function union($union){
		if(!empty($union)&&is_string($union)){
			$this->union=$union;
		}
		return $this;
	}

	/**
	 * create瀵硅嚜鍔ㄥ～鍏呭瓧娈垫敮鎸
	 * @access public
	 * @param array $auto
	 * @return Model
	 */
	public function filled($_filled){
		if(!empty($_filled)&&is_array($_filled)){
			$this->_filled=$_filled;
		}
		return $this;
	}

	/**
	 * create瀵硅嚜鍔ㄩ獙璇佹敮鎸
	 * @access public
	 * @param array $_validate
	 * @return Model
	 */
	public function validate($_validate){
		if(!empty($_validate)&&is_array($_validate)){
			$this->_validate=$_validate;
		}
		return $this;
	}

	/**
	 * create瀵硅嚜鍔ㄦ槧灏勫瓧娈垫敮鎸
	 * @access public
	 * @param array $_map
	 * @return Model
	 */
	public function map($_map){
		if(!empty($_map)&&is_array($_map)){
			$this->_map=$_map;
		}
		return $this;
	}

	/**
	 * 璁剧疆妯″瀷鐨勫睘鎬у€
	 * @access public
	 * @param string $name 鍚嶇О
	 * @param mixed $value 鍊
	 * @return Model
	 */
	public function setProperty($name,$value) {
		if(property_exists($this,$name))
		$this->$name = $value;
		return $this;
	}
	/**
	 * 鑾峰彇涓婚敭鍚嶇О
	 * @access public
	 * @return string:涓婚敭鍚
	 */
	public function getPk() {
		return $this->pk;
	}
	/**
	 * 鑾峰彇琛ㄥ悕绉
	 * @access public
	 * @return string:琛ㄥ悕
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * 浣跨敤姝ｅ垯楠岃瘉鏁版嵁
	 * @access public
	 * @param string $value  瑕侀獙璇佺殑鏁版嵁
	 * @param string $rule 楠岃瘉瑙勫垯
	 * @return boolean
	 */
	public function regex($value,$rule) {
		$validate = array(
            'require'   =>  '/.+/',
            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency'  =>  '/^\d+(\.\d+)?$/',
            'number'    =>  '/^\d+$/',
            'zip'       =>  '/^\d{6}$/',
            'integer'   =>  '/^[-\+]?\d+$/',
            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
            'english'   =>  '/^[A-Za-z]+$/',
		);
		// 妫€鏌ユ槸鍚︽湁鍐呯疆鐨勬?鍒欒〃杈惧紡
		if(isset($validate[strtolower($rule)]))
		$rule       =   $validate[strtolower($rule)];
		return preg_match($rule,$value)===1;
	}
	/**
	 * 璁剧疆鏁版嵁瀵硅薄鐨勫€
	 * @access public
	 * @param string $name 鍚嶇О
	 * @param mixed $value 鍊
	 * @return void
	 */
	public function __set($name,$value) {
		// 璁剧疆鏁版嵁瀵硅薄灞炴€
		$this->$name  =   $value;
	}

	/**
	 * 鑾峰彇鏁版嵁瀵硅薄鐨勫€
	 * @access public
	 * @param string $name 鍚嶇О
	 * @return property
	 */
	public function __get($name) {
		return isset($this->$name)?$this->$name:null;
	}
	/**
	 * 鍒ゆ柇鏁扮粍缁存暟
	 * @access protected
	 * @param array $arr:闇€瑕佸垽鏂?殑鏁扮粍
	 * @return integer :缁存暟
	 */
	protected  function arrayLevel($arr){
		$al = array(0);
		$this->aL($arr,$al);
		return max($al);
	}
	/**
	 * 閫掑綊璁＄畻鏁扮粍缁存暟
	 * @access private
	 * @param array $arr
	 * @param array $al
	 * @param integer $level
	 * @return void
	 */
	private function aL($arr,&$al,$level=0){
		if(is_array($arr)){
			$level++;
			$al[] = $level;
			foreach($arr as $v){
				$this->aL($v,$al,$level);
			}
		}
	}
	/**
	 * 璁剧疆琛ㄥ悕
	 * @access public
	 * @param string $tableName:琛ㄥ悕
	 * @return void
	 */
	public function setTableName($tableName=''){
		if(!empty($tableName)){
			$this->tableName=$tableName;
			$this->fields=$this->getFields();
		}
	}

	/**
	 * 鑾峰彇琛ㄥ瓧娈典俊鎭
	 * @access public
	 * @param string $tableName
	 * @return array
	 */
	public function getFields($tableName=''){

		$tableName=!empty($tableName)?$tableName:$this->tableName;
		$sql="SHOW COLUMNS FROM ".DB::table($tableName)." ;";
		if(!empty($tableName)&&$tableName!=$this->tableName){
			return DB::fetch_all($sql);
		}
		if(!empty($this->fields))return $this->fields;
		return DB::fetch_all($sql);
	}
	/**
	 * 瀵筪iscuz %s,%d,%i鏀?寔,浠呭?涓昏〃瀛楁?鏀?寔
	 * @access pretected
	 * @param string $columnName:鍒楀悕|瀛楁?鍚
	 * @return string :鏍煎紡鍖栫?鍙
	 */
	protected function formatColumn($columnName){
		$format['%d']='/.*int.*/';
		$format['%s']='/varchar.*|text|longtext/';
		if(!is_array($this->fields))return false;
		foreach ($this->fields as $key => $val) {
			if(preg_match('/.*\.?'.strtolower($val['Field']).'$/',strtolower($columnName))){
				foreach ($format as $fkey=>$fval) {
					if(preg_match($fval,strtolower($val['Type'])))return $fkey;
				}
			}
		}
		return '%i';
	}

	/**
	 * 閲嶇疆鏉′欢鏁版嵁鍜屽弬鏁
	 * @access protected
	 * @return void
	 */
	protected function resetOption(){
		$this->join=null;
		$this->where=null;
		$this->group=null;
		$this->having=null;
		$this->order=null;
		$this->limit=null;
		$this->union=null;
		$this->data=null;
		$this->distinct='';
		$this->field='';
		$this->tables='';
		$this->tablesParams=null;
		$this->joinParams=null;
		$this->error='';
		$this->errorField='';
		$this->options=array();
		$this->options[]=$this->tableName;
	}
}