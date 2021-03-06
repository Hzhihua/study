<?php

namespace app\fulltextsearch\model;

use think\Db;

class Tp5search extends Fulltextsearch {
	
	/**
	 * 搜索关键字分词
	 * @param  string $q 搜索字符串
	 * @return string 
	 */
	protected function wordSegmentation($q) {
		return $q;
		// return '空调 电视';
	}

	/**
	 * 从数据库中查询数据
	 * @param  string $keywords 搜索字符串
	 * @param  int $limit 每张表应查询出的数据行数
	 * @return array 数据库中查询的数据
	 */
	protected function DBSearch($keywords, $limit) {
		$DBdata = array();

		// 一张表格对应 $DBdata 数组的一个键值
		$DBdata[] = Db::query("select wuliaomiaoshu,tiaoma from wuliu_tiaoxingma where `tiaoma` LIKE '%$keywords%' order by id desc limit {$limit} ");
		// $DBdata[] = $model->query("select wuliaomiaoshu,tiaoma from __PREFIX__tiaoxingma where `tiaoma` LIKE '%$keywords%' order by id desc limit {$limit} ");
		// $DBdata[] = $model->query("select wuliaomiaoshu,tiaoma from __PREFIX__nulldata where `tiaoma` LIKE '%$keywords%' order by id desc limit {$limit} ");

		return $this->array_merge($DBdata);
	}
}
