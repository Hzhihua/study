<?php

$func = '';

if(!function_exists('CC_fpage')){
	$func .= '
/**
 * 分页函数（自动生成）
 * @param int $total 查询数据的总数
 * @param int $listRows 每页显示的行数
 * @param string $pa URL附加参数
 * @return array 分页导航栏以及limit
 */
function CC_fpage($total, $listRows=10, $pa=""){
	import("Component.Page");
	$page = new Page($total, $listRows, $pa);
	
	$limit = $page->limit;
	$fpage = $page->fpage();

	return array(
		\'limit\' => $limit,
		\'fpage\' => $fpage,
	);
}
	';
}

if(!function_exists('CC_sessionFilesEmpty')){
	$func .= '
/**
 * 清空	html5 ajax切割文件上次保存的文件数据（自动生成）
 * 用于上传文件后重新刷新页面时 清空之前上传的文件数据
 * session(\'files\', array());  //清空之前上传的文件数据
 *
 */
function CC_sessionFilesEmpty(){
	$filesArr = session(\'files\');
	$rootDir = C(\'FILESAVEPATH\');
	if($filesArr){
		foreach($filesArr as $k=>$v){
			@unlink($rootDir . $v);
			
			$dir = date(\'Ymd\', $k) . \'/\';
			if(count(scandir($rootDir . $dir))==2){//目录为空,=2是因为.和..存在
				@rmdir($rootDir . $dir);// 删除空目录 
			} 
		}
		session(\'files\', array());
	}
}

	';
}

return $func;