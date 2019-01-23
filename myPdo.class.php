<?php
/**
 * @author <[<xxxx@qq.com>]>
 * pdo数据库操作类
 */
class myPdo
{
	protected $table_name; //数据表名字
	protected $pdo;  //pdo连接对象
	protected $where;

	public function __construct()
	{
		$this->pdo = new PDO('mysql:host=127.0.0.1;dbname=test','root','root');
		$this->pdo->query("set names utf8");

		//改变表名
		//$this->table_name = $table_name;
	}

	/**
	 * 指定表名
	 * @return [type] [description]
	 */
	public function table($table_name)
	{
		$this->table_name = $table_name;
		return $this;
	}

	/**
	 * 添加
	 * @param [type] $arr [要添加的数据]
	 */
	public function add($arr)
	{	
		$arr = $this->checkField($arr);  //自动过滤

		//构建字段
		$keysArr = array_keys($arr);
		$keysStr = implode(",",$keysArr);
		//var_dump($arr);
		//构造绑定
		$vStr = '';
		for ($i=0; $i <count($arr);$i++) { 
			$vStr .= ",:p".$i;
		}
		$vStr = substr($vStr,1);
		//构建SQL
		$sql = "INSERT INTO {$this->table_name} ($keysStr) VALUES ($vStr)";
		//预处理
		$st = $this->pdo->prepare($sql);
		//绑定
		$vArr = array_values($arr);
		//var_dump($vArr);die;
		foreach ($vArr as $key => $value) {
			$name = ':p'.$key;
			$$name = $value;
			$st->bindParam($name,$$name);
			//$st->bindValue($name,$value);
		}
		//$st->bindParam(":p2",'xxx');
		//执行预处理
		$res = $st->execute();

		//如果添加成功 返回自增id
		if($res){
			return $this->pdo->lastInsertId();
		}else{
			return false;
		}
	}


	public function addAll()
	{
		//"insert biao (xxx,xxx) VALUES (值1),(值2)"	
	}

	/**
	 * where条件拼接
	 * @param  string $condition [description]
	 * @return [type]            [description]
	 */
	public function where($condition='')
	{	
		if(is_array($condition)){
			//如果是处理 进行拼接处理
			$condStr = '1=1';
			foreach ($condition as $key => $value) {
				$condStr .= " AND $key = '$value'";
			}
			$this->where = $condStr;
			return $this;
			//where news_id = 1 AND num = 1;
		}elseif(is_string($condition)){
			$this->where = $condition;
			return $this;
		}else{
			return false;
		}

	}

	/**
	 * 查询全部数据
	 * @return [type] [description]
	 */
	public function select($field='*')
	{	
		$where = !empty($this->where) ? ' where '.$this->where : '';  

		$sql = "SELECT $field FROM {$this->table_name} $where";
		//echo $sql;die;
		$st = $this->pdo->query($sql);
		$data = $st->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}


	/**
	 * 查询单条数据
	 * @return [type] [description]
	 */
	public function find($field='*')
	{
		$where = !empty($this->where) ? ' where '.$this->where : '';  
		
		$sql = "SELECT $field FROM {$this->table_name} $where";

		echo $sql;die;
		$st = $this->pdo->query($sql);
		$data = $st->fetch(PDO::FETCH_ASSOC);
		return $data;
	}

	/**
	 * 删除
	 * @return [type] [description]
	 */
	public function delete($idArr='')
	{	
		$where = !empty($this->where) ? ' where '.$this->where : '';
		$keyName = $this->getKeyName();
		if(is_numeric($idArr)){
			$where = "where {$keyName} = $idArr";
		}elseif(is_array($idArr)){
			$idArr = implode(",",$idArr);
			$where = "where {$keyName} in($idArr)";
		}

		$sql = "DELETE FROM {$this->table_name} $where";
		//echo $sql;die;
		return $this->pdo->exec($sql);
	}

	/**
	 * 修改数据
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function save($data)
	{	
		$where = !empty($this->where) ? ' where '.$this->where : '';
		//构造修改数据
		$saveStr = '';
		foreach ($data as $key => $value) {
			$saveStr.= ",$key = '$value'";
		}
		//$str = substr($str,1);
		$saveStr = ltrim($saveStr,',');
		//echo $str;die;
		// SET news_name = '' AND num =1;
		$sql = "UPDATE {$this->table_name} SET $saveStr $where";

		return $this->pdo->exec($sql);
	}

	/**
	 * 检查键名是否包含在数据表中 
	 * @return [type] [description]
	 */
	public function checkField($arr)
	{
		//找到数据表 所有的字段名称
		$sql = "SHOW COLUMNS FROM {$this->table_name}";
		$st = $this->pdo->query($sql);
		$field_data = $st->fetchAll(PDO::FETCH_ASSOC);
		$columns = [];
		foreach ($field_data as $key => $value) {
			$columns[] = $value['Field'];
		}
		//循环$arr 一一对比
		foreach ($arr as $key => $value) {
			if(!in_array($key,$columns)){
				//不在表中 删除
				unset($arr[$key]);
			}
		}
		return $arr;
		
	}

	/**
	 * 得到当前表的主键名称
	 * @return [type] [description]
	 */
	public function getKeyName()
	{
		$sql = "SHOW COLUMNS FROM {$this->table_name}";
		$st = $this->pdo->query($sql);
		$field_data = $st->fetchAll(PDO::FETCH_ASSOC);

		$keyName = '';
		foreach ($field_data as $key => $value) {
			if($value['Key'] == 'PRI'){
				$keyName = $value['Field'];
			}
		}

		return $keyName;
	}
}
