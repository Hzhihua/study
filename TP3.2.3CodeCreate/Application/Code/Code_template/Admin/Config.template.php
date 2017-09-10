<?php if(!defined('CODE_CONFIG_PATH')) exit('非法打开文件Config.template');?>

return array(
	'show' => '<?php echo $config['ch_arr']['show']?>', //显示数据方法名称
	'add' => '<?php echo $config['ch_arr']['add']?>',  //添加数据方法名称
	'update' => '<?php echo $config['ch_arr']['update']?>',  //更改数据方法名称
	'del' => '<?php echo $config['ch_arr']['del']?>',   //删除数据方法名称
<?php
	 //--------------------------------分割线-----------------------------
	 //------------------------------以上为配置-----------------------------
	 //----------------------------以下不建议配置-----------------------------
?>
	'table_name' => '<?php echo $GLOBALS['table_name'];?>',  // 不可删除 命名空间有关

	<?php 
		//将字符串拼成数组  字段映射  art_id@id,art_content@content  array('id'=>'art_id', 'content'=>'art_content')
		$map = $_GET['map'];
		$map_array = array();
		$arr = explode($config['map_rule_arr'][0], $map);
		foreach($arr as $k=>$v){
			$tmp_array = explode($config['map_rule_arr'][1], $v);
			foreach($table_cnt as $value){
				if($tmp_array[0] == $value['field']){
					$map_array[$tmp_array[0]] = $tmp_array[1];
					break;
				}
			}
		}

		// 处理字段备注  性别(1-男,2-女)  ====>    array("1"=>"男", "2"=>"女")
		function commentToArr($comment,$comment_rule_arr){
			$str_comment = substr($comment, strpos($comment, $comment_rule_arr[0])+1);
			$str_comment = rtrim($str_comment, $comment_rule_arr[1]);
			$arr = explode($comment_rule_arr[2], $str_comment); 
			$tmp_str = 'array(';
			foreach ($arr as $value) {
				$ex_arr = explode($comment_rule_arr[3], $value);
				$tmp_str .= '"';
				$tmp_str .= $ex_arr[0];
				$tmp_str .= '"';
				$tmp_str .= "=>";
				$tmp_str .= '"';
				$tmp_str .= $ex_arr[1];
				$tmp_str .= '", ';
			}
			$tmp_str = rtrim($tmp_str, ', ');
			$tmp_str .= ')';

			return $tmp_str;
		}

		function commentToString($comment,$comment_rule_arr, $getValue=1){
			$str_comment = substr($comment, strpos($comment, $comment_rule_arr[0])+1);
			$str_comment = rtrim($str_comment, $comment_rule_arr[1]);
			$arr = explode($comment_rule_arr[2], $str_comment); 
			$tmp_str = '';
			foreach ($arr as $key=>$value) {
				if($getValue){
					$ex_arr = explode($comment_rule_arr[3], $value);
				}else{
					$ex_arr = explode($comment_rule_arr[3], $key);
				}
				$tmp_str .= $ex_arr[0];
				$tmp_str .= ',';
			}
			$tmp_str = rtrim($tmp_str, ',');

			return $tmp_str;
		}

		/**
		 * 提取字段的备注信息  性别(1-男,2-女) => 性别
		 * @param  [string] $comment          [字段内容]
		 * @param  [string] $comment_rule_arr [验证规则]
		 * @return [string]                   [提取后的内容]
		 */
		function handleComment($comment, $comment_rule_arr){
			return (($s=strpos($comment, $comment_rule_arr)) !== FALSE) ? substr($comment, 0, $s) : $comment;
		}

		/**
		 * 拼接验证规则数组
		 * @param  [string] $fieldName [字段名称]
		 * @param  [array] $str        [字符串数组内容]
		 * @return [string]            [拼接后的字符串]
		 */
		function makeValidateArr($fieldName, $arr){
			$data = '';
			foreach ($arr as $value) {
				$str = T.T.'array(';
				// 验证字段
				// 字段真实名称(映射用真实数据字段名称)
				$str .= "'{$fieldName}', ";
				$str .= $value;
				$str .= '),'.ENTER;
				$data .= $str;
			}
			
			return $data;
		}

		/**
		 * 获取字段类型的长度
		 * @param  [string] $type [字段类型]
		 * @return [number]       [字段长度]
		 */
		function getTypeLength($type){
			$start = strpos($type, '(');
			$end = strpos($type, ')');
			return substr($type, $start+1, $end-$start-1);
		}
	?>

	'pk' => '<?php 
		$pk_true = '';  // 表字段的主键名称
		foreach($table_cnt as $v){
			if(strtoupper($v['key']) == 'PRI')
				$pk_true = $v['field'];
				echo $map_array[$v['field']] ? $map_array[$v['field']] : $v['field'];
				break;
		}
	?>',  // 主键(有映射显示映射名称)
	'pk_true' => '<?php echo $pk_true;?>', // 表字段的主键名称
	
	// model模型数据
	//字段映射	
	'_map' => "<?php 
		$data = 'array(';
 		foreach($map_array as $k=>$v){
			$data .= "'";
			$data .= $v;
			$data .= "'";

			$data .= '=>';
			
			$data .= "'";
			$data .= $k;
			$data .= "', ";
 		}
 		$data = rtrim($data, ", ");
 		$data .= ')';
		echo $data;
	?>",  // 字符串数组（假数组）

	'_map_true_arr' => <?php echo $data?>,  //真实数组

	'table_all_fields' => <?php 
		$data = 'array(';
		foreach($table_cnt as $v){
			$data .= "'";
			$data .= $v['field'];
			$data .= "'";
			$data .= "=>";
			$data .= "'";
			$data .= $v['comment'];
			$data .= "', ";
		}
		$data = rtrim($data, ', ');
		$data .= ')';
		echo $data;
	?>,

	'_insertFields' => "<?php
		$data = 'array(';
 		foreach($table_cnt as $v){
 			if(
 				strtoupper($v['key']) == 'PRI' || 
 				preg_match($config['field_rule_arr']['time'], $v['field']) ||
 				preg_match($config['field_rule_arr']['file'], $v['field']) ||
 				preg_match($config['field_rule_arr']['image'], $v['field']) 
 			){
 				//主键
 				continue;
 			}else{
 				$data .= "'";
 				$data .= $map_array[$v['field']] ? $map_array[$v['field']] : $v['field'];
 				// $data .= $v['field'];
 				$data .= "', ";

 			}
 		}
 		$data = rtrim($data, ", ");
 		$data .= ')';
 		echo $data;
	?>",

	'_updateFields' => "<?php
		$data = 'array(';
 		foreach($table_cnt as $v){
 			if(
 				preg_match($config['field_rule_arr']['time'], $v['field']) ||
 				preg_match($config['field_rule_arr']['file'], $v['field']) ||
 				preg_match($config['field_rule_arr']['image'], $v['field']) 
 			){
 				//主键
 				continue;
 			}else{
 				$data .= "'";
 				$data .= $map_array[$v['field']] ? $map_array[$v['field']] : $v['field'];
 				// $data .= $v['field'];
 				$data .= "', ";

 			}
 		}
 		$data = rtrim($data, ", ");
 		$data .= ')';
 		echo $data;
	?>",
	
	'_validate' => "<?php
		echo 'array(' . ENTER;
 		foreach($table_cnt as $v){
 			// 图片、文件、时间关键字
 			$rst = (
 				   strtoupper($v['key']) == 'PRI') ||
 				   preg_match($config['field_rule_arr']['file'], $v['field'])  || 
 				   preg_match($config['field_rule_arr']['time'], $v['field'])  ||
 				   preg_match($config['field_rule_arr']['image'], $v['field']
 				); 
 			if($rst){
 				continue;
 			}
 			
 			$data = array();

 			// 验证规则及验证失败后的提示信息,验证条件,附加规则,验证时间
 			if(!$v['default'] && $v['default'] != '0'){
 				//  性别(1-男,2-女)  --->  性别
 				$comment = handleComment($v['comment'], $config['comment_rule_arr']['0']);

 				$str = '';
 				$str .= "'_require', ";
 				$str .= "'{$comment}不能为空', ";
 				$str .= 'self::EXISTS_VALIDATE, ';
 				$str .= "'callback', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['username'], $v['field'])){
 				$str = '';
 				$str .= "'', ";
 				$str .= "'{$v['comment']}已经存在', ";
 				$str .= 'self::EXISTS_VALIDATE, ';
 				$str .= "'unique', ";
 				$str .= 'self::MODEL_INSERT';
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['pwd'], $v['field'])){
 				$pwd = $map_array[$v['field']] ? $map_array[$v['field']] : $v['field'];
 				$str = '';
 				$str .= "'re{$pwd}', ";
 				$str .= "'两次输入的密码不一致', ";
 				$str .= 'self::EXISTS_VALIDATE, ';
 				$str .= "'confirm', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['email'], $v['field'])){
 				$str = '';
 				$str .= "'email', ";
 				$str .= "'{$v['comment']}格式不正确', ";
 				$str .= 'self::EXISTS_VALIDATE, '; // 验证条件
 				$str .= "'regex', ";
 				$str .= 'self::MODEL_BOTH'; // 验证时间 update insert
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['url'], $v['field'])){
 				$str = '';
 				$str .= "'url', ";
 				$str .= "'{$v['comment']}格式不正确', ";
 				$str .= 'self::EXISTS_VALIDATE, ';
 				$str .= "'regex', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['money'], $v['field'])){
 				$str = '';
 				$str .= "'currency', ";
 				$str .= "'{$v['comment']}格式不正确', ";
 				$str .= 'self::EXISTS_VALIDATE, ';
 				$str .= "'regex', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['radio'], $v['field'])){
 				// 多选一
 				$comment = handleComment($v['comment'], $config['comment_rule_arr']['0']);
 				$in = commentToString($v['comment'],$config['comment_rule_arr'], 0);

 				$str = '';
 				$str .= "array({$in}), ";
 				$str .= "'{$comment}数值不在范围内', ";
 				$str .= 'self::EXISTS_VALIDATE , ';
 				$str .= "'in', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			if(preg_match($config['field_rule_arr']['checkbox'], $v['field'])){
 				// 多选多
 				$comment = handleComment($v['comment'], $config['comment_rule_arr']['0']);

 				$str = '';
 				$str .= "'checkBoxValivate_{$v['field']}', ";
 				$str .= "'{$comment}数值不在范围内', ";
 				$str .= 'self::EXISTS_VALIDATE , ';
 				$str .= "'callback', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			if(strpos($v['type'], 'char') !== FALSE){
 				$comment = handleComment($v['comment'], $config['comment_rule_arr']['0']);
 				$length = getTypeLength($v['type']);
 				$length_zh = (int)($length / 3);

 				$str = '';
 				$str .= "'0,{$length_zh}', ";
 				$str .= "'{$comment}长度大于{$length_zh}位', ";
 				$str .= 'self::EXISTS_VALIDATE, ';
 				$str .= "'length', ";
 				$str .= 'self::MODEL_BOTH';
 				$data[] = $str;
 			}
 			

 			echo makeValidateArr($v['field'], $data) . ENTER;
 		}
 		echo T.')';
	?>",

	//生成html内容  add/update.html
	'html' => <?php
		$data = 'array(' . ENTER;
		foreach($table_cnt as $v){
			if(strtoupper($v['key']) == 'PRI' || preg_match($config['field_rule_arr']['time'], $v['field'])){
				continue;
			}
			$data .= T.T;
			$data .= "'";
			$data .= $v['field'];
			$data .= "'";
			$data .= '=>';

			$data .= 'array(';
			if(preg_match($config['field_rule_arr']['textarea'], $v['field'])){
				$data .= "'html_lab'=>'textarea', "; 
				$data .= "'cols'=>'{$textarea_config['cols']}', "; 
				$data .= "'rows'=>'{$textarea_config['rows']}', "; 
			}else{
				$data .= "'html_lab'=>'input', "; 
			}
			$rst = preg_match($config['field_rule_arr']['image'], $v['field']) || preg_match($config['field_rule_arr']['file'], $v['field']); // 图片、文件关键字
			if($rst){
				$data .= "'type'=>'file', ";   //  文件上传(文件/图片)
			}elseif(preg_match($config['field_rule_arr']['radio'], $v['field'])){  
				// 多选一
				$data .= "'type'=>'radio', "; 
				$data .= "'select_default_code'=>'checked', "; 
				$data .= "'select_item'=>".commentToArr($v['comment'],$config['comment_rule_arr']).", ";
			}elseif(preg_match($config['field_rule_arr']['checkbox'], $v['field'])){   
				//  多选多
				$data .= "'type'=>'checkbox', ";   
				$data .= "'select_default_code'=>'checked', "; 
				$data .= "'select_item'=>".commentToArr($v['comment'],$config['comment_rule_arr']).", ";
			}elseif(preg_match($config['field_rule_arr']['email'], $v['field'])){  
				// email
				$data .= "'type'=>'email', "; 
			}elseif(preg_match($config['field_rule_arr']['url'], $v['field'])){  
				// url
				$data .= "'type'=>'url', "; 
			}elseif(preg_match($config['field_rule_arr']['pwd'], $v['field'])){  
				// pwd
				$data .= "'type'=>'password', "; 
			}else{  // 普通文本输入框
				$data .= "'type'=>'text', "; 
			}
			$comment = handleComment($v['comment'], $config['comment_rule_arr']['0']);  //  性别(1-男,2-女)  --->  性别
			$data .= "'text'=>'"; 
			$data .= $comment . "', ";
			$data .= "'tip'=>'请输入{$comment}', "; 
			$data .= "'map'=>'";
			$data .= $map_array[$v['field']] ? $map_array[$v['field']] : $v['field']; 
			$data .= '\'),' . ENTER . ENTER;
		}
		$data .= T.'),' . ENTER;
		echo $data;
	?>


	//==================   search   ======================
	// show.html  model.class.php
	// 一下内容关联show文件的搜索内容以及model文件的searchLimit函数内容
	'search' => <?php
		$data = 'array(' . ENTER;
		foreach($table_cnt as $k=>$v){
			// 主键、图片、文件不进行搜索
			$rst = strtoupper($v['key']) == 'PRI' || 
				   preg_match($config['field_rule_arr']['pwd'], $v['field'])   ||
				   preg_match($config['field_rule_arr']['file'], $v['field'])  || 
				   preg_match($config['field_rule_arr']['image'], $v['field']) ;
			if($rst){
				continue;
			}
			$data .= T.T;
			$data .= "'";
			$data .= $v['field'];
			$data .= "'";
			$data .= '=>';

			$data .= 'array(';
			if(preg_match($config['field_rule_arr']['radio'], $v['field'])){  // 多选一
				$data .= "'type'=>'radio', "; 
				$data .= "'search_method'=>'EQ', ";   
				$data .= "'select_default_code'=>'checked', ";
				$data .= "'select_item'=>".commentToArr($v['comment'],$config['comment_rule_arr']).", ";
			}elseif(preg_match($config['field_rule_arr']['checkbox'], $v['field'])){   //  多选多
				$data .= "'type'=>'checkbox', ";   
				$data .= "'search_method'=>'IN', ";  
				$data .= "'select_default_code'=>'checked', "; 
				$data .= "'select_item'=>".commentToArr($v['comment'],$config['comment_rule_arr']).", ";
			}elseif(preg_match($config['field_rule_arr']['email'], $v['field'])){
				$data .= "'type'=>'email', "; // html5 email插件
				$data .= "'search_method'=>'LIKE', "; 
			}elseif(preg_match($config['field_rule_arr']['url'], $v['field'])){
				$data .= "'type'=>'url', "; // html5 url插件
				$data .= "'search_method'=>'LIKE', "; 
			}elseif(preg_match($config['field_rule_arr']['time'], $v['field'])){
				$data .= "'type'=>'datetime-local', "; // html5 时间插件
				$data .= "'map1'=>'time_start_{$k}', "; 
				$data .= "'map2'=>'time_end_{$k}', "; 
				$data .= "'search_method'=>'BETWEEN', "; 
			}else{  // 普通文本输入框  模糊查询
				$data .= "'type'=>'text', "; 
				$data .= "'search_method'=>'LIKE', "; 
			}
			$comment = handleComment($v['comment'], $config['comment_rule_arr']['0']);  //  性别(1-男,2-女)  --->  性别
			$data .= "'text'=>'"; 
			$data .= $comment . "', ";
			$data .= "'map'=>'";
			$data .= $map_array[$v['field']] ? $map_array[$v['field']] : $v['field']; 
			$data .= '\'),' . ENTER . ENTER;
		}
		$data .= T.'),' . ENTER;
		echo $data;
	?>
);