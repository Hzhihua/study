<?php

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE);
define('ENTER', "\r\n");

/*
function dump($data){
	echo '<pre>';
	var_dump($data);
	echo '<pre><br />';
}
*/

$g = array();
foreach($_GET as $k=>$v){
	if(is_string($v)){
		$g[$k] = trim($v);
	}elseif(is_array($v)){
		foreach ($v as $_k => $_v) {
			$g[$k][$_k] = trim($_v);
		}

	}
}

if(@$g['sub']){

	$mysqli = new mysqli($g['host'], $g['username'], $g['password'], $g['database']); 
	$mysqli->connect_errno && dump($mysqli->connect_error);

	// 获取数据表的结构
	$result = $mysqli->query("show full fields from `{$g['table']}`");
	$table = array();
	while($arr = $result->fetch_assoc()){
		$table[] = $arr;
	}
	
	$values = '';  // 占位符
	$bind_param = '';  // 变量类型
	$key = '';  // 占位符对应的变量名称
	foreach($table as $k=>$v){
		if($v['Key'] == 'PRI') {
			$values .= 'NULL, '; //主键
			continue;
		}else{
			$values .= '?, ';
			$key[$k] = '$val' . $k;
		}

		if(false !== strpos($v['Type'], 'int')){
			$bind_param .= 'i';
		}elseif(false !== strpos($v['Type'], 'char')){
			$bind_param .= 's';
		}elseif(false !== strpos($v['Type'], 'text')){
			$bind_param .= 's';
		}elseif(false !== strpos($v['Type'], 'decimal')){
			$bind_param .= 'd';
		}

	}

	//*****************************************************
	//************* 将sql insert语句写入php文件 **************
	//*****************************************************
	$values = rtrim($values, ', ');
	$file_data = '$sql = "insert into `'.$g['table'].'` values('.$values.')";'.ENTER;
	$file_data .= '$stmt = $mysqli->prepare($sql);'.ENTER;

	
	$tmpKey = '';
	foreach($key as $v){
		$tmpKey .= $v . ', ';
	}
	$tmpKey = rtrim($tmpKey, ', ');

	$file_data .= '$stmt->bind_param("'.$bind_param.'", '.$tmpKey.');'.ENTER;
	
	
	$file_data .= 'for($i = 1; $i<='.$g['for_num'].'; $i++){'.ENTER;		

		$tmpKey = '';
		$n = 0;
		foreach($key as $v){
			$tmpKey .= "\t";
			$tmpKey .= $v.' = "';
			$tmpKey .= $g['data'][$n];
			$tmpKey .= '"';

			if($g['rand'.$n]){
				$tmpKey .= '.($i*' . ($n+10) . ');';  // 是否产生随机数
			}else{
				$tmpKey .= ';';
			}

			$tmpKey .= ENTER;
			$n++;
		}

	$file_data .= $tmpKey;
	$file_data .= '
	$rst = $stmt->execute();
	var_dump($rst);
	if(!$rst) {
		var_dump($stmt->error);
		break;
	}
	usleep(50);
}'.ENTER;

	// 生成php文件
	$fileName = './insert.php';

	file_put_contents($fileName, "<?php".ENTER.$file_data);
	if($g['sub'] == 'insert'){
		if($g['clear']){
			$sql = "truncate table `{$g['table']}`";
			$mysqli->query($sql);
		}
		
		require($fileName);
		@unlink($fileName);
	}
}
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>数据添加</title>
	<style type="text/css">
		form{
			width: 100%;
		}
		table{
			width: 50%;
			margin: 0 auto;
		}
		td{
			width: 50%;
		}
	</style>
</head>
<body>
	<form>
		<table>
			<caption><h1>数据添加</h1></caption>
			<tr>
				<td>数据库地址：</td>
				<td><input type="text" name="host" value="<?php echo $g['host'];?>"></td>
			</tr>
			<tr>
				<td>用户名：</td>
				<td><input type="text" name="username" value="<?php echo $g['username'];?>"></td>
			</tr>
			<tr>
				<td>密码：</td>
				<td><input type="text" name="password" value="<?php echo $g['password'];?>"></td>
			</tr>
			<tr>
				<td>数据库名称：</td>
				<td><input type="text" name="database" value="<?php echo $g['database'];?>"></td>
			</tr>
			<tr>
				<td>表格名称：</td>
				<td><input type="text" name="table" value="<?php echo $g['table'];?>"></td>
			</tr>
			<tr>
				<td>清空表格内容：</td>
				<td>
					清空：<input type="radio" name="clear" value="1" <?php if($g['clear']) echo 'checked';?>>
					保留：<input type="radio" name="clear" value="0" <?php if(!$g['clear']) echo 'checked';?>>
				</td>
			</tr>
			<tr>
				<td>循环次数：</td>
				<td><input type="text" name="for_num" value="<?php echo $g['for_num'];?>"></td>
			</tr>
			<?php if($g['sub']): $n=0;?>
			<?php foreach($key as $k=>$v):?>
				<tr>
					<td><?php echo $table[$k]['Comment'].'('.$table[$k]['Field'].','.$table[$k]['Type'].','.$table[$k]['Default'].')';?></td>
					<td>
						<input type="text" name="data[]" value="<?php echo $g['data'][$n];?>">
						随机：是<input type="radio" name="rand<?php echo $n;?>" value="1" <?php if($g['rand'.$n]) echo 'checked';?>>
						否<input type="radio" name="rand<?php echo $n;?>" value="0" <?php if(!$g['rand'.$n]) echo 'checked';?>>
					</td>
				</tr>
			<?php $n++;endforeach;?>
			<?php endif;?>
			<tr>
				<td><input type="submit" name="sub" value="create"></td>
				<td><input type="submit" name="sub" value="insert"></td>
			</tr>
		</table>
	</form>
</body>
</html>

