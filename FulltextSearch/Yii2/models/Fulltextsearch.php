<?php

namespace frontend\models;

/**
 * ================================================================================
 * ==== 联表全站全文搜索
 * ==== 基本思路：将多张表合并成一张表，再从一张表中按关键字的出现次数进行倒叙排序
 * ==== autor：Hzhihua
 * ==== date：2017年6月21日
 * ================================================================================
 * ==== 需要修改/重写wordSegmentation($q)与DBSearch($keywords, $num)函数
 * ==== 函数进行关键字分词
 * 		wordSegmentation($q) {
 *   		return $q;
 *      }
 * ==== 函数从数据库中查询关键字数据
 * 		DBSearch($keywords, $num){
 * 		
 *   		return $this->array_merge($DBdata);
 *      } 
 * ================================================================================
 * ==== eg:
 * 		URL：&q=搜索关键字&page=1&rows=10
 		$model = new \app\fulltextsearch\model\Tp5search();
		$model->highlightStyle = 'style="color:yellow"'; // 关键字高亮样式
		$model->page = 1;  // 默认从第一页开始
		$model->rows = 10; // 默认每页显示10行数据
		$model->expireForCache = 300; // 缓存文件有效时间（S）
		$model->cacheFileDir = './Application/Runtime/Cache/Fulltextsearch/'; // 缓存文件目录路径
		$model->wordDelimiter = ' ';  // 关键字分割附（eg:康佳 冰箱 康佳灰）
		
		$searchData = $model->search($q);
		var_dump($searchData);
 */

abstract class fulltextsearch {
	/**
	 * 每张表应查询的数据行数 * 10页
	 * 初始化自动计算
	 * @var integer
	 */
	protected $perTableNumber = 100;

	/**
	 * 匹配到关键字高亮样式
	 * @var string
	 */
	public $highlightStyle = 'style="color:red"';

	/**
	 * 关键字分割附（eg:康佳 冰箱 康佳灰）
	 * @var string
	 */
	public $wordDelimiter = ' ';

	/**
	 * 分页显示多少条数据
	 * @var integer
	 */
	public $rows = 10;
	
	/**
	 * 默认从第几页开始
	 * @var integer
	 */
	public $page = 1;

	/**
	 * 缓存文件路径
	 * 缓存文件的名称与搜索的内容有关 (md5($q))
	 * @var string
	 */
	protected $cacheFile = '';

	/**
	 * 缓存文件目录路径
	 * @var string
	 */
	public $cacheFileDir = '';

	/**
	 * 缓存数据有效时间（s）
	 * @var integer
	 */
	public $expireForCache = 300;

	/**
	 * 初始化变量
	 */
	protected function init() {
		// 从默认的page和rows值计算
		$this->perTableNumber = $this->page * 10 * $this->rows;

		$this->rows = $_GET['rows'] ? (int)$_GET['rows'] : $this->rows;
		$this->page = $_GET['page'] ? (int)$_GET['page'] : $this->page;

		$this->cacheFileDir = $this->cacheFileDir ? $this->cacheFileDir : './Application/Runtime/Cache/Fulltextsearch/';

	}

	/**
	 * 搜索关键字分词
	 * @param  string $q 搜索字符串
	 * @return string 
	 */
	protected function wordSegmentation($q) {
		// return '康佳 冰箱 康佳灰';
		// return '美的';
		// return $q;
	}

	/**
	 * 从数据库中查询数据
	 * @param  string $keywords 搜索字符串
	 * @param  int $limit 每张表应查询出的数据行数
	 * @return array 数据库中查询的数据
	 */
	protected function DBSearch($keywords, $limit) {
		// $DBdata = array();
		// $model = new \Think\Model();

		// 一张表格对应 $DBdata 数组的一个键值
		// $DBdata[] = $model->query("select * from __PREFIX__tiaoxingma where `tiaoma` LIKE '%$keywords%' order by id desc limit {$limit} ");
		// $DBdata[] = $model->query("select * from __PREFIX__nulldata where `tiaoma` LIKE '%$keywords%' order by id desc limit {$limit} ");

		// return $this->array_merge($DBdata);
	}

	/**
	 * 对结果进行数组拼接
	 * 并且统计所使用的数据库表的数量
	 * @param  array  $data 搜索结果
	 * @return array        合并后的数组
	 */
	protected function array_merge(array $data) {
		$result = array();

		if (1 == count($data))	return $data[0];

		foreach ($data as $v) {
			$result = array_merge($result, $v);
		}

		return $result;
	}

