<?php

namespace Fulltextsearch\Controller;

class FulltextsearchController extends \Think\Controller {
	/**
	 * 显示搜索页面
	 * @return [type] [description]
	 */
	public function index(){
		$this->display();
	}	

	/**
	 * URL：&q=搜索关键字&page=1&rows=10
	 * @param  string $q 搜索关键字
	 * @return array     搜索分页后的数据
	 */
	public function search($q) {
		$model = new \Fulltextsearch\Model\Tp32searchModel();
		$model->highlightStyle = 'style="color:blue"'; // 关键字高亮样式
		$model->page = 1;  // 默认从第一页开始
		$model->rows = 10; // 默认每页显示10行数据
		$model->expireForCache = 300; // 缓存数据有效时间（S）
		$model->cacheFileDir = './Application/Runtime/Cache/Fulltextsearch/'; // 缓存文件目录路径
		$model->wordDelimiter = ' ';  // 关键字分割附（eg:康佳 冰箱 康佳灰）

		$searchData = $model->search($q);

		$this->assign('data', $searchData);
		$this->display();
	}
}