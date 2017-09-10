<?php

namespace frontend\models;

use yii\db\Query;

class Yii2search extends Fulltextsearch {
	
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
		$query = new Query();
		$DBdata[] = $query->select('wuliaomiaoshu,tiaoma')
						  ->from('wuliu_tiaoxingma')
						  ->where(['like', '`tiaoma`', $keywords])
						  ->limit($limit)
						  ->all();

		return $this->array_merge($DBdata);
	}
}
