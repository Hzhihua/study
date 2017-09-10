<!-- 2017年4月2日  星期天   powerBy  黄志华 -->
<?php 
	function param($url_param,$name){
		$param = 'array(';
		foreach($url_param as $k=>$v){
			$param .= "'";
			$param .= $k;
			$param .= "'";
			$param .= "=>";
			$param .= 'I(\'get.'.$k.'\',\''.$v.'\')';
			$param .= ",";
		}
		$url = "'".ucfirst($GLOBALS['admin_module_name']).'/'.ucfirst($GLOBALS['table_name']).'/'.$name.'\', '.$param;
		return $url;
	}


	$pkUrl = '\'' . $config_create['pk'] . '\'=>';

	$delUrl = param($config['url_param'], $config_create['del']);
	$delUrl .= $pkUrl;
	$updateUrl = param($config['url_param'], $config_create['update']);
	$updateUrl .= $pkUrl;
	// 很乱 打印出这两个变量会比较好理解
	// echo $delUrl.'<br/>';
	// echo $updateUrl.'<br/>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $config_create['show'];?></title>
	<style>
		table{
			border: 1px solid teal;
			border-collapse: collapse;
		}
		th{
			border: 1px solid teal;
			width: 100px;
		}
		tr,td{
			border: 1px solid teal;
		}
	</style>
</head>
<body>
	<div id="search">
		<form action="" method="GET">
			<input type="hidden" name="m" value="<?php echo '<?php echo MODULE_NAME;?>';?>">
			<input type="hidden" name="c" value="<?php echo '<?php echo CONTROLLER_NAME;?>';?>">
			<input type="hidden" name="a" value="<?php echo '<?php echo ACTION_NAME;?>';?>">
			<ul>
<?php foreach($config_create['search'] as $v):?>
				<li>
<?php if(strtoupper($v['search_method']) === 'BETWEEN'):?>
					<?php echo $v['text'];?>：<input type="<?php echo $v['type'];?>" name="<?php echo $v['map1'];?>" value="<?php echo '<?php echo I(\'get.'.$v['map1'].'\');?>';?>"/>
					--
					<input type="<?php echo $v['type'];?>" name="<?php echo $v['map2'];?>" value="<?php echo '<?php echo I(\'get.'.$v['map2'].'\');?>';?>"/>
<?php elseif(strtoupper($v['search_method']) === 'LIKE'):?>					
				<?php echo $v['text'];?>：<input type="<?php echo $v['type'];?>" name="<?php echo $v['map'];?>" value="<?php echo '<?php echo I(\'get.'.$v['map'].'\');?>';?>"/>
<?php elseif(strtoupper($v['search_method']) === 'EQ' && $v['select_item']):  //EQ模式?>
				<?php echo $v['text'];?>：
				<?php foreach($v['select_item'] as $_k=>$_v):?>			
					<?php echo $_v;?>:<input type="<?php echo $v['type'];?>" name="<?php echo $v['map'];?>" value="<?php echo $_k;?>" <?php echo '<?php if(I(\'get.'.$v['map'].'\') == "'.$_k.'") echo \''.$v['select_default_code'].'\';?>'?>/>&nbsp;
				<?php endforeach;?>
<?php elseif(strtoupper($v['search_method']) === 'IN' && $v['select_item']):  //IN模式?>
				<?php echo $v['text'];?>：
				<?php foreach($v['select_item'] as $_k=>$_v):?>			
					<?php echo $_v;?>:<input type="<?php echo $v['type'];?>" name="<?php echo $v['map'];?>[]" value="<?php echo $_k;?>" <?php echo '<?php if(in_array("'.$_k.'", $_GET[\''.$v['map'].'\'])) echo \''.$v['select_default_code'].'\';?>'?>/>&nbsp;
				<?php endforeach;?>
<?php endif;?>
				</li>
<?php endforeach;?>
				<li><input type="submit" value="搜索"></li>
			</ul>
		</form>
	</div>
	<table>
		<tr>
		<?php foreach($config_create['table_all_fields'] as $v):?>
			<th><?php echo $v;?></th>
		<?php endforeach;?>
			<th colspan="2">操作</th>
		</tr>
		<?php echo '<?php foreach($data as $v):?>' .ENTER?>
		<tr>
<?php $n=0; foreach($config_create['table_all_fields'] as $_k=>$_v): $n++;?>
			<td><?php echo '<?php echo $v[\''.$_k.'\'];?>'?></td>
<?php endforeach;?>
			<td>
				<a href="<?php echo '<?php echo U('.$updateUrl.'$v[\''.$config_create['pk_true'].'\']));?>';?>">编辑</a>
			</td>
			<td><a href="<?php echo '<?php echo U('.$delUrl.'$v[\''.$config_create['pk_true'].'\']));?>';?>">删除</a></td>
		</tr>
		<?php echo '<?php endforeach;?>'.ENTER?>
		<tr><td colspan="<?php echo $n+2;?>"><?php echo '<?php echo $page;?>'?></td></tr>
	</table>
</body>
</html>