	/**
	 * 查询关键字出现的频率
	 * @param  array $keywords  搜索关键字
	 * @param  string $data 表格单行数据
	 * @return array       array(频率,单行表数据)
	 */
	protected function keyFrequence($keywords, array $data) {
		$countAll = 0;  // 关键字查询  数字越大  关键字越多
		$keyword = explode($this->wordDelimiter, $keywords);  // 将字符串关键字转为数组

		foreach ($keyword as $v) {
			$data = str_ireplace($v, '<font '.$this->highlightStyle.' >'.$v.'</font>', $data, $count);
			$countAll += $count;
		}

		return ($countAll ? array($countAll, $data) : false);
	}

	/**
	 * 对搜索结果进行遍历查询关键字
	 * @param  string $keywords  搜索关键字
	 * @param  array  $data     搜索数据
	 * @return array           按相关性排序后的数据
	 */
	protected function handleFrequence($keywords, array $data) {
		$result = array();

		// 对搜索结果进行相关性处理
		foreach ($data as $k=>$v) {
			$frequence = $this->keyFrequence($keywords, $v);
			// 加 $k 是处理数组key名重复覆盖问题
			// $frequence[0]  关键字出现的次数
			// $frequence[1]  进行关键字高亮处理的数据
			$frequence && $result[$frequence[0].'-'.$k] = $frequence[1];			 
		}

		// 根据关键字出现的次数降序排序
		krsort($result);

		return array_slice($result, 0, $this->perTableNumber);
	}

	/**
	 * 创建并保存数据至缓存中
	 * 默认为文件缓存   也可重写为其他缓存
	 * @param  array  $data      数据
	 * @param  string $cacheFile 缓存文件路径
	 * @return bool              true/falce
	 */
	protected function saveCache(array $data, $cacheFile) {
		$dir = dirname($cacheFile);

		if(!is_dir($dir)){
			mkdir($dir, 0755, true);
		}

		return ($data ? file_put_contents($cacheFile, serialize($data)) : false);
	}

	/**
	 * 从数据库中查询数据
	 * @param  string $keywords 搜索关键字
	 * @param  int $limit 每张表应查询出的数据行数
	 * @param  string $cacheFile 缓存文件路径
	 * @return array 数据库中查询的数据
	 */
	protected function fromDB($keywords, $limit, $cacheFile) {

		$data = $this->DBSearch($keywords, $limit);

		$data = $this->handleFrequence($keywords, $data);
		
		$this->saveCache($data, $cacheFile);		

		return $data;
	}

	/**
	 * 从缓存中获取数据
	 * 缓存有效时间为  $this->expireForCache  
	 * @param  string $keywords 搜索关键字
	 * @return  false/array
	 */
	protected function fromCache($cacheFile) {

		if ( 
			is_file($cacheFile) &&
			$_SERVER['REQUEST_TIME'] - filemtime($cacheFile) < $this->expireForCache
		) {
			return unserialize(file_get_contents($cacheFile));
		} else {
			@unlink($cacheFile);
			return false;
		}
	}

	/**
	 * 对搜索结果进行数据分页
	 * @param  array  $data 分页的数据
	 * @param  int    $page 分页的页数
	 * @param  int    $rows 分页的行数
	 * @return mix    false/array
	 */
	protected function searchPage(array $data, $page=0, $rows=0) {
		
		$page = $page ? $page : $this->page;
		$rows = $rows ? $rows : $this->rows;

		$offset = ($page-1) * $rows;

		return array_slice($data, $offset, $rows);
	}

	/**
	 * 获取分页后的数据
	 * 
	 * step 1 如果缓存数据不存在   --->   直接去数据库中查询    --->  分页处理
	 * step 2 如果缓存数据存在     --->   直接去缓存数据中查询  --->  分页处理
	 * 		  若缓存数据分页失败   --->   直接去数据库中查询    --->  分页处理
	 * 
	 * @param  string $keywords 搜索关键字
	 * @return array 单张表数据
	 */
	protected function DBdata($keywords) {
		$cacheFile = $this->cacheFile;

		if ( $data = $this->fromCache($cacheFile) ) {

			if ( $data = $this->searchPage($data) ) {
				return $data;
			
			} else {

				// 当缓存数据不够分页时，再查询多5页数据
				$data = $this->fromDB($keywords, '0,'.($this->perTableNumber+($this->page+5)*$this->rows), $cacheFile);
				return $this->searchPage($data);
			}

		} else {
			$data = $this->fromDB($keywords, '0,'.$this->perTableNumber, $cacheFile);

			return $this->searchPage($data);
		}
	}	

	/**
	 * @param  string $q 搜索字符串
	 * @return array
	 */
	public function search($q) {
		// 初始化变量
		$this->init();

		// 缓存文件的名称与搜索的内容有关
		$this->cacheFile = $this->cacheFileDir . hash('md5', $q);

		// 搜索关键字分词
		$keywords = $this->wordSegmentation($q);
		
		// 获取已经分页处理的数据	
		return $this->DBdata($keywords);
	}
}