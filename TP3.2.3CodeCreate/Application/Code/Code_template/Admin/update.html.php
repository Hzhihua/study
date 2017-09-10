<!-- 2017年4月2日  星期天  powerBy  黄志华 -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $config_create['update'];?></title>
<?php echo '<?php $html = C(\'HTML\');?>';?>

	<script src="<?php echo '<?php echo $html[\'p_js\'];?>';?>jquery.js"></script>
<?php foreach($config_create['html'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['file'], $k)):?>

	<link rel="stylesheet" href="<?php echo '<?php echo $html[\'p_css\'];?>'?>html5uploader.css">
	<script src="<?php echo '<?php echo $html[\'p_js\'];?>';?>jQ.html5SliceUpload.js"></script>
	<script type="text/javascript">
	$(function(){
		$('#upload').html5SliceUpload({
			url:'<?php echo U('Pub/Pub/upload');?>',
			onNumEnough: function(num){
				alert('超过文件上传数量限制: '+num);
			}
		});
	});
	</script>
<?php endif;?>
<?php endforeach;?>
</head>
<body>
	<form action="<?php echo U(I('get.ToModule').'/'.$GLOBALS['table_name'].'/'.$config_create['update']);?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="<?php echo $config_create['pk'];?>" value="<?php echo '<?php echo I(\'get.'.$config_create['pk'].'\');?>';?>">
		<table>
<?php foreach($config_create['html'] as $k=>$v):?>
			<tr>
				<td>
					<?php switch($v['html_lab']): case 'input':  // <input></input>?>
<?php if($v['select_item']):   //<input type="checkbox/radio" />?>
<?php echo $v['text'];?>：
				<?php foreach($v['select_item'] as $_k=>$_v):?>
	<?php echo $_v;?>:<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" value="<?php echo $_k;?>"  name="<?php echo $v['map'];if(strtolower($v['type']) == 'checkbox') echo '[]'; // []用于多选多 type="checkbox"?>" <?php echo '<?php if("'.$_k.'" == $'.$v['map'].') echo \''.$v['select_default_code'].'\';?>'?>/>&nbsp;
				<?php endforeach;?>

<?php elseif(preg_match($config['field_rule_arr']['file'], $k)): //<input type="file" />?>
<?php echo $v['text'];?>：<div id="dropFile"><div id="upload"></div></div><ol id="showFileList"></ol>

<?php elseif(preg_match($config['field_rule_arr']['pwd'], $k))://<input type="password" />?>
<?php echo $v['text'];?>：<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" placeholder="<?php echo $v['tip'];?>" name="<?php echo $v['map'];?>"/>
					<br />
<?php echo T.T.T.T.T.$v['text'];?>确认：<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" placeholder="<?php echo $v['tip'];?>" name="re<?php echo $v['map'];?>"/>

<?php else:  // <input type="text/email/url" />?>
<?php echo $v['text'];?>：<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" placeholder="<?php echo $v['tip'];?>" name="<?php echo $v['map'];?>" value="<?php echo '<?php echo $'.$v['map'].';?>'?>"/>
<?php endif;?><?php echo ENTER;break;?>

					<?php case 'textarea':  // <textarea></textarea>?>
<?php echo $v['text'];?>：<<?php echo $v['html_lab']?> placeholder="<?php echo $v['tip'];?>" name="<?php echo $v['map'];?>" cols="<?php echo $v['cols'];?>" rows="<?php echo $v['rows'];?>"><?php echo '<?php echo $'.$v['map'].';?>'?></<?php echo $v['html_lab']?>>
<?php break;?>
<?php endswitch;?>
				</td>
			</tr>
<?php endforeach;?>
			<tr><td colspan="2"><input type="submit" value="添加" name="sub"></td></tr>
		</table>
	</form>
</body>
</html>
<?php 
	foreach($config_create['table_all_fields'] as $k=>$v){
		if(preg_match($config['field_rule_arr']['pwd'], $k)){
			$pwd = $k;
			break;
		}
	}
?>
<script type="text/javascript">
	var inputSubmit = $('input[type="submit"]');
	function sendValidate(obj, _this, __this){
		$.post("<?php echo U(I('get.ToModule').'/'.ucfirst($GLOBALS['table_name']).'/'.'ajaxValidate');?>",obj,function(result){
		    if(result){
		    	alert(result);
		    	inputSubmit.attr('disabled', 'disabled');
		    	_this.parent().css('color', 'red');
		    	if(__this){
		    		__this.parent().css('color', 'red');
		    	}
		    }else{
		    	inputSubmit.removeAttr('disabled');
		    	_this.parent().css('color', '#000');
		    	if(__this){
		    		__this.parent().css('color', '#000');
		    	}
		    }
		});
	}
	$('input[type="password"]').blur(function(){
		var _this = $('input[name="<?php echo $pwd;?>"]');
		var __this = $('input[name="re<?php echo $pwd;?>"]');
		var value0 = _this.val();
		var value1 = __this.val();
		var name0 = _this[0].name;
		var name1 = __this[0].name;
		var obj = {};
		obj[name0] = value0;
		obj[name1] = value1;

		sendValidate(obj, _this);
	});
	$('input[type!="password"]').blur(function(){
		var _this = $(this);
		var value = _this.val();
		var name = _this[0].name;
		var obj = {};
		obj[name] = value;

		sendValidate(obj, _this);
	});
	$('textarea').blur(function(){
		var _this = $(this);
		var value = _this.val();
		var name = _this[0].name;
		var obj = {};
		obj[name] = value;
		
		sendValidate(obj, _this);
	});
</script>
